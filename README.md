# Address Book Demo

This is a demo address book application scaffolded in PHP, utilizing the following technologies:
- Dice (Dependency Injection Container)
- Phinx (Database Migrations
- Dotenv (Environment Variables)
- FastRoute (Router)

## Getting Started

### Prerequisites

Make sure you have the following software installed:
- PHP (>= 7.4)
- Composer
- MySQL

### Installation

1. **Clone the repository:**

```bash
git clone <repository_url>
cd <repository_directory>
```

2. **Install dependencies:**

```bash
composer install
```

3.	**Set up environment variables**:

Rename the .env.development file to .env and update it with your MySQL database details.

```bash
mv .env.development .env
```

Update the .env file with your database credentials:
    
```env
DB_HOST=127.0.0.1
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
DB_PORT=3306
```
4.	**Run database migrations**:


```bash
vendor/bin/phinx migrate
```

5.	**Run database seeders**:

```bash
vendor/bin/phinx seed:run
```

**Usage**

After setting up the environment variables and running the migrations and seeders, you can start using the application. The application will be available at your local server’s root URL.

**Routing**

All routes are handled by FastRoute. The routes are defined in the Routes/routes.php file.


**Folder Structure**

	•	Config: Configuration files
	•	Controllers: Application controllers
	•	Models: Database models
	•	Services: Service classes
	•	Views: Template files
	•	Routes: Routing configuration & Router


**.htaccess**

Ensure you have the following .htaccess file in your project’s public directory to redirect all routes to index.php:

```apache
# Enable the Rewrite Engine
RewriteEngine On

# Rewrite rule to direct all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [L,QSA]
```
