# Deployment — Studio Kassandra

## Automated deploys (CI/CD)

`.github/workflows/ci-cd.yml` runs the full test suite on every push/PR, and — on a
successful push to `main` — SSHes into the configured VPS and deploys automatically
(`git reset --hard origin/main`, dependency install, asset build, migrations, cache
rebuild). It needs four GitHub Actions secrets set once (repo Settings → Secrets and
variables → Actions): `VPS_HOST`, `VPS_USERNAME`, `VPS_SSH_KEY`, `VPS_PORT` — and the
`DEPLOY_PATH` inside the workflow file updated to the real path on the server. Until
those are set, the deploy job simply fails to connect; it never touches an
unconfigured server. Step 6 below covers setting these up.

Everything else in this document is the one-time manual server setup the automated
deploy assumes already exists.

## 1. Server prerequisites

Confirmed working against **PHP 8.4** and **MariaDB 10.11** — this assumes a
Debian/Ubuntu server (matching your MariaDB build).

```bash
sudo apt update
sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring \
  php8.4-xml php8.4-curl php8.4-gd php8.4-zip php8.4-bcmath php8.4-intl \
  nginx composer git
```

- **`php8.4-gd`** — needed to embed a salon's logo on printed receipts. Without it,
  receipts still print fine, just without the logo (this degrades gracefully; it
  won't crash anything — see `Salon::logoDataUri()`).
- **Node.js + npm** — needed once, to build frontend assets. Easiest via
  [NodeSource](https://github.com/nodesource/distributions) for a current LTS:
  ```bash
  curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
  sudo apt install -y nodejs
  ```

## 2. Database

```bash
sudo mysql -u root
```
```sql
CREATE DATABASE studio_kassandra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'studio_kassandra'@'localhost' IDENTIFIED BY 'a-real-generated-password';
GRANT ALL PRIVILEGES ON studio_kassandra.* TO 'studio_kassandra'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3. A dedicated deploy user + SSH key (for GitHub Actions)

Don't reuse your own personal SSH key for automated deploys. Create a separate,
narrowly-scoped one:

```bash
# On the VPS, as root or with sudo:
sudo adduser --disabled-password deploy
sudo usermod -aG www-data deploy   # so it can own files the web server also reads

# Generate a keypair FOR THIS PURPOSE ONLY — do this on your own machine, not the VPS:
ssh-keygen -t ed25519 -f ~/.ssh/studio_kassandra_deploy -C "github-actions-deploy" -N ""

# Copy the PUBLIC key's contents to the VPS:
sudo mkdir -p /home/deploy/.ssh
echo "<contents of studio_kassandra_deploy.pub>" | sudo tee -a /home/deploy/.ssh/authorized_keys
sudo chown -R deploy:deploy /home/deploy/.ssh
sudo chmod 700 /home/deploy/.ssh
sudo chmod 600 /home/deploy/.ssh/authorized_keys
```

Keep `studio_kassandra_deploy` (the *private* key) — its contents become the
`VPS_SSH_KEY` GitHub secret in step 6. Give `deploy` passwordless `sudo` only for the
specific commands it needs (e.g. restarting PHP-FPM), if any — the deploy script in
`ci-cd.yml` doesn't currently need `sudo` for anything.

## 4. First-time app setup (manual — the automated deploy only ever updates this)

```bash
sudo mkdir -p /var/www/studio-kassandra
sudo chown deploy:deploy /var/www/studio-kassandra

# As the deploy user:
su - deploy
git clone git@github.com:petranpap/salomCRM.git /var/www/studio-kassandra
cd /var/www/studio-kassandra

composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm ci
npm run build

cp .env.example .env
php artisan key:generate
```

**Edit `.env` for real production values** — the ones that matter most:
```
APP_NAME="Studio Kassandra"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-real-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=studio_kassandra
DB_USERNAME=studio_kassandra
DB_PASSWORD=the-password-from-step-2

CACHE_STORE=file
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=your-real-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-real-smtp-username
MAIL_PASSWORD=your-real-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-real-domain.example
```
`APP_ENV=production` matters — leaving it `local` shows real stack traces to real
customers on any error. Also see Round 23 in `Fixes.md`: this project's
`.env.example` had a stale pre-Laravel-11 env var name (`CACHE_DRIVER` instead of
`CACHE_STORE`) that silently broke login — it's fixed now, but double-check
`CACHE_STORE=file` is actually what's in your `.env`, not a leftover `CACHE_DRIVER`.

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set ownership so both `deploy` and the web server (`www-data`) can write where
Laravel needs to:
```bash
sudo chown -R deploy:www-data /var/www/studio-kassandra/storage /var/www/studio-kassandra/bootstrap/cache
sudo chmod -R 775 /var/www/studio-kassandra/storage /var/www/studio-kassandra/bootstrap/cache
```

## 5. Nginx + PHP-FPM

```nginx
server {
    listen 80;
    server_name your-real-domain.example;
    root /var/www/studio-kassandra/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 10M;   # logo uploads are capped at 2MB app-side, this just
                                 # gives headroom rather than nginx rejecting first
}
```

```bash
sudo ln -s /etc/nginx/sites-available/studio-kassandra /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

**SSL** (once DNS actually points at this server):
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-real-domain.example
```

## 6. Wire up the GitHub Actions secrets

Repo → Settings → Secrets and variables → Actions → New repository secret:

| Secret | Value |
|---|---|
| `VPS_HOST` | the server's IP or domain |
| `VPS_USERNAME` | `deploy` |
| `VPS_SSH_KEY` | contents of the **private** key from step 3 (`cat ~/.ssh/studio_kassandra_deploy`) |
| `VPS_PORT` | `22` (or whatever port SSH actually listens on) |

Then edit `.github/workflows/ci-cd.yml` and change:
```bash
DEPLOY_PATH="/var/www/salon-manager"  # <-- change to the real path
```
to
```bash
DEPLOY_PATH="/var/www/studio-kassandra"
```

Commit that change, push to `main`, and the deploy job should run for real for the
first time.

## 7. Scheduled commands (cron)

```bash
crontab -e   # as the deploy user, or whichever user owns the app
```
```
* * * * * php /var/www/studio-kassandra/artisan appointments:send-reminders
0 3 * * * php /var/www/studio-kassandra/artisan db:backup
```
- Reminders only send within their configured window, so running every minute is a
  safe no-op the rest of the time.
- Backups once a day, not every minute. The command is `db:backup`, not `backup:db`.
- No `inventory:check-low-stock` entry needed — low-stock alerts are computed live
  from the dashboard query, not a scheduled job.

## 8. First login

There's no public sign-up. On the server, once, create your own platform admin:
```bash
cd /var/www/studio-kassandra
php artisan make:superadmin
```
Then log in at your real domain and follow the Day-1 flow: **Platform Admin → Salons
→ New Salon**, then **Staff → New Staff** to create that salon's owner account.

## Optional: queues

Only needed if you turn on queued jobs (nothing in the app currently requires this —
`QUEUE_CONNECTION=sync` is fine as shipped). If you do:
```env
QUEUE_CONNECTION=redis
```
```bash
sudo apt install -y redis-server
php artisan queue:work
```
— and run `queue:work` under something that restarts it (systemd unit or
supervisor), not directly in a terminal.
