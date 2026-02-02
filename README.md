# Restaurant Management System (RMS)

A simple, efficient web-based application for managing restaurant orders and menu items. Built with PHP and MySQL.

## Features

### For Customers
- **Browse Menu**: View all available products with prices and descriptions.
- **Live Search**: Instantly filter products by name using the search bar (AJAX).
- **Shopping Cart**: Add items, adjust quantities, and remove items.
- **Checkout**: Place orders with validation for Name, Phone, and Address.

### For Administrators
- **Dashboard**: View total sales, order counts, and recent activity.
- **Menu Management**: Add, Edit, and Delete menu items.
- **Order Management**: View customer orders and update their status (Pending/Completed/Cancelled).

## Installation & Setup

1.  **Database Setup**
    - Create a database (e.g., `np03cy4a240058`).
    - Import the `setup.sql` file into your database.
    
2.  **Configuration**
    - Open `includes/db.php`.
    - Update the database credentials (`$host`, `$username`, `$password`, `$database`) to match your server environment.

3.  **Run**
    - Host the files on a PHP-enabled server (e.g., XAMPP, Apache).
    - Access `index.php` in your browser.



---
*Developed for 5CS045 Assessment.*
