# Installation
  # Clone the repository to your local machine:
  
  git clone https://github.com/sivanathan1820/sparckoutPMS.git
  
  cd sparckoutPMS
  
  # Install the vendor and packages using Composer:
  
  composer install
  
  # Generate an application key:
  
  php artisan key:generate
  
  # Configure your database settings in the .env file:
  
  DB_CONNECTION=mysql
  
  DB_HOST=127.0.0.1
  
  DB_PORT=3306
  
  DB_DATABASE=sparkout
  
  DB_USERNAME=root
  
  DB_PASSWORD=

# Database Migration and Seeding
  # Migrate the database tables:
  
  php artisan migrate

  # Seed the database with roles:
  
  php artisan db:seed --class=RoleSeeder

  # Seed the database with default admin credentials:
  
  php artisan db:seed --class=UserSeeder

# Default Admin Login
  
  You can log in using the default admin credentials:
  
  Email: admin@gmail.com
  
  Password: Admin@123

# Running the Application
  
  php artisan serve
