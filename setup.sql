-- RMS Database Setup
-- Restaurant Management System - Complete Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS np03cy4a240058;
USE np03cy4a240058;

-- Users table for customer and admin accounts
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'customer'
);

-- Products table for menu items
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table for customer purchases
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

-- Reviews table for product feedback
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample products
INSERT INTO products (name, description, price, image_url) VALUES
('Espresso', 'Strong and bold coffee shot', 150.00, 'uploads/espresso.jpg'),
('Cappuccino', 'Espresso with steamed milk foam', 200.00, 'uploads/cappuccino.jpg'),
('Latte', 'Smooth espresso with steamed milk', 220.00, 'uploads/latte.jpg'),
('Americano', 'Espresso with hot water', 180.00, 'uploads/americano.jpg'),
('Mocha', 'Chocolate flavored coffee', 250.00, 'uploads/mocha.jpg');
