# Salon Management Web Application

Welcome to the Salon Management Web Application! This application is designed to help salon owners manage their appointments, customers, products, and inventory efficiently. Built with Laravel 11, PHP 8.2, and MySQL/MariaDB, it offers a modern and responsive user interface using TailwindCSS and DaisyUI.

## Features

- **Appointments Management**: Schedule, update, and manage appointments with a user-friendly calendar interface.
- **Customer Management**: Maintain customer profiles, treatment history, and communication preferences.
- **Product Inventory**: Manage products with detailed information, including stock levels and low-stock alerts.
- **To-Do Lists**: Keep track of tasks and reminders related to appointments and inventory.
- **Dashboard**: Get an overview of today's appointments, low-stock products, and quick actions.

## Installation

1. **Clone the repository**:
   ```
   git clone <repository-url>
   cd salon-manager
   ```

2. **Install dependencies**:
   ```
   composer install
   npm install
   ```

3. **Set up the environment**:
   - Copy the `.env.example` file to `.env` and configure your database and other settings.
   ```
   cp .env.example .env
   ```

4. **Generate application key**:
   ```
   php artisan key:generate
   ```

5. **Run migrations**:
   ```
   php artisan migrate
   ```

6. **Seed the database** (optional):
   ```
   php artisan db:seed
   ```

7. **Run the application**:
   ```
   php artisan serve
   ```

## Usage

- Access the application at `http://localhost:8000`.
- Use the provided authentication system to log in or register.
- Navigate through the dashboard to manage appointments, customers, products, and tasks.

## Technologies Used

- **Backend**: Laravel 11, PHP 8.2
- **Database**: MySQL/MariaDB
- **Frontend**: TailwindCSS, DaisyUI, Heroicons
- **Image Handling**: Spatie Laravel Medialibrary
- **Task Scheduling**: Laravel Scheduler

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.

## Documentation

For detailed documentation on architecture, deployment, and multi-tenancy setup, please refer to the `docs` directory.