-- RMS Database Setup
-- Restaurant Management System - Complete Database Schema

CREATE DATABASE IF NOT EXISTS rms_db;
USE rms_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    delivery_location TEXT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_details TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Admin User (password: admin123)
INSERT INTO users (email, password, role) VALUES 
('admin@rms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Products
INSERT INTO products (name, description, price, image_url) VALUES
('Espresso', 'Strong and bold coffee shot', 150.00, 'uploads/espresso.jpg'),
('Cappuccino', 'Espresso with steamed milk foam', 200.00, 'uploads/cappuccino.jpg'),
('Latte', 'Smooth espresso with steamed milk', 220.00, 'uploads/latte.jpg'),
('Americano', 'Espresso with hot water', 180.00, 'uploads/americano.jpg'),
('Mocha', 'Chocolate flavored coffee', 250.00, 'uploads/mocha.jpg');
