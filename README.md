# Pet Adoption System

A web-based platform that connects pet seekers with adoptable pets, built with PHP and MySQL. Users can browse pets, register/login, view detailed pet profiles, manage their profile, and adopt — with a full admin panel for managing listings, handling adoption requests, and generating adoption certificates.

## Features

- User registration and login/logout
- Browse and view detailed pet listings
- User profile management
- Admin dashboard for managing pets and adoption requests
- Add, edit, and delete pet listings (admin)
- Approve/reject adoption requests (admin)
- Adoption certificate generation
- Image uploads for pet listings
- Secure authentication (password hashing)

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL (see `/sql` for schema)
- **Frontend:** HTML, CSS
- **Structure:** Modular PHP with shared includes

## Project Structure

```
├── admin/
│   ├── add_pet.php
│   ├── approve_request.php
│   ├── dashboard.php
│   ├── delete_pet.php
│   ├── edit_pet.php
│   ├── reject_request.php
│   └── requests_list.php
├── assets/
│   ├── css/style.css
│   └── images/
├── includes/
│   ├── config.php
│   ├── footer.php
│   └── header.php
├── sql/                     # Database schema
├── uploads/                 # Uploaded pet images
├── index.php
├── login.php
├── register.php
├── logout.php
├── profile.php
├── pet_detail.php
├── generate_certificate.php
└── get_admin_hash.php
```

## Setup

1. Clone the repository
   ```bash
   git clone https://github.com/kavin-vk26/Pet-adoption-system.git
   cd Pet-adoption-system
   ```
2. Import the database schema from `/sql` into MySQL
3. Update database credentials in `includes/config.php`
4. Serve the project with a local PHP server (e.g. XAMPP/WAMP or `php -S localhost:8000`)
5. Visit `http://localhost:8000` (or your configured host) in your browser

## Admin Access

Use `get_admin_hash.php` to generate a hashed password for the admin account, then update it in the database/config as needed.

## Contributors

- [Kavin](https://github.com/kavin-vk26)
- [Aishu5432](https://github.com/Aishu5432)

## License

This project is for academic/educational purposes.

