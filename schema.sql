-- ==============================
-- schema.sql  –  Database structuur Webshop
-- ==============================

-- Database aanmaken (als hij nog niet bestaat)
CREATE DATABASE IF NOT EXISTS webshop 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE webshop;

-- ===========================
-- Customers tabel (NAW + login)
-- ===========================
CREATE TABLE IF NOT EXISTS customers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,

    -- NAW gegevens
    voornaam    VARCHAR(100),
    achternaam  VARCHAR(100),
    adres       VARCHAR(255),
    postcode    VARCHAR(10),
    stad        VARCHAR(100),
    land        VARCHAR(100) DEFAULT 'Nederland',
    telefoon    VARCHAR(20),

    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===========================
-- Products tabel
-- ===========================
CREATE TABLE IF NOT EXISTS products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT,
    price       DECIMAL(10, 2) NOT NULL,
    image       VARCHAR(255) DEFAULT 'placeholder.jpg',
    stock       INT DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ===========================
-- Orders tabel (optioneel voor later)
-- ===========================
CREATE TABLE IF NOT EXISTS orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_id     INT NOT NULL,
    total_price     DECIMAL(10, 2) NOT NULL,
    status          ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
);

-- ===========================
-- Order items tabel (optioneel)
-- ===========================
CREATE TABLE IF NOT EXISTS order_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    order_id    INT NOT NULL,
    product_id  INT NOT NULL,
    quantity    INT NOT NULL,
    unit_price  DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===========================
-- Sample data (testproducten)
-- ===========================
INSERT INTO products (name, description, price, image, stock) VALUES
('Laptop',      'Krachtige laptop geschikt voor werk en gaming.',                  999.00, 'Laptop.png',  10),
('Telefoon',    'Moderne smartphone met uitstekende camera en batterij.',          599.00, 'Telefoon.png',25),
('Headphones',  'Draadloze noise-cancelling koptelefoon.',                         199.00, 'Headphones.png', 50)
ON DUPLICATE KEY UPDATE 
    name = VALUES(name),
    description = VALUES(description),
    price = VALUES(price),
    image = VALUES(image),
    stock = VALUES(stock);

-- Indexen voor betere performance
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_customers_email ON customers(email);