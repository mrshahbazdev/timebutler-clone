# TimeCheck - Employee Vacation & Absence Management

A comprehensive vacation and absence planner built with Laravel. Supports German holidays, school breaks by Bundesland, team calendars, PDF/Excel exports, and full DE/EN bilingual UI.

## Features

### Vacation & Absence Management
- **Request vacation** with date range, half-day support, and substitute selection
- **Block vacation** (auto-approved, cancellable later)
- **Cancel** pending or blocked vacation requests
- **Vacation balance** dashboard: total / taken / requested / remaining with progress bar
- **9 absence types** with unique colors: Vacation, Sick (with/without note), Sick Child, Home Office, Business Trip, Parental Leave, Special Leave, Continuing Education

### Holiday & Calendar Import
- **German public holidays** for all 16 Bundeslaender with state-specific holidays (Epiphany, Corpus Christi, Reformation Day, etc.)
- **School breaks** imported from [schulferien.org](https://www.schulferien.org) per Bundesland
- **Weekend import** (optional)
- Selectable year (current + 3 years ahead)

### Team Calendar
- Monthly grid view: employees as rows, days as columns
- Color-coded absence types with opacity for pending requests
- Holiday and weekend indicators
- Department filter
- **Printable PDF** (A3 landscape)

### Manager Features
- **Team requests** view: approve or reject with reason
- **Email notifications**: manager notified on new request, employee notified on decision
- **Decision letter PDF**: formal printable document with status, dates, and signature line
- Enter other absence types (sick leave, child care, education) for employees

### Employee Management
- Full CRUD with search, department/status filters, pagination
- Profile page with recent absences, time entries, vacation balance
- Role assignment (Admin, Manager, Employee) via Spatie Permissions
- Soft delete (mark inactive)

### Department Management
- Card grid with color indicator, employee count, manager display
- Color picker, manager assignment

### Reports with Export
- **Absence report**: date range filter, summary by type, PDF + Excel export
- **Time tracking report**: date range + employee filter, total hours, PDF + Excel export

### Company Registration
- Self-service registration at `/register-company`
- Creates organization with Bundesland selection
- Admin user with role assignment and vacation balance

### Bilingual UI (DE/EN)
- Language switcher in navbar (globe icon)
- Persisted per user session and user.locale field
- Full translations for all modules

## Tech Stack

- **Backend**: Laravel 13, PHP 8.2+
- **Frontend**: Tailwind CSS, Alpine.js, Vite
- **Auth**: Laravel Breeze
- **RBAC**: Spatie Laravel Permissions
- **PDF**: barryvdh/laravel-dompdf
- **Excel**: maatwebsite/excel
- **Database**: MySQL / SQLite

## Installation

```bash
# Clone
git clone https://github.com/mrshahbazdev/timebutler-clone.git
cd timebutler-clone

# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Build assets
npm run build

# Serve
php artisan serve
```

## Demo Credentials

| Role     | Email            | Password |
|----------|------------------|----------|
| Admin    | admin@demo.com   | password |
| Manager  | manager@demo.com | password |
| Employee | anna@demo.com    | password |
| Employee | thomas@demo.com  | password |

## Deployment (Apache)

A root `.htaccess` file is included to redirect all requests to the `public/` directory. For Apache:

1. Point your virtual host to the project root (or use the `.htaccess`)
2. Ensure `mod_rewrite` is enabled: `a2enmod rewrite`
3. Set `AllowOverride All` in your Apache config
4. Run `composer install --optimize-autoloader --no-dev`
5. Run `npm run build`
6. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`

## Project Structure

```
app/
  Http/Controllers/
    AbsenceController.php       # Vacation requests, block, cancel, approve/reject
    CompanyRegistrationController.php  # Self-service org registration
    DepartmentController.php    # Department CRUD
    EmployeeController.php      # Employee CRUD with filters
    HolidayController.php       # Holiday import by Bundesland
    ReportController.php        # Reports with PDF/Excel export
    TeamCalendarController.php  # Monthly team calendar + PDF
  Models/
    AbsenceRequest.php          # request_type: request|blocked
    Holiday.php                 # public_holiday|school_break|weekend|custom
    Organization.php            # federal_state for Bundesland
  Notifications/
    AbsenceRequestNotification.php   # Email to manager
    AbsenceDecisionNotification.php  # Email to employee
  Services/
    GermanHolidayService.php    # 16 Bundeslaender, easter calc, schulferien.org scraper
  Exports/
    AbsenceReportExport.php     # Excel export
    TimeTrackingReportExport.php
lang/
  de/app.php                    # German translations
  en/app.php                    # English translations
```

## License

MIT
