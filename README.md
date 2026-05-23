# ISM Event Registration App

[![Symfony](https://img.shields.io/badge/Symfony-7-000000?logo=symfony)](https://symfony.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)](https://mysql.com)

## Web Engineering Project
ISM Data Science Institute Presentation Roadshow 2026

This is my web engineering project for the ISM Data Science Institute Presentation Roadshow 2026. The app lets guests register for the summit and generates a PDF ticket with QR code. Admins can log in to manage registrations.

## About

The International School of Management (ISM) is doing a roadshow to present their new Data Science Institute at campuses in Hamburg, Dortmund and Munich. This Symfony application handles guest registrations for one active summit at a time. When seats are full, new registrations are blocked automatically.

## What the App Can Do

- Guests can register on the public homepage
- The system checks if there are still free seats before saving
- After registration a PDF ticket with QR code can be downloaded
- Admins can log in to see all registrations
- The registration table in admin is sortable by clicking column headers (jQuery)
- Admins can edit and delete registrations (full CRUD)
- Admins can export all registrations as Excel file
- Admins can download Attendance List as PDF for check-in
- Admins can download Name Tags as PDF for event badges
- The design follows ISM branding with official logo, colors and font

## Technologies Used

- Symfony 7
- PHP 8.2
- MySQL / MariaDB 10.4
- Doctrine ORM
- Twig
- Bootstrap 5
- jQuery
- Dompdf (for PDF generation)
- endroid/qr-code v6 (for QR codes)
- PhpSpreadsheet (for Excel export)

## Data Model

The app uses 4 database tables:

**Location** — stores campus data: city, campus name, address, capacity, event date

**Summit** — the current active event, linked to one Location. The isActive flag controls which summit is currently shown.

**Registration** — one row per guest. Stores: firstName, lastName, email, company, mealPreference, dietaryNotes, ticketNumber, registeredAt. Linked to one Summit.

**User** — admin users who can log into the backend.

## How to Install

### What you need first
- PHP 8.2 or higher (I used XAMPP)
- MySQL or MariaDB (comes with XAMPP)
- Composer
- Symfony CLI (I installed via Scoop on Windows)
- Git

### Installation steps

**1. Clone the project**
git clone https://github.com/jasmeenkaur-1/ism-event-registration.git
cd ism-event-registration

**2. Install PHP dependencies**
composer install

**3. Configure the database**

Open the `.env` file and update this line with your database settings:
DATABASE_URL="mysql://root:@127.0.0.1:3306/ism_event_db?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

If you use MySQL instead of MariaDB change the serverVersion to `8.0`.

**4. Create the database**
php bin/console doctrine:database:create

**5. Run migrations**
php bin/console doctrine:migrations:migrate

Type `yes` when asked.

**6. Load test data**
php bin/console doctrine:fixtures:load

Type `yes` when asked.

**7. Start the server**
symfony serve

**8. Open in browser**
http://127.0.0.1:8000

## Admin Login

After loading fixtures use these credentials:

- URL: http://127.0.0.1:8000/admin/login
- Email: admin@ism.de
- Password: admin123

## Individual Option C — PDF Ticket with QR Code

After a guest registers they see a success page with a button to download their PDF ticket. The ticket contains their name, email, company, meal preference, event details and a QR code. The QR code encodes the unique ticket number which can be scanned at check-in.

The PDF is generated using Dompdf. The QR code is generated using endroid/qr-code v6 with PngWriter, encoded as base64 and embedded into the HTML that Dompdf renders.

## Architecture Decisions

- Controllers are thin — they only receive the request, call a service and return a response
- Services contain the business logic — RegistrationService checks capacity, saves registration and generates ticket number. PdfService generates the PDF and QR code.
- Twig handles all HTML display
- Doctrine handles database access through repositories
- Location and Summit are separate tables so the app can be reused for different campuses and dates
- The isActive boolean on Summit controls which summit is currently live

## User Flow

1. Guest opens homepage and sees active summit info (city, date, seats left)
2. Guest clicks Register Now and fills in the form
3. System checks if seats are available
4. If yes: registration saved, ticket number generated, success page shown
5. Guest downloads PDF ticket with QR code
6. If no seats left: guest sees registration closed message
7. Admin goes to /admin/login and logs in with email and password
8. Admin sees full list of registrations, can sort by any column
9. Admin can edit or delete any registration
10. Admin can download Excel file with all registrations
11. Admin can download Attendance List PDF for check-in at event
12. Admin can download Name Tags PDF for printing event badges

## Problems I Ran Into

**Problem 1 - MySQL not recognized in Command Prompt**
Running mysql --version gave not recognized error.
Fixed by adding C:\xampp\mysql\bin to Windows PATH environment variables.

**Problem 2 - MariaDB version mismatch**
Default DATABASE_URL used serverVersion=8.0 but XAMPP has MariaDB 10.4.
Fixed by changing serverVersion to 10.4.32-MariaDB in .env file.

**Problem 3 - Entity class name case mismatch**
Got RuntimeException saying Case mismatch between App\Entity\location and App\Entity\Location.
Fixed by opening Summit.php and Registration.php and changing all lowercase location and summit type hints to capital letters.

**Problem 4 - GD extension disabled**
QR code generation failed with Unable to generate image error.
Fixed by removing semicolon from extension=gd in php.ini and restarting XAMPP.

**Problem 5 - Symfony CLI could not be downloaded from website**
Official website download did not work on Windows.
Fixed by installing Scoop package manager via PowerShell and running scoop install symfony-cli.

**Problem 6 - Scoop failed when running as Administrator**
Got error Running the installer as administrator is disabled by default.
Fixed by closing PowerShell and reopening as normal user without administrator rights.

**Problem 7 - Wrong field name evenDate instead of eventDate**
Created Location entity with evenDate (missing letter t) instead of eventDate.
Fixed by using setEvenDate and getEvenDate consistently everywhere to match the entity.

**Problem 8 - Server started from wrong folder**
Running symfony serve from wrong folder caused index.php does not exist error.
Fixed by always running cd C:\xampp\htdocs\ism-event-registration before symfony serve.

**Problem 9 - Database connection refused**
Got SQLSTATE HY000 2002 No connection could be made error when loading fixtures.
Fixed by starting MySQL in XAMPP Control Panel.

**Problem 10 - QR code writer not working**
PngWriter failed because GD was not enabled. SvgWriter did not render inside Dompdf.
Fixed by enabling GD extension first then using PngWriter with base64 encoded image embedded in HTML.

**Problem 11 - phpspreadsheet installation failed**
Composer said ext-gd is missing from platform.
Fixed by enabling GD extension in php.ini first then running composer require again.

**Problem 12 - Admin login route not found**
Going to /admin/login gave No route found error.
Fixed by creating SecurityController with login route and configuring firewall in security.yaml.

**Problem 13 - Wrong property name in Registration entity**
Summit property was $Summit with capital S but referenced as $summit elsewhere.
Fixed by renaming to $summit lowercase in Registration.php.

**Problem 14 - endroid/qr-code API methods not found**
Tried QrCode::create() and setSize() but got undefined method errors because version 6.0.9 has different API.
Fixed by using correct constructor new QrCode($data) compatible with version 6.