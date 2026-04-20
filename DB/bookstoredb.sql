-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 19, 2026 at 03:21 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstoredb`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_add_order_item`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_order_item` (IN `p_order_id` INT, IN `p_book_id` INT, IN `p_quantity` INT)   BEGIN
    DECLARE v_price DECIMAL(12,2);

    -- Lấy giá sách
    SELECT price INTO v_price
    FROM BOOKS
    WHERE book_id = p_book_id;

    -- Thêm vào ORDER_ITEMS
    INSERT INTO ORDER_ITEMS (order_id, book_id, quantity, price)
    VALUES (p_order_id, p_book_id, p_quantity, v_price);

    -- Cập nhật tổng tiền đơn hàng
    UPDATE ORDERS
    SET total_amount = (
        SELECT SUM(quantity * price)
        FROM ORDER_ITEMS
        WHERE order_id = p_order_id
    )
    WHERE order_id = p_order_id;
END$$

DROP PROCEDURE IF EXISTS `sp_create_order`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_order` (IN `p_user_id` INT)   BEGIN
    INSERT INTO ORDERS (user_id, total_amount, status)
    VALUES (p_user_id, 0, 'pending');

    SELECT LAST_INSERT_ID() AS order_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE IF NOT EXISTS `addresses` (
  `address_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `street` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `ward` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`address_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `fullname`, `phone`, `street`, `city`, `district`, `ward`, `is_default`, `created_at`) VALUES
(1, 1, 'Nguyễn Trọng Thắng', '0901000001', '12 Lê Lợi', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Bến Nghé', 1, '2025-02-11 09:42:18'),
(2, 2, 'Lê Thị Thuỳ Dung', '0901000002', '45 Nguyễn Huệ', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Bến Nghé', 1, '2025-03-05 14:21:44'),
(3, 3, 'Trần Văn Long', '0901000003', '88 Đồng Khởi', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Bến Nghé', 1, '2025-01-29 08:11:02'),
(4, 4, 'Phạm Minh Duy', '0901000004', '120 Hai Bà Trưng', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Đa Kao', 1, '2025-04-16 19:33:51'),
(5, 5, 'Hoàng Thanh Ngọc', '0901000005', '35 Điện Biên Phủ', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 15', 1, '2025-05-02 10:05:09'),
(6, 6, 'Võ Đình Hiếu', '0901000006', '90 Xô Viết Nghệ Tĩnh', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 21', 1, '2025-06-18 22:41:37'),
(7, 7, 'Bùi Thị Hồng', '0901000007', '210 Phan Đăng Lưu', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 3', 1, '2025-07-09 07:58:12'),
(8, 8, 'Đỗ Minh Quân', '0901000008', '55 Hoàng Văn Thụ', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 8', 1, '2025-02-27 16:49:05'),
(9, 9, 'Phan Văn Tài', '0901000009', '78 Nguyễn Kiệm', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 3', 1, '2025-08-14 11:26:54'),
(10, 10, 'Ngô Hoàng Yến', '0901000010', '150 Quang Trung', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 10', 1, '2025-03-19 20:12:33'),
(11, 11, 'Trịnh Xuân Hùng', '0901000011', '22 Lê Đức Thọ', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 6', 1, '2025-04-07 09:37:41'),
(12, 12, 'Vũ Ngọc La', '0901000012', '105 Phạm Văn Chiêu', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 9', 1, '2025-01-18 15:08:20'),
(13, 13, 'Dương Chí Dũng', '0901000013', '60 Nguyễn Oanh', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 17', 1, '2025-09-03 18:55:06'),
(14, 14, 'Bạch Mai Phương', '0901000014', '88 Thống Nhất', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 11', 1, '2025-02-03 07:44:59'),
(15, 15, 'Đặng Quang Huy', '0901000015', '14 Lê Văn Thọ', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 8', 1, '2025-06-25 13:22:48'),
(16, 16, 'Tô Thị Kim', '0901000016', '45 Nguyễn Văn Lượng', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 16', 1, '2025-07-30 21:09:10'),
(17, 17, 'Huỳnh Thế Anh', '0901000017', '120 Phạm Ngũ Lão', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Phạm Ngũ Lão', 1, '2025-03-12 10:16:02'),
(18, 18, 'Nguyễn Phương Nhi', '0901000018', '99 Bùi Viện', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Phạm Ngũ Lão', 1, '2025-04-28 23:45:17'),
(19, 19, 'Lý Văn Thành', '0901000019', '210 Trần Hưng Đạo', 'TP Hồ Chí Minh', 'Quận 1', 'Phường Cô Giang', 1, '2025-01-06 08:32:11'),
(20, 20, 'Trần Huyền Trang', '0901000020', '75 Nguyễn Trãi', 'TP Hồ Chí Minh', 'Quận 5', 'Phường 2', 1, '2025-05-21 17:54:39'),
(21, 21, 'Phạm Quang Vinh', '0901000021', '188 Trần Bình Trọng', 'TP Hồ Chí Minh', 'Quận 5', 'Phường 4', 1, '2025-08-01 14:09:26'),
(22, 22, 'Đoàn Thị Thủy', '0901000022', '40 Hồng Bàng', 'TP Hồ Chí Minh', 'Quận 5', 'Phường 1', 1, '2025-02-20 09:18:55'),
(23, 23, 'Lưu Đình Kiên', '0901000023', '90 An Dương Vương', 'TP Hồ Chí Minh', 'Quận 5', 'Phường 9', 1, '2025-06-11 19:37:42'),
(24, 24, 'Võ Thanh Tú', '0901000024', '33 Nguyễn Chí Thanh', 'TP Hồ Chí Minh', 'Quận 10', 'Phường 6', 1, '2025-04-04 11:05:01'),
(25, 25, 'Nguyễn Bá Sơn', '0901000025', '120 Ba Tháng Hai', 'TP Hồ Chí Minh', 'Quận 10', 'Phường 12', 1, '2025-09-15 22:48:19'),
(26, 26, 'Hoàng Ánh Ngọc', '0901000026', '58 Sư Vạn Hạnh', 'TP Hồ Chí Minh', 'Quận 10', 'Phường 9', 1, '2025-03-08 16:40:03'),
(27, 27, 'Mai Hồng Phát', '0901000027', '102 Lạc Long Quân', 'TP Hồ Chí Minh', 'Quận 11', 'Phường 3', 1, '2025-07-17 08:12:56'),
(28, 28, 'Lê Văn Duẩn', '0901000028', '210 Âu Cơ', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 10', 1, '2025-02-14 19:44:21'),
(29, 29, 'Trần Thị Mai', '0901000029', '88 Trường Chinh', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 13', 1, '2025-05-26 10:31:09'),
(30, 30, 'Đinh Quốc Việt', '0901000030', '15 Cộng Hòa', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 4', 1, '2025-06-09 22:05:47'),
(31, 31, 'Nguyễn Hải Nam', '0901000031', '120 Hoàng Hoa Thám', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 12', 1, '2025-01-22 07:58:30'),
(32, 32, 'Phan Thị Kim Ngân', '0901000032', '77 Phạm Phú Thứ', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 11', 1, '2025-08-19 14:26:54'),
(33, 33, 'Võ Văn Dũng', '0901000033', '50 Lũy Bán Bích', 'TP Hồ Chí Minh', 'Quận Tân Phú', 'Phường Hòa Thạnh', 1, '2025-03-03 09:41:22'),
(34, 34, 'Hồ Ngọc Diệp', '0901000034', '140 Tân Kỳ Tân Quý', 'TP Hồ Chí Minh', 'Quận Tân Phú', 'Phường Sơn Kỳ', 1, '2025-04-15 18:33:10'),
(35, 35, 'Trần Quốc Bảo', '0901000035', '99 Gò Dầu', 'TP Hồ Chí Minh', 'Quận Tân Phú', 'Phường Tân Quý', 1, '2025-07-28 11:06:49'),
(36, 36, 'Lê Thanh Thúy', '0901000036', '210 Thoại Ngọc Hầu', 'TP Hồ Chí Minh', 'Quận Tân Phú', 'Phường Phú Thạnh', 1, '2025-02-06 20:19:55'),
(37, 37, 'Đỗ Trọng Nghĩa', '0901000037', '35 Hòa Bình', 'TP Hồ Chí Minh', 'Quận Tân Phú', 'Phường Hiệp Tân', 1, '2025-06-22 08:57:13'),
(38, 38, 'Nguyễn Hồng Hạnh', '0901000038', '88 Kinh Dương Vương', 'TP Hồ Chí Minh', 'Quận Bình Tân', 'Phường An Lạc', 1, '2025-03-29 15:42:00'),
(39, 39, 'Phạm Văn Cường', '0901000039', '120 Lê Văn Quới', 'TP Hồ Chí Minh', 'Quận Bình Tân', 'Phường Bình Trị Đông', 1, '2025-09-07 10:11:36'),
(40, 40, 'Lý Kim Chi', '0901000040', '77 Tên Lửa', 'TP Hồ Chí Minh', 'Quận Bình Tân', 'Phường Bình Trị Đông B', 1, '2025-01-13 17:26:44'),
(41, 41, 'Hoàng Quốc Đại', '0901000041', '55 Nguyễn Thị Tú', 'TP Hồ Chí Minh', 'Quận Bình Tân', 'Phường Bình Hưng Hòa', 1, '2025-05-18 09:08:19'),
(42, 42, 'Mai Hồng Nga', '0901000042', '160 Mã Lò', 'TP Hồ Chí Minh', 'Quận Bình Tân', 'Phường Bình Trị Đông A', 1, '2025-08-26 21:35:02'),
(43, 43, 'Bùi Thanh Liêm', '0901000043', '98 Phạm Văn Hai', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 2', 1, '2025-04-01 13:54:28'),
(44, 44, 'Vũ Quỳnh Hương', '0901000044', '12 Nguyễn Hồng Đào', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 14', 1, '2025-06-30 07:49:51'),
(45, 45, 'Trần Minh Khang', '0901000045', '230 Bàu Cát', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 12', 1, '2025-02-24 16:18:07'),
(46, 46, 'Nguyễn Thị Yến', '0901000046', '66 Đồng Đen', 'TP Hồ Chí Minh', 'Quận Tân Bình', 'Phường 10', 1, '2025-07-12 22:01:33'),
(47, 47, 'Đinh Tuấn Kiệt', '0901000047', '102 Cách Mạng Tháng 8', 'TP Hồ Chí Minh', 'Quận 3', 'Phường 5', 1, '2025-03-17 08:40:12'),
(48, 48, 'Lê Phương Anh', '0901000048', '55 Nguyễn Đình Chiểu', 'TP Hồ Chí Minh', 'Quận 3', 'Phường 4', 1, '2025-05-07 19:27:46'),
(49, 49, 'Phan Văn Minh', '0901000049', '88 Võ Văn Tần', 'TP Hồ Chí Minh', 'Quận 3', 'Phường 6', 1, '2025-01-09 11:15:58'),
(50, 50, 'Hà Thị Tuyết', '0901000050', '140 Trần Quốc Thảo', 'TP Hồ Chí Minh', 'Quận 3', 'Phường 7', 1, '2025-08-11 14:59:21'),
(51, 51, 'Nguyễn Anh Khoa', '0901000051', '75 Lý Chính Thắng', 'TP Hồ Chí Minh', 'Quận 3', 'Phường 8', 1, '2025-04-22 20:06:39'),
(52, 52, 'Trần Huyền My', '0901000052', '120 Trần Huy Liệu', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 15', 1, '2025-02-08 09:34:10'),
(53, 53, 'Võ Minh Trí', '0901000053', '55 Huỳnh Văn Bánh', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 11', 1, '2025-06-14 18:22:49'),
(54, 54, 'Lê Kim Cương', '0901000054', '98 Phan Xích Long', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 2', 1, '2025-03-26 07:51:33'),
(55, 55, 'Hoàng Quốc Trung', '0901000055', '210 Hoa Lan', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 7', 1, '2025-09-01 21:09:18'),
(56, 56, 'Bùi Thị Dung', '0901000056', '33 Nguyễn Kiệm', 'TP Hồ Chí Minh', 'Quận Phú Nhuận', 'Phường 4', 1, '2025-01-27 15:43:06'),
(57, 57, 'Mai Văn Tấ', '0901000057', '150 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 11', 1, '2025-05-10 10:17:52'),
(58, 58, 'Đinh Thị Loa', '0901000058', '88 Nơ Trang Long', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 7', 1, '2025-07-03 22:35:41'),
(59, 59, 'Phạm Hữu Đức', '0901000059', '120 Bạch Đằng', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 2', 1, '2025-04-19 08:09:27'),
(60, 60, 'Trần Mai Linh', '0901000060', '66 Nguyễn Xí', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 26', 1, '2025-02-16 19:58:04'),
(61, 61, 'Nguyễn Đình Khoa', '0901000061', '35 Ung Văn Khiêm', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 25', 1, '2025-08-24 11:46:39'),
(62, 62, 'Lý Thu Hằng', '0901000062', '210 D2', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 25', 1, '2025-06-01 14:32:08'),
(63, 63, 'Văn Đức Huy', '0901000063', '88 Điện Biên Phủ', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 22', 1, '2025-03-21 09:05:55'),
(64, 64, 'Dương Thị Hoà', '0901000064', '77 Nguyễn Gia Trí', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 25', 1, '2025-01-11 20:18:44'),
(65, 65, 'Bùi Anh Tuấn', '0901000065', '120 Lê Quang Định', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 14', 1, '2025-07-27 16:57:19'),
(66, 66, 'Phạm Thu Nguyệt', '0901000066', '99 Phan Văn Trị', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 5', 1, '2025-04-05 08:36:02'),
(67, 67, 'Lê Minh Cường', '0901000067', '140 Nguyễn Văn Khối', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 8', 1, '2025-06-20 21:44:28'),
(68, 68, 'Trần Văn Long', '0901000068', '55 Thống Nhất', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 16', 1, '2025-02-28 10:13:47'),
(69, 69, 'Đỗ Thị Ngọc', '0901000069', '210 Lê Đức Thọ', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 6', 1, '2025-09-12 18:25:36'),
(70, 70, 'Nguyễn Quang Minh', '0901000070', '88 Quang Trung', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 10', 1, '2025-05-14 07:59:11'),
(71, 71, 'Vũ Đình Khải', '0901000071', '120 Phạm Văn Chiêu', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 9', 1, '2025-01-24 13:40:58'),
(72, 72, 'Hoàng Thị Vân', '0901000072', '66 Nguyễn Oanh', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 17', 1, '2025-08-05 22:17:04'),
(73, 73, 'Mai Hữu Tài', '0901000073', '35 Lê Văn Thọ', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 11', 1, '2025-03-31 09:26:39'),
(74, 74, 'Lê Kim Ngân', '0901000074', '150 Thống Nhất', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 15', 1, '2025-06-27 15:52:18'),
(75, 75, 'Phạm Văn Trung', '0901000075', '99 Nguyễn Kiệm', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 3', 1, '2025-04-12 20:08:47'),
(76, 76, 'Trần Ngọc Hằng', '0901000076', '210 Phan Huy Ích', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 12', 1, '2025-07-19 11:34:55'),
(77, 77, 'Đặng Thanh Tùng', '0901000077', '88 Lê Quang Định', 'TP Hồ Chí Minh', 'Quận Gò Vấp', 'Phường 5', 1, '2025-02-01 18:41:09'),
(78, 78, 'Nguyễn Thùy Linh', '0901000078', '120 Võ Văn Ngân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Bình Thọ', 1, '2025-05-23 09:58:14'),
(79, 79, 'Lý Văn Bách', '0901000079', '55 Kha Vạn Cân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Tây', 1, '2025-01-16 21:22:36'),
(80, 80, 'Võ Thị Trâm', '0901000080', '88 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Hiệp Bình Chánh', 1, '2025-08-17 14:47:02'),
(81, 81, 'Hoàng Minh Nhật', '0901000081', '210 Tô Ngọc Vân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Tam Phú', 1, '2025-03-09 08:13:55'),
(82, 82, 'Bùi Kim Anh', '0901000082', '66 Quốc Lộ 13', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Hiệp Bình Phước', 1, '2025-06-02 19:35:41'),
(83, 83, 'Mai Đình Phong', '0901000083', '120 Đặng Văn Bi', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Trường Thọ', 1, '2025-04-27 10:24:18'),
(84, 84, 'Đinh Thanh Hương', '0901000084', '35 Nguyễn Văn Bá', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Bình Thọ', 1, '2025-07-21 22:09:07'),
(85, 85, 'Phạm Duy Anh', '0901000085', '88 Võ Văn Ngân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Chiểu', 1, '2025-02-10 15:56:39'),
(86, 86, 'Trần Hải Yến', '0901000086', '210 Kha Vạn Cân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Đông', 1, '2025-05-06 08:32:11'),
(87, 87, 'Nguyễn Quang Huy', '0901000087', '55 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Hiệp Bình Chánh', 1, '2025-09-09 20:44:53'),
(88, 88, 'Lý Thị Thanh', '0901000088', '120 Tô Ngọc Vân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Tam Bình', 1, '2025-03-15 11:18:27'),
(89, 89, 'Võ Văn Nam', '0901000089', '66 Quốc Lộ 1K', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Xuân', 1, '2025-06-23 16:05:49'),
(90, 90, 'Dương Thúy Nga', '0901000090', '88 Xa Lộ Hà Nội', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Trường Thọ', 1, '2025-01-20 09:41:02'),
(91, 91, 'Bùi Minh Khôi', '0901000091', '210 Võ Văn Ngân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Bình Thọ', 1, '2025-04-18 22:27:35'),
(92, 92, 'Phạm Phương Thảo', '0901000092', '55 Kha Vạn Cân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Tây', 1, '2025-07-04 14:59:06'),
(93, 93, 'Lê Đình Trọng', '0901000093', '120 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Đông', 1, '2025-02-25 08:10:48'),
(94, 94, 'Trần Mai Chi', '0901000094', '66 Tô Ngọc Vân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Tam Phú', 1, '2025-05-29 19:36:21'),
(95, 95, 'Đỗ Văn Quyết', '0901000095', '88 Quốc Lộ 13', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Hiệp Bình Phước', 1, '2025-08-08 11:47:55'),
(96, 96, 'Nguyễn Thị Hoa', '0901000096', '210 Đặng Văn Bi', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Trường Thọ', 1, '2025-03-01 16:22:09'),
(97, 97, 'Vũ Quang Vinh', '0901000097', '55 Nguyễn Văn Bá', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Bình Thọ', 1, '2025-06-16 09:54:37'),
(98, 98, 'Hoàng Tuyết Mai', '0901000098', '120 Võ Văn Ngân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Chiểu', 1, '2025-01-28 20:18:44'),
(99, 99, 'Mai Văn Lợi', '0901000099', '66 Kha Vạn Cân', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Tây', 1, '2025-04-10 13:05:31'),
(100, 100, 'Lê Thị Hương', '0901000100', '88 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'TP Thủ Đức', 'Phường Hiệp Bình Chánh', 1, '2025-07-14 18:06:42'),
(101, 101, 'Thư', '0901000101', '75 Phạm Văn Đồng', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 13', 1, '2025-12-22 09:41:15'),
(102, 102, 'Thư', '0901000102', '120 Nơ Trang Long', 'TP Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 7', 1, '2026-01-03 20:27:58'),
(108, 110, 'Huỳnh Minh Thư', '902000020', '1234', 'TP.HCM', 'Q.10', '13', 1, '2026-04-19 21:02:50'),
(107, 110, 'Huỳnh Minh Thư', '902000020', '0986', 'TP.HCM', 'Q.10', '13', 0, '2026-04-19 21:02:40'),
(105, 109, 'HMT', '902000020', '56780345', 'TP.HCM', 'Q.10', '13', 1, '2026-04-18 22:34:34');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `category_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` int DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `images` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `link_images` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `sold_quantity` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_books_title` (`title`(250)),
  KEY `idx_books_author` (`author`(250))
) ENGINE=MyISAM AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `category_id`, `price`, `discount`, `description`, `images`, `link_images`, `sold_quantity`, `created_at`) VALUES
(1, 'Đắc Nhân Tâm', 'Dale Carnegie', 1, 120000.00, 20, 'Sách giao tiếp kinh điển giúp cải thiện quan hệ và ảnh hưởng.', '1.jpg', 'https://firstnews.vn/upload/products/original/-1726817123.jpg', 3500, '2024-01-05 00:00:00'),
(2, '7 Thói Quen Hiệu Quả', 'Stephen R. Covey', 1, 150000.00, 15, 'Xây dựng 7 thói quen nền tảng để thành công.', '2.jpg', 'https://cdn1.fahasa.com/media/catalog/product/7/h/7h_700x650_bia1.png', 2870, '2024-01-06 00:00:00'),
(3, 'Tư Duy Nhanh Và Chậm', 'Daniel Kahneman', 1, 180000.00, 10, 'Khám phá hai hệ thống tư duy và cơ chế quyết định.', '3.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/197/269/products/462558750-1083111936819329-1957541486232979466-n.png?v=1730363480047', 1940, '2024-01-07 00:00:00'),
(4, 'Sức Mạnh Của Thói Quen', 'Charles Duhigg', 1, 140000.00, 25, 'Làm rõ vòng lặp thói quen và cách thay đổi hành vi.', '4.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/197/269/products/z4105964911654-5e5a35259898ca70607286170319aa65.jpg?v=1676275362800', 2600, '2024-01-08 00:00:00'),
(5, 'Dám Bị Ghét', 'Kishimi Ichiro', 1, 130000.00, 20, 'Triết lý Adler giúp giải phóng bản thân khỏi áp lực xã hội.', '5.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935235215283_1.jpg', 2202, '2024-01-09 00:00:00'),
(6, 'Nghệ Thuật Tinh Tế Của Việc \"Đếch Quan Tâm\"', 'Mark Manson', 1, 150000.00, 18, 'Sống tối giản cảm xúc, tập trung vào điều quan trọng.', '6.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935095632077---copy.jpg', 3100, '2024-01-10 00:00:00'),
(7, 'Tâm Lý Học Hài Hước', 'Richard Wiseman', 1, 125000.00, 10, 'Những thí nghiệm tâm lý thú vị trong cuộc sống.', '7.jpg', 'https://product.hstatic.net/200000900535/product/499601302_tam-ly-hoc-hai-huoc-2_c0ec0e76b2ad46e8a058877bdc02c796.jpg', 940, '2024-01-11 00:00:00'),
(8, 'Hiểu Về Trái Tim', 'Minh Niệm', 1, 160000.00, 15, 'Chia sẻ về chữa lành và sống tỉnh thức.', '8.jpg', 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1473045794i/13640125.jpg', 1800, '2024-01-12 00:00:00'),
(9, 'Tôi Tự Học', 'Thu Giang Nguyễn Duy Cần', 1, 100000.00, 0, 'Tư duy tự học và phát triển bản thân.', '9.jpg', 'https://www.nxbtre.com.vn/Images/Book/NXBTreStoryFull_21172011_111729.jpg', 2100, '2024-01-13 00:00:00'),
(10, 'Mẹ Vắng Nhà', 'Nguyễn Thi', 1, 90000.00, 5, 'Góc nhìn tâm lý gia đình và trẻ nhỏ.', '10.jpg', 'https://newshop.vn/public/uploads/products/8339/me-vang-nha-bia.gif', 1300, '2024-01-14 00:00:00'),
(11, 'Bạn Thông Minh Hơn Bạn Nghĩ', 'David Perlmutter', 1, 145000.00, 12, 'Mối liên hệ giữa sức khỏe não bộ và hành vi.', '11.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/197/269/products/ban-thong-minh-hon-ban-nghi-01.jpg?v=1712198365253', 890, '2024-01-15 00:00:00'),
(12, 'Nghệ Thuật Giao Tiếp Để Thành Công', 'Leil Lowndes', 1, 135000.00, 20, '92 kỹ thuật giao tiếp áp dụng ngay trong thực tế.', '12.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDlC4rkYuTpkc9dlNqcuNGZCfbTd1bXjuwHw&s', 1750, '2024-01-16 00:00:00'),
(13, 'Đừng Lựa Chọn An Nhàn Khi Còn Trẻ', 'Cảnh Thiên', 1, 110000.00, 10, 'Truyền cảm hứng vượt qua vùng an toàn.', '13.jpg', 'https://cdn1.fahasa.com/media/catalog/product/d/u/dung_lua_chon_an_nhan_khi_con_tre_tai_ban_.jpg', 1600, '2024-01-17 00:00:00'),
(14, 'Thay Đổi Cuộc Sống Với Nhân Số Học', 'David A. Phillips', 1, 160000.00, 15, 'Giải mã nhân số học và ứng dụng trong đời sống.', '14.jpg', 'https://ebookvie.com/wp-content/uploads/2023/12/Thay-Doi-Cuoc-Song-Voi-Nhan-So-Hoc.jpg', 1450, '2024-01-18 00:00:00'),
(15, 'Sống Tối Giản', 'Fumio Sasaki', 1, 120000.00, 5, 'Phương pháp tối giản để giảm áp lực tinh thần.', '15.jpg', 'https://cdn.hstatic.net/products/200000900535/loi_song_toi_gian_cua_nguoi_nhat_-_bia_1__tb_2025__93f9f9855b114093b404bf145e0be656.jpg', 1320, '2024-01-19 00:00:00'),
(16, 'Bạn Đắt Giá Bao Nhiêu', 'Vãn Tình', 1, 125000.00, 8, 'Bài học về giá trị bản thân và sự độc lập.', '16.jpg', 'https://cdn1.fahasa.com/media/catalog/product/b/a/bandatgiabaonhieu.jpg', 2700, '2024-01-20 00:00:00'),
(17, 'Trò Chuyện Với Chính Mình', 'Ethan Kross', 1, 155000.00, 15, 'Khám phá tiếng nói nội tâm và cách kiểm soát nó.', '17.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/chatter___tro_chuyen_voi_chinh_minh/2021_05_10_08_40_49_1-390x510.jpg', 980, '2024-01-21 00:00:00'),
(18, 'Điều Kỳ Diệu Của Thái Độ Sống', 'Norman Vincent Peale', 1, 130000.00, 20, 'Sức mạnh của suy nghĩ tích cực.', '18.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQgYyjxDS8CHegEj8GherMqKS-pwk-LEKqgRQ&s', 2000, '2024-01-22 00:00:00'),
(19, 'Bước Chậm Lại Giữa Thế Gian Vội Vã', 'Hae Min', 1, 145000.00, 18, 'Sống chậm và lắng nghe chính mình.', '19.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/363/455/products/buocchamlaigiuathegianvoiva01-4e74490c-3e27-444a-8edc-eb3d5278892e.jpg?v=1731981615520', 3500, '2024-01-23 00:00:00'),
(20, 'Quẳng Gánh Lo Đi Và Vui Sống', 'Dale Carnegie', 1, 140000.00, 15, 'Hướng dẫn giảm lo âu, sống tích cực.', '20.jpg', 'https://firstnews.vn/upload/products/thumb_800x0/-1727087322.jpg', 3200, '2024-01-24 00:00:00'),
(21, 'Toán 1', 'Bộ GD&ĐT', 3, 35000.00, 5, 'Sách giáo khoa Toán lớp 1 giúp học sinh làm quen với các khái niệm số đếm và phép tính đơn giản.', '21.jpg', 'https://hoc10.monkeyuni.net/E_Learning/page_public/AHc89lMuEtkPbVIlJQNWZIYItWNZQ3s5.jpg', 200, '2024-11-26 00:00:00'),
(22, 'Tiếng Việt 1 - Tập 1', 'Bộ GD&ĐT', 3, 38000.00, 5, 'SGK Tiếng Việt 1 tập 1, giúp học sinh làm quen với chữ cái và tập đọc.', '22.jpg', 'https://online.fliphtml5.com/vjzyu/qbtq/files/large/c4199b127d6d32b23b29b6725ebe6f96.webp?1701439993', 180, '2024-11-26 00:00:00'),
(23, 'Tiếng Việt 1 - Tập 2', 'Bộ GD&ĐT', 3, 38000.00, 5, 'SGK Tiếng Việt 1 tập 2, củng cố đọc – viết cho học sinh lớp 1.', '23.jpg', 'https://mntuoixanh.elearning.tptdm.edu.vn/uploads/sys/elearning/thphuhoa3/2024_05/images/sgk-tieng-viet-1-tap-lop-2-ket-noi-tri-thuc-voi-cuoc-song-23.jpg', 175, '2024-11-26 00:00:00'),
(24, 'Tự Nhiên & Xã Hội 1', 'Bộ GD&ĐT', 3, 32000.00, 5, 'Giới thiệu thế giới tự nhiên và xã hội xung quanh cho học sinh lớp 1.', '24.jpg', 'https://phuongnam.edu.vn/storage/media/bia-sach/202204/e66b6fe1-e481-423e-a023-1325ce455920.jpg', 150, '2024-11-26 00:00:00'),
(25, 'Âm Nhạc 1', 'Bộ GD&ĐT', 3, 30000.00, 5, 'Học hát, tiết tấu và cảm thụ âm nhạc cơ bản.', '25.jpg', 'https://newshop.vn/public/uploads/products/28872/sach-am-nhac-1-bo-sach-giao-khoa-cung-hoc-de-phat-trien-nang-luc.jpg', 90, '2024-11-26 00:00:00'),
(26, 'Mỹ Thuật 1', 'Bộ GD&ĐT', 3, 30000.00, 5, 'Làm quen với màu sắc và hình khối.', '26.jpg', 'https://sachdientu.sachthietbigiaoduc.vn/upload/bia-sgk-lop-1/bia-sach-giao-khoa-mi-thuat.jpg?v=1.0.1', 85, '2024-11-26 00:00:00'),
(27, 'Tin Học 1', 'Bộ GD&ĐT', 3, 35000.00, 5, 'Làm quen với máy tính và thao tác chuột.', '27.png', 'https://nxb.hcmue.edu.vn/wp-content/uploads/2022/07/BIA-EM-VUI-HOC-TIN-HOC-LOP-1.png', 80, '2024-11-26 00:00:00'),
(28, 'Đạo Đức 1', 'Bộ GD&ĐT', 3, 32000.00, 5, 'Bài học về cư xử, lễ phép và kỷ luật.', '28.jpg', 'https://online.pubhtml5.com/uohzi/sigy/files/large/834c0a27984816e1551a9956f212b0c7.jpg', 95, '2024-11-26 00:00:00'),
(29, 'Toán 2', 'Bộ GD&ĐT', 3, 36000.00, 5, 'SGK Toán 2 với phép cộng, trừ có nhớ và các bài toán thực tế.', '29.jpg', 'https://hieutruong.com/uploads/sach/2023/05/16/toan-lop-2-tap-1-canh-dieu.png', 164, '2024-11-26 00:00:00'),
(30, 'Tiếng Việt 2 - Tập 1', 'Bộ GD&ĐT', 3, 39000.00, 5, 'Bài học nâng cao về đọc hiểu và luyện viết.', '30.jpg', 'https://www.robins.vn/wp-content/uploads/2025/09/erttSUOt8XHyhaVvU5egEzABYjMlJB2o.jpg.jpg', 142, '2024-11-26 00:00:00'),
(31, 'Tiếng Việt 2 - Tập 2', 'Bộ GD&ĐT', 3, 39000.00, 5, 'Phát triển kỹ năng viết đoạn và đọc truyện ngắn cho học sinh.', '31.jpg', 'https://cdn2-retail-images.kiotviet.vn/nhasachtinnghia/d0c441a449794492800057e4f12ed556.jpg', 132, '2024-11-26 00:00:00'),
(32, 'Tự Nhiên & Xã Hội 2', 'Bộ GD&ĐT', 3, 33000.00, 5, 'Giúp học sinh tìm hiểu cơ thể người, thực vật, động vật.', '32.png', 'https://ebdbook.vn/upload/sgk/lop2/tu-nhien-va-xa-hoi-2-bo-sach-chan-troi-sang-tao/4-compressed.jpg?v=1.0.1', 127, '2024-11-26 00:00:00'),
(33, 'Âm Nhạc 2', 'Bộ GD&ĐT', 3, 30000.00, 5, 'Học hát, nhịp điệu và nhận biết nhạc cụ.', '33.jpg', 'https://thuvienkiengiang.vn/wp-content/uploads/2025/12/am-nhac-2-canh-dieu.jpg', 72, '2024-11-26 00:00:00'),
(34, 'Mỹ Thuật 2', 'Bộ GD&ĐT', 3, 30000.00, 5, 'Vẽ tranh đề tài đơn giản.', '34.jpg', 'https://sachdientu.sachthietbigiaoduc.vn/upload/bia-web5.jpg?v=1.0.4', 70, '2024-11-26 00:00:00'),
(35, 'Tin Học 2', 'Bộ GD&ĐT', 3, 35000.00, 5, 'SGK Tin học cơ bản cho học sinh lớp 2.', '35.jpg', 'https://khangphuc.vn/storage/617f5f573030311ff4a5928a/images/EVHTH2/TH-05-02.jpg', 77, '2024-11-26 00:00:00'),
(36, 'Đạo Đức 2', 'Bộ GD&ĐT', 3, 33000.00, 5, 'Giáo dục kỹ năng sống và ứng xử.', '36.jpg', 'https://thuvienkiengiang.vn/wp-content/uploads/2025/12/dao-duc-2-canh-dieu.jpg', 74, '2024-11-26 00:00:00'),
(37, 'Toán 3', 'Bộ GD&ĐT', 3, 38000.00, 5, 'Bao gồm phép nhân, chia và bài toán có lời văn.', '37.jpg', 'https://giaokhoaonline.com/wp-content/uploads/2024/06/Toan-3-Tap-1-Bo-Ket-Noi.jpg', 140, '2024-11-26 00:00:00'),
(38, 'Tiếng Việt 3 - Tập 1', 'Bộ GD&ĐT', 3, 40000.00, 5, 'Phát triển năng lực đọc hiểu và diễn đạt.', '38.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/tieng_viet_3___tap_1_canh_dieu_2023/2024_05_14_11_54_13_1-390x510.jpg', 135, '2024-11-26 00:00:00'),
(39, 'Tiếng Việt 3 - Tập 2', 'Bộ GD&ĐT', 3, 40000.00, 5, 'Luyện viết đoạn văn miêu tả và kể chuyện.', '39.jpg', 'https://giaokhoaonline.com/wp-content/uploads/2024/06/Tieng-Viet-3-Tap-2-Bo-Ket-Noi.jpg', 128, '2024-11-26 00:00:00'),
(40, 'Khoa Học 3', 'Bộ GD&ĐT', 3, 34000.00, 5, 'Khám phá hiện tượng tự nhiên và đời sống hằng ngày.', '40.png', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/tu_nhien_va_xa_hoi_3_ket_noi_chuan/2025_09_11_13_57_13_1-390x510.jpg', 120, '2024-11-26 00:00:00'),
(41, 'Âm Nhạc 3', 'Bộ GD&ĐT', 3, 31000.00, 5, 'Học hát và nhạc lý cơ bản.', '41.jpg', 'https://nxb.hcmue.edu.vn/wp-content/uploads/2022/07/1-7-scaled-1.jpg', 65, '2024-11-26 00:00:00'),
(42, 'Mỹ Thuật 3', 'Bộ GD&ĐT', 3, 31000.00, 5, 'Tập vẽ tranh phong cảnh và con vật.', '42.jpg', 'https://congtysachbackan.vn/wp-content/uploads/2024/11/Mi-Thuat-3.jpg', 60, '2024-11-26 00:00:00'),
(43, 'Tin Học 3', 'Bộ GD&ĐT', 3, 36000.00, 5, 'Làm quen phần mềm vẽ và gõ phím.', '43.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040307033_1.jpg', 70, '2024-11-26 00:00:00'),
(44, 'Đạo Đức 3', 'Bộ GD&ĐT', 3, 33000.00, 5, 'Kỹ năng sống và ứng xử hằng ngày.', '44.png', 'https://sachcanhdieu.vn/wp-content/uploads/2025/04/Bia-STKTY-Bai-tap-thuc-hanh-Dao-duc-3.png', 60, '2024-11-26 00:00:00'),
(45, 'Toán 4', 'Bộ GD&ĐT', 3, 42000.00, 5, 'Phân số, phép tính với số lớn và toán hình học cơ bản.', '45.jpg', 'https://cdn.dlib.vn/dlibk12/tieuhoc/tvstieuhoc/resources/images/2023/20231127/origin/71966244.webp', 125, '2024-11-26 00:00:00'),
(46, 'Tiếng Việt 4 - Tập 1', 'Bộ GD&ĐT', 3, 42000.00, 5, 'Luyện đọc hiểu nâng cao và kỹ năng viết đoạn văn.', '46.png', 'https://stbcuulong.edu.vn/wp-content/uploads/2023/06/L4_KNTT_TiengViet4.1-scaled.jpg', 118, '2024-11-26 00:00:00'),
(47, 'Tiếng Việt 4 - Tập 2', 'Bộ GD&ĐT', 3, 42000.00, 5, 'Học sinh rèn luyện văn miêu tả, kể chuyện.', '47.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/tieng_viet_4_2_cd_n3/2023_07_01_10_13_20_1-390x510.jpg', 110, '2024-11-26 00:00:00'),
(48, 'Khoa Học 4', 'Bộ GD&ĐT', 3, 36000.00, 5, 'Nghiên cứu cơ thể người, thực vật, động vật và môi trường.', '48.jpg', 'https://bestflashcard.com/images/vocabulary/english/sgk-tieng-anh-4-global-success-unit-07-lesson-0/science.PNG', 100, '2024-11-26 00:00:00'),
(49, 'Âm Nhạc 4', 'Bộ GD&ĐT', 3, 32000.00, 5, 'Nhạc lý đơn giản và bài hát thiếu nhi.', '49.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040351265.jpg', 68, '2024-11-26 00:00:00'),
(50, 'Mỹ Thuật 4', 'Bộ GD&ĐT', 3, 32000.00, 5, 'Vẽ tranh nâng cao và xem tranh minh hoạ.', '50.png', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040350305.jpg', 65, '2024-11-26 00:00:00'),
(51, 'Tin Học 4', 'Bộ GD&ĐT', 3, 38000.00, 5, 'Học Word, Paint đơn giản.', '51.png', 'https://stbcuulong.edu.vn/wp-content/uploads/2023/06/L4_CTST_Tinhoc.jpg', 75, '2024-11-26 00:00:00'),
(52, 'Đạo Đức 4', 'Bộ GD&ĐT', 3, 34000.00, 5, 'Học về tinh thần tự giác và trách nhiệm.', '52.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/dao_duc_4_ct_n3/2023_07_12_09_36_46_1-390x510.jpg', 60, '2024-11-26 00:00:00'),
(53, 'Toán 5', 'Bộ GD&ĐT', 3, 45000.00, 5, 'Số thập phân, đo lường, tỉ số, phần trăm.', '53.png', 'https://pos.nvncdn.com/5002d2-93534/ps/20240731_4zhQ9R0QLw.png?v=1722433266', 140, '2024-11-26 00:00:00'),
(54, 'Tiếng Việt 5 - Tập 1', 'Bộ GD&ĐT', 3, 45000.00, 5, 'Nâng cao kỹ năng đọc hiểu và viết văn.', '54.jpg', 'https://sobee.vn/site/wp-content/uploads/2024/12/VBT-Tieng-Viet-5-tap-1-CTST-scaled.jpg', 135, '2024-11-26 00:00:00'),
(55, 'Tiếng Việt 5 - Tập 2', 'Bộ GD&ĐT', 3, 45000.00, 5, 'Luyện viết văn nghị luận đơn giản.', '55.png', 'https://sobee.vn/site/wp-content/uploads/2024/04/Tieng-Viet-5-tap-hai-Chan-troi-sang-tao.png', 130, '2024-11-26 00:00:00'),
(56, 'Khoa Học 5', 'Bộ GD&ĐT', 3, 38000.00, 5, 'SGK giúp học sinh tìm hiểu khoa học tự nhiên trong đời sống.', '56.jpg', 'https://giaokhoaonline.com/wp-content/uploads/2024/06/e2edc000-f164-b6a8-9f1a-8b4f88933532-202405091502570279-scaled.jpg', 125, '2024-11-26 00:00:00'),
(57, 'Âm Nhạc 5', 'Bộ GD&ĐT', 3, 33000.00, 5, 'Luyện hát và nhạc lý nâng cao.', '57.png', 'https://stbcuulong.edu.vn/wp-content/uploads/2024/10/L5_Amnhac-scaled.jpg', 55, '2024-11-26 00:00:00'),
(58, 'Mỹ Thuật 5', 'Bộ GD&ĐT', 3, 33000.00, 5, 'Học vẽ bố cục tranh hoàn chỉnh.', '58.jpg', 'https://nhasachtandinh.com/wp-content/uploads/2024/08/MT-5-Ban-2.png', 50, '2024-11-26 00:00:00'),
(59, 'Tin Học 5', 'Bộ GD&ĐT', 3, 40000.00, 5, 'Học Word, PowerPoint cơ bản.', '59.jpg', 'https://giaokhoaonline.com/wp-content/uploads/2025/01/Tin-hoc-lop-5-Chan-Troi-Sang-Tao.jpg', 70, '2024-11-26 00:00:00'),
(60, 'Đạo Đức 5', 'Bộ GD&ĐT', 3, 35000.00, 5, 'Bài học đạo đức và kỹ năng sống cho học sinh cuối cấp tiểu học.', '60.png', 'https://bizweb.dktcdn.net/thumb/grande/100/567/082/products/bia-lop-5-kntt3-e66eb839-ad7e-455e-900b-2ff3149b5e8d.jpg?v=1747319153953', 58, '2024-11-26 00:00:00'),
(61, 'Toán Học 6 - Cơ Bả', 'Nguyễn Văn A', 3, 55000.00, 10, 'Bài học cơ bản về số học, đại số và hình học lớp 6.', '61.jpg', 'https://images.nxbdsh.vn/Picture/2024/10/7/image-20241007144001586.jpg', 120, '2024-11-26 00:00:00'),
(62, 'Ngữ Văn 6', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Giới thiệu văn bản, tiếng Việt và tập làm văn.', '62.png', 'https://giaokhoaonline.com/wp-content/uploads/2024/06/Ngu-Van-6-Tap-1-Bo-Chan-Troi.jpg', 110, '2024-11-26 00:00:00'),
(63, 'Khoa Học Tự Nhiên 6', 'Bộ GD&ĐT', 3, 50000.00, 5, 'Kết hợp kiến thức Vật lý - Hóa học - Sinh học.', '63.jpg', 'https://img.loigiaihay.com/picture/2024/0118/img-5596_1.jpeg', 100, '2024-11-26 00:00:00'),
(64, 'Lịch Sử & Địa Lý 6', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Trình bày cơ bản về lịch sử cổ đại và kiến thức địa lý đại cương.', '64.png', 'https://vn-live-01.slatic.net/p/c64f31c20fcdbdc41eeeeffcb5ff2c4d.jpg', 90, '2024-11-26 00:00:00'),
(65, 'Sinh Học 6', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Thế giới thực vật, vi sinh vật và môi trường.', '65.jpg', 'https://product.hstatic.net/1000175596/product/tranh_sinh_hoc_lop_6_28tranh_nhua_1291ca9875604eab9f7d51d9cad7eae6_grande.jpg', 80, '2024-11-26 00:00:00'),
(66, 'Tin Học 6', 'Bộ GD&ĐT', 3, 46000.00, 5, 'Máy tính và thuật toán cơ bản.', '66.jpg', 'https://sobee.vn/site/wp-content/uploads/2021/03/Tin-H%E1%BB%8Dc-6-SGK-K%E1%BA%BFt-n%E1%BB%91i-tri-th%E1%BB%A9c-v%E1%BB%9Bi-cu%E1%BB%99c-s%E1%BB%91ng-scaled.jpg', 75, '2024-11-26 00:00:00'),
(67, 'GDCD 6', 'Bộ GD&ĐT', 3, 45000.00, 5, 'Hành vi, đạo đức và pháp luật cơ bản.', '67.jpg', 'https://sachcanhdieu.vn/wp-content/uploads/2024/04/Bia-GDCD-6-OK-1.jpg', 72, '2024-11-26 00:00:00'),
(68, 'Công Nghệ 6', 'Bộ GD&ĐT', 3, 47000.00, 5, 'Kĩ thuật nhà ở và trồng trọt đơn giản.', '68.jpg', 'https://images.nxbdsh.vn/Picture/2024/10/7/image-20241007003525646.jpg', 70, '2024-11-26 00:00:00'),
(69, 'Toán 7', 'Bộ GD&ĐT', 3, 56000.00, 5, 'Số hữu tỉ, đại số và hình học cơ bản.', '69.png', 'https://thcs.toanmath.com/wp-content/uploads/2022/08/sach-giao-khoa-toan-7-tap-1-ket-noi-tri-thuc-voi-cuoc-song.png', 115, '2024-11-26 00:00:00'),
(70, 'Ngữ Văn 7', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Văn bản nghị luận, thơ ca Việt Nam.', '70.jpg', 'https://online.pubhtml5.com/iqiw/lrgu/files/large/1.jpg?1652866935', 108, '2024-11-26 00:00:00'),
(71, 'Khoa Học Tự Nhiên 7', 'Bộ GD&ĐT', 3, 51000.00, 5, 'Giới thiệu hóa học, sinh học và vật lý nâng cao.', '71.jpg', 'https://img.loigiaihay.com/picture/2024/0122/2024-01-22-140853.png', 100, '2024-11-26 00:00:00'),
(72, 'Lịch Sử & Địa Lý 7', 'Bộ GD&ĐT', 3, 49000.00, 5, 'Lịch sử trung đại và kiến thức địa lý châu lục.', '72.jpg', 'https://vietjack.com/sach-moi/images/sach-lich-su-va-dia-li-lop-7-chan-troi-sang-tao-sua2024-1.PNG', 95, '2024-11-26 00:00:00'),
(73, 'Sinh Học 7', 'Bộ GD&ĐT', 3, 49000.00, 5, 'Thế giới động vật và sinh thái học cơ bản.', '73.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Sinh-hoc-7.jpg', 78, '2024-11-26 00:00:00'),
(74, 'Tin Học 7', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Lập trình Scratch, thuật toán đơn giản.', '74.jpg', 'https://phuongnam.edu.vn/storage/media/202502/b58158ab-07dd-4579-9a1a-cc1d623be692.jpg', 73, '2024-11-26 00:00:00'),
(75, 'GDCD 7', 'Bộ GD&ĐT', 3, 46000.00, 5, 'Quyền và nghĩa vụ của công dân trẻ.', '75.jpg', 'https://huph.hueuni.edu.vn/wp-content/uploads/2022/03/0f540b15d1bc1ee247ad1.jpg', 70, '2024-11-26 00:00:00'),
(76, 'Công Nghệ 7', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Nông nghiệp và chăn nuôi cơ bản.', '76.jpg', 'https://sobee.vn/site/wp-content/uploads/2024/04/Cong-nghe-7-Ket-noi-tri-thuc-voi-cuoc-song.png', 68, '2024-11-26 00:00:00'),
(77, 'Toán 8', 'Bộ GD&ĐT', 3, 58000.00, 5, 'Đại số, phương trình, bất phương trình và hình học.', '77.jpg', 'https://stbcuulong.edu.vn/wp-content/uploads/2023/06/L8_CTST_Toan8.1.jpg', 110, '2024-11-26 00:00:00'),
(78, 'Ngữ Văn 8', 'Bộ GD&ĐT', 3, 53000.00, 5, 'Tập trung vào văn bản thuyết minh và nghị luận.', '78.png', 'https://vietjack.com/sach-moi/images/sach-ngu-van-lop-8-chan-troi-sang-tao.PNG', 100, '2024-11-26 00:00:00'),
(79, 'Vật Lý 8', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Cơ học và nhiệt học cơ bản.', '79.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Vat-li-8.jpg', 95, '2024-11-26 00:00:00'),
(80, 'Hóa Học 8', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Khái niệm nguyên tử, phân tử và các phản ứng hóa học.', '80.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Hoa-hoc-8.jpg', 90, '2024-11-26 00:00:00'),
(81, 'Sinh Học 8', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Cơ thể người và sinh lý học.', '81.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Sinh-hoc-8.jpg', 85, '2024-11-26 00:00:00'),
(82, 'Tin Học 8', 'Bộ GD&ĐT', 3, 50000.00, 5, 'Ngôn ngữ lập trình Pascal cơ bản.', '82.jpg', 'https://sobee.vn/site/wp-content/uploads/2024/04/Tin-hoc-8-Ket-noi-tri-thuc-voi-cuoc-song.png', 82, '2024-11-26 00:00:00'),
(83, 'GDCD 8', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Quan hệ xã hội và chuẩn mực đạo đức.', '83.png', 'https://sobee.vn/site/wp-content/uploads/2023/06/Giao-duc-cong-dan-8-Chan-troi-sang-tao.png', 75, '2024-11-26 00:00:00'),
(84, 'Công Nghệ 8', 'Bộ GD&ĐT', 3, 50000.00, 5, 'Kỹ thuật điện và cơ khí cơ bản.', '84.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040350367.jpg', 70, '2024-11-26 00:00:00'),
(85, 'Toán 9', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Hàm số bậc nhất, hệ phương trình, hình học không gian.', '85.jpg', 'https://media.metaisach.com/2025/04/sach-giao-khoa-toan-lop-9-74db3d63.jpeg', 105, '2024-11-26 00:00:00'),
(86, 'Ngữ Văn 9', 'Bộ GD&ĐT', 3, 55000.00, 5, 'Văn bản hiện đại, thơ, nghị luận xã hội.', '86.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Ngu-Van-9-Tap-1.jpg', 98, '2024-11-26 00:00:00'),
(87, 'Vật Lý 9', 'Bộ GD&ĐT', 3, 54000.00, 5, 'Điện học và quang học.', '87.jpg', 'https://media.metaisach.com/2025/04/sach-giao-khoa-vat-li-lop-9-e0c6da3c.jpeg', 92, '2024-11-26 00:00:00'),
(88, 'Hóa Học 9', 'Bộ GD&ĐT', 3, 54000.00, 5, 'Hóa học vô cơ và hữu cơ cơ bản.', '88.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Hoa-hoc-9.jpg', 90, '2024-11-26 00:00:00'),
(89, 'Sinh Học 9', 'Bộ GD&ĐT', 3, 54000.00, 5, 'Di truyền học, sinh học tế bào.', '89.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Sinh-hoc-9.jpg', 90, '2024-11-26 00:00:00'),
(90, 'Tin Học 9', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Lập trình Python cơ bản.', '90.jpg', 'https://giaokhoaonline.com/wp-content/uploads/2024/08/tin-hoc-9.jpg', 85, '2024-11-26 00:00:00'),
(91, 'GDCD 9', 'Bộ GD&ĐT', 3, 50000.00, 5, 'Pháp luật và trách nhiệm công dân.', '91.jpg', 'https://data.ihoc.vn/ihoc-bucket/2023/07/sach-giao-khoa-giao-duc-cong-dan-lop-9-nxb-giao-duc.jpg', 80, '2024-11-26 00:00:00'),
(92, 'Công Nghệ 9', 'Bộ GD&ĐT', 3, 53000.00, 5, 'Kỹ thuật điện nâng cao và hướng nghiệp.', '92.jpg', 'https://nhasachquangloi.vn/pub/media/catalog/product/cache/3bd4b739bad1f096e12e3a82b40e551a/t/k/tk-l9-spc-050.jpg', 78, '2024-11-26 00:00:00'),
(93, 'Toán 10', 'Bộ GD&ĐT', 3, 65000.00, 5, 'Đại số, bất đẳng thức, hình học vectơ.', '93.jpg', 'https://media.metaisach.com/2025/04/sach-giao-khoa-dai-so-lop-10-f34b6bf0.jpeg', 120, '2024-11-26 00:00:00'),
(94, 'Ngữ Văn 10', 'Bộ GD&ĐT', 3, 63000.00, 5, 'Văn học trung đại và hiện đại.', '94.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Ngu-Van-10-Tap-1-1.jpg', 110, '2024-11-26 00:00:00'),
(95, 'Vật Lý 10', 'Bộ GD&ĐT', 3, 62000.00, 5, 'Động lực học, công – năng lượng.', '95.jpg', 'https://thuvienvatly.com/home/images/download_thumb/1PAWkMteydy2rR7UEJFmuLL4KXwwj1Wer.jpg', 100, '2024-11-26 00:00:00'),
(96, 'Hóa Học 10', 'Bộ GD&ĐT', 3, 62000.00, 5, 'Cấu tạo chất, bảng tuần hoàn.', '96.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Hoa-hoc-10-1.jpg', 95, '2024-11-26 00:00:00'),
(97, 'Sinh Học 10', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Sinh học tế bào và vi sinh vật.', '97.jpg', 'https://file2.hanoi.edu.vn/uploadfolder/hnedu/thptxuanthuy/uploadimages/2020_5_image/sach-giao-khoa-sinh-hoc-lop-10_19052020.jpg', 100, '2024-11-26 00:00:00'),
(98, 'Tin Học 10', 'Bộ GD&ĐT', 3, 58000.00, 5, 'Tổng quan tin học và thuật toán.', '98.jpg', 'https://thi247.com/wp-content/uploads/2021/09/sach-giao-khoa-tin-hoc-10.png', 90, '2024-11-26 00:00:00'),
(99, 'GDCD 10', 'Bộ GD&ĐT', 3, 56000.00, 5, 'Công dân và pháp luật.', '99.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Giao-duc-cong-dan-10-1.jpg', 85, '2024-11-26 00:00:00'),
(100, 'Công Nghệ 10', 'Bộ GD&ĐT', 3, 58000.00, 5, 'Sản xuất nông – lâm nghiệp cơ bản.', '100.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/cong-nghe-10-1.jpg', 82, '2024-11-26 00:00:00'),
(101, 'Toán 10 - Nâng Cao', 'Trần Minh Khoa', 3, 68000.00, 5, 'Bài tập và lý thuyết nâng cao dành cho học sinh khá giỏi.', '101.jpg', 'https://cdn0166.cdn4s.com/media/giao%20duc%20da%20nang/1%20nang%20cao%20va%20pt%20toan%2010-1.jpg', 120, '2024-11-26 00:00:00'),
(102, 'Toán 10 - Bài Tập', 'Lê Anh Tuấ', 3, 52000.00, 5, 'Tuyển tập bài tập từ cơ bản đến nâng cao.', '102.jpg', 'https://file.toan7.edu.vn/img-post/sach-bai-tap-toan-10-tap-1-chan-troi-sang-tao.jpg', 100, '2024-11-26 00:00:00'),
(103, 'Ngữ Văn 10 - Chuyên Đề', 'Nguyễn Trọng Hoà', 3, 59000.00, 5, 'Phân tích tác phẩm và luyện tập nghị luận văn học.', '103.png', 'https://huph.hueuni.edu.vn/wp-content/uploads/2022/11/Bia-Ngu-van-10-chuyen-de-Taibanlan1-1.jpg', 95, '2024-11-26 00:00:00'),
(104, 'Vật Lý 10 - Bài Tập Nâng Cao', 'Huỳnh Tấn Phát', 3, 65000.00, 5, 'Bài tập vận dụng và vận dụng cao.', '104.webp', 'https://cdn0166.cdn4s.com/media/tien%20tho/1-vat-li-nang-cao-10.jpg', 110, '2024-11-26 00:00:00'),
(105, 'Hóa Học 10 - Chuyên Đề', 'Phạm Ngọc Hà', 3, 62000.00, 5, 'Ôn tập chương Electron, Liên kết hóa học và bảng tuần hoàn.', '105.png', 'https://online.pubhtml5.com/yxzb/nakq/files/large/1.jpg', 90, '2024-11-26 00:00:00'),
(106, 'Sinh Học 10 - Bài Tập', 'Bộ GD&ĐT', 3, 56000.00, 5, 'Tổng hợp bài tập tế bào học.', '106.png', 'https://product.hstatic.net/200000886883/product/thiet_ke_chua_co_ten__57__b1d24b75298049e8a19bb39184a173d9_master.png', 80, '2024-11-26 00:00:00'),
(107, 'Lịch Sử 10 - Học Tốt', 'Nguyễn Quốc Vương', 3, 50000.00, 5, 'Hướng dẫn ôn tập lịch sử thế giới cổ đại và trung đại.', '107.webp', 'https://sachgiaokhoa.vn/pub/media/catalog/product/cache/3bd4b739bad1f096e12e3a82b40e551a/s/g/sgk-l10-gd-614.jpg', 85, '2024-11-26 00:00:00'),
(108, 'Địa Lý 10 - Chuyên Đề', 'Bùi Thị Mai', 3, 54000.00, 5, 'Ôn tập bản đồ, dân cư và tự nhiên thế giới.', '108.png', 'https://images.nxbdsh.vn/Picture/2024/10/8/image-20241008105111355.jpg', 75, '2024-11-26 00:00:00'),
(109, 'Tin Học 10 - Tin Học Ứng Dụng', 'Hoàng Văn Minh', 3, 58000.00, 5, 'Thực hành Word, Excel và thuật toán.', '109.png', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040310903_1.jpg', 90, '2024-11-26 00:00:00'),
(110, 'GDCD 10 - Bài Tập', 'Bộ GD&ĐT', 3, 48000.00, 5, 'Tổng hợp câu hỏi đạo đức và pháp luật cơ bản.', '110.jpg', 'https://book.sachgiai.com/uploads/book/trac-nghiem-khach-quan-giao-duc-cong-dan-10/trac-nghiem-giao-duc-cong-dan-10-0.jpg', 60, '2024-11-26 00:00:00'),
(111, 'Toán 11', 'Bộ GD&ĐT', 3, 67000.00, 5, 'Giải tích, dãy số và hình học không gian.', '111.jpg', 'https://toanmath.com/wp-content/uploads/2016/12/sach-giao-khoa-dai-so-va-giai-tich-11-co-ban.png', 115, '2024-11-26 00:00:00'),
(112, 'Ngữ Văn 11', 'Bộ GD&ĐT', 3, 65000.00, 5, 'Văn học hiện đại và nghị luận.', '112.jpg', 'https://media.metaisach.com/2025/04/sach-giao-khoa-ngu-van-lop-11-ddcce0bc.jpeg', 108, '2024-11-26 00:00:00'),
(113, 'Vật Lý 11', 'Bộ GD&ĐT', 3, 63000.00, 5, 'Điện từ học.', '113.jpg', 'https://media.metaisach.com/2025/04/sach-giao-khoa-vat-li-lop-11-a3d70a37.jpeg', 100, '2024-11-26 00:00:00'),
(114, 'Hóa Học 11', 'Bộ GD&ĐT', 3, 63000.00, 5, 'Hóa học hữu cơ cơ bản.', '114.jpg', 'https://thuvienkiengiang.vn/wp-content/uploads/2025/12/hoa-hoc-11.jpg', 95, '2024-11-26 00:00:00'),
(115, 'Sinh Học 11', 'Bộ GD&ĐT', 3, 62000.00, 5, 'Sinh lý học cơ thể thực vật và động vật.', '115.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Sinh-hoc-11.jpg', 95, '2024-11-26 00:00:00'),
(116, 'Tin Học 11', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Lập trình C++ căn bản.', '116.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Tin-hoc-11.jpg', 90, '2024-11-26 00:00:00'),
(117, 'GDCD 11', 'Bộ GD&ĐT', 3, 58000.00, 5, 'Kinh tế và pháp luật ứng dụng.', '117.jpg', 'https://thi247.com/wp-content/uploads/2021/09/sach-giao-khoa-giao-duc-cong-dan-11.png', 82, '2024-11-26 00:00:00'),
(118, 'Công Nghệ 11', 'Bộ GD&ĐT', 3, 61000.00, 5, 'Công nghệ điện và thiết kế kỹ thuật.', '118.jpg', 'https://thuvienkiengiang.vn/wp-content/uploads/2025/12/cong-nghe-11.jpg', 75, '2024-11-26 00:00:00'),
(119, 'Toán 11 - Chuyên Đề Giải Tích', 'Nguyễn Hữu Điể', 3, 72000.00, 5, 'Chuyên sâu hàm số, giới hạn và đạo hàm.', '119.jpg', 'https://sobee.vn/site/wp-content/uploads/2024/04/Chuyen-de-hoc-tap-Toan-11-Ket-noi-tri-thuc-voi-cuoc-song.png', 130, '2024-11-26 00:00:00'),
(120, 'Toán 11 - Đại Số Nâng Cao', 'Phạm Minh Hoàng', 3, 70000.00, 5, 'Tổ hợp, cấp số và lượng giác nâng cao.', '120.jpg', 'https://sachhoc.com/image/cache/catalog/LuyenThi/Lop10-12/Dai-so-va-giai-tich-11-nang-cao-500x554.jpg', 100, '2024-11-26 00:00:00'),
(121, 'Văn 11 - Tuyển Tập Nghị Luận Xã Hội', 'Nguyễn Thị Thanh', 3, 58000.00, 5, 'Bài mẫu nghị luận & hướng dẫn lập dàn ý.', '121.jpg', 'https://salt.tikicdn.com/cache/w300/ts/product/3a/bd/9c/d6b415a0834c8febb14612bf46e5ff7b.png', 92, '2024-11-26 00:00:00'),
(122, 'Hóa Học 11 - Bài Tập', 'Hoàng Tiến Dũng', 3, 68000.00, 5, 'Bài tập về Ancol, Ete, Hidrocacbon.', '122.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786040349675.jpg', 95, '2024-11-26 00:00:00'),
(123, 'Vật Lý 11 - Chuyên Đề Điện Học', 'Trần Ngọc Anh', 3, 64000.00, 5, 'Ôn tập điện trường, điện thế và mạch điện.', '123.jpg', 'https://sobee.vn/site/wp-content/uploads/2024/04/Chuyen-de-hoc-tap-Vat-li-11-Ket-noi-tri-thuc-voi-cuoc-song.png', 105, '2024-11-26 00:00:00'),
(124, 'Sinh Học 11 - Chuyên Đề', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Chuyên đề sinh lý thực vật và động vật.', '124.jpg', 'https://images.nxbdsh.vn/Picture/2024/10/8/image-20241008154300635.jpg', 90, '2024-11-26 00:00:00'),
(125, 'Lịch Sử 11 - Học Tốt', 'Lê Văn Quang', 3, 52000.00, 5, 'Ôn tập lịch sử Việt Nam và thế giới hiện đại.', '125.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_117225.jpg', 85, '2024-11-26 00:00:00'),
(126, 'Địa Lý 11 - Chuyên Đề Kinh Tế', 'Bùi Mai Trang', 3, 56000.00, 5, 'Kinh tế thế giới và các khu vực địa lý lớn.', '126.jpg', 'https://images.nxbdsh.vn/Picture/2024/10/8/image-20241008153140075.jpg', 78, '2024-11-26 00:00:00'),
(127, 'Tin Học 11 - Lập Trình C++', 'Đỗ Mạnh Tiế', 3, 69000.00, 5, 'C++ cơ bản đến nâng cao cho học sinh.', '127.jpg', 'https://cdn1.fahasa.com/media/catalog/product/4/f/4fddd6ffbe2f432eb70795b521aa84c3_1.jpg', 95, '2024-11-26 00:00:00'),
(128, 'GDCD 11 - Bài Tập', 'Bộ GD&ĐT', 3, 52000.00, 5, 'Câu hỏi trắc nghiệm pháp luật và kinh tế.', '128.jpg', 'https://product.hstatic.net/200000886883/product/23_96a83e88b75244099ceb36430403a6a4.png', 72, '2024-11-26 00:00:00'),
(129, 'Toán 12', 'Bộ GD&ĐT', 3, 70000.00, 5, 'Giải tích, tích phân và ứng dụng.', '129.jpg', 'https://toanmath.com/wp-content/uploads/2016/12/sach-giao-khoa-giai-tich-12-co-ban.png', 130, '2024-11-26 00:00:00'),
(130, 'Ngữ Văn 12', 'Bộ GD&ĐT', 3, 68000.00, 5, 'Văn học Việt Nam hiện đại và luyện thi THPT.', '130.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Ngu-Van-12-Tap-1.jpg', 118, '2024-11-26 00:00:00'),
(131, 'Vật Lý 12', 'Bộ GD&ĐT', 3, 66000.00, 5, 'Sóng cơ, dao động và điện xoay chiều.', '131.jpg', 'https://library.ans.edu.vn/wp-content/uploads/2025/09/2032021.jpg', 105, '2024-11-26 00:00:00'),
(132, 'Hóa Học 12', 'Bộ GD&ĐT', 3, 66000.00, 5, 'Hóa học nâng cao phục vụ kỳ thi THPT.', '132.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Hoa-hoc-12.jpg', 100, '2024-11-26 00:00:00'),
(133, 'Sinh Học 12', 'Bộ GD&ĐT', 3, 65000.00, 5, 'Tiến hóa, di truyền, sinh thái học.', '133.jpg', 'https://thuvienkiengiang.vn/wp-content/uploads/2025/12/sinh-hoc-12.jpg', 110, '2024-11-26 00:00:00'),
(134, 'Tin Học 12', 'Bộ GD&ĐT', 3, 63000.00, 5, 'Lập trình Python nâng cao và cơ sở dữ liệu.', '134.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Tin-hoc-12.jpg', 100, '2024-11-26 00:00:00'),
(135, 'GDCD 12', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Công dân với Nhà nước, xã hội và pháp luật.', '135.jpg', 'https://hieusach24h.com/wp-content/uploads/2021/09/Giao-duc-cong-dan-12.jpg', 95, '2024-11-26 00:00:00'),
(136, 'Công Nghệ 12', 'Bộ GD&ĐT', 3, 64000.00, 5, 'Kỹ thuật cơ khí, điện và hướng nghiệp.', '136.jpg', 'https://nhasachtquangloi.vn/pub/media/catalog/product/cache/3bd4b739bad1f096e12e3a82b40e551a/s/g/sgk-l12-gd-309.jpg', 85, '2024-11-26 00:00:00'),
(137, 'Toán 12 - Ôn Thi THPT Quốc Gia', 'Lê Hồng Đức', 3, 85000.00, 5, '500+ bài tập cực hay và các dạng toán trọng tâm.', '137.jpg', 'https://salt.tikicdn.com/ts/product/66/d4/83/4d961fd85d9cb98537a7188454d82059.jpg', 180, '2024-11-26 00:00:00'),
(138, 'Toán 12 - Bài Tập Nâng Cao', 'Nguyễn Trung Kiê', 3, 78000.00, 5, 'Nâng cao giải tích và hình học không gian.', '138.jpg', 'https://salt.tikicdn.com/cache/w300/ts/product/eb/a1/61/3f1afeb5b71ee3d3efb49ec2c2280c8c.png', 160, '2024-11-26 00:00:00'),
(139, 'Ngữ Văn 12 - Tuyển Tập Nghị Luận Văn Học', 'Nguyễn Thị Hạnh', 3, 68000.00, 5, 'Phân tích 25 tác phẩm trọng tâm.', '139.jpg', 'https://salt.tikicdn.com/cache/w300/ts/product/3a/bd/9c/d6b415a0834c8febb14612bf46e5ff7b.png', 140, '2024-11-26 00:00:00'),
(140, 'Vật Lý 12 - Chuyên Đề Luyện Thi', 'Ngô Quốc Tuấ', 3, 75000.00, 5, 'Luyện thi điện xoay chiều và lượng tử ánh sáng.', '140.jpg', 'https://vietjack.com/sach-moi/images/chuyen-de-vat-li-lop-12-ket-noi-tri-thuc-1.PNG', 150, '2024-11-26 00:00:00'),
(141, 'Hóa Học 12 - Ôn Thi', 'Phạm Tuấn Anh', 3, 72000.00, 5, 'Hóa vô cơ, hữu cơ và bài tập tổng hợp.', '141.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_194275.jpg', 135, '2024-11-26 00:00:00'),
(142, 'Sinh Học 12 - Chuyên Đề Di Truyề', 'Bộ GD&ĐT', 3, 68000.00, 5, 'Hệ thống bài tập di truyền và biến dị.', '142.jpg', 'https://cdn.trungtamsach.vn/static_cdn/image_items/wp-content/uploads/2024/04/Bia-CDHT-Sinh-hoc-12-1-512x719.jpg', 125, '2024-11-26 00:00:00'),
(143, 'Lịch Sử 12 - Học Tốt', 'Trần Mậu Thâ', 3, 56000.00, 5, 'Ôn lịch sử Việt Nam từ 1919–2000.', '143.jpg', 'https://product.hstatic.net/200000981675/product/upload_de7fc2faad804938983d31fc88036970.jpg', 100, '2024-11-26 00:00:00'),
(144, 'Địa Lý 12 - Ôn Thi', 'Bùi Thị Hoa', 3, 58000.00, 5, 'Ôn tập toàn bộ kiến thức và bài tập bản đồ.', '144.jpg', 'https://cdn0166.cdn4s.com/media/tien%20tho/boi-duong-hsg-dia-12.jpg', 95, '2024-11-26 00:00:00'),
(145, 'Tin Học 12 - Lập Trình Python Nâng Cao', 'Hoàng Long', 3, 74000.00, 5, 'Python, thuật toán và cấu trúc dữ liệu.', '145.jpg', 'https://img.loigiaihay.com/picture/2024/1016/screenshot-11_1.png', 130, '2024-11-26 00:00:00'),
(146, 'GDCD 12 - Ôn Thi THPT', 'Bộ GD&ĐT', 3, 60000.00, 5, 'Bộ câu hỏi trắc nghiệm và lý thuyết.', '146.jpg', 'https://product.hstatic.net/200000343833/product/screenshot_2024-07-09_154228_a410b62192614b2ca87ebf7af2ad2d94_master.png', 110, '2024-11-26 00:00:00'),
(147, 'Dế Mèn Phiêu Lưu Ký', 'Tô Hoài', 2, 95000.00, 10, 'Cuộc phiêu lưu đầy ý nghĩa của chú dế mèn gan dạ và tinh nghịch.', '147.jpg', 'https://nhasachmienphi.com/images/thumbnail/nhasachmienphi-de-men-phieu-luu-ky.jpg', 4200, '2024-02-01 00:00:00'),
(148, 'Toto Chan Bên Cửa Sổ', 'Tetsuko Kuroyanagi', 2, 120000.00, 15, 'Câu chuyện giáo dục nhân văn về cô bé Toto Chan tại ngôi trường Tomoe.', '148.jpg', 'https://metiz.vn/media/poster_film/totto-chan_-_teaser_poster_kt_facebook_-_dkkc_31.05.2024.jpg', 3900, '2024-02-02 00:00:00'),
(149, 'Harry Potter và Hòn Đá Phù Thủy', 'J.K. Rowling', 2, 150000.00, 20, 'Phần đầu tiên của series Harry Potter nổi tiếng toàn cầu.', '149.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSKKFnflLeb9sb7UqxlKXOJbh_KHBccY-Uc1w&s', 8505, '2024-02-03 00:00:00'),
(150, 'Hoàng Tử Bé', 'Antoine de Saint-Exupéry', 2, 80000.00, 5, 'Một trong những tác phẩm thiếu nhi kinh điển giàu triết lý.', '150.jpg', 'https://product.hstatic.net/200000343865/product/hoang-tu-be---tb-2022_f0f2f9b813c246c4878e7e685f683d50_5b46a794d64c4996a6695f6e9e8d3213.jpg', 7600, '2024-02-04 00:00:00'),
(151, 'Alice Ở Xứ Sở Thần Tiê', 'Lewis Carroll', 2, 100000.00, 12, 'Hành trình kỳ ảo của Alice trong thế giới phép thuật.', '151.jpg', 'https://www.nxbtre.com.vn/Images/Book/nxbtre_full_28402016_104013.jpg', 5400, '2024-02-05 00:00:00'),
(152, 'Cây Táo Yêu Thương', 'Shel Silverstei', 2, 90000.00, 10, 'Câu chuyện đầy cảm động về tình yêu vô điều kiện.', '152.jpg', 'https://minhkhai.com.vn/hinhlon/8935036669513.JPG', 6801, '2024-02-06 00:00:00'),
(153, 'Chú Bé Rồng', 'Nguyễn Khánh Trung', 2, 85000.00, 8, 'Hành trình khám phá sức mạnh và lòng dũng cảm.', '153.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_217138.jpg', 2100, '2024-02-07 00:00:00'),
(154, 'Nhật Ký Chú Bé Nhút Nhát', 'Jeff Kinney', 2, 110000.00, 10, 'Hài hước và gần gũi, kể về cuộc sống thường ngày của Greg Heffley.', '154.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9CQz5USzD0769vIdEZyhDBFwVYtAGN4jYog&s', 7302, '2024-02-08 00:00:00'),
(155, 'Doraemon Từ Điển Bí mật', 'Fujiko F. Fujio', 2, 70000.00, 5, 'Bộ truyện tranh nổi tiếng về chú mèo máy tương lai.', '155.jpg', 'https://bookbuy.vn/Res/Images/Product/doraemon-tu-dien-bi-mat_31564_1.jpg', 9204, '2024-02-09 00:00:00'),
(156, 'Cậu Bé Mũ Rơm', 'Nguyễn Nhật Ánh', 2, 85000.00, 5, 'Câu chuyện trong trẻo về ký ức tuổi thơ.', '156.jpg', 'https://nxbhcm.com.vn/Image/Biasach/342873_p88126mmurom.jpg', 4100, '2024-02-10 00:00:00'),
(157, 'Mười Vạn Câu Hỏi Vì Sao', 'Nhiều tác giả', 2, 160000.00, 18, 'Kho kiến thức giúp trẻ giải đáp các câu hỏi về thế giới.', '157.jpg', 'https://cdn1.fahasa.com/media/catalog/product/m/u/muoivancauhoi2.jpg', 6803, '2024-02-11 00:00:00'),
(158, 'Ngôi Nhà Nhỏ Trên Thảo Nguyê', 'Laura Ingalls Wilder', 2, 115000.00, 15, 'Cuộc sống gia đình ấm áp ở miền thảo nguyên nước Mỹ.', '158.jpg', 'https://product.hstatic.net/200000979221/product/ngoi-nha-nho-tren-thao-nguyen-tap-8_22721209cc5c4491b874b0f87527ecb5_grande.jpeg', 2400, '2024-02-12 00:00:00'),
(159, 'Khu Vườn Bí Mật', 'Frances Hodgson Burnett', 2, 105000.00, 12, 'Câu chuyện chữa lành đầy yêu thương về tình bạn và thiên nhiên.', '159.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/363/455/products/1-32f0e785-1d30-4911-b09d-f8e3c98bad46.png?v=1759391334623', 3000, '2024-02-13 00:00:00'),
(160, 'Charlie và Nhà Máy Sô-Cô-La', 'Roald Dahl', 2, 125000.00, 20, 'Hành trình kỳ diệu của Charlie trong nhà máy sô-cô-la.', '160.jpg', 'https://product.hstatic.net/200000343865/product/roald-dahl---charlie-va-nha-may-socola_a4875bf2d6c440d4bd1024288fb6ada2_master.jpg', 5100, '2024-02-14 00:00:00'),
(161, 'Cô Bé Quàng Khăn Đỏ', 'Grimm Brothers', 2, 65000.00, 10, 'Truyện cổ tích kinh điển dành cho trẻ nhỏ.', '161.jpg', 'https://bizweb.dktcdn.net/100/418/570/products/8935210226969-0.jpg?v=1614915890400', 5700, '2024-02-15 00:00:00'),
(162, 'Nàng Bạch Tuyết Và Bảy Chú Lù', 'Grimm Brothers', 2, 65000.00, 10, 'Câu chuyện cổ tích nổi tiếng về lòng tốt và sự dũng cảm.', '162.jpg', 'https://bizweb.dktcdn.net/100/567/082/products/8935210237163.jpg?v=1747217382613', 6301, '2024-02-16 00:00:00'),
(163, 'Bí Kíp Trở Thành Thiên Tài', 'Nhiều tác giả', 2, 140000.00, 18, 'Bộ sách kỹ năng giúp trẻ phát triển tư duy và sáng tạo.', '163.jpg', 'https://www.netabooks.vn/Data/Sites/1/Product/28324/tu-ao-nhiem-mau-bi-kip-tro-thanh-co-nang-thoi-trang-trong-nhay-mat.jpg', 2800, '2024-02-17 00:00:00'),
(164, 'Doraemon - Vương Quốc Mèo', 'Tsujimura Mizuki', 2, 100000.00, 12, 'Chuyến phiêu lưu kỳ lạ vào thế giới của những chú mèo.', '164.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOYPEc_PdAzrrmrQDuKhYOr1x8qKTpeCSEAA&s', 3400, '2024-02-18 00:00:00'),
(165, 'Cuộc Phiêu Lưu Của Pinocchio', 'Carlo Collodi', 2, 85000.00, 10, 'Câu chuyện kinh điển về chú bé gỗ thích nói dối.', '165.jpg', 'https://ntthnue.edu.vn/uploads/Images/2020/06/035.jpg', 5902, '2024-02-19 00:00:00'),
(166, 'Cậu Bé Hobbit', 'J.R.R. Tolkie', 2, 140000.00, 15, 'Cuộc phiêu lưu kỳ ảo đưa Bilbo bước vào thế giới thần thoại.', '166.jpg', 'https://www.netabooks.vn/Data/Sites/1/Product/49297/anh-chang-hobbit-tai-ban-2022.jpg', 5100, '2024-02-20 00:00:00'),
(167, 'Cây Cam Ngọt Của Tôi', 'José Mauro de Vasconcelos', 4, 91500.00, 15, 'Với một đứa trẻ, thế giới không giới hạn trong một bữa ăn, mà thế giới cần có hào quang của tình thương. Bạn có bao giờ cảm thấy bị lạc lõng trong chính ngôi nhà của mình? Một câu chuyện chạm đến tận cùng cảm xúc.', '167.jpg', 'https://library.hust.edu.vn/sites/default/files/C%C3%A2y%20cam%20ng%E1%BB%8Dt%20c%E1%BB%A7a%20t%C3%B4i%20-%20%E1%BA%A2nh%20b%C3%ACa.jpg', 2000, '2024-01-25 00:00:00'),
(168, 'Nhà Giả Kim', 'Paulo Coelho', 4, 67000.00, 15, 'Những bài học về lòng dũng cảm, sự hy sinh vì người khác sẽ khiến độc giả suy ngẫm và trân trọng hơn những giá trị cuộc sống.', '168.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/363/455/products/nhagiakimnew03.jpg?v=1705552576547', 2900, '2020-09-11 00:00:00'),
(169, 'Trốn Lên Mái Nhà Để Khóc', 'Lam', 4, 80500.00, 15, 'Sự đồng cảm sâu sắc – Nếu bạn từng cảm thấy cô đơn, lạc lõng giữa thế giới này, hãy để những trang sách an ủi tâm hồn bạn.', '169.jpg', 'https://bizweb.dktcdn.net/thumb/1024x1024/100/465/223/products/tron-len-mai-nha-de-khoc.jpg?v=1699519417920', 1400, '2023-01-22 00:00:00'),
(170, 'Cánh Đồng Bất Tậ', 'Nguyễn Ngọc Tư', 4, 85000.00, 15, 'Mỗi trang sách là những lát cắt sắc lạnh về tình yêu, sự phản bội và những tổn thương sâu thẳm, khiến người đọc đau lòng nhưng không thể rời mắt. Ở đó, đàn ông bỏ đi sau những cuộc tình, đàn bà bị tổn thương và trả giá, còn những đứa trẻ chỉ biết câm lặng mà lớn lên…', '170.jpg', 'https://www.nxbtre.com.vn/Images/Book/nxbtre_full_30122019_111202.jpg', 4, '2024-09-26 00:00:00'),
(171, 'Hai Số Phậ', 'Jeffrey Archer', 4, 188000.00, 20, 'Cái nhìn chân thực về sự đối lập giữa giàu và nghèo, về sự khác biệt trong cách con người đi đến thành công qua hai nhân vật chính.', '171.jpg', 'https://bizweb.dktcdn.net/100/180/408/products/hai-so-phan-tai-ban-2023-bia-cung-2.png?v=1680625069413', 1600, '2023-11-12 00:00:00'),
(172, 'Chí Phèo', 'Nam Cao', 4, 92000.00, 20, 'Tái hiện bức tranh chân thực nông thôn Việt Nam trước 1945, nghèo đói, xơ xác trên con đường phá sản, bần cùng, hết sức thê thảm, người nông dân bị đẩy vào con đường tha hóa, lưu manh hóa.', '172.jpg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcThoihr05OKMM0Oy8QMiZLcq1XQF8CRDM87_A&s', 8, '2023-11-13 00:00:00'),
(173, 'Sống Mò', 'Nam Cao', 4, 67500.00, 25, 'Bản tính cốt yếu của sự sống chính là cảm giác và tư tưởng. Cảm giác càng mạnh, càng linh diệu, tư tưởng càng dồi dào, càng sâu sắc, càng sáng suốt thì sự sống càng cao.', '173.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/song_mon/2024_06_10_16_12_48_1-390x510.jpg', 9, '2023-11-14 00:00:00'),
(174, 'Đời Thừa', 'Nam Cao', 4, 56000.00, 20, 'Đời Thừa đã ghi lại chân thật hình ảnh buồn thảm của người tri thức tiểu tư sản nghèo', '174.jpg', 'https://vn-live-01.slatic.net/p/ee88d65aaaf61d1965e65b765e16cebb.jpg', 20, '2023-11-15 00:00:00'),
(175, 'Trường Ca Achilles', 'Madeline Miller', 4, 132500.00, 15, 'Lấy cảm hứng từ sử thi Iliad, Madeline Miller đã tái hiện một câu chuyện tình yêu đầy say đắm nhưng cũng nhuốm màu bi kịch giữa hai người anh hùng Hy Lạp.', '175.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_195509_1_41170.jpg', 2600, '2020-12-02 00:00:00'),
(176, 'Mưa Đỏ', 'Chu Lai', 4, 184500.00, 10, 'Trong cuộc chiến đấu 81 ngày đêm bảo vệ thành Cổ Quảng Trị, bảo vệ Tổ quốc với những gian khổ, thiểu thốn lẫn những mất mát đau thương.', '176.jpg', 'https://upload.wikimedia.org/wikipedia/vi/thumb/f/f2/Muado_sach.webp/250px-Muado_sach.webp.png', 10008, '2025-02-16 00:00:00'),
(177, 'Nếu Biết Trăm Năm Là Hữu Hạ', 'Phạm Lữ Â', 4, 143000.00, 10, 'Bạn đã bao giờ tự hỏi: Nếu biết trước cuộc đời là hữu hạn, bạn sẽ sống khác đi chứ?', '177.jpg', 'https://pos.nvncdn.com/fd5775-40602/ps/20240627_KOrtgLU0Kc.jpeg?v=1719479082', 2900, '2024-03-29 00:00:00'),
(178, 'Hai Vạn Dặm Dưới Biể', 'Jules Verne', 4, 69300.00, 30, 'Con tàu Nautilus kỳ diệu của thuyền trưởng Nemo đưa người đọc tới với những cuộc phiêu lưu và kho báu, chinh phục những miền đất lạ.', '178.jpg', 'https://m.media-amazon.com/images/S/compressed.photo.goodreads.com/books/1630640093i/29426640.jpg', 393, '2024-10-15 00:00:00'),
(179, 'Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 4, 127500.00, 15, 'Những câu chuyện nhỏ xảy ra ở một ngôi làng nhỏ: chuyện người, chuyện cóc, chuyện ma, rồi chuyện đói ăn, cháy nhà, lụt lội,...', '179.jpg', 'https://upload.wikimedia.org/wikipedia/vi/3/3d/T%C3%B4i_th%E1%BA%A5y_hoa_v%C3%A0ng_tr%C3%AAn_c%E1%BB%8F_xanh.jpg', 3200, '2023-04-22 00:00:00'),
(180, 'Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'Nguyễn Nhật Ánh', 4, 76500.00, 15, 'Bạn có bao giờ muốn quay ngược thời gian, trở lại những ngày vô tư chạy chân trần trên sân, háo hức đợi cây kem ốc quế hay trốn ngủ trưa để chơi cùng lũ bạn?', '180.jpg', 'https://www.nxbtre.com.vn/Images/Book/nxbtre_full_09422022_034212.jpg', 386, '2023-04-11 00:00:00'),
(181, 'Bà Nại Tôi Gửi Lời Xin Lỗi', 'Fredrik Backma', 4, 161500.00, 15, 'Một bức tranh sống động về gia đình, tình yêu và những mối quan hệ tưởng chừng đơn giản nhưng lại phức tạp vô cùng.', '181.jpg', 'https://www.nxbtre.com.vn/Images/Book/nxbtre_full_08082018_040821.jpg', 259, '2023-08-17 00:00:00'),
(182, 'Vợ Nhặt', 'Kim Lâ', 4, 42000.00, 30, 'Về cuộc sống và con người ở nông thôn bằng tình cảm, tâm hồn của một người vốn là con đẻ của đồng ruộng.', '182.jpg', 'https://isach.info/images/story/cover/vo_nhat__kim_lan.jpg', 6, '2022-05-17 00:00:00'),
(183, 'Vụn Vỡ Từ Những Vì Sao', 'Châu sa đáy mắt', 4, 79500.00, 15, 'Một cuốn sách không có mục lục và cuộc đời của những đứa trẻ ấy cũng vậy, chẳng biết tương lai phía trước còn điều gì sẽ diễn ra.', '183.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/465/223/products/0120b21936c05b57f35ba5bed30a471d.jpg?v=1720495673427', 4, '2024-07-22 00:00:00'),
(184, 'Người Đàn Ông Mang Tên OVE', 'Fredrik Backma', 4, 136000.00, 15, 'Bạn có tin rằng một ông lão cộc cằn, khó tính lại có thể khiến bạn rơi nước mắt vì xúc động?', '184.jpg', 'https://www.nxbtre.com.vn/Images/Book/nxbtre_full_11002017_040008.jpg', 4000, '2022-12-11 00:00:00'),
(185, 'Lũ Trẻ Đường Tàu', 'Edith Nesbit', 4, 76300.00, 30, 'Chúng chỉ là những đứa trẻ nại ô bình thường, sống cùng cha mẹ trong một căn biệt thự bình thường có mặt tiền ốp gạch đỏ, cửa ra vào lắp kính màu và “mọi tiện nghi hiện đại”.', '185.jpg', 'https://dinhtibooks.com.vn/images/products/2023/11/14/large/2d_lu-tre-duong-tau_1699943817.webp', 506, '2023-11-16 00:00:00'),
(186, 'Lén Nhặt Chuyện Đời', 'Mộc Trầm', 4, 44200.00, 45, 'Đây chỉ là những câu chuyện, những suy nghĩ về cuộc đời của một người trẻ đang chông chênh.', '186.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/len_nhat_chuyen_doi/2023_02_01_09_29_58_1-390x510.jpg', 2600, '2022-08-25 00:00:00'),
(187, 'Ứng Dụng AI Vào Doanh Nghiệp', 'Hoàng Nam Tiế', 5, 216000.00, 24, 'Là lời mời gọi đáng tin cậy và mới mẻ các bạn bước chân trên hành trình làm chủ AI.', '187.jpg', 'https://cdn1.fahasa.com/media/catalog/product/_/n/_ng-d_ng-ai-v_o-doanh-nghi_p_9.jpg', 597, '2025-02-19 00:00:00'),
(188, 'Biến Mọi Thứ Thành Tiề', 'Nguyễn Anh Dũng', 5, 84000.00, 50, 'Chỉ ra cho bạn phương thức, công cụ phù hợp đạt được cơ hội chiến thắng là hiểu chính bản thân mình.', '188.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786043450293.jpg', 444, '2023-10-23 00:00:00'),
(189, 'Bí Mật Tư Duy Triệu Phú', 'T Harv Eker', 5, 91500.00, 15, 'Những bí mật tại sao một số người lại đạt được những thành công vượt bậc', '189.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_188995_1_1.jpg', 751, '2021-07-25 00:00:00'),
(190, 'Người Giàu Có Nhất Thành Babylo', 'George S Claso', 5, 83000.00, 15, 'Quyển sách này sẽ giúp bạn biết cách vận dụng những nguyên lý quan trọng để phát triển tài chính.', '190.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_195509_1_41914.jpg', 597, '2020-12-05 00:00:00'),
(191, 'Không Bao Giờ Là Thất Bại! Tất Cả Là Thử Thách', 'Chung Ju Yung', 5, 176500.00, 15, 'Cuốn tự truyện chi tiết về cuộc đời lẫn sự nghiệp của cố Chủ tịch Chung Ju-yung.', '191.jpg', 'https://cdn1.fahasa.com/media/catalog/product/k/h/khongbaogiothatbai_bia01_1.jpg', 241, '2023-03-17 00:00:00'),
(192, 'Lời Thú Tội Của Một Sát Thủ Kinh Tế', 'John Perkins', 5, 159250.00, 35, 'Một chiến lược mà Trung Quốc đã học được từ những lỗi sai của các EHM Mỹ và từ những thành công của đất nước Mỹ, để dần dần vươn lên trở thành đối tác kinh tế quan trọng của nhiều quốc gia trên thế giới.', '192.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935210307910.jpg', 353, '2023-04-01 00:00:00'),
(193, 'Youtube Thực Chiến Phải Kiếm Ra Tiề', 'Nguyễn Tiến Quốc, Luân Đức Đại', 5, 454000.00, 20, 'Cẩm nang toàn diện dành cho những ai mong muốn xây dựng sự nghiệp và thu nhập bền vững từ nền tảng video lớn nhất hành tinh – YouTube.', '193.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786044802558.jpg', 143, '2025-03-12 00:00:00'),
(194, 'Từ Tốt Đến Vĩ Đại', 'Jim Collins', 5, 110500.00, 15, 'Nghiên cứu những công ty có bước nhảy vọt và bền vững để rút ra những kết luận sát sườn.', '194.jpg', 'https://cdn1.fahasa.com/media/catalog/product/n/x/nxbtre_full_09462021_024609.jpg', 1000, '2021-04-26 00:00:00'),
(195, 'Kỹ Năng Bán Hàng Tuyệt Đỉnh', 'Grant Cardone', 5, 81600.00, 32, 'Những quy tắc bán hàng, vốn là điều kiện tiên quyết để gặt hái thành công.', '195.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935278607342_1.png', 148, '2022-09-19 00:00:00'),
(196, 'Cổ Phiếu Thường Lợi Nhuận Phi Thường', 'Philip A. Fisher', 5, 169000.00, 15, 'Mang lại cho bạn một cái nhìn toàn diện, từ những vấn đề hết sức cụ thể như sức mạnh của “lời đồn đại”.', '196.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_239806.jpg', 120, '2018-08-16 00:00:00'),
(197, 'Logistics Và Quản Trị Chuỗi Cung Ứng', 'Paul R. Murphy, Jr., A. Michael Knemeyer', 5, 336600.00, 32, 'Những kiến thức, hướng dẫn đầy đủ nhất về Logistics và quản trị chuỗi cung ứng đương đại.', '197.jpg', 'https://cdn1.fahasa.com/media/catalog/product/l/o/logistic_v_qu_n_tr_chu_i_cung_ng_-_b_a_1.jpg', 231, '2024-06-28 00:00:00'),
(198, 'Quản Trị Bằng Văn Hóa', 'Giản Tư Trung', 5, 200000.00, 20, 'Cách thức kiến tạo và tạo văn hoá tổ chức', '198.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935280401068.jpg', 428, '2023-01-28 00:00:00'),
(199, 'Phân Tích Kỹ Thuật Trong Thị Trường Tài Chính', 'John J Murphy', 5, 254000.00, 15, 'Cẩm nang hướng dẫn toàn diện về các phương pháp giao dịch và các ứng dụng', '199.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_225971.jpg', 133, '2023-03-07 00:00:00'),
(200, 'Nghệ Thuật Đàm Phá', 'Donald J Trump, Tony Schartz', 5, 92500.00, 15, 'Cách Trump làm việc mỗi ngày - cách ông điều hành công việc kinh doanh và cuộc sống, làm ăn với đối thủ,… và xây dựng những tòa nhà chọc trời trên thế giới.', '200.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_195509_1_49918.jpg', 525, '2020-09-07 00:00:00'),
(201, 'Tính Trước 5 Bước', 'Greg Dinkin, Patrick Bet-David', 5, 176000.00, 20, 'Tất cả những kiến thức cần thiết để tư duy như một chiến lược gia bậc thầy.', '201.jpg', 'https://cdn1.fahasa.com/media/flashmagazine/images/page_images/tinh_truoc_5_buoc/2023_06_22_08_36_25_1-390x510.jpg', 120, '2023-06-20 00:00:00');
INSERT INTO `books` (`book_id`, `title`, `author`, `category_id`, `price`, `discount`, `description`, `images`, `link_images`, `sold_quantity`, `created_at`) VALUES
(202, 'AI Thực Chiến Phải Kiếm Ra Tiề', 'Nguyễn Tiến Quốc, Luân Đức Đại', 5, 295000.00, 24, 'Khai phá sức mạnh của AI và biến nó thành người bạn đồng hành đắc lực trong hành trình hiện thực hóa ước mơ.', '202.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786044802671.jpg', 250, '2025-05-29 00:00:00'),
(203, 'Lãnh Đạo Thật Đừng Chỉ Ngồi Ghế', 'Brian Tracy', 5, 157500.00, 20, 'Cách người điều hành xuất sắc tạo ảnh hưởng, dẫn dắt và bức phá đội ngũ.', '203.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935246957554.jpg', 56, '2025-11-07 00:00:00'),
(204, 'Nghệ Thuật Viết Quảng Cáo', 'Victor O Schwab', 5, 102700.00, 35, 'Công thức 5 bước cơ bản tối ưu cho mọi cuộc thương thuyết, đàm phán và giao dịch thành công tự cổ chí kim.', '204.jpg', 'https://cdn1.fahasa.com/media/catalog/product/i/m/image_243547_1.jpg', 75, '2025-09-12 00:00:00'),
(205, 'Kế Toán Vỉa Hè', 'Darrell Mullis, Judith Orloff', 5, 169000.00, 15, 'Biến kế toán khô khan trở thành trò chơi con trẻ, dễ hiểu và dễ áp dụng.', '205.jpg', 'https://cdn1.fahasa.com/media/catalog/product/9/7/9786047787616.jpg', 1400, '2023-05-28 00:00:00'),
(206, 'MBA Bằng Hình', 'Jason Barron , MBA', 5, 160500.00, 15, 'Khóa học MBA trong trường kinh doanh thông qua những ghi chép trực quan.', '206.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935235238978.jpg', 779, '2023-11-11 00:00:00'),
(207, 'Thực Đơn Cuối Tuần Lạ Miệng', 'Quỳnh Chi', 6, 32000.00, 20, 'Cuốn sách giúp bạn soạn ra những thực đơn món ăn hàng ngày.', '207.jpg', 'https://cdn1.fahasa.com/media/catalog/product/8/9/8935073085000.jpg', 120, '2020-08-14 00:00:00'),
(208, 'Sinh Tố, Nước Ép, Chè - Kem Nn & Bổ Dưỡng', 'Tường Vâ', 6, 36000.00, 20, 'Pha chế nước trái cây cho ra những thức uống nn lành, giúp bổ sung đủ lượng chất dinh dưỡng cần thiết cho cơ thể mỗi ngày.', '208.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/image-66059.jpg?v=1628777804213', 80, '2022-03-12 00:00:00'),
(209, 'Nghệ Thuật Làm Kem & Các Loại Bánh Kem', 'Lê Quốc Hùng', 6, 28000.00, 10, 'Tập sách này sẽ giới thiệu kỹ thuật tạo hình trang trí bánh kem như tạo hình nhân vật, tạo hình động vật, tạo hình hoa lá cây trái.', '209.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/image-59456.jpg?v=1628777762917', 136, '2023-04-24 00:00:00'),
(210, 'Món Ăn Á - Âu Nn Mỗi Ngày', 'Nguyễn Viên Chi', 6, 32000.00, 10, 'Cuốn sách giới thiệu những món ăn Á - Âu đặc trưng và hấp dẫn.', '210.jpg', 'https://bizweb.dktcdn.net/thumb/large/100/418/570/products/8935210218568.jpg', 250, '2022-05-27 00:00:00'),
(211, '179 Món Chay Bổ Dưỡng', 'Nguyễn Viên Chi', 6, 30400.00, 15, 'Công thức đa dạng và tinh tế nhất để tạo nên những món ăn nn, hấp dẫn trong bữa ăn.', '211.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/179-mon-chay-bo-duong-76778-320-76778.jpg?v=1628777737843', 200, '2022-01-22 00:00:00'),
(212, '125 Món Nướng Đặc Sắc', 'Nguyễn Viên Chi', 6, 27200.00, 20, 'Cuốn sách này giới thiệu bí quyết và công thức làm 125 món nướng đặc sắc, phong phú.', '212.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/img962-3.jpg?v=1623640422853', 170, '2022-02-16 00:00:00'),
(213, 'Mẹo Vặt Nấu Ăn Và Chế Biến Thực Phẩm', 'Nguyễn Viên Chi', 6, 32000.00, 15, 'Những mẹo vặt nấu ăn và cách chế biến thực phẩm tinh tế để tạo ra những món ăn nn.', '213.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/img375-11.jpg?v=1623638561140', 350, '2023-09-18 00:00:00'),
(214, '168 Món Cháo Dinh Dưỡng', 'Nguyễn Viên Chi', 6, 28800.00, 15, 'Cuốn sách này đem đến cho độc giả bí quyết chế biến cháo dinh dưỡng nn và bổ dưỡng.', '214.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/image-231319.jpg?v=1628777741947', 180, '2024-09-19 00:00:00'),
(215, 'Món Kho Nn - Dễ Làm', 'Nguyễn Viên Chi', 6, 28800.00, 20, 'Công thức đa dạng và tinh tế nhất để tạo nên những món ăn nn, hấp dẫn trong bữa ăn.', '215.jpg', 'https://bizweb.dktcdn.net/thumb/large/100/418/570/products/mon-kho-ngon-de-lam-76772-320-76772.jpg', 270, '2022-11-12 00:00:00'),
(216, 'Sổ Tay Nội Trợ', 'Nguyễn Viên Chi', 6, 32000.00, 15, 'Những bí quyết nấu ăn có thể được ví như món ăn tổng hợp của rất nhiều sự thử nghiệm, kinh nghiệm đến những câu chuyện buồn vui xung quanh.', '216.jpg', 'https://bizweb.dktcdn.net/thumb/grande/100/418/570/products/9786045508923.jpg?v=1629051984753', 120, '2025-05-17 00:00:00');

--
-- Triggers `books`
--
DROP TRIGGER IF EXISTS `trg_UpdateBookSetPrice_OnBookUpdate`;
DELIMITER $$
CREATE TRIGGER `trg_UpdateBookSetPrice_OnBookUpdate` AFTER UPDATE ON `books` FOR EACH ROW BEGIN
    -- Chỉ chạy khi price thay đổi
    IF OLD.price <> NEW.price THEN

        UPDATE BOOK_SETS bs
        JOIN (
            SELECT 
                bsi.set_id,
                SUM(b.price * bsi.quantity) AS total_price
            FROM BOOK_SET_ITEMS bsi
            JOIN BOOKS b ON bsi.book_id = b.book_id
            WHERE bsi.set_id IN (
                SELECT set_id FROM BOOK_SET_ITEMS WHERE book_id = NEW.book_id
            )
            GROUP BY bsi.set_id
        ) AS sp ON bs.set_id = sp.set_id
        SET bs.price = sp.total_price * (1 - bs.discount / 100);

    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_books_prevent_high_discount_insert`;
DELIMITER $$
CREATE TRIGGER `trg_books_prevent_high_discount_insert` BEFORE INSERT ON `books` FOR EACH ROW BEGIN
    IF NEW.discount > 80 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Discount cannot exceed 80%';
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_books_prevent_high_discount_update`;
DELIMITER $$
CREATE TRIGGER `trg_books_prevent_high_discount_update` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
    IF NEW.discount > 80 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Discount cannot exceed 80%';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book_sets`
--

DROP TABLE IF EXISTS `book_sets`;
CREATE TABLE IF NOT EXISTS `book_sets` (
  `set_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `images` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `link_images` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `discount` decimal(5,2) DEFAULT '0.00',
  `sold_quantity` int DEFAULT '0',
  `stock` int DEFAULT '0',
  `reorder_level` int DEFAULT '10',
  `stock_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `book_sets`
--

INSERT INTO `book_sets` (`set_id`, `name`, `description`, `images`, `link_images`, `price`, `created_at`, `discount`, `sold_quantity`, `stock`, `reorder_level`, `stock_status`, `last_updated`) VALUES
(1, 'Bộ SGK Lớp 1', 'Bộ sách giáo khoa lớp 1: Toán, Tiếng Việt 1 (Tập 1+2), Tự Nhiên & Xã Hội, Âm Nhạc, Mỹ Thuật, Tin Học, Đạo Đức.', '1_1.jpg', 'https://cdn.giaoducthoidai.vn/images/b4508baace0d9fe4c8bbd296e259642ec6e9fee887635e3df24ab654c546ca1cb3160b9c347f6ee6e206e745863ae257/a2.jpg', 243000.00, '2025-12-09 07:32:16', 10.00, 918, -22, 300, 'HIDDEN', '2025-12-09 07:32:16'),
(2, 'Bộ SGK Lớp 2', 'Bộ sách giáo khoa lớp 2: Toán, Tiếng Việt 2 (Tập 1+2), Tự Nhiên & Xã Hội, Âm Nhạc, Mỹ Thuật, Tin Học, Đạo Đức.', '2_2.jpg', 'https://sachhoc.com/image/cache/catalog/Sachluyenthi/Tron-bo-sach-giao-khoa-lop-2-canh-dieu-500x554.jpg', 247500.00, '2025-12-09 07:32:16', 10.00, 790, 1089, 500, 'ACTIVE', '2025-12-09 07:32:16'),
(3, 'Bộ SGK Lớp 3', 'Bộ sách giáo khoa lớp 3: Toán, Tiếng Việt 3 (Tập 1+2), Khoa Học, Âm Nhạc, Mỹ Thuật, Tin Học, Đạo Đức.', '3_3.jpg', 'https://cdn.trungtamsach.vn/static_cdn/image_items/media/catalog/product/z/3/z3583701664401_e430ce5fb18a8cfe99dc61419c29c24f_1.jpg', 254700.00, '2025-12-09 07:32:16', 10.00, 997, 288, 100, 'ACTIVE', '2025-12-09 07:32:16'),
(4, 'Bộ SGK Lớp 4', 'Bộ sách giáo khoa lớp 4: Toán, Tiếng Việt 4 (Tập 1+2), Khoa Học, Âm Nhạc, Mỹ Thuật, Tin Học, Đạo Đức.', '4_4.jpg', 'https://congdankhuyenhoc.qltns.mediacdn.vn/449484899827462144/2023/6/13/sgk-16866628729001856592197.jpg', 268200.00, '2025-12-09 07:32:16', 10.00, 813, 527, 300, 'ACTIVE', '2025-12-09 07:32:16'),
(5, 'Bộ SGK Lớp 5', 'Bộ sách giáo khoa lớp 5: Toán, Tiếng Việt 5 (Tập 1+2), Khoa Học, Âm Nhạc, Mỹ Thuật, Tin Học, Đạo Đức.', '5_5.jpg', 'https://phuthien.gialai.gov.vn/upload/105709/fck/phuthien/33.jpg', 282600.00, '2025-12-09 07:32:16', 10.00, 876, 1854, 500, 'ACTIVE', '2025-12-09 07:32:16'),
(6, 'Bộ SGK Lớp 6', 'Bộ sách giáo khoa lớp 6: Toán, Ngữ Văn, Khoa Học Tự Nhiên, Lịch Sử & Địa Lý, Sinh Học, Tin Học, GDCD, Công Nghệ.', '6_6.jpg', 'https://media.vneconomy.vn/images/upload/2021/08/13/gk-1.jpg', 351900.00, '2025-12-09 07:32:16', 10.00, 742, 363, 100, 'ACTIVE', '2025-12-09 07:32:16'),
(7, 'Bộ SGK Lớp 7', 'Bộ sách giáo khoa lớp 7: Toán, Ngữ Văn, Khoa Học Tự Nhiên, Lịch Sử & Địa Lý, Sinh Học, Tin Học, GDCD, Công Nghệ.', '7_7.jpg', 'https://nhasachquangloi.vn/pub/media/catalog/product/cache/3bd4b739bad1f096e12e3a82b40e551a/b/_/b_s_ch.jpg', 359100.00, '2025-12-09 07:32:16', 10.00, 483, 838, 300, 'ACTIVE', '2025-12-09 07:32:16'),
(8, 'Bộ SGK Lớp 8', 'Bộ sách giáo khoa lớp 8: Toán, Ngữ Văn, Vật Lý, Hóa Học, Sinh Học, Tin Học, GDCD, Công Nghệ.', '8_8.jpg', 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-ltz5lfbqr9xt33', 373500.00, '2025-12-09 07:32:16', 10.00, 589, 945, 500, 'ACTIVE', '2025-12-09 07:32:16'),
(9, 'Bộ SGK Lớp 9', 'Bộ sách giáo khoa lớp 9: Toán, Ngữ Văn, Vật Lý, Hóa Học, Sinh Học, Tin Học, GDCD, Công Nghệ.', '9_9.jpg', 'https://sachhoc.com/image/cache/catalog/LuyenThi/Lop6-9/Tron-bo-sach-giao-khoa-lop-9-500x554.jpg', 388800.00, '2025-12-09 07:32:16', 10.00, 698, 330, 100, 'ACTIVE', '2025-12-09 07:32:16'),
(10, 'Bộ SGK Lớp 10', 'Bộ sách giáo khoa lớp 10: Toán, Ngữ Văn, Vật Lý, Hóa Học, Sinh Học, Tin Học, GDCD, Công Nghệ.', '10_10.jpg', 'https://sachhoc.com/image/cache/catalog/LuyenThi/Lop10-12/Tron-bo-sach-giao-khoa-lop-10-500x554.jpg', 435600.00, '2025-12-09 07:32:16', 10.00, 292, 0, 300, 'OUT_OF_STOCK', '2025-12-09 07:32:16'),
(11, 'Bộ SGK Lớp 11', 'Bộ sách giáo khoa lớp 11: Toán, Ngữ Văn, Vật Lý, Hóa Học, Sinh Học, Tin Học, GDCD, Công Nghệ.', '11_11.jpg', 'https://www.bachdang.info/image/cache/catalog/5376fbb828c717db4c682af176838927-500x524.jpg', 449100.00, '2025-12-09 07:32:16', 10.00, 292, -14, 500, 'HIDDEN', '2025-12-09 07:32:16'),
(12, 'Bộ SGK Lớp 12', 'Bộ sách giáo khoa lớp 12: Toán, Ngữ Văn, Vật Lý, Hóa Học, Sinh Học, Tin Học, GDCD, Công Nghệ.', '12_12.jpg', 'https://img.lsvn.vn/resize/vQJ-d_HrLpFqQbGfPCl6A_22mfWSeDZS24aKlh8XpDmgIrxZHEy0-v8H4HhkqOvz0/vne-news/January2024/qtV9yW1ewiXOhHzTU6M9.jpeg', 469800.00, '2025-12-09 07:32:16', 10.00, 384, 309, 100, 'ACTIVE', '2025-12-09 07:32:16');

-- --------------------------------------------------------

--
-- Table structure for table `book_set_items`
--

DROP TABLE IF EXISTS `book_set_items`;
CREATE TABLE IF NOT EXISTS `book_set_items` (
  `set_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`set_id`,`book_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `book_set_items`
--

INSERT INTO `book_set_items` (`set_id`, `book_id`, `quantity`) VALUES
(1, 21, 1),
(1, 22, 1),
(1, 23, 1),
(1, 24, 1),
(1, 25, 1),
(1, 26, 1),
(1, 27, 1),
(1, 28, 1),
(2, 29, 1),
(2, 30, 1),
(2, 31, 1),
(2, 32, 1),
(2, 33, 1),
(2, 34, 1),
(2, 35, 1),
(2, 36, 1),
(3, 37, 1),
(3, 38, 1),
(3, 39, 1),
(3, 40, 1),
(3, 41, 1),
(3, 42, 1),
(3, 43, 1),
(3, 44, 1),
(4, 45, 1),
(4, 46, 1),
(4, 47, 1),
(4, 48, 1),
(4, 49, 1),
(4, 50, 1),
(4, 51, 1),
(4, 52, 1),
(5, 53, 1),
(5, 54, 1),
(5, 55, 1),
(5, 56, 1),
(5, 57, 1),
(5, 58, 1),
(5, 59, 1),
(5, 60, 1),
(6, 61, 1),
(6, 62, 1),
(6, 63, 1),
(6, 64, 1),
(6, 65, 1),
(6, 66, 1),
(6, 67, 1),
(6, 68, 1),
(7, 69, 1),
(7, 70, 1),
(7, 71, 1),
(7, 72, 1),
(7, 73, 1),
(7, 74, 1),
(7, 75, 1),
(7, 76, 1),
(8, 77, 1),
(8, 78, 1),
(8, 79, 1),
(8, 80, 1),
(8, 81, 1),
(8, 82, 1),
(8, 83, 1),
(8, 84, 1),
(9, 85, 1),
(9, 86, 1),
(9, 87, 1),
(9, 88, 1),
(9, 89, 1),
(9, 90, 1),
(9, 91, 1),
(9, 92, 1),
(10, 93, 1),
(10, 94, 1),
(10, 95, 1),
(10, 96, 1),
(10, 97, 1),
(10, 98, 1),
(10, 99, 1),
(10, 100, 1),
(11, 111, 1),
(11, 112, 1),
(11, 113, 1),
(11, 114, 1),
(11, 115, 1),
(11, 116, 1),
(11, 117, 1),
(11, 118, 1),
(12, 129, 1),
(12, 130, 1),
(12, 131, 1),
(12, 132, 1),
(12, 133, 1),
(12, 134, 1),
(12, 135, 1),
(12, 136, 1);

-- --------------------------------------------------------

--
-- Table structure for table `book_views`
--

DROP TABLE IF EXISTS `book_views`;
CREATE TABLE IF NOT EXISTS `book_views` (
  `view_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `book_id` int NOT NULL,
  `viewed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`view_id`),
  KEY `idx_book_views_user` (`user_id`),
  KEY `idx_book_views_book` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `book_views`
--

INSERT INTO `book_views` (`view_id`, `user_id`, `book_id`, `viewed_at`) VALUES
(1, NULL, 1, '2025-11-20 08:15:23'),
(2, 11, 1, '2025-11-20 14:22:10'),
(3, 11, 3, '2025-11-20 14:25:40'),
(4, 12, 33, '2025-11-21 09:11:05'),
(5, NULL, 33, '2025-11-21 10:30:12'),
(6, 15, 37, '2025-11-21 15:44:30'),
(7, 18, 57, '2025-11-22 07:20:18'),
(8, 20, 97, '2025-11-22 19:55:44'),
(9, 24, 2, '2025-11-23 11:12:33'),
(10, 27, 79, '2025-11-23 13:45:21'),
(11, 31, 11, '2025-11-24 08:30:55'),
(12, 35, 96, '2025-11-24 16:22:10'),
(13, 41, 53, '2025-11-25 10:18:44'),
(14, 45, 1, '2025-11-25 14:55:12'),
(15, NULL, 57, '2025-11-26 09:33:21'),
(16, 13, 12, '2025-11-26 11:11:11'),
(17, 16, 57, '2025-11-26 17:22:33'),
(18, 19, 87, '2025-11-27 08:08:08'),
(19, 22, 4, '2025-11-27 12:45:30'),
(20, 50, 39, '2025-11-27 15:20:45'),
(21, NULL, 51, '2025-11-28 08:10:11'),
(22, 11, 52, '2025-11-28 09:22:45'),
(23, 12, 53, '2025-11-28 14:10:33'),
(24, 13, 54, '2025-11-28 16:44:20'),
(25, 15, 55, '2025-11-28 18:55:01'),
(26, 16, 56, '2025-11-28 20:12:19'),
(27, 18, 57, '2025-11-29 08:05:33'),
(28, NULL, 58, '2025-11-29 09:55:49'),
(29, 20, 59, '2025-11-29 11:10:10'),
(30, 21, 60, '2025-11-29 12:40:40'),
(31, 22, 61, '2025-11-29 15:30:55'),
(32, 24, 62, '2025-11-29 17:15:22'),
(33, 25, 63, '2025-11-29 18:40:02'),
(34, 27, 64, '2025-11-29 20:10:44'),
(35, NULL, 65, '2025-11-30 09:09:09'),
(36, 28, 66, '2025-11-30 10:20:55'),
(37, 30, 67, '2025-11-30 12:33:19'),
(38, 31, 68, '2025-11-30 14:22:42'),
(39, 33, 69, '2025-11-30 15:48:10'),
(40, 35, 70, '2025-11-30 17:55:32'),
(41, 38, 71, '2025-12-01 08:22:11'),
(42, 41, 72, '2025-12-01 10:55:44'),
(43, 43, 73, '2025-12-01 12:10:05'),
(44, 45, 74, '2025-12-01 13:33:33'),
(45, 46, 75, '2025-12-01 14:50:27'),
(46, 47, 76, '2025-12-01 15:59:11'),
(47, 48, 77, '2025-12-01 17:10:00'),
(48, 49, 78, '2025-12-01 18:44:55'),
(49, 50, 79, '2025-12-02 08:18:30'),
(50, 11, 80, '2025-12-02 09:55:22'),
(51, 12, 81, '2025-12-02 11:22:40'),
(52, 13, 82, '2025-12-02 13:10:11'),
(53, 15, 83, '2025-12-02 15:55:26'),
(54, NULL, 84, '2025-12-02 18:33:44'),
(55, 16, 85, '2025-12-03 08:40:55'),
(56, 18, 86, '2025-12-03 10:12:20'),
(57, 20, 87, '2025-12-03 12:22:09'),
(58, 21, 88, '2025-12-03 14:14:44'),
(59, 22, 89, '2025-12-03 15:55:33'),
(60, 24, 90, '2025-12-03 17:50:10'),
(61, NULL, 91, '2025-12-04 09:09:09'),
(62, 25, 92, '2025-12-04 10:33:21'),
(63, 27, 93, '2025-12-04 12:10:55'),
(64, 28, 94, '2025-12-04 14:22:48'),
(65, 30, 95, '2025-12-04 16:40:11'),
(66, 31, 96, '2025-12-04 19:05:33'),
(67, 33, 97, '2025-12-05 09:22:50'),
(68, 35, 98, '2025-12-05 11:30:14'),
(69, 38, 99, '2025-12-05 13:55:55'),
(70, 41, 100, '2025-12-05 16:12:00'),
(71, NULL, 215, '2026-04-16 07:28:16'),
(72, NULL, 215, '2026-04-16 07:28:19'),
(73, NULL, 215, '2026-04-16 07:29:41'),
(74, NULL, 215, '2026-04-16 07:29:51'),
(75, 101, 213, '2026-04-16 08:18:55'),
(76, 101, 215, '2026-04-16 08:20:11'),
(77, 101, 215, '2026-04-16 08:24:41'),
(78, 101, 214, '2026-04-16 08:24:46'),
(79, 101, 214, '2026-04-16 08:24:50'),
(80, NULL, 214, '2026-04-17 11:13:09'),
(81, NULL, 215, '2026-04-17 11:18:54'),
(82, NULL, 150, '2026-04-17 11:26:42'),
(83, 20, 150, '2026-04-17 11:36:03'),
(84, NULL, 2, '2026-04-17 11:47:17'),
(85, NULL, 155, '2026-04-17 11:50:30'),
(86, NULL, 155, '2026-04-17 11:53:59'),
(87, 20, 154, '2026-04-17 11:54:28'),
(88, 20, 154, '2026-04-17 11:56:41'),
(89, 20, 154, '2026-04-17 11:56:47'),
(90, 20, 154, '2026-04-17 11:58:33'),
(91, 20, 154, '2026-04-17 12:01:10'),
(92, 20, 154, '2026-04-17 12:01:13'),
(93, 20, 154, '2026-04-17 12:01:34'),
(94, 20, 154, '2026-04-17 12:02:24'),
(95, 20, 150, '2026-04-17 12:07:37'),
(96, 20, 149, '2026-04-17 12:07:51'),
(97, 20, 176, '2026-04-17 12:08:03'),
(98, 20, 190, '2026-04-17 12:33:35'),
(99, 20, 149, '2026-04-17 12:34:46'),
(100, NULL, 149, '2026-04-17 12:37:47'),
(101, NULL, 149, '2026-04-17 12:37:54'),
(102, 20, 149, '2026-04-17 12:38:10'),
(103, 20, 150, '2026-04-17 12:48:45'),
(104, 20, 149, '2026-04-17 12:48:52'),
(105, 20, 155, '2026-04-17 12:56:21'),
(106, 20, 2, '2026-04-17 13:09:16'),
(107, 20, 2, '2026-04-17 13:15:56'),
(108, 20, 2, '2026-04-17 13:16:10'),
(109, 20, 2, '2026-04-17 13:21:42'),
(110, 20, 155, '2026-04-17 13:23:32'),
(111, 20, 149, '2026-04-17 13:31:57'),
(112, 20, 149, '2026-04-17 13:37:36'),
(113, 20, 155, '2026-04-17 13:40:39'),
(114, 20, 155, '2026-04-17 13:43:02'),
(115, 20, 150, '2026-04-17 13:43:09'),
(116, 20, 162, '2026-04-17 13:43:12'),
(117, 20, 2, '2026-04-17 13:43:52'),
(118, 20, 155, '2026-04-17 13:55:58'),
(119, 20, 2, '2026-04-17 13:56:37'),
(120, 20, 155, '2026-04-17 13:59:52'),
(121, 20, 149, '2026-04-17 14:03:34'),
(122, 20, 155, '2026-04-17 14:05:48'),
(123, 20, 149, '2026-04-17 14:07:44'),
(124, 20, 155, '2026-04-17 14:09:44'),
(125, 20, 150, '2026-04-17 14:09:49'),
(126, 20, 176, '2026-04-17 14:09:52'),
(127, 20, 151, '2026-04-17 14:10:11'),
(128, 20, 162, '2026-04-17 14:10:13'),
(129, 20, 176, '2026-04-17 14:10:21'),
(130, 20, 165, '2026-04-17 14:10:28'),
(131, 20, 149, '2026-04-17 14:10:35'),
(132, 20, 152, '2026-04-17 14:10:42'),
(133, 20, 149, '2026-04-17 14:10:54'),
(134, 20, 155, '2026-04-17 14:15:43'),
(135, 20, 29, '2026-04-17 14:16:11'),
(136, 20, 3, '2026-04-17 14:16:16'),
(137, 20, 155, '2026-04-17 14:20:25'),
(138, 20, 155, '2026-04-17 14:26:12'),
(139, 20, 152, '2026-04-17 14:26:18'),
(140, 20, 149, '2026-04-17 14:34:57'),
(141, 20, 155, '2026-04-17 14:36:15'),
(142, 20, 216, '2026-04-17 14:42:25'),
(143, 20, 204, '2026-04-17 15:00:17'),
(144, 20, 2, '2026-04-17 15:03:27'),
(145, 20, 2, '2026-04-17 15:04:06'),
(146, 20, 2, '2026-04-17 15:05:58'),
(147, 20, 2, '2026-04-17 15:06:00'),
(148, 20, 2, '2026-04-17 15:06:04'),
(149, 20, 2, '2026-04-17 15:08:23'),
(150, 20, 2, '2026-04-17 15:08:25'),
(151, 20, 2, '2026-04-17 15:11:14'),
(152, 20, 155, '2026-04-17 15:11:22'),
(153, 20, 1, '2026-04-17 15:11:50'),
(154, 20, 1, '2026-04-17 15:20:48'),
(155, 20, 176, '2026-04-17 15:20:54'),
(156, 20, 190, '2026-04-17 15:23:28'),
(157, 20, 150, '2026-04-17 15:30:29'),
(158, NULL, 150, '2026-04-18 12:37:26'),
(159, 108, 149, '2026-04-18 12:52:25'),
(160, 109, 176, '2026-04-18 12:59:13'),
(161, 109, 152, '2026-04-18 14:24:39'),
(162, 109, 162, '2026-04-18 14:35:45'),
(163, 109, 149, '2026-04-18 14:39:15'),
(164, 109, 157, '2026-04-18 14:54:21'),
(165, 109, 155, '2026-04-18 15:08:48'),
(166, 109, 155, '2026-04-18 15:09:08'),
(167, 109, 149, '2026-04-18 15:13:14'),
(168, 109, 149, '2026-04-18 15:16:28'),
(169, 109, 149, '2026-04-18 15:16:55'),
(170, 109, 149, '2026-04-18 15:20:47'),
(171, 109, 149, '2026-04-18 15:20:50'),
(172, 109, 149, '2026-04-18 15:20:53'),
(173, 109, 149, '2026-04-18 15:23:19'),
(174, 109, 149, '2026-04-18 15:23:22'),
(175, 109, 149, '2026-04-18 15:25:55'),
(176, 109, 155, '2026-04-18 15:26:01'),
(177, 109, 155, '2026-04-18 15:26:21'),
(178, 109, 3, '2026-04-18 15:27:25'),
(179, 109, 3, '2026-04-18 15:28:58'),
(180, 109, 3, '2026-04-18 15:29:03'),
(181, 109, 1, '2026-04-18 15:33:13'),
(182, 109, 150, '2026-04-18 15:33:21'),
(183, 109, 154, '2026-04-18 15:33:40'),
(184, 109, 149, '2026-04-18 15:39:13'),
(185, 109, 149, '2026-04-18 15:59:05'),
(186, 109, 155, '2026-04-18 16:03:17'),
(187, 109, 176, '2026-04-18 16:11:20'),
(188, 109, 155, '2026-04-18 16:13:47'),
(189, NULL, 1, '2026-04-19 04:28:28'),
(190, NULL, 22, '2026-04-19 04:28:42'),
(191, 110, 176, '2026-04-19 04:52:42'),
(192, 110, 150, '2026-04-19 05:04:54'),
(193, 110, 162, '2026-04-19 05:04:57'),
(194, 110, 162, '2026-04-19 05:05:00'),
(195, 110, 149, '2026-04-19 07:37:47'),
(196, 110, 176, '2026-04-19 07:48:00'),
(197, 110, 162, '2026-04-19 08:41:08'),
(198, 1, 162, '2026-04-19 08:52:00'),
(199, 110, 149, '2026-04-19 08:52:26'),
(200, 110, 149, '2026-04-19 08:52:28'),
(201, 110, 155, '2026-04-19 08:56:05'),
(202, 110, 165, '2026-04-19 13:52:11'),
(203, 110, 176, '2026-04-19 20:54:20'),
(204, 110, 161, '2026-04-19 21:30:39'),
(205, 110, 157, '2026-04-19 21:30:45'),
(206, 110, 176, '2026-04-19 21:50:48'),
(207, 110, 149, '2026-04-19 21:52:32'),
(208, 110, 149, '2026-04-19 21:52:36'),
(209, 110, 149, '2026-04-19 21:53:42'),
(210, 110, 161, '2026-04-19 21:53:47'),
(211, 110, 152, '2026-04-19 21:53:50'),
(212, 110, 152, '2026-04-19 21:53:52'),
(213, 110, 3, '2026-04-19 21:56:26'),
(214, 110, 3, '2026-04-19 21:56:28'),
(215, 110, 149, '2026-04-19 21:57:44'),
(216, 110, 11, '2026-04-19 21:57:59'),
(217, 110, 3, '2026-04-19 21:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-01-01 13:25:00', '2025-01-02 15:10:00'),
(2, 2, '2025-01-05 16:40:00', '2025-01-06 18:05:00'),
(3, 3, '2025-01-10 10:20:00', '2025-01-12 09:35:00'),
(4, 4, '2025-01-16 19:15:00', '2025-01-18 14:45:00'),
(5, 5, '2025-01-21 14:55:00', '2025-01-23 16:20:00'),
(6, 6, '2025-01-25 16:30:00', '2025-01-27 12:10:00'),
(7, 7, '2025-01-30 09:50:00', '2025-02-01 11:35:00'),
(8, 8, '2025-02-05 19:05:00', '2025-02-07 10:25:00'),
(9, 9, '2025-02-10 13:20:00', '2025-02-12 15:50:00'),
(10, 10, '2025-02-15 17:45:00', '2025-02-17 09:30:00'),
(11, 11, '2025-03-01 09:30:00', '2025-03-02 11:15:00'),
(12, 12, '2025-03-02 14:10:00', '2025-03-03 15:40:00'),
(13, 13, '2025-03-03 19:45:00', '2025-03-05 10:20:00'),
(14, 14, '2025-03-04 11:25:00', '2025-03-05 13:00:00'),
(15, 15, '2025-03-05 16:40:00', '2025-03-07 09:10:00'),
(16, 16, '2025-03-06 12:50:00', '2025-03-07 15:25:00'),
(17, 17, '2025-03-07 18:30:00', '2025-03-09 10:05:00'),
(18, 18, '2025-03-08 11:55:00', '2025-03-09 14:20:00'),
(19, 19, '2025-03-09 17:20:00', '2025-03-11 08:45:00'),
(20, 20, '2025-03-10 09:35:00', '2025-03-11 12:10:00'),
(21, 21, '2025-03-11 14:00:00', '2025-03-13 15:35:00'),
(22, 22, '2025-03-12 19:25:00', '2025-03-14 10:50:00'),
(23, 23, '2025-03-13 08:40:00', '2025-03-14 13:15:00'),
(24, 24, '2025-03-14 12:10:00', '2025-03-15 16:55:00'),
(25, 25, '2025-03-15 17:35:00', '2025-03-17 09:20:00'),
(26, 26, '2025-03-16 10:50:00', '2025-03-18 11:45:00'),
(27, 27, '2025-03-17 15:25:00', '2025-03-19 14:10:00'),
(28, 28, '2025-03-18 11:45:00', '2025-03-20 13:30:00'),
(29, 29, '2025-03-19 16:20:00', '2025-03-21 09:05:00'),
(30, 30, '2025-03-20 09:50:00', '2025-03-22 11:35:00'),
(31, 31, '2025-03-21 13:15:00', '2025-03-23 15:50:00'),
(32, 32, '2025-03-22 18:40:00', '2025-03-24 10:25:00'),
(33, 33, '2025-03-23 09:25:00', '2025-03-25 12:00:00'),
(34, 34, '2025-03-24 14:50:00', '2025-03-26 16:35:00'),
(35, 35, '2025-03-25 11:15:00', '2025-03-27 09:00:00'),
(36, 36, '2025-03-26 16:40:00', '2025-03-28 11:25:00'),
(37, 37, '2025-03-27 08:25:00', '2025-03-29 13:10:00'),
(38, 38, '2025-03-28 13:50:00', '2025-03-30 15:35:00'),
(39, 39, '2025-03-29 18:15:00', '2025-03-31 10:00:00'),
(40, 40, '2025-03-30 07:00:00', '2025-04-01 11:45:00'),
(41, 41, '2025-04-01 12:25:00', '2025-04-03 14:10:00'),
(42, 42, '2025-04-02 17:50:00', '2025-04-04 16:35:00'),
(43, 43, '2025-04-03 09:35:00', '2025-04-05 11:20:00'),
(44, 44, '2025-04-04 14:00:00', '2025-04-06 12:45:00'),
(45, 45, '2025-04-05 11:45:00', '2025-04-07 14:30:00'),
(46, 46, '2025-04-06 16:10:00', '2025-04-08 16:55:00'),
(47, 47, '2025-04-07 08:35:00', '2025-04-09 09:20:00'),
(48, 48, '2025-04-08 13:00:00', '2025-04-10 11:45:00'),
(49, 49, '2025-04-09 17:25:00', '2025-04-11 13:10:00'),
(50, 50, '2025-04-10 09:50:00', '2025-04-12 15:35:00'),
(51, 51, '2025-04-11 14:15:00', '2025-04-13 17:20:00'),
(52, 52, '2025-04-12 11:30:00', '2025-04-14 10:05:00'),
(53, 53, '2025-04-13 16:55:00', '2025-04-15 12:40:00'),
(54, 54, '2025-04-14 10:20:00', '2025-04-16 13:25:00'),
(55, 55, '2025-04-15 15:45:00', '2025-04-17 14:50:00'),
(56, 56, '2025-04-16 08:00:00', '2025-04-18 11:35:00'),
(57, 57, '2025-04-17 13:25:00', '2025-04-19 15:10:00'),
(58, 58, '2025-04-18 18:50:00', '2025-04-20 09:45:00'),
(59, 59, '2025-04-19 07:35:00', '2025-04-21 12:20:00'),
(60, 60, '2025-04-20 12:00:00', '2025-04-22 14:05:00'),
(61, 61, '2025-04-21 17:25:00', '2025-04-23 15:50:00'),
(62, 62, '2025-04-22 09:10:00', '2025-04-24 11:45:00'),
(63, 63, '2025-04-23 14:35:00', '2025-04-25 13:20:00'),
(64, 64, '2025-04-24 11:00:00', '2025-04-26 16:35:00'),
(65, 65, '2025-04-25 16:25:00', '2025-04-27 12:10:00'),
(66, 66, '2025-04-26 08:50:00', '2025-04-28 14:55:00'),
(67, 67, '2025-04-27 13:15:00', '2025-04-29 15:40:00'),
(68, 68, '2025-04-28 17:40:00', '2025-04-30 09:25:00'),
(69, 69, '2025-04-29 09:25:00', '2025-05-01 12:10:00'),
(70, 70, '2025-04-30 14:50:00', '2025-05-02 14:35:00'),
(71, 71, '2025-05-01 11:15:00', '2025-05-03 15:50:00'),
(72, 72, '2025-05-02 16:40:00', '2025-05-04 12:25:00'),
(73, 73, '2025-05-03 08:25:00', '2025-05-05 14:10:00'),
(74, 74, '2025-05-04 13:50:00', '2025-05-06 15:35:00'),
(75, 75, '2025-05-05 18:15:00', '2025-05-07 10:20:00'),
(76, 76, '2025-05-06 07:00:00', '2025-05-08 12:45:00'),
(77, 77, '2025-05-07 12:25:00', '2025-05-09 15:10:00'),
(78, 78, '2025-05-08 17:50:00', '2025-05-10 11:35:00'),
(79, 79, '2025-05-09 09:35:00', '2025-05-11 13:20:00'),
(80, 80, '2025-05-10 14:00:00', '2025-05-12 16:55:00'),
(81, 81, '2025-05-11 11:45:00', '2025-05-13 09:30:00'),
(82, 82, '2025-05-12 16:10:00', '2025-05-14 12:15:00'),
(83, 83, '2025-05-13 08:35:00', '2025-05-15 14:50:00'),
(84, 84, '2025-05-14 13:00:00', '2025-05-16 16:35:00'),
(85, 85, '2025-05-15 17:25:00', '2025-05-17 12:10:00'),
(86, 86, '2025-05-16 09:50:00', '2025-05-18 14:05:00'),
(87, 87, '2025-05-17 14:15:00', '2025-05-19 15:50:00'),
(88, 88, '2025-05-18 11:30:00', '2025-05-20 13:20:00'),
(89, 89, '2025-05-19 16:55:00', '2025-05-21 16:35:00'),
(90, 90, '2025-05-20 10:20:00', '2025-05-22 11:45:00'),
(91, 91, '2025-05-21 15:45:00', '2025-05-23 14:10:00'),
(92, 92, '2025-05-22 08:00:00', '2025-05-24 16:35:00'),
(93, 93, '2025-05-23 13:25:00', '2025-05-25 12:20:00'),
(94, 94, '2025-05-24 18:50:00', '2025-05-26 15:50:00'),
(95, 95, '2025-05-25 07:35:00', '2025-05-27 14:05:00'),
(96, 96, '2025-05-26 12:00:00', '2025-05-28 11:35:00'),
(97, 97, '2025-05-27 17:25:00', '2025-05-29 13:20:00'),
(98, 98, '2025-05-28 09:10:00', '2025-05-30 15:50:00'),
(99, 99, '2025-05-29 14:35:00', '2025-05-31 16:35:00'),
(100, 100, '2025-05-30 11:00:00', '2025-06-01 12:45:00'),
(101, 108, '2026-04-18 19:52:26', '2026-04-18 19:52:26'),
(102, 109, '2026-04-18 19:59:14', '2026-04-18 19:59:14'),
(103, 110, '2026-04-19 11:52:43', '2026-04-19 11:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `cart_item_id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  UNIQUE KEY `cart_id` (`cart_id`,`book_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `book_id`, `quantity`, `added_at`) VALUES
(1, 1, 87, 2, '2025-01-03 10:22:11'),
(2, 2, 143, 1, '2025-01-07 14:55:09'),
(3, 3, 12, 5, '2025-01-12 09:41:33'),
(4, 4, 199, 3, '2025-01-18 18:20:45'),
(5, 5, 64, 1, '2025-01-23 11:05:27'),
(6, 6, 178, 4, '2025-01-28 16:48:19'),
(7, 7, 31, 2, '2025-02-01 20:10:54'),
(8, 8, 216, 1, '2025-02-07 08:35:42'),
(9, 9, 95, 3, '2025-02-12 22:14:08'),
(10, 10, 150, 5, '2025-02-17 13:09:37'),
(11, 11, 7, 2, '2025-03-03 09:44:16'),
(12, 12, 183, 4, '2025-03-05 17:33:58'),
(13, 13, 56, 1, '2025-03-06 11:11:11'),
(14, 14, 129, 3, '2025-03-07 19:25:44'),
(15, 15, 202, 5, '2025-03-08 07:40:21'),
(16, 16, 88, 2, '2025-03-09 21:55:06'),
(17, 17, 41, 1, '2025-03-10 10:18:59'),
(18, 18, 167, 4, '2025-03-11 15:47:13'),
(19, 19, 24, 3, '2025-03-12 18:06:32'),
(21, 21, 72, 5, '2025-03-14 08:21:07'),
(22, 22, 155, 1, '2025-03-15 20:34:55'),
(23, 23, 9, 3, '2025-03-16 14:02:18'),
(24, 24, 134, 4, '2025-03-17 09:50:39'),
(25, 25, 61, 2, '2025-03-18 17:27:41'),
(26, 26, 211, 5, '2025-03-19 11:36:04'),
(27, 27, 48, 1, '2025-03-20 22:08:56'),
(28, 28, 173, 3, '2025-03-21 06:45:23'),
(29, 29, 102, 4, '2025-03-22 19:12:10'),
(30, 30, 186, 2, '2025-03-23 13:58:34'),
(31, 31, 15, 1, '2025-03-24 09:11:42'),
(32, 32, 98, 3, '2025-03-25 16:20:09'),
(33, 33, 44, 5, '2025-03-26 21:35:18'),
(34, 34, 171, 2, '2025-03-27 08:42:51'),
(35, 35, 66, 4, '2025-03-28 14:06:33'),
(36, 36, 209, 1, '2025-03-29 19:54:02'),
(37, 37, 27, 3, '2025-03-30 11:09:45'),
(38, 38, 114, 2, '2025-03-31 17:40:58'),
(39, 39, 53, 5, '2025-04-01 20:18:29'),
(40, 40, 198, 1, '2025-04-02 07:33:16'),
(41, 41, 8, 4, '2025-04-03 22:47:01'),
(42, 42, 162, 2, '2025-04-04 10:59:37'),
(43, 43, 39, 1, '2025-04-05 13:25:48'),
(44, 44, 120, 3, '2025-04-06 18:11:52'),
(45, 45, 74, 5, '2025-04-07 09:44:06'),
(46, 46, 187, 2, '2025-04-08 21:05:41'),
(47, 47, 21, 4, '2025-04-09 15:32:19'),
(48, 48, 159, 1, '2025-04-10 08:08:27'),
(49, 49, 62, 3, '2025-04-11 19:56:14'),
(50, 50, 205, 2, '2025-04-12 12:41:53'),
(51, 51, 167, 5, '2025-04-13 14:15:00'),
(52, 52, 94, 1, '2025-04-14 11:30:00'),
(53, 53, 201, 3, '2025-04-15 16:55:00'),
(54, 54, 138, 4, '2025-04-16 10:20:00'),
(55, 55, 176, 2, '2025-04-17 15:45:00'),
(56, 56, 121, 1, '2025-04-18 08:00:00'),
(57, 57, 209, 5, '2025-04-19 13:25:00'),
(58, 58, 158, 3, '2025-04-20 18:50:00'),
(59, 59, 190, 4, '2025-04-21 07:35:00'),
(60, 60, 133, 2, '2025-04-22 12:00:00'),
(61, 61, 172, 1, '2025-04-23 17:25:00'),
(62, 62, 149, 5, '2025-04-24 09:10:00'),
(63, 63, 207, 3, '2025-04-25 14:35:00'),
(64, 64, 116, 4, '2025-04-26 11:00:00'),
(65, 65, 184, 2, '2025-04-27 16:25:00'),
(66, 66, 154, 1, '2025-04-28 08:50:00'),
(67, 67, 198, 5, '2025-04-29 13:15:00'),
(68, 68, 129, 3, '2025-04-30 17:40:00'),
(69, 69, 212, 4, '2025-05-01 09:25:00'),
(70, 70, 141, 2, '2025-05-02 14:50:00'),
(71, 71, 186, 1, '2025-05-03 11:15:00'),
(72, 72, 118, 5, '2025-05-04 16:40:00'),
(73, 73, 203, 3, '2025-05-05 08:25:00'),
(74, 74, 135, 4, '2025-05-06 13:50:00'),
(75, 75, 169, 2, '2025-05-07 18:15:00'),
(76, 76, 124, 1, '2025-05-08 07:00:00'),
(77, 77, 210, 5, '2025-05-09 12:25:00'),
(78, 78, 146, 3, '2025-05-10 17:50:00'),
(79, 79, 193, 4, '2025-05-11 09:35:00'),
(80, 80, 131, 2, '2025-05-12 14:00:00'),
(81, 81, 175, 1, '2025-05-13 11:45:00'),
(82, 82, 160, 5, '2025-05-14 16:10:00'),
(83, 83, 204, 3, '2025-05-15 08:35:00'),
(84, 84, 147, 4, '2025-05-16 13:00:00'),
(85, 85, 191, 2, '2025-05-17 17:25:00'),
(86, 86, 126, 1, '2025-05-18 09:50:00'),
(87, 87, 213, 5, '2025-05-19 14:15:00'),
(88, 88, 139, 3, '2025-05-20 11:30:00'),
(89, 89, 182, 4, '2025-05-21 16:55:00'),
(90, 90, 156, 2, '2025-05-22 10:20:00'),
(91, 91, 200, 1, '2025-05-23 15:45:00'),
(92, 92, 134, 5, '2025-05-24 08:00:00'),
(93, 93, 168, 3, '2025-05-25 13:25:00'),
(94, 94, 214, 4, '2025-05-26 18:50:00'),
(95, 95, 145, 2, '2025-05-27 07:35:00'),
(96, 96, 187, 1, '2025-05-28 12:00:00'),
(97, 97, 158, 5, '2025-05-29 17:25:00'),
(98, 98, 197, 3, '2025-05-30 09:10:00'),
(99, 99, 122, 4, '2025-05-31 14:35:00'),
(100, 100, 210, 2, '2025-06-01 11:00:00'),
(111, 101, 149, 1, '2026-04-18 19:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `description` text COLLATE utf8mb4_vietnamese_ci,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Sách tâm lý - kỹ năng', NULL),
(2, 'Sách thiếu nhi', NULL),
(3, 'Sách giáo khoa', NULL),
(4, 'Sách văn học', NULL),
(5, 'Sách kinh tế', NULL),
(6, 'Sách nấu ăn', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `coupon_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `code`, `discount`, `status`, `created_at`) VALUES
(1, 'WELCOME2025', 20.00, 'active', '2025-01-01 00:00:00'),
(2, 'FREESHIP50', 18.00, 'active', '2025-03-01 00:00:00'),
(3, 'SALE12_12', 12.00, 'active', '2025-11-01 00:00:00'),
(4, 'BLACKFRIDAY', 30.00, 'active', '2025-11-20 00:00:00'),
(5, 'NEWYEAR25', 250000.00, 'active', '2025-12-25 00:00:00'),
(6, 'READMORE', 15.00, 'active', '2025-02-14 00:00:00'),
(7, 'SUMMER2025', 100000.00, 'expired', '2025-06-01 00:00:00'),
(8, 'BOOKLOVER', 10.00, 'active', '2025-04-01 00:00:00'),
(9, 'MUA2TANG1', 100.00, 'active', '2025-07-01 00:00:00'),
(10, 'VIP2025', 25.00, 'active', '2025-09-01 00:00:00'),
(11, 'GIAM18', 18.00, 'active', '2025-10-01 00:00:00'),
(12, 'KHUYENMAI50', 50.00, 'expired', '2025-07-27 00:00:00'),
(13, 'READY45', 45.00, 'active', '2025-09-04 00:00:00'),
(14, 'VIP35', 35.00, 'active', '2025-12-29 00:00:00'),
(15, 'BIG24', 24.00, 'active', '2025-11-05 00:00:00'),
(16, 'FUN5', 5.00, 'active', '2025-04-15 00:00:00'),
(17, 'SUNDAY8', 8.00, 'active', '2025-06-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `inventory_id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `stock` int DEFAULT '0',
  `reorder_level` int DEFAULT '10',
  `stock_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_id`),
  UNIQUE KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `book_id`, `stock`, `reorder_level`, `stock_status`, `last_updated`) VALUES
(1, 1, -21, 300, 'HIDDEN', '2024-01-05 00:00:00'),
(2, 2, 1271, 500, 'ACTIVE', '2024-01-06 00:00:00'),
(3, 3, 208, 100, 'ACTIVE', '2024-01-07 00:00:00'),
(4, 4, 670, 300, 'ACTIVE', '2024-01-08 00:00:00'),
(5, 5, 762, 500, 'ACTIVE', '2024-01-09 00:00:00'),
(6, 6, 437, 100, 'ACTIVE', '2024-01-10 00:00:00'),
(7, 7, 619, 300, 'ACTIVE', '2024-01-11 00:00:00'),
(8, 8, 1050, 500, 'ACTIVE', '2026-04-19 09:07:00'),
(9, 9, 236, 100, 'ACTIVE', '2024-01-13 00:00:00'),
(10, 10, 0, 300, 'OUT_OF_STOCK', '2026-04-19 13:29:23'),
(11, 11, -25, 500, 'HIDDEN', '2024-01-15 00:00:00'),
(12, 12, 311, 100, 'ACTIVE', '2024-01-16 00:00:00'),
(13, 13, 526, 300, 'ACTIVE', '2024-01-17 00:00:00'),
(14, 14, 1504, 500, 'ACTIVE', '2024-01-18 00:00:00'),
(15, 15, 291, 100, 'ACTIVE', '2024-01-19 00:00:00'),
(16, 16, 612, 300, 'ACTIVE', '2024-01-20 00:00:00'),
(17, 17, 1583, 500, 'ACTIVE', '2024-01-21 00:00:00'),
(18, 18, 222, 100, 'ACTIVE', '2024-01-22 00:00:00'),
(19, 19, 450, 300, 'ACTIVE', '2024-01-23 00:00:00'),
(20, 20, 0, 500, 'OUT_OF_STOCK', '2024-01-24 00:00:00'),
(21, 21, -39, 100, 'HIDDEN', '2024-11-26 00:00:00'),
(22, 22, 696, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(23, 23, 1479, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(24, 24, 258, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(25, 25, 858, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(26, 26, 1309, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(27, 27, 384, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(28, 28, 593, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(29, 29, 1429, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(30, 30, -1, 100, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(31, 31, -42, 300, 'HIDDEN', '2024-11-26 00:00:00'),
(32, 32, 951, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(33, 33, 400, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(34, 34, 619, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(35, 35, 1558, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(36, 36, 180, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(37, 37, 636, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(38, 38, 671, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(39, 39, 400, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(40, 40, 0, 300, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(41, 41, -1, 500, 'HIDDEN', '2024-11-26 00:00:00'),
(42, 42, 327, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(43, 43, 848, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(44, 44, 1596, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(45, 45, 410, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(46, 46, 501, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(47, 47, 1173, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(48, 48, 282, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(49, 49, 890, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(50, 50, 0, 500, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(51, 51, -29, 100, 'HIDDEN', '2024-11-26 00:00:00'),
(52, 52, 875, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(53, 53, 623, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(54, 54, 219, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(55, 55, 452, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(56, 56, 1763, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(57, 57, 402, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(58, 58, 756, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(59, 59, 655, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(60, 60, 0, 100, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(61, 61, -4, 300, 'HIDDEN', '2024-11-26 00:00:00'),
(62, 62, 857, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(63, 63, 371, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(64, 64, 470, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(65, 65, 1282, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(66, 66, 154, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(67, 67, 708, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(68, 68, 649, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(69, 69, 248, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(70, 70, 0, 300, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(71, 71, -27, 500, 'HIDDEN', '2024-11-26 00:00:00'),
(72, 72, 355, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(73, 73, 809, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(74, 74, 663, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(75, 75, 380, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(76, 76, 754, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(77, 77, 927, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(78, 78, 163, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(79, 79, 659, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(80, 80, 0, 500, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(81, 81, -24, 100, 'HIDDEN', '2024-11-26 00:00:00'),
(82, 82, 778, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(83, 83, 1150, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(84, 84, 359, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(85, 85, 553, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(86, 86, 1225, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(87, 87, 243, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(88, 88, 508, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(89, 89, 807, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(90, 90, 0, 100, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(91, 91, -5, 300, 'HIDDEN', '2024-11-26 00:00:00'),
(92, 92, 610, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(93, 93, 380, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(94, 94, 806, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(95, 95, 1669, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(96, 96, 264, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(97, 97, 707, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(98, 98, 1901, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(99, 99, 390, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(100, 100, 0, 300, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(101, 101, -12, 500, 'HIDDEN', '2024-11-26 00:00:00'),
(102, 102, 367, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(103, 103, 871, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(104, 104, 1357, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(105, 105, 413, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(106, 106, 781, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(107, 107, 854, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(108, 108, 336, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(109, 109, 679, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(110, 110, 0, 500, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(111, 111, -47, 100, 'HIDDEN', '2024-11-26 00:00:00'),
(112, 112, 894, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(113, 113, 802, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(114, 114, 376, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(115, 115, 572, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(116, 116, 1239, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(117, 117, 224, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(118, 118, 839, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(119, 119, 1505, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(120, 120, 0, 100, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(121, 121, -30, 300, 'HIDDEN', '2024-11-26 00:00:00'),
(122, 122, 657, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(123, 123, 274, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(124, 124, 879, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(125, 125, 1365, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(126, 126, 406, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(127, 127, 717, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(128, 128, 1453, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(129, 129, 193, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(130, 130, 0, 300, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(131, 131, -45, 500, 'HIDDEN', '2024-11-26 00:00:00'),
(132, 132, 153, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(133, 133, 594, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(134, 134, 1872, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(135, 135, 263, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(136, 136, 486, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(137, 137, 1611, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(138, 138, 178, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(139, 139, 554, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(140, 140, 0, 500, 'OUT_OF_STOCK', '2024-11-26 00:00:00'),
(141, 141, -13, 100, 'HIDDEN', '2024-11-26 00:00:00'),
(142, 142, 583, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(143, 143, 679, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(144, 144, 205, 100, 'ACTIVE', '2024-11-26 00:00:00'),
(145, 145, 774, 300, 'ACTIVE', '2024-11-26 00:00:00'),
(146, 146, 871, 500, 'ACTIVE', '2024-11-26 00:00:00'),
(147, 147, 366, 100, 'ACTIVE', '2024-02-01 00:00:00'),
(148, 148, 415, 300, 'ACTIVE', '2024-02-02 00:00:00'),
(149, 149, 1982, 500, 'ACTIVE', '2024-02-03 00:00:00'),
(150, 150, 0, 100, 'OUT_OF_STOCK', '2024-02-04 00:00:00'),
(151, 151, -43, 300, 'HIDDEN', '2024-02-05 00:00:00'),
(152, 152, 976, 500, 'ACTIVE', '2024-02-06 00:00:00'),
(153, 153, 391, 100, 'ACTIVE', '2024-02-07 00:00:00'),
(154, 154, 510, 300, 'ACTIVE', '2024-02-08 00:00:00'),
(155, 155, 1566, 500, 'ACTIVE', '2024-02-09 00:00:00'),
(156, 156, 387, 100, 'ACTIVE', '2024-02-10 00:00:00'),
(157, 157, 841, 300, 'ACTIVE', '2024-02-11 00:00:00'),
(158, 158, 664, 500, 'ACTIVE', '2024-02-12 00:00:00'),
(159, 159, 323, 100, 'ACTIVE', '2024-02-13 00:00:00'),
(160, 160, 0, 300, 'OUT_OF_STOCK', '2024-02-14 00:00:00'),
(161, 161, -38, 500, 'HIDDEN', '2024-02-15 00:00:00'),
(162, 162, 153, 100, 'ACTIVE', '2024-02-16 00:00:00'),
(163, 163, 808, 300, 'ACTIVE', '2024-02-17 00:00:00'),
(164, 164, 666, 500, 'ACTIVE', '2024-02-18 00:00:00'),
(165, 165, 385, 100, 'ACTIVE', '2024-02-19 00:00:00'),
(166, 166, 795, 300, 'ACTIVE', '2024-02-20 00:00:00'),
(167, 167, 1435, 500, 'ACTIVE', '2024-01-25 00:00:00'),
(168, 168, 333, 100, 'ACTIVE', '2020-09-11 00:00:00'),
(169, 169, 530, 300, 'ACTIVE', '2023-01-22 00:00:00'),
(170, 170, 0, 500, 'OUT_OF_STOCK', '2024-09-26 00:00:00'),
(171, 171, -24, 100, 'HIDDEN', '2023-11-12 00:00:00'),
(172, 172, 701, 300, 'ACTIVE', '2023-11-13 00:00:00'),
(173, 173, 1417, 500, 'ACTIVE', '2023-11-14 00:00:00'),
(174, 174, 182, 100, 'ACTIVE', '2023-11-15 00:00:00'),
(175, 175, 795, 300, 'ACTIVE', '2020-12-02 00:00:00'),
(176, 176, 1477, 500, 'ACTIVE', '2025-02-16 00:00:00'),
(177, 177, 381, 100, 'ACTIVE', '2024-03-29 00:00:00'),
(178, 178, 886, 300, 'ACTIVE', '2024-10-15 00:00:00'),
(179, 179, 1374, 500, 'ACTIVE', '2023-04-22 00:00:00'),
(180, 180, 0, 100, 'OUT_OF_STOCK', '2023-04-11 00:00:00'),
(181, 181, -43, 300, 'HIDDEN', '2023-08-17 00:00:00'),
(182, 182, 1378, 500, 'ACTIVE', '2022-05-17 00:00:00'),
(183, 183, 225, 100, 'ACTIVE', '2024-07-22 00:00:00'),
(184, 184, 697, 300, 'ACTIVE', '2022-12-11 00:00:00'),
(185, 185, 897, 500, 'ACTIVE', '2023-11-16 00:00:00'),
(186, 186, 233, 100, 'ACTIVE', '2022-08-25 00:00:00'),
(187, 187, 780, 300, 'ACTIVE', '2025-02-19 00:00:00'),
(188, 188, 1952, 500, 'ACTIVE', '2023-10-23 00:00:00'),
(189, 189, 314, 100, 'ACTIVE', '2021-07-25 00:00:00'),
(190, 190, 0, 300, 'OUT_OF_STOCK', '2020-12-05 00:00:00'),
(191, 191, -43, 500, 'HIDDEN', '2023-03-17 00:00:00'),
(192, 192, 320, 100, 'ACTIVE', '2023-04-01 00:00:00'),
(193, 193, 554, 300, 'ACTIVE', '2025-03-12 00:00:00'),
(194, 194, 1785, 500, 'ACTIVE', '2021-04-26 00:00:00'),
(195, 195, 241, 100, 'ACTIVE', '2022-09-19 00:00:00'),
(196, 196, 890, 300, 'ACTIVE', '2018-08-16 00:00:00'),
(197, 197, 4989, 500, 'ACTIVE', '2026-04-19 13:29:15'),
(198, 198, 155, 100, 'ACTIVE', '2023-01-28 00:00:00'),
(199, 199, 458, 300, 'ACTIVE', '2023-03-07 00:00:00'),
(200, 200, 3, 500, 'LOW_STOCK', '2026-04-19 13:17:26'),
(201, 201, -27, 100, 'HIDDEN', '2023-06-20 00:00:00'),
(202, 202, 542, 300, 'ACTIVE', '2025-05-29 00:00:00'),
(203, 203, 1779, 500, 'ACTIVE', '2025-11-07 00:00:00'),
(204, 204, 257, 100, 'ACTIVE', '2025-09-12 00:00:00'),
(205, 205, 533, 300, 'ACTIVE', '2023-05-28 00:00:00'),
(206, 206, 959, 500, 'ACTIVE', '2023-11-11 00:00:00'),
(207, 207, 294, 100, 'ACTIVE', '2020-08-14 00:00:00'),
(208, 208, 721, 300, 'ACTIVE', '2022-03-12 00:00:00'),
(209, 209, 1671, 500, 'ACTIVE', '2023-04-24 00:00:00'),
(210, 210, 0, 100, 'OUT_OF_STOCK', '2022-05-27 00:00:00'),
(211, 211, -45, 300, 'HIDDEN', '2022-01-22 00:00:00'),
(212, 212, 883, 500, 'ACTIVE', '2022-02-16 00:00:00'),
(213, 213, 244, 100, 'ACTIVE', '2023-09-18 00:00:00'),
(214, 214, 880, 300, 'ACTIVE', '2024-09-19 00:00:00'),
(215, 215, 1809, 500, 'ACTIVE', '2022-11-12 00:00:00'),
(216, 216, 280, 100, 'ACTIVE', '2025-05-17 00:00:00'),
(217, 217, 111, 10, NULL, '2026-04-19 13:18:31'),
(218, 218, 111, 10, NULL, '2026-04-19 14:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_04_15_155713_create_sessions_table', 2),
(4, '2026_04_15_155722_create_password_reset_tokens_table', 2),
(5, '2026_04_16_060858_add_email_to_password_reset_tokens_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'unread',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_notifications_user` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `status`, `created_at`) VALUES
(1, 11, 'Đơn hàng #11 của bạn đã được giao thành công. Cảm ơn bạn đã mua sắm!', 'read', '2025-06-05 10:30:00'),
(2, 12, 'Đơn hàng #12 đã giao thành công. Hãy đánh giá sản phẩm để nhận 10k xu nhé!', 'unread', '2025-06-06 14:20:00'),
(3, 13, 'Khuyến mãi 11.11 sắp tới - Giảm tới 50% toàn sàn!', 'read', '2025-11-01 00:00:00'),
(4, 15, 'Đơn hàng #15 đã được giao. Mong bạn hài lòng với sản phẩm.', 'read', '2025-06-08 09:15:00'),
(5, 17, 'Đơn hàng #17 của bạn đã bị hủy theo yêu cầu. Tiền sẽ hoàn trong 3-5 ngày.', 'read', '2025-06-07 10:00:00'),
(6, 20, 'Chúc mừng bạn nhận được mã giảm 20% cho lần mua tiếp theo: READMORE', 'unread', '2025-11-22 20:00:00'),
(7, 24, 'Review của bạn cho sách “Nghệ Thuật Tinh Tế...” đã được duyệt và hiển thị!', 'unread', '2025-06-16 08:30:00'),
(8, 27, 'Flash sale 24h - Giảm 30% sách kỹ năng sống!', 'read', '2025-11-20 00:00:00'),
(9, 31, 'Đơn hàng #31 đã giao thành công. Cảm ơn bạn đã ủng hộ shop!', 'read', '2025-06-25 11:11:00'),
(10, 41, 'Bạn vừa được tặng 50k xu vì đánh giá 5 sao. Tiếp tục mua sắm thôi nào!', 'unread', '2025-07-03 09:45:00'),
(11, 45, 'Black Friday 2025 - Giảm tới 70% + Freeship toàn quốc!', 'unread', '2025-11-25 00:00:00'),
(12, 50, 'Chào mừng thành viên mới! Nhận ngay mã WELCOME2025 giảm 20% cho đơn đầu tiên.', 'read', '2025-04-10 00:05:00'),
(13, 12, 'Bạn vừa nhận được mã giảm 10% cho sách văn học!', 'unread', '2025-11-26 09:10:00'),
(14, 13, 'Sách trong wishlist của bạn đang giảm giá!', 'read', '2025-11-26 11:30:00'),
(15, 15, 'Đơn hàng #1525 đang trên đường giao.', 'unread', '2025-11-26 14:20:00'),
(16, 16, 'Bạn có thông báo mới từ cửa hàng.', 'unread', '2025-11-26 18:42:00'),
(17, 17, 'Flash sale đêm nay - giảm mạnh sách thiếu nhi!', 'read', '2025-11-27 00:00:00'),
(18, 18, 'Đơn hàng #1825 đã giao thành công.', 'read', '2025-11-27 10:50:00'),
(19, 20, 'Ưu đãi Cyber Monday - giảm tới 50%!', 'unread', '2025-11-27 13:22:00'),
(20, 22, 'Mời bạn đánh giá sách đã mua.', 'unread', '2025-11-27 20:10:00'),
(21, 24, 'Bạn có mã freeship cho đơn hàng kế tiếp!', 'unread', '2025-11-28 09:55:00'),
(22, 25, 'Cảm ơn bạn đã đánh giá 5 sao sản phẩm!', 'read', '2025-11-28 13:33:00'),
(23, 27, 'Khuyến mãi cuối tháng 11 đang diễn ra!', 'unread', '2025-11-28 18:18:00'),
(24, 28, 'Cập nhật trạng thái đơn hàng #2825.', 'read', '2025-11-29 08:22:00'),
(25, 30, 'Thêm ưu đãi mới dành riêng cho bạn!', 'unread', '2025-11-29 10:14:00'),
(26, 31, 'Sách bán chạy tuần này đã giảm giá!', 'read', '2025-11-29 14:44:00'),
(27, 33, 'Bạn có voucher đọc sách miễn phí 3 ngày!', 'unread', '2025-11-29 19:20:00'),
(28, 35, 'Bạn vừa đăng nhập từ thiết bị mới.', 'read', '2025-11-30 09:30:00'),
(29, 38, 'Flash sale 12.12 sắp bắt đầu!', 'unread', '2025-11-30 12:00:00'),
(30, 41, 'Bạn vừa nhận được điểm thưởng mới.', 'unread', '2025-11-30 17:15:00'),
(31, 43, 'Hôm nay có sách giảm tới 40%!', 'read', '2025-12-01 08:55:00'),
(32, 45, 'Ưu đãi độc quyền cho thành viên thân thiết.', 'unread', '2025-12-01 12:10:00'),
(33, 46, 'Mời bạn tham gia khảo sát người dùng.', 'read', '2025-12-01 15:45:00'),
(34, 47, 'Bạn có thông báo quan trọng.', 'unread', '2025-12-01 18:22:00'),
(35, 48, 'Voucher 30% đã được thêm vào ví của bạn.', 'read', '2025-12-02 08:00:00'),
(36, 49, 'Bạn đã mở khóa huy hiệu \"Người đọc tích cực\".', 'unread', '2025-12-02 11:11:00'),
(37, 50, 'Đơn hàng #5025 đang được xử lý.', 'unread', '2025-12-02 14:00:00'),
(38, 11, 'Sách mới ra mắt phù hợp với lịch sử xem của bạn.', 'read', '2025-12-02 18:40:00'),
(39, 12, 'Bạn có mã giảm giá sinh nhật!', 'unread', '2025-12-03 09:09:00'),
(40, 13, 'Notification hệ thống: bảo trì lúc 0h.', 'unread', '2025-12-03 11:55:00'),
(41, 16, 'Bạn nhận được mã giảm 15% cho sách mới ra mắt.', 'unread', '2025-12-03 18:10:00'),
(42, 18, 'Thông báo: sắp diễn ra Hội Sách Tháng 12.', 'unread', '2025-12-04 08:30:00'),
(43, 20, 'Bạn có mã freeship cho đơn tiếp theo.', 'read', '2025-12-04 10:22:00'),
(44, 21, 'Đơn hàng #2125 đang được vận chuyển.', 'unread', '2025-12-04 12:44:00'),
(45, 22, 'Sách bạn xem hôm qua đang giảm 25%.', 'read', '2025-12-04 15:11:00'),
(46, 24, 'Black Friday mở rộng thêm 2 ngày!', 'unread', '2025-12-04 18:20:00'),
(47, 25, 'Bạn đã mở khóa huy hiệu \"Đánh giá chăm chỉ\".', 'read', '2025-12-05 09:10:00'),
(48, 27, 'Voucher giảm 30% đã được thêm vào ví.', 'unread', '2025-12-05 12:00:00'),
(49, 28, 'Sách trending tuần này đã cập nhật.', 'read', '2025-12-05 13:33:00'),
(50, 30, 'Bạn có thông báo mới từ cửa hàng.', 'unread', '2025-12-05 15:20:00'),
(51, 31, 'Mời bạn đánh giá sản phẩm đã mua.', 'read', '2025-12-05 17:55:00'),
(52, 33, 'Thông báo ưu đãi độc quyền cho thành viên.', 'unread', '2025-12-06 08:40:00'),
(53, 35, 'Bạn nhận được mã quà tặng 20k.', 'read', '2025-12-06 10:55:00'),
(54, 38, 'Giảm mạnh sách kỹ năng cuối tuần.', 'unread', '2025-12-06 14:22:00'),
(55, 41, 'Đơn hàng #4125 đã được giao thành công.', 'read', '2025-12-06 18:33:00'),
(56, 43, 'Bạn có mã ưu đãi 12.12 trong ví.', 'unread', '2025-12-07 09:05:00'),
(57, 45, 'Lịch sử xem của bạn gợi ý nhiều sách phù hợp.', 'read', '2025-12-07 11:30:00'),
(58, 46, 'Thông báo: bảo trì hệ thống lúc 2h sáng.', 'unread', '2025-12-07 14:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `coupon_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `delivered_at` datetime DEFAULT NULL,
  `tracking_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `fullname`, `phone`, `total_amount`, `status`, `created_at`, `shipping_address`, `payment_method`, `shipping_fee`, `discount_amount`, `coupon_code`, `notes`, `delivered_at`, `tracking_number`, `updated_at`) VALUES
(1, 11, NULL, NULL, 585000.00, 'shipped', '2025-05-31 10:05:00', '22 Lê Đức Thọ, Phường 6, Quận Gò Vấp, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Gọi trước khi giao\n[19/04/2026 09:25 ADMIN]: HK BIT', NULL, 'TRACK000001', '2026-04-19 13:34:14'),
(2, 12, NULL, NULL, 155000.00, 'delivered', '2025-06-01 14:10:00', '105 Phạm Văn Chiêu, Phường 9, Quận Gò Vấp, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, 'WELCOME2025', 'Để hàng ở bảo vệ', NULL, 'TRACK000002', '2026-04-19 15:09:39'),
(3, 13, NULL, NULL, 405000.00, 'shipped', '2025-06-02 09:30:00', '60 Nguyễn Oanh, Phường 17, Quận Gò Vấp, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Giao buổi chiều', '2025-06-04 09:30:00', 'TRACK000003', '2026-04-19 15:09:39'),
(4, 14, NULL, NULL, 85000.00, 'pending', '2025-06-03 16:55:00', '88 Thống Nhất, Phường 11, Quận Gò Vấp, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, 'FREESHIP50', NULL, NULL, 'TRACK000004', '2026-04-19 15:09:39'),
(5, 15, NULL, NULL, 620000.00, 'delivered', '2025-06-04 11:20:00', '14 Lê Văn Thọ, Phường 8, Quận Gò Vấp, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao giờ hành chính', NULL, 'TRACK000005', '2026-04-19 15:09:39'),
(6, 16, NULL, NULL, 225000.00, 'delivered', '2025-06-05 08:45:00', '45 Nguyễn Văn Lượng, Phường 16, Quận Gò Vấp, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, 'SALE12_12', 'Gọi trước khi giao', NULL, 'TRACK000006', '2026-04-19 15:09:39'),
(7, 17, NULL, NULL, 310000.00, 'cancelled', '2025-06-06 13:00:00', '120 Phạm Ngũ Lão, Phường Phạm Ngũ Lão, Quận 1, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', NULL, 'TRACK000007', '2026-04-19 15:09:39'),
(8, 18, NULL, NULL, 195000.00, 'delivered', '2025-06-07 17:15:00', '99 Bùi Viện, Phường Phạm Ngũ Lão, Quận 1, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, 'BOOKLOVER', 'Giao buổi chiều', NULL, 'TRACK000008', '2026-04-19 15:09:39'),
(9, 19, NULL, NULL, 50000.00, 'shipped', '2025-06-08 10:30:00', '210 Trần Hưng Đạo, Phường Cô Giang, Quận 1, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, NULL, NULL, '2025-06-10 10:30:00', 'TRACK000009', '2026-04-19 15:09:39'),
(10, 20, NULL, NULL, 780000.00, 'delivered', '2025-06-09 15:40:00', '75 Nguyễn Trãi, Phường 2, Quận 5, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, 'VIP2025', 'Giao giờ hành chính', NULL, 'TRACK000010', '2026-04-19 15:09:39'),
(11, 21, NULL, NULL, 120000.00, 'pending', '2025-06-10 09:00:00', '188 Trần Bình Trọng, Phường 4, Quận 5, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, 'Gọi trước khi giao', NULL, 'TRACK000011', '2026-04-19 15:09:39'),
(12, 22, NULL, NULL, 345000.00, 'delivered', '2025-06-11 14:25:00', '40 Hồng Bàng, Phường 1, Quận 5, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, 'GIAM18', 'Để hàng ở bảo vệ', NULL, 'TRACK000012', '2026-04-19 15:09:39'),
(13, 23, NULL, NULL, 99000.00, 'shipped', '2025-06-12 18:40:00', '90 An Dương Vương, Phường 9, Quận 5, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, 'Giao buổi chiều', '2025-06-14 18:40:00', 'TRACK000013', '2026-04-19 15:09:39'),
(14, 24, NULL, NULL, 450000.00, 'delivered', '2025-06-13 07:50:00', '33 Nguyễn Chí Thanh, Phường 6, Quận 10, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, 'READY45', NULL, NULL, 'TRACK000014', '2026-04-19 15:09:39'),
(15, 25, NULL, NULL, 275000.00, 'delivered', '2025-06-14 12:15:00', '120 Ba Tháng Hai, Phường 12, Quận 10, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, NULL, 'Giao giờ hành chính', NULL, 'TRACK000015', '2026-04-19 15:09:39'),
(16, 26, NULL, NULL, 680000.00, 'shipped', '2025-06-15 16:30:00', '58 Sư Vạn Hạnh, Phường 9, Quận 10, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, 'FUN5', 'Gọi trước khi giao', '2025-06-17 16:30:00', 'TRACK000016', '2026-04-19 15:09:39'),
(17, 27, NULL, NULL, 105000.00, 'delivered', '2025-06-16 11:55:00', '102 Lạc Long Quân, Phường 3, Quận 11, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', NULL, 'TRACK000017', '2026-04-19 15:09:39'),
(18, 28, NULL, NULL, 51000.00, 'delivered', '2025-06-17 15:20:00', '210 Âu Cơ, Phường 10, Quận Tân Bình, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, 'SUNDAY8', 'Giao buổi chiều', NULL, 'TRACK000018', '2026-04-19 15:09:39'),
(19, 29, NULL, NULL, 390000.00, 'pending', '2025-06-18 08:05:00', '88 Trường Chinh, Phường 13, Quận Tân Bình, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, NULL, NULL, NULL, 'TRACK000019', '2026-04-19 15:09:39'),
(20, 30, NULL, NULL, 145000.00, 'cancelled', '2025-06-19 13:30:00', '15 Cộng Hòa, Phường 4, Quận Tân Bình, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, 'BLACKFRIDAY', 'Giao giờ hành chính', NULL, 'TRACK000020', '2026-04-19 15:09:39'),
(21, 31, NULL, NULL, 720000.00, 'delivered', '2025-06-20 17:45:00', '120 Hoàng Hoa Thám, Phường 12, Quận Tân Bình, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Gọi trước khi giao', NULL, 'TRACK000021', '2026-04-19 15:09:39'),
(22, 32, NULL, NULL, 200000.00, 'shipped', '2025-06-21 10:10:00', '77 Phạm Phú Thứ, Phường 11, Quận Tân Bình, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', '2025-06-23 10:10:00', 'TRACK000022', '2026-04-19 15:09:39'),
(23, 33, NULL, NULL, 49000.00, 'delivered', '2025-06-22 14:55:00', '50 Lũy Bán Bích, Phường Hòa Thạnh, Quận Tân Phú, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao buổi chiều', NULL, 'TRACK000023', '2026-04-19 15:09:39'),
(24, 34, NULL, NULL, 550000.00, 'pending', '2025-06-23 09:20:00', '140 Tân Kỳ Tân Quý, Phường Sơn Kỳ, Quận Tân Phú, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, NULL, NULL, 'TRACK000024', '2026-04-19 15:09:39'),
(25, 35, NULL, NULL, 170000.00, 'delivered', '2025-06-24 16:05:00', '99 Gò Dầu, Phường Tân Quý, Quận Tân Phú, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Giao giờ hành chính', NULL, 'TRACK000025', '2026-04-19 15:09:39'),
(26, 36, NULL, NULL, 305000.00, 'shipped', '2025-06-25 11:30:00', '210 Thoại Ngọc Hầu, Phường Phú Thạnh, Quận Tân Phú, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Gọi trước khi giao', '2025-06-27 11:30:00', 'TRACK000026', '2026-04-19 15:09:39'),
(27, 37, NULL, NULL, 88000.00, 'delivered', '2025-06-26 15:45:00', '35 Hòa Bình, Phường Hiệp Tân, Quận Tân Phú, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', NULL, 'TRACK000027', '2026-04-19 15:09:39'),
(28, 38, NULL, NULL, 650000.00, 'cancelled', '2025-06-27 08:10:00', '88 Kinh Dương Vương, Phường An Lạc, Quận Bình Tân, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao buổi chiều', NULL, 'TRACK000028', '2026-04-19 15:09:39'),
(29, 39, NULL, NULL, 115000.00, 'delivered', '2025-06-28 12:25:00', '120 Lê Văn Quới, Phường Bình Trị Đông, Quận Bình Tân, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, NULL, NULL, 'TRACK000029', '2026-04-19 15:09:39'),
(30, 40, NULL, NULL, 420000.00, 'shipped', '2025-06-29 16:40:00', '77 Tên Lửa, Phường Bình Trị Đông B, Quận Bình Tân, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao giờ hành chính', '2025-07-01 16:40:00', 'TRACK000030', '2026-04-19 15:09:39'),
(31, 41, NULL, NULL, 190000.00, 'delivered', '2025-06-30 10:00:00', '55 Nguyễn Thị Tú, Phường Bình Hưng Hòa, Quận Bình Tân, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Gọi trước khi giao', NULL, 'TRACK000031', '2026-04-19 15:09:39'),
(32, 42, NULL, NULL, 53000.00, 'pending', '2025-07-01 14:15:00', '160 Mã Lò, Phường Bình Trị Đông A, Quận Bình Tân, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', NULL, 'TRACK000032', '2026-04-19 15:09:39'),
(33, 43, NULL, NULL, 370000.00, 'delivered', '2025-07-02 09:35:00', '98 Phạm Văn Hai, Phường 2, Quận Tân Bình, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, 'Giao buổi chiều', NULL, 'TRACK000033', '2026-04-19 15:09:39'),
(34, 44, NULL, NULL, 160000.00, 'shipped', '2025-07-03 17:00:00', '12 Nguyễn Hồng Đào, Phường 14, Quận Tân Bình, TP Hồ Chí Minh', 'Banking', 20000.00, 0.00, NULL, NULL, '2025-07-05 17:00:00', 'TRACK000034', '2026-04-19 15:09:39'),
(35, 45, NULL, NULL, 750000.00, 'delivered', '2025-07-04 11:25:00', '230 Bàu Cát, Phường 12, Quận Tân Bình, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao giờ hành chính', NULL, 'TRACK000035', '2026-04-19 15:09:39'),
(36, 46, NULL, NULL, 95000.00, 'delivered', '2025-07-05 08:50:00', '66 Đồng Đen, Phường 10, Quận Tân Bình, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Gọi trước khi giao', NULL, 'TRACK000036', '2026-04-19 15:09:39'),
(37, 47, NULL, NULL, 280000.00, 'cancelled', '2025-07-06 13:05:00', '102 Cách Mạng Tháng 8, Phường 5, Quận 3, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Để hàng ở bảo vệ', NULL, 'TRACK000037', '2026-04-19 15:09:39'),
(38, 48, NULL, NULL, 41000.00, 'delivered', '2025-07-07 17:20:00', '55 Nguyễn Đình Chiểu, Phường 4, Quận 3, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Giao buổi chiều', NULL, 'TRACK000038', '2026-04-19 15:09:39'),
(39, 49, NULL, NULL, 580000.00, 'shipped', '2025-07-08 10:35:00', '88 Võ Văn Tần, Phường 6, Quận 3, TP Hồ Chí Minh', 'Momo', 20000.00, 0.00, NULL, NULL, '2025-07-10 10:35:00', 'TRACK000039', '2026-04-19 15:09:39'),
(40, 50, NULL, NULL, 130000.00, 'pending', '2025-07-09 15:50:00', '140 Trần Quốc Thảo, Phường 7, Quận 3, TP Hồ Chí Minh', 'COD', 20000.00, 0.00, NULL, 'Giao giờ hành chính', NULL, 'TRACK000040', '2026-04-19 15:09:39'),
(41, 5, NULL, NULL, 0.00, 'pending', '2025-12-09 07:49:02', '35 Điện Biên Phủ, Phường 15, Quận Bình Thạnh, TP Hồ Chí Minh', 'ZaloPay', 20000.00, 0.00, NULL, 'Gọi trước khi giao', NULL, 'TRACK000041', '2026-04-19 07:33:56'),
(42, 20, NULL, NULL, 285020.00, 'pending', '2026-04-17 16:28:17', '75 Nguyễn Trãi, Phường 2, Quận 5, TP Hồ Chí Minh', 'COD', 30000.00, 55980.00, NULL, NULL, NULL, NULL, '2026-04-19 15:09:39'),
(43, 109, NULL, NULL, 181290.00, 'cancelled', '2026-04-18 13:45:02', '123456, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 33210.00, NULL, '123456', NULL, NULL, '2026-04-19 07:33:56'),
(44, 109, NULL, NULL, 120000.00, 'cancelled', '2026-04-18 14:24:47', '123456, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(45, 109, NULL, NULL, 95000.00, 'cancelled', '2026-04-18 14:35:59', '5678, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(46, 109, NULL, NULL, 180000.00, 'cancelled', '2026-04-18 14:52:43', '123456, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(47, 109, NULL, NULL, 190000.00, 'cancelled', '2026-04-18 14:54:24', '123456, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(48, 109, NULL, NULL, 210400.00, 'cancelled', '2026-04-18 15:34:55', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 39600.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(49, 109, NULL, NULL, 180000.00, 'cancelled', '2026-04-18 15:39:17', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(50, 109, NULL, NULL, 180000.00, 'cancelled', '2026-04-18 15:59:12', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(51, 109, NULL, NULL, 100000.00, 'cancelled', '2026-04-18 16:03:21', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(52, 109, NULL, NULL, 214500.00, 'pending', '2026-04-18 16:11:24', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(53, 109, NULL, NULL, 100000.00, 'cancelled', '2026-04-18 16:13:50', '56780345, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(54, 110, NULL, NULL, 214500.00, 'cancelled', '2026-04-19 04:53:06', '12345678, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:33:56'),
(55, 110, 'Huỳnh Minh Thư', '0911111111', 180000.00, 'cancelled', '2026-04-19 07:37:52', '5678, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 14:15:55'),
(56, 110, NULL, NULL, 214500.00, 'pending', '2026-04-19 07:48:06', '12345678, 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 07:48:06'),
(57, 110, NULL, NULL, 100000.00, 'pending', '2026-04-19 08:56:14', NULL, 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 08:56:14'),
(58, 110, NULL, NULL, 115000.00, 'cancelled', '2026-04-19 13:52:16', NULL, 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 14:05:22'),
(59, 110, 'Huỳnh Minh Thư', '902000020', 214500.00, 'pending', '2026-04-19 21:05:08', '123456789, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 14:26:39'),
(60, 110, 'Huỳnh Minh Thư', '902000020', 190000.00, 'pending', '2026-04-19 21:30:50', '123, phường 13, Q.10, TP.HCM', 'COD', 30000.00, 0.00, NULL, NULL, NULL, NULL, '2026-04-19 14:39:40');

--
-- Triggers `orders`
--
DROP TRIGGER IF EXISTS `trg_prevent_order_banned_user`;
DELIMITER $$
CREATE TRIGGER `trg_prevent_order_banned_user` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE user_status VARCHAR(50);

    -- Lấy status của user
    SELECT status INTO user_status
    FROM USERS
    WHERE user_id = NEW.user_id;

    -- Nếu bị banned → chặn insert
    IF user_status = 'banned' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'User is banned. Cannot create order.';
    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 5, 2, 120000.00),
(2, 1, 12, 1, 85000.00),
(3, 2, 3, 1, 99000.00),
(4, 2, 18, 3, 150000.00),
(5, 3, 7, 2, 110000.00),
(6, 3, 9, 1, 80000.00),
(7, 4, 22, 1, 130000.00),
(8, 4, 11, 2, 125000.00),
(9, 5, 14, 1, 95000.00),
(10, 5, 20, 4, 140000.00),
(11, 6, 1, 3, 89000.00),
(12, 6, 15, 1, 119000.00),
(13, 7, 8, 2, 105000.00),
(14, 7, 33, 1, 99000.00),
(15, 8, 40, 1, 135000.00),
(16, 8, 41, 2, 145000.00),
(17, 9, 2, 2, 115000.00),
(18, 9, 29, 1, 99000.00),
(19, 10, 10, 1, 78000.00),
(20, 10, 31, 3, 150000.00),
(21, 11, 50, 2, 160000.00),
(22, 11, 37, 1, 108000.00),
(23, 12, 44, 1, 98000.00),
(24, 12, 16, 1, 122000.00),
(25, 13, 13, 2, 118000.00),
(26, 13, 6, 1, 96000.00),
(27, 14, 4, 3, 89000.00),
(28, 14, 52, 1, 132000.00),
(29, 15, 27, 2, 99000.00),
(30, 15, 19, 1, 88000.00),
(31, 16, 55, 1, 145000.00),
(32, 16, 60, 2, 155000.00),
(33, 17, 58, 1, 120000.00),
(34, 17, 59, 1, 99000.00),
(35, 18, 61, 3, 140000.00),
(36, 18, 62, 2, 135000.00),
(37, 19, 63, 1, 138000.00),
(38, 19, 64, 1, 132000.00),
(39, 20, 65, 2, 140000.00),
(40, 20, 66, 1, 150000.00),
(41, 21, 67, 1, 152000.00),
(42, 21, 68, 2, 145000.00),
(43, 22, 69, 1, 99000.00),
(44, 22, 70, 3, 125000.00),
(45, 23, 71, 2, 130000.00),
(46, 23, 72, 1, 118000.00),
(47, 24, 73, 1, 143000.00),
(48, 24, 74, 2, 156000.00),
(49, 25, 75, 3, 165000.00),
(50, 25, 76, 1, 148000.00),
(51, 26, 77, 1, 127000.00),
(52, 26, 78, 2, 132000.00),
(53, 27, 79, 1, 99000.00),
(54, 27, 80, 2, 119000.00),
(55, 28, 81, 3, 150000.00),
(56, 28, 82, 1, 110000.00),
(57, 29, 83, 1, 120000.00),
(58, 29, 84, 2, 99000.00),
(59, 30, 85, 2, 160000.00),
(60, 30, 86, 1, 143000.00),
(61, 31, 87, 1, 149000.00),
(62, 31, 88, 2, 138000.00),
(63, 32, 89, 3, 154000.00),
(64, 32, 90, 1, 118000.00),
(65, 33, 91, 1, 144000.00),
(66, 33, 92, 2, 99000.00),
(67, 34, 93, 1, 123000.00),
(68, 34, 94, 1, 126000.00),
(69, 35, 95, 2, 112000.00),
(70, 35, 96, 1, 99000.00),
(71, 36, 97, 3, 135000.00),
(72, 36, 98, 1, 155000.00),
(73, 37, 99, 1, 110000.00),
(74, 37, 100, 2, 99000.00),
(75, 1, 5, 2, 130000.00),
(76, 42, 29, 2, 36000.00),
(77, 42, 30, 1, 39000.00),
(78, 42, 31, 1, 39000.00),
(79, 42, 32, 1, 33000.00),
(80, 42, 33, 1, 30000.00),
(81, 42, 34, 1, 30000.00),
(82, 42, 35, 1, 35000.00),
(83, 42, 36, 1, 33000.00),
(84, 43, 176, 1, 184500.00),
(85, 44, 152, 1, 90000.00),
(86, 45, 162, 1, 65000.00),
(87, 46, 149, 1, 150000.00),
(88, 47, 157, 1, 160000.00),
(89, 48, 154, 2, 110000.00),
(90, 49, 149, 1, 150000.00),
(91, 50, 149, 1, 150000.00),
(92, 51, 155, 1, 70000.00),
(93, 52, 176, 1, 184500.00),
(94, 53, 155, 1, 70000.00),
(95, 54, 176, 1, 184500.00),
(96, 55, 149, 1, 150000.00),
(97, 56, 176, 1, 184500.00),
(98, 57, 155, 1, 70000.00),
(99, 58, 165, 1, 85000.00),
(100, 59, 176, 1, 184500.00),
(101, 60, 157, 1, 160000.00);

--
-- Triggers `order_items`
--
DROP TRIGGER IF EXISTS `trg_update_sold_quantity`;
DELIMITER $$
CREATE TRIGGER `trg_update_sold_quantity` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE BOOKS
    SET sold_quantity = sold_quantity + NEW.quantity
    WHERE book_id = NEW.book_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `book_id`, `rating`, `comment`, `created_at`) VALUES
(1, 11, 2, 5, 'Sách cực kỳ thực tế, đọc xong áp dụng ngay được trong giao tiếp hàng ngày. Rất đáng tiền!', '2025-06-02 08:15:00'),
(2, 12, 7, 4, 'Nhiều ví dụ hài hước, dễ hiểu. Mình thích phần về vòng lặp thói quen.', '2025-06-03 14:20:00'),
(3, 13, 12, 5, 'Dễ thương cực kỳ, con mình 7 tuổi đọc mê mẩn cả tuần. Cảm ơn shop!', '2025-06-04 19:30:00'),
(4, 15, 37, 3, 'Nội dung ổn nhưng mình thấy hơi dài dòng ở một số chương.', '2025-06-06 10:10:00'),
(5, 16, 57, 5, 'Nam Cao thì khỏi bàn rồi, bản in đẹp, giấy tốt, giao nhanh!', '2025-06-07 07:45:00'),
(6, 18, 38, 4, 'Doraemon đỉnh chóp luôn.', '2025-06-09 12:20:00'),
(7, 20, 1, 5, 'Đắc Nhân Tâm mà chưa đọc thì phí cả đời. Cực kỳ recommend!', '2025-06-11 09:55:00'),
(8, 21, 4, 5, 'Đọc 3 lần vẫn thấy mới. Sách thay đổi tư duy của mình hoàn toàn.', '2025-06-12 16:40:00'),
(9, 24, 39, 2, 'Mình hơi thất vọng, nội dung không như kỳ vọng, có lẽ không hợp gu.', '2025-06-15 11:25:00'),
(10, 25, 59, 5, 'Cực hay! Mua cho con mà cả nhà cùng đọc, rất đáng tiền.', '2025-06-16 20:10:00'),
(11, 27, 79, 4, 'Nhiều bài học sâu sắc, đặc biệt phần về sự tha thứ.', '2025-06-18 14:30:00'),
(12, 31, 63, 5, 'Nguyễn Nhật Ánh không bao giờ làm mình thất vọng. Tuổi thơ ùa về!', '2025-06-22 10:05:00'),
(13, 35, 96, 3, 'Bình thường, có lẽ hợp với học sinh hơn là người lớn.', '2025-06-26 17:15:00'),
(14, 41, 53, 5, 'Cuốn này phải nói là “đỉnh của chóp” trong mảng kỹ năng sống!', '2025-07-02 13:40:00'),
(15, 45, 2, 4, 'Đã đọc bản cũ, mua bản mới tặng bạn. Vẫn hay như ngày nào.', '2025-07-06 09:20:00'),
(16, 12, 51, 5, 'Sách cực kỳ truyền cảm hứng, đọc xong muốn thay đổi bản thân ngay.', '2025-07-07 09:10:00'),
(17, 13, 52, 4, 'Văn phong nhẹ nhàng, phù hợp để đọc mỗi tối.', '2025-07-07 14:22:00'),
(18, 15, 54, 5, 'Quá hay, kiến thức mới mẻ và rất thực tế.', '2025-07-08 08:33:00'),
(19, 16, 55, 3, 'Nội dung ổn nhưng hơi lan man ở chương giữa.', '2025-07-08 17:45:00'),
(20, 18, 56, 4, 'Sách hay, giao nhanh, đóng gói cẩn thận.', '2025-07-09 10:12:00'),
(21, 20, 58, 5, 'Tác giả phân tích rất sâu, cực kỳ đáng đọc.', '2025-07-09 18:50:00'),
(22, 21, 60, 4, 'Nội dung phù hợp với người đi làm.', '2025-07-10 13:40:00'),
(23, 22, 61, 5, 'Rất hữu ích, đọc xong thông suốt luôn.', '2025-07-10 20:10:00'),
(24, 24, 62, 4, 'Ngắn gọn, súc tích và đầy đủ.', '2025-07-11 07:55:00'),
(25, 25, 64, 5, 'Truyện thiếu nhi nhưng người lớn đọc cũng mê.', '2025-07-11 16:25:00'),
(26, 27, 65, 3, 'Hơi khó đọc nhưng ý nghĩa.', '2025-07-12 11:50:00'),
(27, 28, 66, 5, 'Tuyệt vời, rất phù hợp làm quà tặng.', '2025-07-12 19:00:00'),
(28, 30, 67, 4, 'Chất lượng in tốt, chữ to dễ đọc.', '2025-07-13 09:35:00'),
(29, 31, 68, 5, 'Nội dung xuất sắc, mình đọc 2 lần rồi.', '2025-07-13 17:22:00'),
(30, 33, 69, 4, 'Giá tốt, nội dung hay, giao nhanh.', '2025-07-14 08:40:00'),
(31, 35, 70, 5, 'Cuốn sách đáng có trong tủ của mọi người.', '2025-07-14 15:50:00'),
(32, 38, 71, 4, 'Phù hợp để học kỹ năng mềm.', '2025-07-15 10:05:00'),
(33, 41, 72, 5, 'Thông tin bổ ích, mình rất thích.', '2025-07-15 19:15:00'),
(34, 43, 73, 4, 'Nội dung lôi cuốn từ đầu tới cuối.', '2025-07-16 08:55:00'),
(35, 45, 74, 5, 'Siêu hay luôn, đọc phát nghiện.', '2025-07-16 13:20:00'),
(36, 46, 75, 3, 'Cũng được, không quá nổi bật.', '2025-07-17 09:10:00'),
(37, 47, 76, 4, 'Sách phù hợp với sinh viên.', '2025-07-17 14:44:00'),
(38, 48, 77, 5, 'Nội dung tuyệt đỉnh, đáng để đọc.', '2025-07-18 11:11:00'),
(39, 49, 78, 4, 'Tổng hợp kiến thức khá ổn.', '2025-07-18 18:33:00'),
(40, 50, 80, 5, 'Rất hài lòng, shop giao siêu nhanh.', '2025-07-19 09:09:00'),
(41, 11, 81, 4, 'Cốt truyện nhẹ nhàng dễ thương.', '2025-07-19 16:16:00'),
(42, 12, 82, 5, 'Thơm mùi sách mới, chất lượng in rất đẹp.', '2025-07-20 08:25:00'),
(43, 13, 83, 4, 'Hay nhưng hơi ít hình minh hoạ.', '2025-07-20 13:55:00'),
(44, 15, 84, 5, 'Từ vựng dễ hiểu, phù hợp người mới.', '2025-07-21 09:33:00'),
(45, 16, 85, 5, 'Quá tuyệt, 10/10.', '2025-07-21 19:50:00'),
(46, 18, 86, 4, 'Sách rất dễ hiểu, phù hợp người mới bắt đầu.', '2025-07-22 09:10:00'),
(47, 20, 87, 5, 'Tuyệt vời, kiến thức thực chiến nhiều.', '2025-07-22 14:30:00'),
(48, 21, 88, 3, 'Cũng ổn nhưng hơi khô.', '2025-07-23 08:55:00'),
(49, 22, 89, 5, 'Rất hay, đáng đọc.', '2025-07-23 17:12:00'),
(50, 24, 90, 4, 'Đọc nhẹ nhàng, rất thư giãn.', '2025-07-24 10:20:00'),
(51, 25, 91, 5, 'Sách cực kỳ ý nghĩa, nội dung sâu sắc.', '2025-07-24 18:00:00'),
(52, 27, 92, 4, 'Bố cục rõ ràng, kiến thức mới.', '2025-07-25 09:33:00'),
(53, 28, 93, 5, 'Câu chuyện rất truyền cảm hứng.', '2025-07-25 15:22:00'),
(54, 30, 94, 4, 'Chất lượng in tốt.', '2025-07-26 08:20:00'),
(55, 31, 95, 5, 'Tuyệt đỉnh, mình sẽ đọc lại lần 2.', '2025-07-26 14:00:00'),
(56, 33, 97, 4, 'Kiến thức hữu ích cho người đi làm.', '2025-07-27 07:45:00'),
(57, 35, 98, 5, 'Ngôn ngữ dễ hiểu, ví dụ thuyết phục.', '2025-07-27 16:10:00'),
(58, 38, 99, 5, 'Quá hay, mình đọc suốt đêm.', '2025-07-28 09:25:00'),
(59, 41, 100, 4, 'Sách ổn, phù hợp làm tài liệu tham khảo.', '2025-07-28 17:20:00'),
(60, 43, 51, 5, 'Cuốn này mình tìm lâu rồi, rất hài lòng.', '2025-07-29 08:55:00'),
(61, 45, 52, 4, 'Nội dung súc tích và logic.', '2025-07-29 14:33:00'),
(62, 46, 53, 3, 'Không hợp gu mình lắm.', '2025-07-30 09:15:00'),
(63, 47, 54, 5, 'Rất hay, nên đọc ít nhất một lần.', '2025-07-30 17:40:00'),
(64, 48, 55, 4, 'Câu chữ đẹp, truyền cảm.', '2025-07-31 10:10:00'),
(65, 49, 56, 5, 'Quyển này xứng đáng 10/10.', '2025-07-31 19:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('a7t5ixzxmOfxYpZOzP4UjesmAuicZgCinMF2p7dm', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJFeEJYMkFWRlBhS242ZzR1M2hUZ01ZYmtBUWxrcTB6UXpxVGoweU5LIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDFcL2FkbWluXC9vcmRlcnNcLzU0P3BhZ2U9MiZzdGF0dXM9Y2FuY2VsbGVkIiwicm91dGUiOiJhZG1pbi5vcmRlcnMuc2hvdyJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1776611984);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `password_hashed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `PASSWORD` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'user',
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `points` int DEFAULT '0',
  `membership_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_vietnamese_ci DEFAULT 'regular',
  `remember_token` varchar(100) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  KEY `idx_users_username` (`username`),
  KEY `idx_users_email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hashed`, `PASSWORD`, `email`, `fullname`, `role`, `status`, `created_at`, `phone`, `birthdate`, `gender`, `last_login`, `points`, `membership_level`, `remember_token`) VALUES
(1, 'admin_user', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'admin@shop.com', 'Nguyễn Trọng Thắng', 'Admin', 'Active', '2025-01-01 10:00:00', '0901000001', '1991-01-01', 'female', NULL, 37, 'regular', NULL),
(2, 'manager_a', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'manager.a@shop.com', 'Lê Thị Thuỳ Dung', 'Manager', 'Active', '2025-01-05 14:30:00', '0901000002', '1992-01-01', 'other', '2026-01-07 18:51:00', 74, 'regular', NULL),
(3, 'manager_b', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'manager.b@shop.com', 'Trần Văn Long', 'Manager', 'Active', '2025-01-10 09:00:00', '0901000003', '1993-01-01', 'male', NULL, 111, 'regular', NULL),
(4, 'support_c', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'support.c@shop.com', 'Phạm Minh Duy', 'Manager', 'Active', '2025-01-15 16:45:00', '0901000004', '1994-01-01', 'female', '2026-01-05 18:51:00', 148, 'regular', NULL),
(5, 'admin_hr', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'hr@shop.com', 'Hoàng Thanh Ngọc', 'Admin', 'Inactive', '2025-01-20 11:20:00', '0901000005', '1995-01-01', 'other', NULL, 185, 'regular', NULL),
(6, 'manager_ops', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'ops@shop.com', 'Võ Đình Hiếu', 'Manager', 'Active', '2025-01-25 13:10:00', '0901000006', '1996-01-01', 'male', '2026-01-03 18:51:00', 222, 'regular', NULL),
(7, 'manager_sale', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'sale@shop.com', 'Bùi Thị Hồng', 'Manager', 'Active', '2025-01-30 08:40:00', '0901000007', '1997-01-01', 'female', NULL, 259, 'regular', NULL),
(8, 'accountant', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'acc@shop.com', 'Đỗ Minh Quâ', 'Manager', 'Active', '2025-02-05 17:00:00', '0901000008', '1998-01-01', 'other', '2026-01-01 18:51:00', 296, 'regular', NULL),
(9, 'super_admi', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'superadmin@shop.com', 'Phan Văn Tài', 'Admin', 'Active', '2025-02-10 10:55:00', '0901000009', '1999-01-01', 'male', NULL, 444, 'gold', NULL),
(10, 'marketing_d', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'marketing@shop.com', 'Ngô Hoàng Yế', 'Manager', 'Inactive', '2025-02-15 15:30:00', '0901000010', '2000-01-01', 'female', '2025-12-30 18:51:00', 370, 'gold', NULL),
(11, 'customer_11', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_11@mail.com', 'Trịnh Xuân Hùng', 'Customer', 'Active', '2025-03-01 07:15:00', '0901000011', '2001-01-01', 'other', NULL, 407, 'gold', NULL),
(12, 'customer_12', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_12@mail.com', 'Vũ Ngọc La', 'Customer', 'Active', '2025-03-02 12:40:00', '0901000012', '2002-01-01', 'male', '2025-12-28 18:51:00', 444, 'gold', NULL),
(13, 'customer_13', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_13@mail.com', 'Dương Chí Dũng', 'Customer', 'Active', '2025-03-03 18:05:00', '0901000013', '2003-01-01', 'female', NULL, 481, 'gold', NULL),
(14, 'customer_14', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_14@mail.com', 'Bạch Mai Phương', 'Customer', 'Active', '2025-03-04 09:50:00', '0901000014', '2004-01-01', 'other', '2025-12-26 18:51:00', 518, 'gold', NULL),
(15, 'customer_15', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_15@mail.com', 'Đặng Quang Huy', 'Customer', 'Active', '2025-03-05 14:15:00', '0901000015', '2005-01-01', 'male', NULL, 555, 'gold', NULL),
(16, 'customer_16', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_16@mail.com', 'Tô Thị Kim', 'Customer', 'Active', '2025-03-06 11:30:00', '0901000016', '2006-01-01', 'female', '2025-12-24 18:51:00', 592, 'gold', NULL),
(17, 'customer_17', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_17@mail.com', 'Huỳnh Thế Anh', 'Customer', 'Active', '2025-03-07 16:55:00', '0901000017', '2007-01-01', 'other', NULL, 629, 'gold', NULL),
(18, 'customer_18', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_18@mail.com', 'Nguyễn Phương Nhi', 'Customer', 'Active', '2025-03-08 10:20:00', '0901000018', '2008-01-01', 'male', '2025-12-22 18:51:00', 666, 'gold', NULL),
(19, 'customer_19', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_19@mail.com', 'Lý Văn Thành', 'Customer', 'Active', '2025-03-09 15:45:00', '0901000019', '2009-01-01', 'female', NULL, 703, 'vip', NULL),
(20, 'customer_20', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_20@mail.com', 'Trần Huyền Trang', 'Customer', 'Active', '2025-03-10 08:00:00', '0901000020', '2010-01-01', 'other', '2025-12-20 18:51:00', 740, 'vip', NULL),
(21, 'customer_21', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_21@mail.com', 'Phạm Quang Vinh', 'Customer', 'Active', '2025-03-11 13:25:00', '0901000021', '2011-01-01', 'male', NULL, 777, 'vip', NULL),
(22, 'customer_22', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_22@mail.com', 'Đoàn Thị Thủy', 'Customer', 'Active', '2025-03-12 18:50:00', '0901000022', '2012-01-01', 'female', '2025-12-18 18:51:00', 814, 'vip', NULL),
(23, 'customer_23', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_23@mail.com', 'Lưu Đình Kiê', 'Customer', 'Active', '2025-03-13 07:35:00', '0901000023', '2013-01-01', 'other', NULL, 851, 'vip', NULL),
(24, 'customer_24', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_24@mail.com', 'Võ Thanh Tú', 'Customer', 'Active', '2025-03-14 12:00:00', '0901000024', '2014-01-01', 'male', '2025-12-16 18:51:00', 888, 'vip', NULL),
(25, 'customer_25', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_25@mail.com', 'Nguyễn Bá Sơ', 'Customer', 'Active', '2025-03-15 17:25:00', '0901000025', '2015-01-01', 'female', NULL, 925, 'vip', NULL),
(26, 'customer_26', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_26@mail.com', 'Hoàng Ánh Ngọc', 'Customer', 'Active', '2025-03-16 09:10:00', '0901000026', '2016-01-01', 'other', '2025-12-14 18:51:00', 962, 'vip', NULL),
(27, 'customer_27', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_27@mail.com', 'Mai Hồng Phát', 'Customer', 'Active', '2025-03-17 14:35:00', '0901000027', '2017-01-01', 'male', NULL, 999, 'vip', NULL),
(28, 'customer_28', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_28@mail.com', 'Lê Văn Duẩ', 'Customer', 'Active', '2025-03-18 11:00:00', '0901000028', '2018-01-01', 'female', '2025-12-12 18:51:00', 36, 'regular', NULL),
(29, 'customer_29', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_29@mail.com', 'Trần Thị Mai', 'Customer', 'Active', '2025-03-19 16:25:00', '0901000029', '2019-01-01', 'other', NULL, 73, 'regular', NULL),
(30, 'customer_30', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_30@mail.com', 'Đinh Quốc Việt', 'Customer', 'Inactive', '2025-03-20 08:50:00', '0901000030', '1990-01-01', 'male', '2026-01-09 18:51:00', 110, 'regular', NULL),
(31, 'customer_31', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_31@mail.com', 'Nguyễn Hải Nam', 'Customer', 'Active', '2025-03-21 13:15:00', '0901000031', '1991-01-01', 'female', NULL, 147, 'regular', NULL),
(32, 'customer_32', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_32@mail.com', 'Phan Thị Kim Ngâ', 'Customer', 'Active', '2025-03-22 17:40:00', '0901000032', '1992-01-01', 'other', '2026-01-07 18:51:00', 184, 'regular', NULL),
(33, 'customer_33', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_33@mail.com', 'Võ Văn Dũng', 'Customer', 'Active', '2025-03-23 09:25:00', '0901000033', '1993-01-01', 'male', NULL, 221, 'regular', NULL),
(34, 'customer_34', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_34@mail.com', 'Hồ Ngọc Diệp', 'Customer', 'Active', '2025-03-24 14:50:00', '0901000034', '1994-01-01', 'female', '2026-01-05 18:51:00', 258, 'regular', NULL),
(35, 'customer_35', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_35@mail.com', 'Trần Quốc Bảo', 'Customer', 'Active', '2025-03-25 11:15:00', '0901000035', '1995-01-01', 'other', NULL, 295, 'regular', NULL),
(36, 'customer_36', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_36@mail.com', 'Lê Thanh Thúy', 'Customer', 'Active', '2025-03-26 16:40:00', '0901000036', '1996-01-01', 'male', '2026-01-03 18:51:00', 332, 'gold', NULL),
(37, 'customer_37', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_37@mail.com', 'Đỗ Trọng Nghĩa', 'Customer', 'Active', '2025-03-27 08:25:00', '0901000037', '1997-01-01', 'female', NULL, 369, 'gold', NULL),
(38, 'customer_38', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_38@mail.com', 'Nguyễn Hồng Hạnh', 'Customer', 'Active', '2025-03-28 13:50:00', '0901000038', '1998-01-01', 'other', '2026-01-01 18:51:00', 406, 'gold', NULL),
(39, 'customer_39', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_39@mail.com', 'Phạm Văn Cường', 'Customer', 'Active', '2025-03-29 18:15:00', '0901000039', '1999-01-01', 'male', NULL, 443, 'gold', NULL),
(40, 'customer_40', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_40@mail.com', 'Lý Kim Chi', 'Customer', 'Active', '2025-03-30 07:00:00', '0901000040', '2000-01-01', 'female', '2025-12-30 18:51:00', 480, 'gold', NULL),
(41, 'customer_41', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_41@mail.com', 'Hoàng Quốc Đại', 'Customer', 'Active', '2025-04-01 12:25:00', '0901000041', '2001-01-01', 'other', NULL, 517, 'gold', NULL),
(42, 'customer_42', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_42@mail.com', 'Mai Hồng Nga', 'Customer', 'Active', '2025-04-02 17:50:00', '0901000042', '2002-01-01', 'male', '2025-12-28 18:51:00', 554, 'gold', NULL),
(43, 'customer_43', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_43@mail.com', 'Bùi Thanh Liêm', 'Customer', 'Active', '2025-04-03 09:35:00', '0901000043', '2003-01-01', 'female', NULL, 591, 'gold', NULL),
(44, 'customer_44', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_44@mail.com', 'Vũ Quỳnh Hương', 'Customer', 'Active', '2025-04-04 14:00:00', '0901000044', '2004-01-01', 'other', '2025-12-26 18:51:00', 628, 'gold', NULL),
(45, 'customer_45', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_45@mail.com', 'Trần Minh Khang', 'Customer', 'Active', '2025-04-05 11:45:00', '0901000045', '2005-01-01', 'male', NULL, 665, 'gold', NULL),
(46, 'customer_46', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_46@mail.com', 'Nguyễn Thị Yế', 'Customer', 'Active', '2025-04-06 16:10:00', '0901000046', '2006-01-01', 'female', '2025-12-24 18:51:00', 702, 'vip', NULL),
(47, 'customer_47', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_47@mail.com', 'Đinh Tuấn Kiệt', 'Customer', 'Active', '2025-04-07 08:35:00', '0901000047', '2007-01-01', 'other', NULL, 739, 'vip', NULL),
(48, 'customer_48', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_48@mail.com', 'Lê Phương Anh', 'Customer', 'Active', '2025-04-08 13:00:00', '0901000048', '2008-01-01', 'male', '2025-12-22 18:51:00', 776, 'vip', NULL),
(49, 'customer_49', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_49@mail.com', 'Phan Văn Minh', 'Customer', 'Active', '2025-04-09 17:25:00', '0901000049', '2009-01-01', 'female', NULL, 813, 'vip', NULL),
(50, 'customer_50', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_50@mail.com', 'Hà Thị Tuyết', 'Customer', 'Active', '2025-04-10 09:50:00', '0901000050', '2010-01-01', 'other', '2025-12-20 18:51:00', 850, 'vip', NULL),
(51, 'customer_51', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_51@mail.com', 'Nguyễn Anh Khoa', 'Customer', 'Active', '2025-04-11 14:15:00', '0901000051', '2011-01-01', 'male', NULL, 887, 'vip', NULL),
(52, 'customer_52', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_52@mail.com', 'Trần Huyền My', 'Customer', 'Active', '2025-04-12 11:30:00', '0901000052', '2012-01-01', 'female', '2025-12-18 18:51:00', 924, 'vip', NULL),
(53, 'customer_53', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_53@mail.com', 'Võ Minh Trí', 'Customer', 'Inactive', '2025-04-13 16:55:00', '0901000053', '2013-01-01', 'other', NULL, 961, 'vip', NULL),
(54, 'customer_54', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_54@mail.com', 'Lê Kim Cương', 'Customer', 'Active', '2025-04-14 10:20:00', '0901000054', '2014-01-01', 'male', '2025-12-16 18:51:00', 998, 'vip', NULL),
(55, 'customer_55', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_55@mail.com', 'Hoàng Quốc Trung', 'Customer', 'Active', '2025-04-15 15:45:00', '0901000055', '2015-01-01', 'female', NULL, 35, 'regular', NULL),
(56, 'customer_56', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_56@mail.com', 'Bùi Thị Dung', 'Customer', 'Active', '2025-04-16 08:00:00', '0901000056', '2016-01-01', 'other', '2025-12-14 18:51:00', 72, 'regular', NULL),
(57, 'customer_57', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_57@mail.com', 'Mai Văn Tấ', 'Customer', 'Active', '2025-04-17 13:25:00', '0901000057', '2017-01-01', 'male', NULL, 109, 'regular', NULL),
(58, 'customer_58', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_58@mail.com', 'Đinh Thị Loa', 'Customer', 'Active', '2025-04-18 18:50:00', '0901000058', '2018-01-01', 'female', '2025-12-12 18:51:00', 146, 'regular', NULL),
(59, 'customer_59', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_59@mail.com', 'Phạm Hữu Đức', 'Customer', 'Active', '2025-04-19 07:35:00', '0901000059', '2019-01-01', 'other', NULL, 183, 'regular', NULL),
(60, 'customer_60', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_60@mail.com', 'Trần Mai Linh', 'Customer', 'Active', '2025-04-20 12:00:00', '0901000060', '1990-01-01', 'male', '2026-01-09 18:51:00', 220, 'regular', NULL),
(61, 'customer_61', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_61@mail.com', 'Nguyễn Đình Khoa', 'Customer', 'Active', '2025-04-21 17:25:00', '0901000061', '1991-01-01', 'female', NULL, 257, 'regular', NULL),
(62, 'customer_62', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_62@mail.com', 'Lý Thu Hằng', 'Customer', 'Active', '2025-04-22 09:10:00', '0901000062', '1992-01-01', 'other', '2026-01-07 18:51:00', 294, 'regular', NULL),
(63, 'customer_63', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_63@mail.com', 'Văn Đức Huy', 'Customer', 'Active', '2025-04-23 14:35:00', '0901000063', '1993-01-01', 'male', NULL, 331, 'gold', NULL),
(64, 'customer_64', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_64@mail.com', 'Dương Thị Hoà', 'Customer', 'Active', '2025-04-24 11:00:00', '0901000064', '1994-01-01', 'female', '2026-01-05 18:51:00', 368, 'gold', NULL),
(65, 'customer_65', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_65@mail.com', 'Bùi Anh Tuấ', 'Customer', 'Active', '2025-04-25 16:25:00', '0901000065', '1995-01-01', 'other', NULL, 405, 'gold', NULL),
(66, 'customer_66', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_66@mail.com', 'Phạm Thu Nguyệt', 'Customer', 'Active', '2025-04-26 08:50:00', '0901000066', '1996-01-01', 'male', '2026-01-03 18:51:00', 442, 'gold', NULL),
(67, 'customer_67', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_67@mail.com', 'Lê Minh Cường', 'Customer', 'Active', '2025-04-27 13:15:00', '0901000067', '1997-01-01', 'female', NULL, 479, 'gold', NULL),
(68, 'customer_68', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_68@mail.com', 'Trần Văn Long', 'Customer', 'Active', '2025-04-28 17:40:00', '0901000068', '1998-01-01', 'other', '2026-01-01 18:51:00', 516, 'gold', NULL),
(69, 'customer_69', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_69@mail.com', 'Đỗ Thị Ngọc', 'Customer', 'Active', '2025-04-29 09:25:00', '0901000069', '1999-01-01', 'male', NULL, 553, 'gold', NULL),
(70, 'customer_70', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_70@mail.com', 'Nguyễn Quang Minh', 'Customer', 'Active', '2025-04-30 14:50:00', '0901000070', '2000-01-01', 'female', '2025-12-30 18:51:00', 590, 'gold', NULL),
(71, 'customer_71', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_71@mail.com', 'Vũ Đình Khải', 'Customer', 'Pending', '2025-05-01 11:15:00', '0901000071', '2001-01-01', 'other', NULL, 627, 'gold', NULL),
(72, 'customer_72', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_72@mail.com', 'Hoàng Thị Vâ', 'Customer', 'Active', '2025-05-02 16:40:00', '0901000072', '2002-01-01', 'male', '2025-12-28 18:51:00', 664, 'gold', NULL),
(73, 'customer_73', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_73@mail.com', 'Mai Hữu Tài', 'Customer', 'Active', '2025-05-03 08:25:00', '0901000073', '2003-01-01', 'female', NULL, 701, 'vip', NULL),
(74, 'customer_74', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_74@mail.com', 'Lê Kim Ngâ', 'Customer', 'Active', '2025-05-04 13:50:00', '0901000074', '2004-01-01', 'other', '2025-12-26 18:51:00', 738, 'vip', NULL),
(75, 'customer_75', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_75@mail.com', 'Phạm Văn Trung', 'Customer', 'Active', '2025-05-05 18:15:00', '0901000075', '2005-01-01', 'male', NULL, 775, 'vip', NULL),
(76, 'customer_76', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_76@mail.com', 'Trần Ngọc Hằng', 'Customer', 'Active', '2025-05-06 07:00:00', '0901000076', '2006-01-01', 'female', '2025-12-24 18:51:00', 812, 'vip', NULL),
(77, 'customer_77', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_77@mail.com', 'Đặng Thanh Tùng', 'Customer', 'Active', '2025-05-07 12:25:00', '0901000077', '2007-01-01', 'other', NULL, 849, 'vip', NULL),
(78, 'customer_78', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_78@mail.com', 'Nguyễn Thùy Linh', 'Customer', 'Active', '2025-05-08 17:50:00', '0901000078', '2008-01-01', 'male', '2025-12-22 18:51:00', 886, 'vip', NULL),
(79, 'customer_79', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_79@mail.com', 'Lý Văn Bách', 'Customer', 'Active', '2025-05-09 09:35:00', '0901000079', '2009-01-01', 'female', NULL, 923, 'vip', NULL),
(80, 'customer_80', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_80@mail.com', 'Võ Thị Trâm', 'Customer', 'Active', '2025-05-10 14:00:00', '0901000080', '2010-01-01', 'other', '2025-12-20 18:51:00', 960, 'vip', NULL),
(81, 'customer_81', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_81@mail.com', 'Hoàng Minh Nhật', 'Customer', 'Active', '2025-05-11 11:45:00', '0901000081', '2011-01-01', 'male', NULL, 997, 'vip', NULL),
(82, 'customer_82', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_82@mail.com', 'Bùi Kim Anh', 'Customer', 'Active', '2025-05-12 16:10:00', '0901000082', '2012-01-01', 'female', '2025-12-18 18:51:00', 34, 'regular', NULL),
(83, 'customer_83', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_83@mail.com', 'Mai Đình Phong', 'Customer', 'Active', '2025-05-13 08:35:00', '0901000083', '2013-01-01', 'other', NULL, 71, 'regular', NULL),
(84, 'customer_84', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_84@mail.com', 'Đinh Thanh Hương', 'Customer', 'Active', '2025-05-14 13:00:00', '0901000084', '2014-01-01', 'male', '2025-12-16 18:51:00', 108, 'regular', NULL),
(85, 'customer_85', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_85@mail.com', 'Phạm Duy Anh', 'Customer', 'Active', '2025-05-15 17:25:00', '0901000085', '2015-01-01', 'female', NULL, 145, 'regular', NULL),
(86, 'customer_86', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_86@mail.com', 'Trần Hải Yế', 'Customer', 'Active', '2025-05-16 09:50:00', '0901000086', '2016-01-01', 'other', '2025-12-14 18:51:00', 182, 'regular', NULL),
(87, 'customer_87', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_87@mail.com', 'Nguyễn Quang Huy', 'Customer', 'Active', '2025-05-17 14:15:00', '0901000087', '2017-01-01', 'male', NULL, 219, 'regular', NULL),
(88, 'customer_88', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_88@mail.com', 'Lý Thị Thanh', 'Customer', 'Active', '2025-05-18 11:30:00', '0901000088', '2018-01-01', 'female', '2025-12-12 18:51:00', 256, 'regular', NULL),
(89, 'customer_89', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_89@mail.com', 'Võ Văn Nam', 'Customer', 'Active', '2025-05-19 16:55:00', '0901000089', '2019-01-01', 'other', NULL, 293, 'regular', NULL),
(90, 'customer_90', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_90@mail.com', 'Dương Thúy Nga', 'Customer', 'Inactive', '2025-05-20 10:20:00', '0901000090', '1990-01-01', 'male', '2026-01-09 18:51:00', 330, 'gold', NULL),
(91, 'customer_91', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_91@mail.com', 'Bùi Minh Khôi', 'Customer', 'Active', '2025-05-21 15:45:00', '0901000091', '1991-01-01', 'female', NULL, 367, 'gold', NULL),
(92, 'customer_92', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_92@mail.com', 'Phạm Phương Thảo', 'Customer', 'Active', '2025-05-22 08:00:00', '0901000092', '1992-01-01', 'other', '2026-01-07 18:51:00', 404, 'gold', NULL),
(93, 'customer_93', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_93@mail.com', 'Lê Đình Trọng', 'Customer', 'Active', '2025-05-23 13:25:00', '0901000093', '1993-01-01', 'male', NULL, 441, 'gold', NULL),
(94, 'customer_94', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_94@mail.com', 'Trần Mai Chi', 'Customer', 'Active', '2025-05-24 18:50:00', '0901000094', '1994-01-01', 'female', '2026-01-05 18:51:00', 478, 'gold', NULL),
(95, 'customer_95', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_95@mail.com', 'Đỗ Văn Quyết', 'Customer', 'Active', '2025-05-25 07:35:00', '0901000095', '1995-01-01', 'other', NULL, 515, 'gold', NULL),
(96, 'customer_96', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_96@mail.com', 'Nguyễn Thị Hoa', 'Customer', 'Active', '2025-05-26 12:00:00', '0901000096', '1996-01-01', 'male', '2026-01-03 18:51:00', 552, 'gold', NULL),
(97, 'customer_97', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_97@mail.com', 'Vũ Quang Vinh', 'Customer', 'Active', '2025-05-27 17:25:00', '0901000097', '1997-01-01', 'female', NULL, 589, 'gold', NULL),
(98, 'customer_98', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_98@mail.com', 'Hoàng Tuyết Mai', 'Customer', 'Active', '2025-05-28 09:10:00', '0901000098', '1998-01-01', 'other', '2026-01-01 18:51:00', 626, 'gold', NULL),
(99, 'customer_99', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_99@mail.com', 'Mai Văn Lợi', 'Customer', 'Active', '2025-05-29 14:35:00', '0901000099', '1999-01-01', 'male', NULL, 663, 'gold', NULL),
(100, 'customer_100', '$2y$12$y.uM92zUvfADc1qIEkqifuG/WamKbl81hmbo74.nduZFaZ97igy2m', '123456abc', 'customer_100@mail.com', 'Lê Thị Hương', 'Customer', 'Pending', '2025-05-30 11:00:00', '0901000100', '2000-01-01', 'female', '2025-12-30 18:51:00', 700, 'vip', NULL),
(110, 'hmt612', '$2y$12$8N7dacnZBPxJ4EpfUxhtSejuZ3ig60ctaL.VqG5fw9jOFNMElApqK', '123456abc', 'hmt06122005@gmail.com', 'Huỳnh Minh Thư', 'Customer', 'Active', '2026-04-19 00:09:59', '0911111112', NULL, NULL, NULL, 0, 'regular', 'eACLJOmicHOWdik5rBtTofxTDjFKMeor1AtkjH48SXN7CgaumqOtmFMU6Cll');

--
-- Triggers `users`
--
DROP TRIGGER IF EXISTS `trg_users_prevent_delete_admin`;
DELIMITER $$
CREATE TRIGGER `trg_users_prevent_delete_admin` BEFORE DELETE ON `users` FOR EACH ROW BEGIN
    IF OLD.role = 'Admin' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Không thể xoá tài khoản Admin!';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_book_order_count`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_book_order_count`;
CREATE TABLE IF NOT EXISTS `vw_book_order_count` (
`book_id` int
,`title` varchar(255)
,`author` varchar(255)
,`order_count` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_order_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_order_summary`;
CREATE TABLE IF NOT EXISTS `vw_order_summary` (
`order_id` int
,`customer_name` varchar(255)
,`total_amount` decimal(12,2)
,`status` varchar(50)
,`created_at` datetime
,`item_count` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_user_orders`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `vw_user_orders`;
CREATE TABLE IF NOT EXISTS `vw_user_orders` (
`user_id` int
,`username` varchar(100)
,`fullname` varchar(255)
,`role` varchar(50)
,`status` varchar(50)
,`handled_orders` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `wishlist_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  PRIMARY KEY (`wishlist_id`),
  UNIQUE KEY `user_id` (`user_id`,`book_id`),
  KEY `idx_wishlist_user` (`user_id`),
  KEY `idx_wishlist_book` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `book_id`) VALUES
(1, 1, 1),
(2, 1, 10),
(3, 1, 20),
(4, 2, 5),
(5, 2, 50),
(6, 3, 12),
(7, 3, 80),
(8, 3, 99),
(9, 4, 44),
(10, 5, 71),
(11, 5, 85),
(12, 6, 30),
(13, 7, 60),
(14, 7, 61),
(15, 8, 137),
(16, 9, 90),
(17, 9, 91),
(18, 9, 92),
(19, 10, 100),
(20, 10, 2),
(21, 11, 15),
(22, 11, 25),
(23, 12, 35),
(24, 13, 45),
(25, 13, 146),
(26, 13, 47),
(27, 14, 55),
(28, 14, 65),
(29, 15, 75),
(30, 16, 88),
(31, 16, 89),
(32, 17, 95),
(33, 17, 3),
(34, 18, 18),
(35, 18, 28),
(36, 19, 38),
(37, 20, 48),
(38, 21, 58),
(39, 21, 68),
(40, 21, 78),
(41, 22, 8),
(42, 23, 17),
(43, 23, 27),
(44, 24, 37),
(45, 24, 147),
(46, 25, 57),
(47, 26, 67),
(48, 26, 77),
(49, 27, 87),
(50, 27, 97),
(51, 28, 7),
(52, 29, 16),
(53, 30, 26),
(54, 30, 36),
(55, 31, 146),
(56, 31, 56),
(57, 32, 6),
(58, 33, 11),
(59, 33, 22),
(60, 33, 33),
(61, 34, 44),
(62, 35, 55),
(63, 36, 66),
(64, 36, 77),
(65, 37, 88),
(66, 38, 99),
(67, 38, 10),
(68, 39, 21),
(69, 39, 32),
(70, 40, 43),
(71, 41, 54),
(72, 42, 65),
(73, 42, 76),
(74, 43, 87),
(75, 44, 98),
(76, 44, 9),
(77, 45, 19),
(78, 45, 29),
(79, 46, 39),
(80, 46, 49),
(81, 47, 59),
(82, 48, 69),
(83, 48, 79),
(84, 49, 89),
(85, 49, 99),
(86, 50, 9),
(87, 51, 12),
(88, 51, 23),
(89, 52, 34),
(90, 53, 45),
(91, 53, 56),
(92, 53, 67),
(93, 54, 78),
(94, 55, 89),
(95, 55, 100),
(96, 56, 110),
(97, 57, 11),
(98, 58, 21),
(99, 58, 31),
(100, 59, 41),
(101, 59, 51),
(102, 60, 61),
(103, 61, 71),
(104, 61, 81),
(105, 61, 91),
(106, 62, 10),
(107, 63, 20),
(108, 64, 30),
(109, 65, 40),
(110, 65, 50),
(111, 66, 60),
(112, 67, 70),
(113, 67, 80),
(114, 68, 90),
(115, 69, 92),
(116, 69, 93),
(117, 70, 94),
(118, 71, 95),
(119, 71, 96),
(120, 72, 97),
(121, 73, 98),
(122, 74, 99),
(123, 74, 100),
(124, 75, 148),
(125, 75, 2),
(126, 76, 3),
(127, 76, 4),
(128, 76, 5),
(129, 77, 6),
(130, 78, 7),
(131, 78, 8),
(132, 79, 9),
(133, 80, 10),
(134, 80, 11),
(135, 81, 12),
(136, 82, 13),
(137, 82, 14),
(138, 83, 15),
(139, 83, 16),
(140, 84, 17),
(141, 84, 18),
(142, 84, 19),
(143, 85, 20),
(144, 86, 21),
(145, 87, 22),
(146, 87, 23),
(147, 88, 24),
(148, 89, 25),
(149, 89, 26),
(150, 90, 27),
(151, 91, 28),
(152, 92, 29),
(153, 93, 30),
(154, 93, 31),
(155, 94, 32),
(156, 95, 33),
(157, 95, 34),
(158, 96, 35),
(159, 97, 36),
(160, 97, 37),
(161, 98, 38),
(162, 98, 39),
(163, 99, 40),
(164, 99, 41),
(165, 100, 42),
(172, 110, 155),
(167, 109, 149),
(171, 110, 162),
(173, 110, 149),
(174, 110, 176),
(175, 110, 152),
(176, 110, 3),
(177, 110, 11);

-- --------------------------------------------------------

--
-- Structure for view `vw_book_order_count`
--
DROP TABLE IF EXISTS `vw_book_order_count`;

DROP VIEW IF EXISTS `vw_book_order_count`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_book_order_count`  AS SELECT `b`.`book_id` AS `book_id`, `b`.`title` AS `title`, `b`.`author` AS `author`, count(distinct `oi`.`order_id`) AS `order_count` FROM (`books` `b` left join `order_items` `oi` on((`b`.`book_id` = `oi`.`book_id`))) GROUP BY `b`.`book_id`, `b`.`title`, `b`.`author` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_order_summary`
--
DROP TABLE IF EXISTS `vw_order_summary`;

DROP VIEW IF EXISTS `vw_order_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_order_summary`  AS SELECT `o`.`order_id` AS `order_id`, `u`.`fullname` AS `customer_name`, `o`.`total_amount` AS `total_amount`, `o`.`status` AS `status`, `o`.`created_at` AS `created_at`, count(`oi`.`order_item_id`) AS `item_count` FROM ((`orders` `o` left join `users` `u` on((`o`.`user_id` = `u`.`user_id`))) left join `order_items` `oi` on((`o`.`order_id` = `oi`.`order_id`))) GROUP BY `o`.`order_id`, `u`.`fullname`, `o`.`total_amount`, `o`.`status`, `o`.`created_at` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_user_orders`
--
DROP TABLE IF EXISTS `vw_user_orders`;

DROP VIEW IF EXISTS `vw_user_orders`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_user_orders`  AS SELECT `u`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`fullname` AS `fullname`, `u`.`role` AS `role`, `u`.`status` AS `status`, count(`o`.`order_id`) AS `handled_orders` FROM (`users` `u` left join `orders` `o` on((`u`.`user_id` = `o`.`user_id`))) GROUP BY `u`.`user_id`, `u`.`username`, `u`.`fullname`, `u`.`role`, `u`.`status` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
