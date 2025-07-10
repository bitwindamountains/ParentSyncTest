# ParentSync Teacher Interface

A comprehensive Laravel-based teacher portal for the ParentSync school-parent engagement system.

## Features

### 🎯 Core Functionality
- **Dashboard**: Overview of classes, students, and recent activities
- **Attendance Management**: Mark and view student attendance with multiple status options
- **Announcements**: Create announcements with flexible scope (General, Section, Class, Individual)
- **Events**: Manage school events with participant tracking
- **Consent Forms**: Create and track consent forms linked to events
- **Student Management**: View assigned students and their details

### 🔐 Security
- Teacher-only authentication with role-based access
- Secure middleware protection for all teacher routes
- Input validation and sanitization

### 📊 Data Management
- Eloquent relationships for efficient data queries
- CSV export functionality for attendance and consent forms
- Real-time form validation and error handling

## Database Schema

The system uses the following key tables:
- `Users` - Authentication and role management
- `Teachers` - Teacher profiles and information
- `Students` - Student records with section assignments
- `Sections` - Class sections with teacher assignments
- `Classes` - Subject-specific classes
- `AttendanceRecords` - Daily attendance tracking
- `Announcements` - Communication with flexible scope
- `Events` - School events and activities
- `ConsentForms` - Parent consent tracking

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Laravel 12.0+

### 1. Clone and Install Dependencies
```bash
git clone <repository-url>
cd parentSync
composer install
```

### 2. Environment Configuration
Copy the `.env.example` file and configure your database:
```bash
cp .env.example .env
```

Update the database configuration in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ParentSync
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Setup
```bash
# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database with test data
php artisan db:seed
```

### 4. Start the Application
```bash
php artisan serve
```

## Test Data

The seeder creates the following test data:
- **School**: Sample Elementary School
- **Teacher**: John Smith (username: `teacher1`, password: `password123`)
- **Sections**: 1-A and 1-B (Grade 1)
- **Students**: 6 students across the sections
- **Classes**: Mathematics, English, and Science classes

## Usage Guide

### Teacher Login
1. Navigate to `/login`
2. Use credentials: `teacher1` / `password123`
3. Access the teacher dashboard at `/teacher/dashboard`

### Marking Attendance
1. Go to **Attendance** in the sidebar
2. Select a section or class
3. Choose a date
4. Mark attendance status for each student (Present, Absent, Late, Excused)
5. Save the attendance record

### Creating Announcements
1. Go to **Announcements** → **Create New**
2. Choose scope:
   - **General**: Visible to all students/parents
   - **Section**: Visible to specific section
   - **Class**: Visible to specific class
   - **Individual**: Visible to selected students only
3. Fill in title and content
4. Submit the announcement

### Managing Events
1. Go to **Events** → **Create New**
2. Fill in event details (title, description, date, time, location)
3. Choose scope (General, Section, or Class)
4. Optionally manage participants

### Creating Consent Forms
1. Go to **Consent Forms** → **Create New**
2. Link to an event (optional)
3. Select target section or class
4. Set deadline
5. Track parent signatures

## API Endpoints

### Teacher Routes (Protected)
```
GET    /teacher/dashboard                    # Teacher dashboard
GET    /teacher/classes                      # List teacher's classes
GET    /teacher/students                     # List teacher's students
GET    /teacher/attendance                   # Mark attendance
POST   /teacher/attendance                   # Save attendance
GET    /teacher/attendance/history           # View attendance history
GET    /teacher/attendance/export            # Export attendance data

# Announcements
GET    /teacher/announcements                # List announcements
GET    /teacher/announcements/create         # Create form
POST   /teacher/announcements                # Store announcement
GET    /teacher/announcements/{id}           # View announcement
GET    /teacher/announcements/{id}/edit      # Edit form
PUT    /teacher/announcements/{id}           # Update announcement
DELETE /teacher/announcements/{id}           # Delete announcement

# Events
GET    /teacher/events                       # List events
GET    /teacher/events/create                # Create form
POST   /teacher/events                       # Store event
GET    /teacher/events/{id}                  # View event
GET    /teacher/events/{id}/edit             # Edit form
PUT    /teacher/events/{id}                  # Update event
DELETE /teacher/events/{id}                  # Delete event

# Consent Forms
GET    /teacher/consent-forms                # List consent forms
GET    /teacher/consent-forms/create         # Create form
POST   /teacher/consent-forms                # Store consent form
GET    /teacher/consent-forms/{id}           # View consent form
GET    /teacher/consent-forms/{id}/edit      # Edit form
PUT    /teacher/consent-forms/{id}           # Update consent form
DELETE /teacher/consent-forms/{id}           # Delete consent form
```

## Model Relationships

### Teacher Model
```php
// Relationships
$teacher->user()              // Belongs to User
$teacher->sections()          // Has many Sections
$teacher->classes()           // Has many Classes
$teacher->attendanceRecords() // Has many AttendanceRecords

// Helper methods
$teacher->getStudents()       // Get all assigned students
$teacher->getClassesWithStudents() // Get classes with student data
```

### Student Model
```php
// Relationships
$student->section()           // Belongs to Section
$student->parents()           // Belongs to many Parents
$student->attendanceRecords() // Has many AttendanceRecords
$student->getTeacher()        // Get assigned teacher

// Helper methods
$student->full_name           // Get full name attribute
```

### Announcement Model
```php
// Relationships
$announcement->creator()      // Belongs to User
$announcement->section()      // Belongs to Section
$announcement->classRoom()    // Belongs to ClassRoom
$announcement->recipients()   // Has many AnnouncementRecipients

// Helper methods
$announcement->scope_display  // Get formatted scope display
```

## Security Features

### Middleware Protection
- `auth` middleware ensures user authentication
- `teacher` middleware validates teacher role and profile
- CSRF protection on all forms
- Input validation and sanitization

### Authorization
- Teachers can only access their assigned sections/classes
- Teachers can only modify their own announcements/events
- Role-based access control for different user types

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TeacherController.php      # Main teacher dashboard
│   │   ├── AttendanceController.php   # Attendance management
│   │   ├── AnnouncementController.php # Announcement CRUD
│   │   ├── EventController.php        # Event management
│   │   ├── ConsentFormController.php  # Consent form handling
│   │   └── AuthController.php         # Authentication
│   └── Middleware/
│       └── TeacherMiddleware.php      # Teacher access control
├── Models/
│   ├── User.php                       # User authentication
│   ├── Teacher.php                    # Teacher profiles
│   ├── Student.php                    # Student records
│   ├── Section.php                    # Class sections
│   ├── ClassRoom.php                  # Subject classes
│   ├── Announcement.php               # Announcements
│   ├── Event.php                      # Events
│   ├── ConsentForm.php                # Consent forms
│   └── ... (other models)
└── ...

resources/views/
├── layouts/
│   └── teacher.blade.php              # Teacher layout template
├── teacher/
│   ├── dashboard.blade.php            # Teacher dashboard
│   ├── attendance/
│   │   └── index.blade.php            # Attendance marking
│   └── announcements/
│       └── create.blade.php           # Announcement creation
└── auth/
    └── login.blade.php                # Login form
```

## Customization

### Adding New Features
1. Create new models with proper relationships
2. Add controllers with appropriate middleware
3. Create Blade views using the teacher layout
4. Add routes to the teacher route group
5. Update the sidebar navigation

### Styling
The interface uses Bootstrap 5 with custom CSS for:
- Gradient sidebar design
- Card-based layouts
- Responsive design
- Font Awesome icons

### Database Modifications
1. Create new migrations for schema changes
2. Update models with new relationships
3. Modify controllers to handle new data
4. Update views to display new information

## Troubleshooting

### Common Issues

**Login not working:**
- Ensure database is properly seeded
- Check that teacher user exists with correct role
- Verify password hashing is working

**Missing data:**
- Run `php artisan db:seed` to populate test data
- Check database connections in `.env`
- Verify migrations have been run

**Permission errors:**
- Ensure TeacherMiddleware is registered
- Check user role is set to 'teacher'
- Verify teacher profile exists

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

## Contributing

1. Follow Laravel coding standards
2. Add proper validation and error handling
3. Include tests for new features
4. Update documentation for changes
5. Use meaningful commit messages

## License

This project is part of the ParentSync school-parent engagement system.

---

**Note**: This is a demonstration system. For production use, implement additional security measures, proper error handling, and comprehensive testing. 