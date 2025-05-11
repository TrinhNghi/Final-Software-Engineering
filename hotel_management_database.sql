-- Create Database
CREATE DATABASE IF NOT EXISTS `HotelManagement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `HotelManagement`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- 1. Account Table
CREATE TABLE `account` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(64) NOT NULL UNIQUE,
    `firstname` VARCHAR(64) NOT NULL,
    `lastname` VARCHAR(64) NOT NULL,
    `email` VARCHAR(64) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `balance` DECIMAL(15,2) DEFAULT 5000000.00,
    `activated` TINYINT(1) DEFAULT 0,
    `activate_token` VARCHAR(255),
    `authorize` ENUM('admin', 'user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample account data
INSERT INTO `account` (`username`, `firstname`, `lastname`, `email`, `password`, `activated`, `activate_token`, `authorize`, `balance`) VALUES
('admin123', 'Administrator', 'Staff', 'admin@example.com', '$2y$10$YrhJcBQdJKkcDWfJwEsDXOV277Kw0uJGQHn8u7/gcagcw4qwl7W2C', 1, '', 'admin', 5000000.00),
('trinhnghi', 'Trinh', 'Nghi', 'guidervirus7486@gmail.com', '$2y$10$DrTDBkDFVpSSOxBrmj/nl.iiaG0cKFyLIC.CaXqeoHsDhidDilamG', 1, '', 'user', 5000000.00);

-- 2. Rooms Table
CREATE TABLE `rooms` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `room_name` VARCHAR(64) NOT NULL,
    `room_category` ENUM('single', 'double', 'suite') NOT NULL,
    `room_type` ENUM('vip', 'normal') NOT NULL,
    `room_number` INT NOT NULL UNIQUE,
    `floor` INT NOT NULL,
    `max_people` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `description` TEXT DEFAULT 'Comfortable room with modern amenities.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample room data
INSERT INTO `rooms` (`id`, `room_name`, `room_category`, `room_type`, `room_number`, `floor`, `max_people`, `price`, `description`) VALUES
(1, 'Mountain View Single', 'single', 'normal', 101, 1, 2, 100000.00, 'Cozy single room with a stunning view of Sapa’s mountains. Includes free Wi-Fi, hot shower, and complimentary breakfast.'),
(2, 'Valley Breeze Single', 'single', 'normal', 102, 1, 2, 200000.00, 'Charming single room with fresh mountain air and a private balcony. Free Wi-Fi and breakfast included.'),
(3, 'Cloud Haven Double', 'double', 'normal', 103, 1, 4, 500000.00, 'Spacious double room perfect for couples or small families, featuring mountain views, free Wi-Fi, and daily breakfast.'),
(4, 'Misty Peak Single', 'single', 'normal', 104, 1, 2, 100000.00, 'Quaint single room with cozy bedding and scenic views. Includes hot shower, Wi-Fi, and breakfast.'),
(5, 'Rice Terrace Double', 'double', 'normal', 105, 1, 4, 200000.00, 'Bright double room overlooking lush rice terraces. Comes with free Wi-Fi, breakfast, and access to guided tours.'),
(6, 'Sunset Suite', 'suite', 'normal', 106, 1, 6, 500000.00, 'Luxurious suite with panoramic views, ideal for groups. Includes Wi-Fi, breakfast, and complimentary tea/coffee.'),
(7, 'Bamboo Grove Single', 'single', 'normal', 107, 1, 2, 100000.00, 'Tranquil single room surrounded by bamboo gardens. Features Wi-Fi, hot shower, and daily breakfast.'),
(8, 'River Song Double', 'double', 'normal', 108, 1, 4, 200000.00, 'Comfortable double room with river views, free Wi-Fi, breakfast, and access to local cultural experiences.'),
(9, 'Starlight Suite', 'suite', 'normal', 109, 2, 6, 500000.00, 'Elegant suite with stargazing balcony. Includes Wi-Fi, breakfast, and complimentary local snacks.'),
(10, 'Morning Dew Single', 'single', 'normal', 110, 2, 2, 100000.00, 'Serene single room with fresh linens and mountain views. Free Wi-Fi and breakfast provided.'),
(11, 'Hillside Double', 'double', 'normal', 201, 2, 4, 200000.00, 'Warm double room with hillside views, Wi-Fi, breakfast, and hot shower.'),
(12, 'Skyward Suite', 'suite', 'normal', 202, 2, 6, 500000.00, 'Spacious suite with sweeping views of Sapa. Includes Wi-Fi, breakfast, and guided tour access.'),
(13, 'Pine Whisper Single', 'single', 'normal', 203, 2, 2, 100000.00, 'Cozy single room with pine forest views. Free Wi-Fi and breakfast included.'),
(14, 'Meadow Bloom Double', 'double', 'normal', 204, 2, 4, 200000.00, 'Bright double room with meadow views, Wi-Fi, breakfast, and hot shower.'),
(15, 'Twilight Suite', 'suite', 'normal', 205, 3, 6, 500000.00, 'Luxurious suite with sunset views, Wi-Fi, breakfast, and complimentary drinks.'),
(16, 'Forest Mist Single', 'single', 'normal', 206, 3, 2, 100000.00, 'Peaceful single room with forest views. Includes Wi-Fi, hot shower, and breakfast.'),
(17, 'Golden Valley Double', 'double', 'normal', 207, 3, 4, 200000.00, 'Spacious double room with valley views, Wi-Fi, breakfast, and cultural tour access.'),
(18, 'Moonlit Suite', 'suite', 'normal', 208, 3, 6, 500000.00, 'Elegant suite with moonlit balcony, Wi-Fi, breakfast, and local snacks.'),
(19, 'Dawn Glow Single', 'single', 'normal', 209, 3, 2, 100000.00, 'Cozy single room with sunrise views, Wi-Fi, and breakfast.'),
(20, 'Emerald Double', 'double', 'normal', 210, 3, 4, 200000.00, 'Comfortable double room with emerald hill views, Wi-Fi, and breakfast.'),
(21, 'VIP Mountain Retreat', 'single', 'vip', 401, 6, 2, 1000000.00, 'Exclusive VIP single room with private balcony, premium amenities, Wi-Fi, gourmet breakfast, and guided tours.'),
(22, 'VIP Valley Sanctuary', 'double', 'vip', 402, 6, 4, 2000000.00, 'Luxurious VIP double room with panoramic views, Wi-Fi, gourmet breakfast, and exclusive cultural experiences.'),
(23, 'VIP Cloud Suite', 'suite', 'vip', 403, 6, 6, 1000000.00, 'Opulent VIP suite with cloud-level views, Wi-Fi, gourmet breakfast, and private tour services.'),
(24, 'VIP Starry Haven', 'single', 'vip', 404, 6, 2, 2000000.00, 'Premium VIP single room with stargazing balcony, Wi-Fi, gourmet breakfast, and luxury amenities.'),
(25, 'VIP Sunset Glow', 'double', 'vip', 405, 6, 4, 1000000.00, 'Elegant VIP double room with sunset views, Wi-Fi, gourmet breakfast, and exclusive services.'),
(26, 'VIP Moonlight Suite', 'suite', 'vip', 406, 6, 6, 2000000.00, 'Grand VIP suite with moonlight views, Wi-Fi, gourmet breakfast, and private cultural tours.'),
(27, 'VIP Pine Oasis', 'single', 'vip', 407, 7, 2, 1000000.00, 'Cozy VIP single room with pine forest views, Wi-Fi, gourmet breakfast, and premium amenities.'),
(28, 'VIP River Harmony', 'double', 'vip', 408, 7, 4, 2000000.00, 'Spacious VIP double room with river views, Wi-Fi, gourmet breakfast, and exclusive tours.'),
(29, 'VIP Sky Suite', 'suite', 'vip', 409, 7, 6, 1000000.00, 'Luxurious VIP suite with sky-high views, Wi-Fi, gourmet breakfast, and private services.'),
(30, 'VIP Meadow Bliss', 'single', 'vip', 410, 7, 2, 2000000.00, 'Premium VIP single room with meadow views, Wi-Fi, gourmet breakfast, and luxury amenities.');

-- 3. Service Table
CREATE TABLE `service` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `service_name` VARCHAR(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample service data
INSERT INTO `service` (`id`, `service_name`) VALUES
(1, 'Room Service'),
(2, 'Cleaning Service'),
(3, 'Laundry Service'),
(4, 'Spa Service'),
(5, 'Gym Access'),
(6, 'Pool Access'),
(7, 'Airport Pickup'),
(8, 'Event Booking'),
(9, 'Massage Service'),
(10, 'Car Rental');

-- 4. Reservation Table
CREATE TABLE `reservation` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `room_id` INT NOT NULL,
    `checkin_date` DATE NOT NULL,
    `checkout_date` DATE NOT NULL,
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('pending', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
    `payment_status` ENUM('unpaid', 'paid', 'partially_paid') DEFAULT 'unpaid',
    FOREIGN KEY (`user_id`) REFERENCES `account`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Trigger to calculate total_amount
DELIMITER $$
CREATE TRIGGER `calculate_reservation_amount` BEFORE INSERT ON `reservation` FOR EACH ROW
BEGIN
    DECLARE room_price DECIMAL(10,2);
    SELECT price INTO room_price
    FROM rooms
    WHERE id = NEW.room_id;
    SET NEW.total_amount = DATEDIFF(NEW.checkout_date, NEW.checkin_date) * room_price;
END$$
DELIMITER ;

-- 5. Request Table
CREATE TABLE `request` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `service_id` INT,
    `reservation_id` INT,
    `request_type` ENUM('checkin', 'checkout', 'service') NOT NULL,
    `request_details` TEXT,
    `status` ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (`user_id`) REFERENCES `account`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `service`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (`reservation_id`) REFERENCES `reservation`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Payment Table
CREATE TABLE `payment` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `reservation_id` INT NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `payment_method` ENUM('credit_card', 'bank_transfer', 'cash', 'online') NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `account`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`reservation_id`) REFERENCES `reservation`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Staff Table
CREATE TABLE `staff` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `firstname` VARCHAR(64) NOT NULL,
    `lastname` VARCHAR(64) NOT NULL,
    `email` VARCHAR(64) NOT NULL UNIQUE,
    `phone` VARCHAR(20),
    `role` ENUM('manager', 'receptionist', 'housekeeping', 'other') NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample staff data
INSERT INTO `staff` (`id`, `firstname`, `lastname`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'John', 'Doe', 'john@peacehome.com', '0123456789', 'manager', '2025-05-09 02:18:56'),
(2, 'Jane', 'Smith', 'jane@peacehome.com', '0987654321', 'receptionist', '2025-05-09 02:18:56'),
(3, 'Michael', 'Nguyen', 'michael.nguyen@peacehome.com', '0912345678', 'housekeeping', '2025-05-09 02:18:56'),
(4, 'Emily', 'Tran', 'emily.tran@peacehome.com', '0934567890', 'receptionist', '2025-05-09 02:18:56'),
(5, 'David', 'Le', 'david.le@peacehome.com', '0901234567', 'manager', '2025-05-09 02:18:56'),
(6, 'Sophie', 'Pham', 'sophie.pham@peacehome.com', '0923456789', 'housekeeping', '2025-05-09 02:18:56'),
(7, 'Thomas', 'Ho', 'thomas.ho@peacehome.com', '0945678901', 'other', '2025-05-09 02:18:56'),
(8, 'Anna', 'Vo', 'anna.vo@peacehome.com', '0956789012', 'receptionist', '2025-05-09 02:18:56'),
(9, 'Peter', 'Dang', 'peter.dang@peacehome.com', '0967890123', 'housekeeping', '2025-05-09 02:18:56'),
(10, 'Lily', 'Bui', 'lily.bui@peacehome.com', '0978901234', 'manager', '2025-05-09 02:18:56'),
(11, 'James', 'Truong', 'james.truong@peacehome.com', '0989012345', 'receptionist', '2025-05-09 02:18:56'),
(12, 'Grace', 'Ngo', 'grace.ngo@peacehome.com', '0990123456', 'housekeeping', '2025-05-09 02:18:56'),
(13, 'William', 'Duong', 'william.duong@peacehome.com', '0911234567', 'other', '2025-05-09 02:18:56'),
(14, 'Chloe', 'Ly', 'chloe.ly@peacehome.com', '0922345678', 'receptionist', '2025-05-09 02:18:56'),
(15, 'Ethan', 'Vu', 'ethan.vu@peacehome.com', '0933456789', 'housekeeping', '2025-05-09 02:18:56'),
(16, 'Mia', 'Huynh', 'mia.huynh@peacehome.com', '0944567890', 'manager', '2025-05-09 02:18:56'),
(17, 'Lucas', 'Do', 'lucas.do@peacehome.com', '0955678901', 'receptionist', '2025-05-09 02:18:56'),
(18, 'Ava', 'Nguyen', 'ava.nguyen@peacehome.com', '0966789012', 'housekeeping', '2025-05-09 02:18:56'),
(19, 'Henry', 'Pham', 'henry.pham@peacehome.com', '0977890123', 'other', '2025-05-09 02:18:56'),
(20, 'Isabella', 'Le', 'isabella.le@peacehome.com', '0988901234', 'receptionist', '2025-05-09 02:18:56'),
(21, 'Mason', 'Tran', 'mason.tran@peacehome.com', '0999012345', 'housekeeping', '2025-05-09 02:18:56'),
(22, 'Amelia', 'Vo', 'amelia.vo@peacehome.com', '0910123456', 'manager', '2025-05-09 02:18:56'),
(23, 'Trinh', 'Nghi', 'test@gmail.com', '0941523729', 'manager', '2025-05-09 03:15:10');

-- 8. Reset Token Table
CREATE TABLE `reset_token` (
    `email` VARCHAR(64) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expire_on` INT(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 9. Room Data View
CREATE VIEW `room_data` AS
SELECT
    r.id AS room_id,
    COUNT(res.id) AS total_checkin,
    COALESCE(SUM(
        CASE
            WHEN res.payment_status IN ('paid', 'partially_paid')
            THEN DATEDIFF(res.checkout_date, res.checkin_date) * r.price
            ELSE 0
        END
    ), 0) AS total_money
FROM rooms r
LEFT JOIN reservation res ON res.room_id = r.id
GROUP BY r.id;

-- 10. Monthly Room Data View
CREATE VIEW `monthly_room_data` AS
SELECT
    r.id AS room_id,
    DATE_FORMAT(res.checkout_date, '%Y-%m') AS month,
    COUNT(res.id) AS total_checkin,
    COALESCE(SUM(
        CASE
            WHEN res.payment_status IN ('paid', 'partially_paid')
            THEN DATEDIFF(res.checkout_date, res.checkin_date) * r.price
            ELSE 0
        END
    ), 0) AS total_money
FROM rooms r
LEFT JOIN reservation res ON res.room_id = r.id
GROUP BY r.id, DATE_FORMAT(res.checkout_date, '%Y-%m')
ORDER BY DATE_FORMAT(res.checkout_date, '%Y-%m') DESC;

-- 11. Procedure: ProcessPayment
DELIMITER $$
CREATE PROCEDURE `ProcessPayment` (
    IN p_user_id INT,
    IN p_reservation_id INT,
    IN p_amount DECIMAL(15,2),
    IN p_payment_method ENUM('credit_card', 'bank_transfer', 'cash', 'online')
)
BEGIN
    DECLARE user_balance DECIMAL(15,2);
    DECLARE reservation_amount DECIMAL(15,2);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Payment processing failed';
    END;

    START TRANSACTION;

    SELECT balance INTO user_balance
    FROM account
    WHERE id = p_user_id
    FOR UPDATE;

    SELECT total_amount INTO reservation_amount
    FROM reservation
    WHERE id = p_reservation_id;

    IF user_balance < p_amount THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient balance';
    END IF;

    IF p_amount > reservation_amount THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Payment amount exceeds reservation total';
    END IF;

    UPDATE account
    SET balance = balance - p_amount
    WHERE id = p_user_id;

    INSERT INTO payment (user_id, reservation_id, amount, payment_method, status)
    VALUES (p_user_id, p_reservation_id, p_amount, p_payment_method, 'completed');

    UPDATE reservation
    SET payment_status = 
        CASE
            WHEN (SELECT SUM(amount) FROM payment WHERE reservation_id = p_reservation_id) >= total_amount THEN 'paid'
            ELSE 'partially_paid'
        END
    WHERE id = p_reservation_id;

    COMMIT;
END$$
DELIMITER ;

-- Indexes
ALTER TABLE `payment`
    ADD KEY `user_id` (`user_id`),
    ADD KEY `reservation_id` (`reservation_id`);

ALTER TABLE `request`
    ADD KEY `user_id` (`user_id`),
    ADD KEY `service_id` (`service_id`),
    ADD KEY `reservation_id` (`reservation_id`);

ALTER TABLE `reservation`
    ADD KEY `user_id` (`user_id`),
    ADD KEY `room_id` (`room_id`);

-- Auto-increment settings
ALTER TABLE `account`
    MODIFY `id` INT AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `payment`
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `request`
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `reservation`
    MODIFY `id` INT AUTO_INCREMENT;

ALTER TABLE `rooms`
    MODIFY `id` INT AUTO_INCREMENT, AUTO_INCREMENT=31;

ALTER TABLE `service`
    MODIFY `id` INT AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `staff`
    MODIFY `id` INT AUTO_INCREMENT, AUTO_INCREMENT=24;