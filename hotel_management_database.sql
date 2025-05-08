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

-- Insert sample room data with the specified price variations
INSERT INTO rooms (room_name, room_type, room_number, floor, max_people, price)
VALUES
('Room 101', 'normal', 101, 1, 2, 100000),
('Room 102', 'normal', 102, 1, 2, 200000),
('Room 103', 'normal', 103, 1, 3, 500000),
('Room 104', 'normal', 104, 1, 3, 100000),
('Room 105', 'normal', 105, 1, 4, 200000),
('Room 106', 'normal', 106, 1, 2, 500000),
('Room 107', 'normal', 107, 1, 3, 100000),
('Room 108', 'normal', 108, 1, 4, 200000),
('Room 109', 'normal', 109, 2, 2, 500000),
('Room 110', 'normal', 110, 2, 3, 100000),
('Room 201', 'normal', 201, 2, 3, 200000),
('Room 202', 'normal', 202, 2, 4, 500000),
('Room 203', 'normal', 203, 2, 2, 100000),
('Room 204', 'normal', 204, 2, 4, 200000),
('Room 205', 'normal', 205, 3, 3, 500000),
('Room 206', 'normal', 206, 3, 2, 100000),
('Room 207', 'normal', 207, 3, 4, 200000),
('Room 208', 'normal', 208, 3, 2, 500000),
('Room 209', 'normal', 209, 3, 3, 100000),
('Room 210', 'normal', 210, 3, 4, 200000),
('Room 301', 'normal', 301, 4, 2, 500000),
('Room 302', 'normal', 302, 4, 3, 100000),
('Room 303', 'normal', 303, 4, 4, 200000),
('Room 304', 'normal', 304, 4, 2, 500000),
('Room 305', 'normal', 305, 4, 3, 100000),
('Room 306', 'normal', 306, 4, 4, 200000),
('Room 307', 'normal', 307, 5, 2, 500000),
('Room 308', 'normal', 308, 5, 3, 100000),
('Room 309', 'normal', 309, 5, 4, 200000),
('Room 310', 'normal', 310, 5, 2, 500000),
('Room 401', 'vip', 401, 6, 2, 1000000),
('Room 402', 'vip', 402, 6, 3, 2000000),
('Room 403', 'vip', 403, 6, 4, 1000000),
('Room 404', 'vip', 404, 6, 2, 2000000),
('Room 405', 'vip', 405, 6, 3, 1000000),
('Room 406', 'vip', 406, 6, 4, 2000000),
('Room 407', 'vip', 407, 7, 2, 1000000),
('Room 408', 'vip', 408, 7, 3, 2000000),
('Room 409', 'vip', 409, 7, 4, 1000000),
('Room 410', 'vip', 410, 7, 2, 2000000),
('Room 411', 'vip', 411, 7, 3, 1000000),
('Room 412', 'vip', 412, 7, 4, 2000000),
('Room 501', 'vip', 501, 8, 2, 1000000),
('Room 502', 'vip', 502, 8, 3, 2000000),
('Room 503', 'vip', 503, 8, 4, 1000000),
('Room 504', 'vip', 504, 8, 2, 2000000),
('Room 505', 'vip', 505, 8, 3, 1000000),
('Room 506', 'vip', 506, 8, 4, 2000000);

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
CREATE VIEW room_data AS
SELECT
    r.id AS room_id,
    COUNT(res.id) AS total_checkin,
    COALESCE(SUM(
        DATEDIFF(res.checkout_date, res.checkin_date) * r.price
    ), 0) AS total_money
FROM rooms r
LEFT JOIN reservation res ON res.room_id = r.id
GROUP BY r.id;


-- 5. Service Table
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(64) NOT NULL
);
-- Insert sample data into service table
INSERT INTO service (service_name)
VALUES
('Room Service'),
('Cleaning Service'),
('Laundry Service'),
('Spa Service'),
('Gym Access'),
('Pool Access'),
('Airport Pickup'),
('Event Booking'),
('Massage Service'),
('Car Rental');

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

CREATE VIEW monthly_room_data AS
SELECT
    r.id AS room_id,
    DATE_FORMAT(res.checkout_date, '%Y-%m') AS month,
    COUNT(res.id) AS total_checkin,
    COALESCE(SUM(DATEDIFF(res.checkout_date, res.checkin_date) * r.price), 0) AS total_money
FROM rooms r
LEFT JOIN reservation res ON res.room_id = r.id
GROUP BY r.id, DATE_FORMAT(res.checkout_date, '%Y-%m')
ORDER BY month DESC;
