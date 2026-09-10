# Deployment Documentation for Salon Management Web Application

## Automated deploys (CI/CD)

`.github/workflows/ci-cd.yml` runs the full test suite on every push/PR, and — on a
successful push to `main` — SSHes into the configured VPS and deploys automatically
(`git reset --hard origin/main`, dependency install, asset build, migrations, cache
rebuild). It needs four GitHub Actions secrets set once, in the repo's Settings →
Secrets and variables → Actions: `VPS_HOST`, `VPS_USERNAME`, `VPS_SSH_KEY`, `VPS_PORT`
— and the `DEPLOY_PATH` inside the workflow file updated to the real path on the
server. Until those are set, the deploy job simply fails to connect; it never touches
an unconfigured server.

Everything below is the manual, first-time server setup the automated deploy assumes
already exists — a fresh server, or troubleshooting.

## Prerequisites

1. **Server Requirements**
   - PHP 8.2 or higher
   - PHP GD or Imagick extension — required to embed a salon's logo on printed receipts;
     without either, receipts still print fine, just without the logo
   - Composer
   - MySQL/MariaDB
   - Node.js and npm (for asset compilation)
   - Redis (optional, for queue management)

2. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd salon-manager
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

## Environment Configuration

1. **Copy the Example Environment File**
   ```bash
   cp .env.example .env
   ```

2. **Update the `.env` File**
   - Set your database connection details:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_user
     DB_PASSWORD=your_database_password
     ```
   - Configure other necessary environment variables as needed.

## Database Setup

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed the Database (Optional)**
   ```bash
   php artisan db:seed
   ```

3. **Link Public Storage**
   ```bash
   php artisan storage:link
   ```
   Required for uploaded files (salon logos) to be reachable over HTTP — without this,
   logo uploads still save but never display, on-screen or on receipts.

## Asset Compilation

1. **Compile Assets**
   ```bash
   npm run build
   ```

## Running the Application

1. **Start the Development Server**
   ```bash
   php artisan serve
   ```

2. **Access the Application**
   - Open your browser and navigate to `http://localhost:8000`.

## Setting Up Queues (Optional)

1. **Configure Queue Driver in `.env`**
   ```
   QUEUE_CONNECTION=redis
   ```

2. **Start the Queue Worker**
   ```bash
   php artisan queue:work
   ```

## Scheduled Commands

1. **Set up cron jobs**
   - Appointment reminders need to run frequently (they only send within the configured
     reminder window, so running every minute is safe — it's a no-op outside the window):
     ```bash
     * * * * * php /path-to-your-project/artisan appointments:send-reminders
     ```
   - Database backups should run once a day, not every minute (the command is
     `db:backup`, not `backup:db`):
     ```bash
     0 3 * * * php /path-to-your-project/artisan db:backup
     ```
   - There is no `inventory:check-low-stock` command — low-stock alerts are computed
     live from the dashboard query, not a scheduled job, so no cron entry is needed
     for inventory.

## Additional Notes

- Ensure that your server meets all the requirements for running Laravel applications.
- For production environments, consider using a web server like Nginx or Apache to serve your application.
- Monitor your application logs for any errors or issues.

This documentation provides a basic overview of deploying the Salon Management web application. For more detailed configurations and optimizations, refer to the Laravel documentation.