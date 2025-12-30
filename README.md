
# Resonance Laravel Project

A Laravel application for the Online-Based Ordering System of GROUP 7 CEIT-37-703A — set up, run, and contribute using the instructions below.

## 📥 Clone the Repository

Clone your project from GitHub:

```bash
git clone https://github.com/gersonlatido/resonance.git
```

Then go into the project directory:

```bash
cd resonance
```

## 🧰 Prerequisites

Make sure these are installed:

- **PHP** (7.3 or higher)
- **Composer** (dependency manager for PHP)
- **MySQL** (or another supported database)
- **Node.js & npm** (for front‑end builds, optional)

## ⚙️ Setup Instructions

### 1. Install Dependencies

Install PHP packages:

```bash
composer install
```

(Optional) Install front‑end packages:

```bash
npm install
```

### 2. Environment File

Create your environment config:

```bash
cp .env.example .env
```

On Windows:

```bash
copy .env.example .env
```

### 3. Generate App Key

Laravel needs an application key:

```bash
php artisan key:generate
```

### 4. Configure Database

Open `.env` and set your database details:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=resonance
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Make sure the database exists in your MySQL server.

### 5. Run Migrations

Create database tables:

```bash
php artisan migrate
```

If you have seeders, run them too:

```bash
php artisan db:seed
```

## 🧪 Running the App

Start the development server:

```bash
php artisan serve
```

By default, the app will be accessible at:

```
http://127.0.0.1:8000
```

## 📦 Optional Build Commands

Compile front‑end assets using:

```bash
npm run dev
```

Or build for production:

```bash
npm run production
```

## 🛠 Useful Commands

- View all Artisan commands:

```bash
php artisan list
```

- Run queue workers:

```bash
php artisan queue:work
```

- Run scheduled tasks:

```bash
php artisan schedule:run
```

## ❗ Troubleshooting

### Database Errors
Double‑check your `.env` database settings.

### Permission Issues
If you run into permission errors (especially on Linux/macOS):

```bash
sudo chmod -R 775 storage bootstrap/cache
```

## 📄 License

This project is licensed under the MIT License — see the `LICENSE` file for more details.
