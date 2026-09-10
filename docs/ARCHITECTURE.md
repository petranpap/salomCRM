# Architecture Overview

## Introduction
The Salon Management web application is designed to provide a comprehensive solution for managing appointments, customers, products, and inventory in a salon environment. Built using Laravel 11, PHP 8.2, and MySQL/MariaDB, the application is structured to support both single-tenant and multi-tenant architectures.

## Core Technologies
- **Backend**: Laravel 11 (PHP 8.2)
- **Database**: MySQL/MariaDB
- **Frontend**: TailwindCSS, DaisyUI
- **Image Handling**: Spatie Laravel Medialibrary, Intervention Image
- **Queue Management**: Redis (preferred) and database fallback

## Application Structure
The application follows a modular architecture, separating concerns into distinct components:

### 1. Controllers
Controllers handle incoming requests and return responses. They are organized into:
- **Web Controllers**: Manage user interactions and views (e.g., `AppointmentController`, `CustomerController`).
- **API Controllers**: Handle API requests for CRUD operations (e.g., `AppointmentApiController`, `CustomerApiController`).

### 2. Models
Models represent the application's data and business logic. Key models include:
- `User`: Represents users of the application.
- `Customer`: Represents salon customers.
- `Appointment`: Represents scheduled appointments.
- `Product`: Represents products in inventory.

### 3. Services
Services encapsulate business logic and are reusable across controllers. Important services include:
- `InventoryAlertService`: Manages low stock alerts.
- `ReminderService`: Sends appointment reminders.
- `ImageVariantService`: Handles image processing and variant generation.

### 4. Jobs
Jobs are queued tasks that run asynchronously. Key jobs include:
- `SendAppointmentReminder`: Sends reminders for upcoming appointments.
- `InventoryLowStockJob`: Checks for low stock items and triggers alerts.
- `GenerateImageVariants`: Creates responsive image variants upon upload.

### 5. Middleware
Middleware handles request filtering and processing. It can be used for authentication, logging, and more.

### 6. Requests
Form request classes define validation rules for incoming data, ensuring data integrity.

## Database Schema
The database schema is designed to support the application's core functionalities. Key tables include:
- `users`: Stores user information and roles.
- `customers`: Stores customer details and consent information.
- `appointments`: Stores appointment details, including status and timestamps.
- `products`: Stores product information, including stock levels and pricing.

## Frontend Design
The frontend utilizes TailwindCSS and DaisyUI to create a modern, responsive user interface. Key design features include:
- A consistent design system with a soft pastel theme.
- Micro-interactions for enhanced user experience.
- Dark mode toggle with persistence in localStorage.
- Responsive layouts for mobile-first design.

## Multi-Tenancy
The application is designed to be easily extendable to a multi-tenant architecture. Key considerations include:
- Environment configuration to switch between single and multi-tenant modes.
- Use of `stancl/tenancy` for managing tenant databases and routing.

## Conclusion
This architectural overview provides a high-level understanding of the Salon Management web application. The modular design, combined with modern technologies and a focus on user experience, ensures a robust and scalable solution for salon management.