## Authentication System 

This project is a secure, API-only Laravel 12 backend for a user authentication system. It features:

- 5-Step Registration Wizard  
- OTP-based Email Verification  
- Secure Password Setup with Strength Validation  
- Login with Rate Limiting (per IP)  
- Secure Logout using Laravel Sanctum  
- UUID-based User IDs  
- PostgreSQL support  
- Proper validation and error responses for each step  

## Setup Instructions

### Prerequisites

- PHP >= 8.2
- Composer
- PostgreSQL
- Laravel 12

### Clone the Repository

```bash
git clone https://github.com/PrinceNiyonshuti/authentication-system
cd authentication-system
```

### Install Dependencies

```bash
composer install
```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` to match your PostgreSQL configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### Run Migrations

```bash
php artisan migrate
```

### Serve the Application

```bash
php artisan serve
```

---

## Features Overview

### Registration Flow

1. **Step 1**: Collect basic details (first name, last name, email).
2. **Step 2**: Validate email and send OTP.
3. **Step 3**: Verify OTP.
4. **Step 4**: Create password (strong validation).
5. **Step 5**: Review all inputs → Confirm → Save to `users` table → Delete from `temporary_users` → Issue Sanctum Token.

### Login

- Email + Password
- Rate limited (5 attempts per minute)
- Token issued on success
- JSON error returned on incorrect credentials

### Logout

- Invalidates Sanctum token
- Handles missing/invalid tokens gracefully
- Returns proper JSON error messages

---

## Design Decisions

- **UUIDs**: Used for `users.id` and `tokenable_id` to ensure uniqueness across services.
- **Sanctum**: Selected for SPA/mobile token-based authentication.
- **Separation of Concerns**: Temporary users are kept in a separate table during registration for security and data isolation.
- **Validation**: Each request (registration, login) uses FormRequest classes with strict rules and error responses.


## API Architecture
| Method | Endpoint                     | Description                         |
| ------ | ---------------------------- | ----------------------------------- |
| POST   | `/api/register/step1`        | Submit personal info                |
| POST   | `/api/register/step2`        | Submit address                      |
| POST   | `/api/register/send-otp`     | Send OTP                            |
| POST   | `/api/register/verify-otp`   | Verify OTP                          |
| POST   | `/api/register/set-password` | Set user password                   |
| POST   | `/api/register/confirm`      | Final confirmation & create user    |
| POST   | `/api/login`                 | Login with email & password         |
| POST   | `/api/logout`                | Logout user (requires Bearer token) |

