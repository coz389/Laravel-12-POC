# Project Setup & Technical Documentation

This guide provides instructions for setting up the development environment and an overview of the technical architecture for this Laravel 12 API project.

## 1. Prerequisites

Ensure you have the following installed on your local machine:
- **PHP**: ^8.2 (8.3 recommended)
- **Composer**: Latest version
- **MySQL/MariaDB**: Or any compatible database server
- **Node.js & NPM**: For frontend asset management (if applicable)

## 2. Initial Setup

Follow these steps to get the project running locally:

### Clone and Install Dependencies
```bash
# Clone the repository
git clone <repository-url>
cd laravel12

# Install PHP dependencies
composer install

# Install JS dependencies
npm install
```

### Environment Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Generate the application key:
   ```bash
   php artisan key:generate
   ```
3. Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel12
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

### Database Setup
Run migrations and seed the database with initial data:
```bash
php artisan migrate --seed
```
*Note: This will create the `users` table and seed it with 20 random users and an admin account (`admin@example.com` / `password123`).*

## 3. API Documentation (Swagger)

This project uses **L5-Swagger** for OpenAPI documentation.

### Accessing Swagger UI
Once the server is running, you can access the interactive API documentation at:
`http://localhost:8000/api/documentation`

### Regenerating Documentation
If you modify any Swagger annotations (`#[OA\...]`) in the controllers, you must regenerate the documentation:
```bash
php artisan l5-swagger:generate
```

### Server Configuration
The Swagger server is configured in `app/Http/Controllers/Controller.php`. Ensure the `url` matches your local development port:
```php
#[OA\Server(
    url: "http://localhost:8000",
    description: "Primary API Server"
)]
```

## 4. Running the Application

Start the local development server:
```bash
php artisan serve
```
The application will be available at `http://localhost:8000`.

## 5. Technical Overview

### Key Technologies
- **Framework**: Laravel 12
- **Authentication**: Laravel Sanctum (configured for API tokens)
- **Documentation**: L5-Swagger (OpenAPI 3.0)
- **Database**: Eloquent ORM with Migrations & Factories

### API Endpoints (Users)
- `GET /api/users` - Paginated list of users
- `POST /api/users` - Create a new user
- `GET /api/users/{id}` - Show user details
- `PUT /api/users/{id}` - Update a user
- `DELETE /api/users/{id}` - Delete a user

### Development Standards
- **Controller Annotations**: Use PHP 8 attributes for Swagger documentation directly in the controllers.
- **Validation**: Request validation is handled within controller methods or dedicated Request classes.
- **JSON Responses**: All API responses follow a consistent format:
  ```json
  {
      "status": true,
      "message": "Success message",
      "data": { ... }
  }
  ```
