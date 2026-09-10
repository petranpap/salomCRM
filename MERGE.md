
PROJECT AUDIT: SALLON CRM
HTTP Controllers
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Http/Controllers/DashboardController.php	Displays dashboard with today's appointments, low stock, no-shows, todos	None	None (Uses Models)	Appointments, Products, Todos
app/Http/Controllers/AppointmentController.php	CRUD for appointments with calendar view	AppointmentRequest (customer_id, service_id, staff_id, start, end, status, notes)	None (Uses Models)	Appointments with relationships
app/Http/Controllers/CustomerController.php	CRUD for customers with pagination	CustomerRequest (name, phone, email, notes, consent_email/sms, date_of_birth)	None	Customers (paginated)
app/Http/Controllers/ProductController.php	CRUD for products with pagination	ProductRequest (sku, name, category, prices, stock_qty, threshold_qty, status, images)	None	Products (paginated)
app/Http/Controllers/ServiceController.php	CRUD for services	ServiceRequest (name, category, duration_min, base_price, is_active)	None	Services
app/Http/Controllers/StaffController.php	CRUD for staff with role and specialty management	name, email, role, specialty, color, password, is_active	None	Staff and StaffProfile relationships
app/Http/Controllers/TodoController.php	CRUD for todos with role-based filtering	title, due_date, type, assigned_to, status	None	Todos (role-filtered)
app/Http/Controllers/ProfileController.php	User profile editing and account deletion	name, email, password	None	User authentication state
API Controllers
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Http/Controllers/Api/AppointmentApiController.php	JSON API for appointments CRUD with pagination	AppointmentRequest	GET/POST/PATCH/DELETE /api/appointments	Appointments (JSON)
app/Http/Controllers/Api/CustomerApiController.php	JSON API for customers CRUD with pagination	CustomerRequest	GET/POST/PATCH/DELETE /api/customers	Customers (JSON)
app/Http/Controllers/Api/ProductApiController.php	JSON API for products CRUD with low stock alerts	ProductRequest	GET/POST /api/products, GET /api/inventory/alerts	Products (JSON)
app/Http/Controllers/Api/TodoApiController.php	JSON API for todos list	None	GET /api/todos	Todos (JSON)
Auth Controllers
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Http/Controllers/Auth/AuthenticatedSessionController.php	Handles login and logout	email, password, remember	Auth::attempt	Session state
app/Http/Controllers/Auth/RegisteredUserController.php	Handles user registration	name, email, password, password_confirmation	None	New User creation
Models
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Models/User.php	User auth model with role and salon relationship	name, email, password, role, salon_id	None	Auth, StaffProfile, Salon
app/Models/Customer.php	Customer entity with appointments and treatments	name, phone, email, notes, consent flags, date_of_birth, salon_id	None	Appointments, Treatments
app/Models/Appointment.php	Appointment linking customers, services, and staff	customer_id, service_id, staff_id, start, end, status, notes	None	Scheduling, Treatments
app/Models/Product.php	Product inventory model with low stock detection	sku, name, category, brand, prices, stock_qty, threshold_qty, status, image_url	None	Inventory, Movements, Treatments
app/Models/Service.php	Service offerings for salon	name, category, duration_min, base_price, is_active	None	Appointments
app/Models/StaffProfile.php	Staff details linked to User	user_id, working_hours, color, specialty, is_active	None	User relationship
app/Models/Todo.php	Task management model	title, type, status, due_date, assigned_to, payload	None	Task tracking
app/Models/Treatment.php	Treatment records from appointments	appointment_id, customer_id, service_id, staff_id, date, price, products_used, notes	None	Customer treatments, Products
app/Models/ProductMovement.php	Inventory movement tracking	product_id, qty_change, reason, ref_type, ref_id, user_id	None	Product history
app/Models/Salon.php	Salon entity for multi-tenancy	name, phone, email, address, timezone, is_active	None	Users, Customers
Services
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Services/ImageVariantService.php	Generates image variants at 4 sizes (128/320/640/1024)	filePath (string)	Intervention Image library	Image variants storage
app/Services/InventoryAlertService.php	Checks for low stock and creates todos	threshold (default: 5)	None	Low stock todos
app/Services/ReminderService.php	Sends appointment reminders to customers	None	Notification::send	Appointment reminders
Form Requests (Validation)
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Http/Requests/AppointmentRequest.php	Validates appointment data with date logic	customer_id, service_id, staff_id, start, end, status, notes	None	Validation rules
app/Http/Requests/CustomerRequest.php	Validates customer data	name, phone, email, notes, consent flags, date_of_birth	None	Validation rules
app/Http/Requests/ProductRequest.php	Validates product data and images	sku, name, category, prices, stock_qty, threshold_qty, status, images	None	Validation rules
app/Http/Requests/ServiceRequest.php	Validates service data	name, category, duration_min, base_price, is_active	None	Validation rules
app/Http/Requests/ProfileUpdateRequest.php	Validates profile update with unique email check	name, email	None	Validation rules
app/Http/Requests/Auth/LoginRequest.php	Validates and authenticates login with rate limiting	email, password, remember	Auth::attempt, RateLimiter	Login, Rate limiting
Jobs (Queue Workers)
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Jobs/SendAppointmentReminder.php	Queued job to send appointment reminders	Appointment model	Notification::send	Notification dispatch
app/Jobs/InventoryLowStockJob.php	Queued job to check low stock and notify	InventoryAlertService	None	Low stock alerts
app/Jobs/GenerateImageVariants.php	Queued job to generate image variants	imagePath (string)	ImageVariantService	Image variant generation
Console Commands
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
app/Console/Commands/SendAppointmentReminders.php	CLI: send reminders for upcoming appointments (2-day window)	None	Carbon date calculations	Appointment reminders
app/Console/Commands/CheckLowStock.php	CLI: check and report low stock items	InventoryAlertService	InventoryAlertService::checkLowStock	Low stock report
app/Console/Commands/BackupDatabase.php	CLI: backup database to SQL file	None	Artisan::call db:dump	Database backup
app/Console/Commands/SetupSalon.php	CLI: create salon and assign users	name (optional)	None	Salon + User assignment
Frontend (JavaScript)
File Path	What it does	Data / Props it expects	API endpoints it calls	State it manages
resources/js/app.js	App entry point — Alpine.js + date picker init with auto-end time calc	input[type="date/datetime-local"] elements	None	Date pickers, auto-end calc
resources/js/bootstrap.js	Axios configuration and HTTP headers setup	None	None	HTTP headers
resources/js/calendar.js	FullCalendar init with drag/resize event handling	#calendar DOM element	GET /api/appointments, PATCH /api/appointments/{id}	Calendar events, drag/resize
resources/js/components/filepond-init.js	FilePond file upload configuration with image preview	input[type="file"] element	None	FilePond instance, image preview
resources/js/components/product-gallery.js	Product image gallery display and management	product_images[] input, image objects (url/alt)	None	Gallery rendering
Routes
File Path	What it does
routes/web.php	Web routes with auth middleware → resource controllers
routes/api.php	JSON API routes with Sanctum auth → API controllers
Config Files
File Path	What it does
package.json	Node deps (Tailwind, FullCalendar, FilePond, Alpine.js) + build scripts
vite.config.ts	Vite build config with Laravel plugin
tailwind.config.js	Tailwind theme with custom salon colors, fonts, spacing
postcss.config.js	PostCSS plugins (Tailwind, autoprefixer)
config/queue.php	Queue driver configuration
config/medialibrary.php	Media library config for image handling
config/tenancy.php	Multi-tenancy configuration
F