-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 05:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotelmanagement`
--
CREATE DATABASE IF NOT EXISTS `hotelmanagement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hotelmanagement`;

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessPayment` (IN `p_user_id` INT, IN `p_reservation_id` INT, IN `p_amount` DECIMAL(15,2), IN `p_payment_method` ENUM('credit_card','bank_transfer','cash','online'))   BEGIN
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

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` decimal(15,2) DEFAULT 5000000.00,
  `activated` tinyint(1) DEFAULT 0,
  `activate_token` varchar(255) DEFAULT NULL,
  `authorize` enum('admin','user','staff') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `balance`, `activated`, `activate_token`, `authorize`) VALUES
(1, 'admin123', 'Administrator', 'Manager', 'admin123@example.com', '$2y$10$NTbC7C2Xw2eVq1BsIX0LiOdFtaR.Ik.asxmVs6WeBulpVQDzMfhhy', 1120000.00, 1, '', 'admin'),
(2, 'trinhnghi', 'Trinh', 'Nghi', 'guidervirus7486@gmail.com', '$2y$10$u7d14QJBmC.43XEmsjuL6erf5sBDdXAvpQNu7MyEiX/BH0ctiaZ/C', 700000.00, 1, '', 'user'),
(3, 'staff12', 'Staff', 'Manager', 'staff@example.com', '$2y$10$aYqrPiDq1lHUwEtN4iBYzucUniE6sxEIEfm60Mhv7U7NQkkk4zsKG', 3800000.00, 1, '', 'staff');

-- --------------------------------------------------------

--
-- Stand-in structure for view `monthly_room_data`
-- (See below for the actual view)
--
CREATE TABLE `monthly_room_data` (
`room_id` int(11)
,`month` varchar(7)
,`total_checkin` bigint(21)
,`total_money` decimal(38,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_method` enum('credit_card','bank_transfer','cash','online') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `user_id`, `reservation_id`, `amount`, `payment_date`, `status`, `payment_method`) VALUES
(3, 2, 22, 100000.00, '2025-05-10 21:34:51', 'completed', 'cash'),
(4, 3, 24, 400000.00, '2025-05-10 21:35:08', 'completed', 'cash'),
(5, 2, 28, 100000.00, '2025-05-10 23:11:50', 'completed', 'cash'),
(6, 3, 18, 400000.00, '2025-05-11 01:27:58', 'completed', 'cash'),
(7, 2, 27, 400000.00, '2025-05-11 02:12:58', 'completed', 'cash'),
(8, 3, 30, 400000.00, '2025-05-11 02:48:50', 'completed', 'cash'),
(9, 2, 16, 100000.00, '2025-05-11 03:17:17', 'completed', 'cash'),
(10, 2, 34, 100000.00, '2025-05-11 03:18:15', 'completed', 'cash'),
(11, 1, 3, 620000.00, '2025-05-11 07:39:08', 'completed', 'cash'),
(12, 1, 4, 1000000.00, '2025-05-11 12:20:12', 'completed', 'cash'),
(13, 2, 36, 700000.00, '2025-05-11 12:37:37', 'completed', 'cash'),
(14, 1, 38, 760000.00, '2025-05-11 13:50:32', 'completed', 'cash'),
(15, 1, 45, 200000.00, '2025-05-11 14:33:30', 'completed', 'cash'),
(16, 1, 44, 200000.00, '2025-05-11 14:33:53', 'completed', 'cash'),
(17, 2, 15, 400000.00, '2025-05-11 14:34:23', 'completed', 'cash'),
(18, 1, 43, 100000.00, '2025-05-11 14:35:37', 'completed', 'cash'),
(19, 2, 42, 100000.00, '2025-05-11 14:36:05', 'completed', 'cash');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `request_type` enum('checkin','checkout','service') NOT NULL,
  `request_details` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `user_id`, `service_id`, `reservation_id`, `request_type`, `request_details`, `status`) VALUES
(1, 2, 1, 15, 'service', 'Extra towels for Room 105', 'approved'),
(2, 2, 2, 16, 'checkout', 'Early check-out for Room 107', 'approved'),
(3, 3, 3, 17, 'service', 'Laundry service for Room 109', 'rejected'),
(4, 3, 4, NULL, 'service', 'Spa booking for tomorrow', 'rejected'),
(6, 3, 6, 3, 'service', 'Pool access request for Room 101', 'cancelled'),
(7, 2, 1, 15, 'service', 'Extra towels for Room 105', 'approved'),
(8, 2, 2, 16, 'checkout', 'Early check-out for Room 107', 'approved'),
(9, 3, 3, 17, 'service', 'Laundry service for Room 109', 'rejected'),
(10, 3, 4, NULL, 'service', 'Spa booking for tomorrow', 'rejected'),
(12, 3, 6, 3, 'service', 'Pool access request for Room 101', 'cancelled'),
(13, 2, 1, 15, 'service', 'Extra towels for Room 105', 'approved'),
(14, 2, 2, 16, 'checkout', 'Early check-out for Room 107', 'approved'),
(15, 3, 3, 17, 'service', 'Laundry service for Room 109', 'rejected'),
(16, 3, 4, NULL, 'service', 'Spa booking for tomorrow', 'rejected'),
(18, 3, 6, 3, 'service', 'Pool access request for Room 101', 'cancelled'),
(19, 1, 7, 10, 'service', '', 'approved'),
(20, 1, 4, 36, 'service', 'I need to bath', 'approved'),
(21, 1, 7, 36, 'service', '', 'approved'),
(22, 1, 7, 3, 'service', '', 'approved'),
(23, 1, 2, 3, 'service', '', 'approved'),
(24, 1, 7, 36, 'service', '', 'approved'),
(25, 1, 7, 36, 'service', '', 'approved'),
(26, 1, 7, 37, 'service', '', 'approved'),
(27, 1, 7, 37, 'service', '', 'approved'),
(28, 1, 7, 37, 'service', '', 'approved'),
(29, 1, 7, 38, 'service', '', 'approved'),
(30, 1, 7, 38, 'service', '', 'approved'),
(31, 1, 10, 38, 'service', '', 'approved'),
(32, 1, 7, 44, 'service', '', 'approved'),
(33, 1, 7, 45, 'service', '', 'approved'),
(34, 2, 7, 21, 'service', 'I need to check it', 'approved'),
(35, 2, 7, 21, 'service', 'I need to check it', 'rejected'),
(36, 1, 7, 46, 'service', '', 'approved'),
(37, 1, 7, 28, 'service', '', 'approved'),
(38, 1, 3, 37, 'service', '', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `total_amount` decimal(15,0) NOT NULL DEFAULT 0,
  `status` enum('pending','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `user_id`, `room_id`, `checkin_date`, `checkout_date`, `total_amount`, `status`, `payment_status`) VALUES
(3, 1, 1, '2025-05-10', '2025-05-15', 620000, 'checked_out', 'paid'),
(4, 1, 2, '2025-05-10', '2025-05-15', 1000000, 'checked_out', 'paid'),
(5, 1, 8, '2025-05-29', '2025-05-31', 400000, 'checked_in', 'unpaid'),
(6, 1, 4, '2025-05-10', '2025-05-15', 500000, 'checked_in', 'unpaid'),
(7, 1, 1, '2025-05-10', '2025-05-15', 500000, 'checked_in', 'unpaid'),
(8, 1, 2, '2025-05-10', '2025-05-15', 1000000, 'cancelled', 'unpaid'),
(9, 1, 3, '2025-05-10', '2025-05-15', 2500000, 'checked_in', 'unpaid'),
(10, 1, 4, '2025-05-10', '2025-05-15', 500000, 'checked_in', 'unpaid'),
(11, 1, 1, '2025-05-10', '2025-05-15', 500000, 'checked_in', 'unpaid'),
(12, 1, 2, '2025-05-10', '2025-05-15', 1000000, 'checked_in', 'unpaid'),
(13, 1, 3, '2025-05-10', '2025-05-15', 2500000, 'checked_in', 'unpaid'),
(14, 1, 4, '2025-05-10', '2025-05-15', 500000, 'checked_in', 'unpaid'),
(15, 2, 5, '2025-05-10', '2025-05-12', 400000, 'checked_out', 'paid'),
(16, 2, 7, '2025-05-09', '2025-05-10', 100000, 'checked_out', 'paid'),
(17, 3, 9, '2025-05-11', '2025-05-14', 1500000, 'checked_in', 'unpaid'),
(18, 3, 11, '2025-05-08', '2025-05-10', 400000, 'checked_out', 'paid'),
(20, 3, 23, '2025-05-10', '2025-05-13', 3000000, 'checked_in', 'unpaid'),
(21, 2, 5, '2025-05-10', '2025-05-12', 500000, 'checked_in', 'unpaid'),
(22, 2, 7, '2025-05-09', '2025-05-10', 100000, 'checked_out', 'paid'),
(23, 3, 9, '2025-05-11', '2025-05-14', 1500000, 'checked_in', 'unpaid'),
(24, 3, 11, '2025-05-08', '2025-05-10', 400000, 'checked_out', 'paid'),
(25, 2, 21, '2025-05-12', '2025-05-15', 3000000, 'checked_in', 'unpaid'),
(26, 3, 23, '2025-05-10', '2025-05-13', 3000000, 'checked_in', 'unpaid'),
(27, 2, 5, '2025-05-10', '2025-05-12', 400000, 'checked_out', 'paid'),
(28, 2, 7, '2025-05-09', '2025-05-10', 200000, 'checked_out', 'paid'),
(29, 3, 9, '2025-05-11', '2025-05-14', 1500000, 'checked_in', 'unpaid'),
(30, 3, 11, '2025-05-08', '2025-05-10', 400000, 'checked_out', 'paid'),
(31, 2, 21, '2025-05-12', '2025-05-15', 3000000, 'checked_in', 'unpaid'),
(33, 1, 1, '2025-05-22', '2025-05-23', 100000, 'checked_in', 'unpaid'),
(34, 2, 1, '2025-05-07', '2025-05-08', 100000, 'checked_out', 'paid'),
(35, 1, 1, '2025-05-17', '2025-05-19', 200000, 'cancelled', 'unpaid'),
(36, 2, 1, '2025-05-31', '2025-06-04', 700000, 'checked_out', 'paid'),
(37, 1, 1, '2025-05-16', '2025-05-18', 525000, 'checked_in', 'unpaid'),
(38, 1, 1, '2025-05-24', '2025-05-30', 760000, 'checked_out', 'paid'),
(39, 2, 2, '2025-05-24', '2025-05-29', 1000000, 'checked_in', 'unpaid'),
(40, 2, 5, '2025-05-16', '2025-05-17', 200000, 'checked_in', 'unpaid'),
(42, 2, 1, '2026-02-19', '2026-02-20', 100000, 'checked_out', 'paid'),
(43, 1, 1, '2026-03-11', '2026-03-12', 100000, 'checked_out', 'paid'),
(44, 1, 1, '2026-11-26', '2026-11-27', 200000, 'checked_out', 'paid'),
(45, 1, 1, '2025-12-31', '2026-01-01', 200000, 'checked_out', 'paid'),
(46, 1, 24, '2025-06-30', '2025-07-02', 4100000, 'checked_in', 'unpaid'),
(47, 2, 7, '2025-05-14', '2025-05-16', 200000, 'checked_in', 'unpaid'),
(49, 2, 10, '2025-05-15', '2025-05-16', 100000, 'checked_in', 'unpaid'),
(50, 2, 13, '2025-05-15', '2025-05-16', 100000, 'pending', 'unpaid'),
(51, 2, 16, '2025-05-15', '2025-05-16', 100000, 'pending', 'unpaid'),
(52, 2, 19, '2025-05-15', '2025-05-16', 100000, 'pending', 'unpaid'),
(53, 2, 27, '2025-05-15', '2025-05-16', 1000000, 'pending', 'unpaid'),
(57, 2, 6, '2025-05-15', '2025-05-16', 500000, 'pending', 'unpaid'),
(58, 2, 8, '2025-05-15', '2025-05-16', 200000, 'pending', 'unpaid'),
(59, 1, 1, '2025-05-20', '2025-05-22', 200000, 'checked_in', 'unpaid'),
(60, 1, 16, '2025-05-11', '2025-05-12', 100000, 'checked_in', 'unpaid'),
(61, 1, 24, '2025-05-12', '2025-05-15', 6000000, 'checked_in', 'unpaid'),
(62, 1, 30, '2025-05-11', '2025-05-13', 4000000, 'pending', 'unpaid'),
(63, 1, 1, '2025-10-11', '2025-10-12', 100000, 'pending', 'unpaid'),
(64, 1, 13, '2025-05-11', '2025-05-12', 100000, 'pending', 'unpaid'),
(65, 1, 20, '2025-05-22', '2025-05-23', 200000, 'pending', 'unpaid');

--
-- Triggers `reservation`
--
DELIMITER $$
CREATE TRIGGER `calculate_reservation_amount` BEFORE INSERT ON `reservation` FOR EACH ROW BEGIN
    DECLARE room_price DECIMAL(10,2);
    SELECT price INTO room_price
    FROM rooms
    WHERE id = NEW.room_id;
    SET NEW.total_amount = DATEDIFF(NEW.checkout_date, NEW.checkin_date) * room_price;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reset_token`
--

CREATE TABLE `reset_token` (
  `email` varchar(64) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire_on` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(64) NOT NULL,
  `room_category` enum('single','double','suite') NOT NULL,
  `room_type` enum('vip','normal') NOT NULL,
  `room_number` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `max_people` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `description` text DEFAULT 'Comfortable room with modern amenities.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `room_category`, `room_type`, `room_number`, `floor`, `max_people`, `price`, `description`) VALUES
(1, 'Mountain View Single', 'single', 'normal', 101, 1, 2, 100000, 'Cozy single room with a stunning view of Sapa’s mountains. Includes free Wi-Fi, hot shower, and complimentary breakfast.'),
(2, 'Valley Breeze Single', 'single', 'normal', 102, 1, 2, 200000, 'Charming single room with fresh mountain air and a private balcony. Free Wi-Fi and breakfast included.'),
(3, 'Cloud Haven Double', 'double', 'normal', 103, 1, 4, 500000, 'Spacious double room perfect for couples or small families, featuring mountain views, free Wi-Fi, and daily breakfast.'),
(4, 'Misty Peak Single', 'single', 'normal', 104, 1, 2, 100000, 'Quaint single room with cozy bedding and scenic views. Includes hot shower, Wi-Fi, and breakfast.'),
(5, 'Rice Terrace Double', 'double', 'normal', 105, 1, 4, 200000, 'Bright double room overlooking lush rice terraces. Comes with free Wi-Fi, breakfast, and access to guided tours.'),
(6, 'Sunset Suite', 'suite', 'normal', 106, 1, 6, 500000, 'Luxurious suite with panoramic views, ideal for groups. Includes Wi-Fi, breakfast, and complimentary tea/coffee.'),
(7, 'Bamboo Grove Single', 'single', 'normal', 107, 1, 2, 100000, 'Tranquil single room surrounded by bamboo gardens. Features Wi-Fi, hot shower, and daily breakfast.'),
(8, 'River Song Double', 'double', 'normal', 108, 1, 4, 200000, 'Comfortable double room with river views, free Wi-Fi, breakfast, and access to local cultural experiences.'),
(9, 'Starlight Suite', 'suite', 'normal', 109, 2, 6, 500000, 'Elegant suite with stargazing balcony. Includes Wi-Fi, breakfast, and complimentary local snacks.'),
(10, 'Morning Dew Single', 'single', 'normal', 110, 2, 2, 100000, 'Serene single room with fresh linens and mountain views. Free Wi-Fi and breakfast provided.'),
(11, 'Hillside Double', 'double', 'normal', 201, 2, 4, 200000, 'Warm double room with hillside views, Wi-Fi, breakfast, and hot shower.'),
(12, 'Skyward Suite', 'suite', 'normal', 202, 2, 6, 500000, 'Spacious suite with sweeping views of Sapa. Includes Wi-Fi, breakfast, and guided tour access.'),
(13, 'Pine Whisper Single', 'single', 'normal', 203, 2, 2, 100000, 'Cozy single room with pine forest views. Free Wi-Fi and breakfast included.'),
(14, 'Meadow Bloom Double', 'double', 'normal', 204, 2, 4, 200000, 'Bright double room with meadow views, Wi-Fi, breakfast, and hot shower.'),
(15, 'Twilight Suite', 'suite', 'normal', 205, 3, 6, 500000, 'Luxurious suite with sunset views, Wi-Fi, breakfast, and complimentary drinks.'),
(16, 'Forest Mist Single', 'single', 'normal', 206, 3, 2, 100000, 'Peaceful single room with forest views. Includes Wi-Fi, hot shower, and breakfast.'),
(17, 'Golden Valley Double', 'double', 'normal', 207, 3, 4, 200000, 'Spacious double room with valley views, Wi-Fi, breakfast, and cultural tour access.'),
(18, 'Moonlit Suite', 'suite', 'normal', 208, 3, 6, 500000, 'Elegant suite with moonlit balcony, Wi-Fi, breakfast, and local snacks.'),
(19, 'Dawn Glow Single', 'single', 'normal', 209, 3, 2, 100000, 'Cozy single room with sunrise views, Wi-Fi, and breakfast.'),
(20, 'Emerald Double', 'double', 'normal', 210, 3, 4, 200000, 'Comfortable double room with emerald hill views, Wi-Fi, and breakfast.'),
(21, 'VIP Mountain Retreat', 'single', 'vip', 401, 6, 2, 1000000, 'Exclusive VIP single room with private balcony, premium amenities, Wi-Fi, gourmet breakfast, and guided tours.'),
(22, 'VIP Valley Sanctuary', 'double', 'vip', 402, 6, 4, 2000000, 'Luxurious VIP double room with panoramic views, Wi-Fi, gourmet breakfast, and exclusive cultural experiences.'),
(23, 'VIP Cloud Suite', 'suite', 'vip', 403, 6, 6, 1000000, 'Opulent VIP suite with cloud-level views, Wi-Fi, gourmet breakfast, and private tour services.'),
(24, 'VIP Starry Haven', 'single', 'vip', 404, 6, 2, 2000000, 'Premium VIP single room with stargazing balcony, Wi-Fi, gourmet breakfast, and luxury amenities.'),
(25, 'VIP Sunset Glow', 'double', 'vip', 405, 6, 4, 1000000, 'Elegant VIP double room with sunset views, Wi-Fi, gourmet breakfast, and exclusive services.'),
(26, 'VIP Moonlight Suite', 'suite', 'vip', 406, 6, 6, 2000000, 'Grand VIP suite with moonlight views, Wi-Fi, gourmet breakfast, and private cultural tours.'),
(27, 'VIP Pine Oasis', 'single', 'vip', 407, 7, 2, 1000000, 'Cozy VIP single room with pine forest views, Wi-Fi, gourmet breakfast, and premium amenities.'),
(28, 'VIP River Harmony', 'double', 'vip', 408, 7, 4, 2000000, 'Spacious VIP double room with river views, Wi-Fi, gourmet breakfast, and exclusive tours.'),
(29, 'VIP Sky Suite', 'suite', 'vip', 409, 7, 6, 1000000, 'Luxurious VIP suite with sky-high views, Wi-Fi, gourmet breakfast, and private services.'),
(30, 'VIP Meadow Bliss', 'single', 'vip', 410, 7, 2, 2000000, 'Premium VIP single room with meadow views, Wi-Fi, gourmet breakfast, and luxury amenities.');

-- --------------------------------------------------------

--
-- Stand-in structure for view `room_data`
-- (See below for the actual view)
--

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `service_name` varchar(64) NOT NULL,
  `price` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `service_name`, `price`) VALUES
(1, 'Room Service', 30000),
(2, 'Cleaning Service', 20000),
(3, 'Laundry Service', 25000),
(4, 'Spa Service', 80000),
(5, 'Gym Access', 50000),
(6, 'Pool Access', 50000),
(7, 'Airport Pickup', 100000),
(8, 'Event Booking', 70000),
(9, 'Massage Service', 90000),
(10, 'Car Rental', 60000);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('manager','receptionist','housekeeping','other') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `firstname`, `lastname`, `email`, `phone`, `role`, `created_at`) VALUES
(1, 'John', 'john', 'john123@peacehome.com', '012345667789', 'receptionist', '2025-05-09 02:18:56'),
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
(24, 'Hihi', 'Haha', 'a@gmail.com', '01234567891', 'manager', '2025-05-11 07:31:21'),
(25, 'AA', 'BB', 'd@gmail.com', '0123345678', 'receptionist', '2025-05-11 16:53:50');

-- --------------------------------------------------------

--
-- Structure for view `monthly_room_data`
--
DROP TABLE IF EXISTS `monthly_room_data`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `monthly_room_data`  AS SELECT `r`.`id` AS `room_id`, date_format(`res`.`checkout_date`,'%Y-%m') AS `month`, count(`res`.`id`) AS `total_checkin`, coalesce(sum(case when `res`.`payment_status` in ('paid','partially_paid') then (to_days(`res`.`checkout_date`) - to_days(`res`.`checkin_date`)) * `r`.`price` else 0 end),0) AS `total_money` FROM (`rooms` `r` left join `reservation` `res` on(`res`.`room_id` = `r`.`id`)) GROUP BY `r`.`id`, date_format(`res`.`checkout_date`,'%Y-%m') ORDER BY date_format(`res`.`checkout_date`,'%Y-%m') DESC ;

-- --------------------------------------------------------

--
-- Structure for view `room_data`
--
--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `request_ibfk_3` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
