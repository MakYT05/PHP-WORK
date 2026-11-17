CREATE DATABASE umnoe_cafe CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE umnoe_cafe;

-- Пользователи (клиенты + сотрудники)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100),
    role ENUM('client','cashier','manager','admin') DEFAULT 'client',
    bonus_balance INT DEFAULT 0,
    sms_code VARCHAR(6),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Кафе (точки самовывоза)
CREATE TABLE cafes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
);

-- Категории меню
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
);

-- Блюда
CREATE TABLE dishes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    is_available TINYINT(1) DEFAULT 1,   -- стоп-лист
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Склад (ингредиенты)
CREATE TABLE stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    quantity DECIMAL(10,2) DEFAULT 0,
    min_quantity DECIMAL(10,2) DEFAULT 5,
    unit VARCHAR(20) DEFAULT 'шт'
);

-- Связь блюдо → ингредиенты (для списания)
CREATE TABLE dish_ingredients (
    dish_id INT,
    stock_id INT,
    amount DECIMAL(10,2),
    PRIMARY KEY (dish_id, stock_id),
    FOREIGN KEY (dish_id) REFERENCES dishes(id),
    FOREIGN KEY (stock_id) REFERENCES stock(id)
);

-- Заказы
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    cafe_id INT,
    pickup_time DATETIME,
    status ENUM('accepted','cooking','ready','done','cancelled') DEFAULT 'accepted',
    total DECIMAL(10,2) DEFAULT 0,
    bonus_used INT DEFAULT 0,
    bonus_earned INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (cafe_id) REFERENCES cafes(id)
);

-- Позиции заказа
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    dish_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (dish_id) REFERENCES dishes(id)
);

-- История бонусов
CREATE TABLE bonus_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_id INT NULL,
    amount INT NOT NULL,          -- + начисление, - списание
    reason VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);