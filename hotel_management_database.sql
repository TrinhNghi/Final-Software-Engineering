-- 0. Create Database
CREATE DATABASE HotelManagement;
USE HotelManagement;

-- 1. Account Table
CREATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL,
    firstname VARCHAR(64) NOT NULL,
    lastname VARCHAR(64) NOT NULL,
    email VARCHAR(64) NOT NULL,
    password VARCHAR(255) NOT NULL,
    activated BOOLEAN DEFAULT 0,
    activate_token VARCHAR(255),
    authorize ENUM('admin', 'user') DEFAULT 'user'
);

-- 2. Rooms Table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(64) NOT NULL,
    room_type ENUM('vip', 'normal') NOT NULL,
    room_number INT NOT NULL UNIQUE,
    floor INT NOT NULL,
    max_people INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);

-- 3. Reservation Table
CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES account(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- 4. Room Data Table
CREATE TABLE room_data (
    room_id INT PRIMARY KEY,
    total_checkin INT DEFAULT 0,
    total_money DECIMAL(12,2) DEFAULT 0.00,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- 5. Service Table
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(64) NOT NULL
);

-- 6. Request Table
CREATE TABLE request (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    request_type VARCHAR(64) NOT NULL,
    request_details TEXT,
    status ENUM('pending', 'in progress', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES account(id),
    FOREIGN KEY (service_id) REFERENCES service(id)
);

CREATE TABLE `reset_token` (
  `email` varchar(64) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire_on` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
