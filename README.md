# Travosy – Travel Agency Website

A travel booking website where users can browse vacation packages, book trips and manage their bookings, and admins can manage packages, users, bookings and reviews.

Group project for the Web Technologies & Programming course (2024–2025).

## Features
- Sign up and login with user and admin roles (passwords hashed with bcrypt)
- Browse travel packages and view package details
- Book packages for adults and children, with details for each traveller
- Wishlist, reviews and contact form
- User area: profile, bookings, wishlist and settings
- Admin panel: statistics and management of packages, users, bookings and reviews

## Technologies
PHP, MySQL (MariaDB), HTML, Tailwind CSS, JavaScript, XAMPP

## How to run
1. Install [XAMPP](https://www.apachefriends.org/) and start Apache and MySQL.
2. Put the project folder in XAMPP's `htdocs` folder and name it `Travosy`:
   - Windows: `C:\xampp\htdocs\Travosy`
   - macOS: `/Applications/XAMPP/xamppfiles/htdocs/Travosy`
3. Open http://localhost/phpmyadmin, create a database named `travel_agency`, then import `database/travel_agency.sql` into it.
4. Open http://localhost/Travosy/

The database connection settings are in `config.php` (default XAMPP: user `root`, no password).

## Demo accounts
Log in with the username:

| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | admin123 |
| User  | user     | user123  |

## Project structure
- `admin/` – admin panel pages
- `user/` – user area pages
- `Base/` – shared menu, footer and page sections
- `assets/` – CSS, JavaScript, fonts and images
- `database/` – database export (`travel_agency.sql`)
- `docs/` – project documentation

## Documentation
See [`docs/DOCUMENTATION.pdf`](docs/DOCUMENTATION.pdf) for requirements, features and the database schema.

## Team
Artemisa Hasalami, Denisa Korra, Megi Almadhi, Nikol Dalipi, Noel Zani, Sidrit Rrustemi, Viola Zeneli
