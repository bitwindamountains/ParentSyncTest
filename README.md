# ParentSync - School Communication Platform

ParentSync is a centralized platform designed to improve communication and engagement between schools and parents. The platform supports web-based access for admins and teachers, and a mobile application for parents.

## Features

### Admin Features (Web - Laravel Blade)
- Add/manage teachers, parents, and students
- Manage grade levels, sections, and subject offerings
- Create announcements and school-wide events
- Assign sections to teachers
- View school-wide attendance and activity logs

### Teacher Features (Web - Laravel Blade)
- Post class or section-level announcements/events
- Take daily section attendance
- Create consent forms linked to events
- View parent consent responses
- View student profiles with linked parent info

### Parent Features (Mobile App - Ionic + Angular)
- View announcements and upcoming events
- Receive and respond to consent forms
- View student attendance
- Link child via student ID (and wait for verification)
- Contact teachers/admin (future feature)

## Project Structure

```
ParentSync/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Authentication
│   │   │   ├── Admin/                      # Admin controllers
│   │   │   └── Teacher/                    # Teacher controllers
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php         # Admin access control
│   │       └── TeacherMiddleware.php       # Teacher access control
│   └── Models/                             # Eloquent models
├── database/
│   ├── migrations/                         # Database migrations
│   └── seeders/
│       └── DatabaseSeeder.php              # Initial data
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php              # Main layout
│       ├── auth/
│       │   └── login.blade.php            # Login page
│       ├── admin/
│       │   └── dashboard.blade.php        # Admin dashboard
│       └── teacher/
│           └── dashboard.blade.php        # Teacher dashboard
└── routes/
    └── web.php                            # Web routes
```

## Database Schema

The application uses a comprehensive database schema with the following main tables:

- **Users**: Authentication and role management
- **Admins/Teachers/Parents**: Role-specific user profiles
- **Schools/Grades/Sections**: Academic structure
- **Students**: Student information and enrollment
- **Announcements/Events**: Communication features
- **ConsentForms/ConsentSignatures**: Parent consent management
- **AttendanceRecords**: Daily attendance tracking

## Setup Instructions

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ParentSync
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   - Update `.env` file with your database credentials
   - Set `SESSION_DRIVER=database` for multi-server compatibility

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Credentials

After running the seeder, you can login with:

### Admin Account
- **Username**: `admin`
- **Password**: `admin`

### Teacher Account
- **Username**: `teacher001`
- **Password**: `password123`

## Development Guidelines

### For Admin Feature Developer
- Work in `app/Http/Controllers/Admin/` directory
- Create views in `resources/views/admin/` directory
- Add routes in `routes/web.php` under admin middleware group
- Focus on user management, school structure, and system-wide features

### For Teacher Feature Developer
- Work in `app/Http/Controllers/Teacher/` directory
- Create views in `resources/views/teacher/` directory
- Add routes in `routes/web.php` under teacher middleware group
- Focus on classroom management, attendance, and communication features

### For Parent Mobile App Developer
- Create a separate Ionic + Angular project
- Use Laravel Sanctum for API authentication
- Implement API endpoints in `app/Http/Controllers/Api/` directory
- Focus on mobile-specific features and user experience

## Authentication & Authorization

- **Session-based authentication** for web interface
- **Laravel Sanctum** for API authentication (mobile app)
- **Role-based middleware** for access control
- **Database session driver** for multi-server compatibility

## API Endpoints (For Mobile App)

The following API endpoints will be needed for the mobile app:

```
POST /api/login                    # Parent login
GET  /api/announcements           # Get announcements
GET  /api/events                  # Get events
GET  /api/consent-forms          # Get consent forms
POST /api/consent-signatures     # Submit consent
GET  /api/attendance             # Get student attendance
POST /api/link-student           # Link student to parent
```

## Contributing

1. Create a feature branch from `main`
2. Implement your feature following the established patterns
3. Test thoroughly
4. Submit a pull request with clear description

## Team Responsibilities

- **Admin Developer**: User management, school structure, system administration
- **Teacher Developer**: Classroom features, attendance, teacher-specific functionality
- **Mobile Developer**: Parent mobile app, API integration, mobile UX

## Security Notes

- All passwords are hashed using Laravel's built-in hashing
- Session data is stored in database for multi-server compatibility
- Role-based access control implemented
- CSRF protection enabled
- Input validation on all forms

## Deployment

For production deployment:
1. Set `APP_ENV=production`
2. Configure database for production
3. Set up proper web server (Apache/Nginx)
4. Configure SSL certificates
5. Set up database backups
6. Configure session storage for multi-server setup

## Support

For questions or issues, please contact the development team or create an issue in the repository.
