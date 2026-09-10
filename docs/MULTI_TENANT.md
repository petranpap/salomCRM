# Multi-Tenancy Setup for Salon Manager

## Overview

This document outlines the steps required to configure the Salon Manager application for multi-tenancy. The application is designed to support multiple tenants using the same codebase, allowing for efficient management of resources and scalability.

## Configuration

1. **Environment Setup**
   - Update your `.env` file to set the application mode to multi-tenant:
     ```
     APP_MODE=multi
     ```

2. **Database Configuration**
   - Ensure that your database is set up to support multiple tenants. Each tenant will have its own database schema. You can use the `stancl/tenancy` package for managing tenant databases.
   - Configure the `tenancy.php` file in the `config` directory to define tenant-specific settings, such as the database connection and tenant identification.

3. **Routing**
   - Set up subdomain routing for tenants. Modify the `routes/web.php` and `routes/api.php` files to handle tenant-specific routes.
   - Use middleware to identify the tenant based on the subdomain and load the appropriate tenant configuration.

4. **Tenant Creation**
   - Implement a command or controller method to create new tenants. This should include creating a new database schema for the tenant and any necessary initial data.
   - Use the `stancl/tenancy` package's features to manage tenant creation and deletion.

5. **Storage and Assets**
   - Configure storage paths to be tenant-specific. This can be done by prefixing storage paths with the tenant identifier.
   - Ensure that uploaded files and assets are stored in a way that they are accessible only to the respective tenant.

6. **Testing Multi-Tenancy**
   - Test the multi-tenancy setup by creating multiple tenants and verifying that data is isolated between them.
   - Ensure that users can only access data relevant to their tenant.

## Conclusion

By following these steps, you can successfully configure the Salon Manager application for multi-tenancy. This setup will allow you to efficiently manage multiple salons while maintaining data integrity and security.