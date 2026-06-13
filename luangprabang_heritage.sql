-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 07:01 PM
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
-- Database: `luangprabang_heritage`
--

-- --------------------------------------------------------

--
-- Table structure for table `heritage_categories`
--

CREATE TABLE `heritage_categories` (
  `category_id` int(11) NOT NULL,
  `category_name_lo` varchar(100) DEFAULT NULL,
  `category_name_en` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `heritage_categories`
--

INSERT INTO `heritage_categories` (`category_id`, `category_name_lo`, `category_name_en`) VALUES
(1, 'ເຮືອນພື້ນເມືອງ', 'Traditional House'),
(2, 'ວັດວາອາຮາມ', 'Temple'),
(3, 'ອາຄານສະໄໝຝຣັ່ງ', 'French Colonial Building'),
(4, 'ອາຄານສະໄໝລາວ-ຝຣັ່ງ', 'Lao-French Architecture'),
(5, 'ຮ້ານຄ້າພື້ນເມືອງ', 'Traditional Shop House');

-- --------------------------------------------------------

--
-- Table structure for table `heritage_houses`
--

CREATE TABLE `heritage_houses` (
  `house_id` int(11) NOT NULL,
  `qr_code` varchar(100) NOT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `house_name_lo` varchar(255) DEFAULT NULL,
  `house_name_en` varchar(255) DEFAULT NULL,
  `owner_name_lo` varchar(255) DEFAULT NULL,
  `owner_name_en` varchar(255) DEFAULT NULL,
  `construction_year` int(11) DEFAULT NULL,
  `architectural_style_lo` varchar(255) DEFAULT NULL,
  `architectural_style_en` varchar(255) DEFAULT NULL,
  `historical_significance_lo` text DEFAULT NULL,
  `historical_significance_en` text DEFAULT NULL,
  `description_lo` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `house_type` varchar(100) DEFAULT NULL,
  `building_material` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `heritage_houses`
--

INSERT INTO `heritage_houses` (`house_id`, `qr_code`, `house_number`, `house_name_lo`, `house_name_en`, `owner_name_lo`, `owner_name_en`, `construction_year`, `architectural_style_lo`, `architectural_style_en`, `historical_significance_lo`, `historical_significance_en`, `description_lo`, `description_en`, `image_main`, `status`, `house_type`, `building_material`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(28, '001', '01', 'ຊຽງທອງ', 'Xieng Thong', '', '', 1937, '', '', 'ໃນອະດີດ ຊາວລາວນິຍົມສ້າງເຮືອນດ້ວຍໄມ້ ເນື່ອງຈາກເປັນວັດສະດຸທີ່ຫາໄດ້ງ່າຍໃນທ້ອງຖິ່ນ ແລະ ເໝາະກັບສະພາບອາກາດຮ້ອນຊຸ່ມ. ເຮືອນສ່ວນໃຫຍ່ຈະຍົກພື້ນສູງ ເພື່ອປ້ອງກັນນ້ຳຖ້ວມ ສັດມີພິດ ແລະ ຊ່ວຍໃຫ້ອາກາດໄຫຼຜ່ານໄດ້ດີ.\r\n\r\nສຳລັບເຮືອນແບບຫຼວງພະບາງ ຈະມີການຜະສົມຜະສານລະຫວ່າງສິນລະປະລາວດັ້ງເດີມ ແລະ ສະຖາປັດຕະຍະກຳຝຣັ່ງ ໃນສະໄໝອານານິຄົມ ຈຶ່ງເຫັນການໃຊ້ຝາປູນສີຂາວ ຮ່ວມກັບໂຄງສ້າງໄມ້ ແລະ ຫຼັງຄາຊົງສູງ.', '', 'ເຮືອນໃນຮູບເປັນເຮືອນໄມ້ແບບດັ້ງເດີມຂອງລາວ ຫຼື ເຮືອນແບບຫຼວງພະບາງ ທີ່ມີການຜະສົມຜະສານລະຫວ່າງສະຖາປັດຕະຍະກຳພື້ນເມືອງ ແລະ ຄວາມເປັນທຳມະຊາດ ໂດຍມີລາຍລະອຽດດັ່ງນີ້:\r\n\r\nໂຄງສ້າງເຮືອນເຮັດດ້ວຍໄມ້ ເພາະໄມ້ເປັນວັດສະດຸຫຼັກທີ່ນິຍົມໃນການກໍ່ສ້າງເຮືອນດັ້ງເດີມ ຊ່ວຍໃຫ້ເຢັນ ແລະ ລະບາຍອາກາດໄດ້ດີ\r\nຫຼັງຄາເປັນຮູບສາມຫຼ່ຽມຊ່ວຍລະບາຍນ້ຳຝົນໄດ້ດີ ແລະ ເໝາະກັບອາກາດຮ້ອນຊຸ່ມ\r\nດ້ານໜ້າມີລະບຽງ ຫຼື ຊານເຮືອນ ໃຊ້ເປັນບ່ອນພັກຜ່ອນ ນັ່ງຮັບລົມ ແລະ ຕ້ອນຮັບແຂກ\r\nມີຮົ້ວໄມ້ລ້ອມຮອບ ເພື່ອແບ່ງເຂດບ້ານ ແລະ ເພີ່ມຄວາມງາມ\r\nຮອບເຮືອນມີຕົ້ນໄມ້ ແລະ ພືດສີຂຽວຫຼາຍ ຊ່ວຍໃຫ້ບັນຍາກາດຮົ່ມເຢັນ ແລະ ສອດຄ່ອງກັບວິຖີຊີວິດແບບທຳມະຊາດ\r\n+ ຫຼັງການບູລະນະບາງສ່ວນຂອງເຮືອນໃຊ້ຝາປູນສີຂາວຮ່ວມກັບໄມ້ ສະແດງເຖິງການປັບປຸງແບບຮ່ວມສະໄໝ ແຕ່ຍັງຮັກສາເອກະລັກດັ້ງເດີມ\r\nຮູບທີສອງເປັນເຮືອນສູງສອງຊັ້ນ ມີພື້ນທີ່ໂລ່ງດ້ານລຸ່ມ ເປັນລັກສະນະເຮືອນຍົກພື້ນ ເພື່ອປ້ອງກັນນ້ຳຖ້ວມ ແລະ ຊ່ວຍໃຫ້ອາກາດໄຫຼຜ່ານໄດ້ດີ', '', '1779245836_6a0d230cc17a7.jpeg', 'active', 'ຫຼັງຄາດ່ຽວ', 'ໄມ້', NULL, NULL, '2026-05-20 02:57:16', '2026-05-20 02:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `heritage_images`
--

CREATE TABLE `heritage_images` (
  `image_id` int(11) NOT NULL,
  `house_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_caption_lo` varchar(255) DEFAULT NULL,
  `image_caption_en` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `heritage_images`
--

INSERT INTO `heritage_images` (`image_id`, `house_id`, `image_path`, `image_caption_lo`, `image_caption_en`, `display_order`) VALUES
(7, 28, '1779245836_6a0d230cc2a53_0.png', 'ຮູບພາບເກົ່າ ໃນຊ່ວງປີ 2000', '', 0),
(8, 28, '1779245836_6a0d230cc3761_1.png', 'ປີກໍ່ສ້າງ', '', 1),
(9, 28, '1779245836_6a0d230cc4390_2.png', 'ທີ່ຕັ້ງ ແລະ ຂອບເຂດ', '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `house_categories`
--

CREATE TABLE `house_categories` (
  `house_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname_lo` varchar(100) DEFAULT NULL,
  `fullname_en` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff','viewer') DEFAULT 'viewer',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `fullname_lo`, `fullname_en`, `email`, `role`, `status`, `created_at`) VALUES
(15, 'ນ່າ', '$2y$10$RWG4QjBNoSmJDf4/nGCFfOSFB1u3fNX5wiixoqa38.OyJAOmZDtBS', 'ນ່າ', '', '', 'staff', 'active', '2026-05-20 02:28:20'),
(16, 'n', '$2y$10$YdeesmnnDCDSfnXkiTwIru7OU.HdLJes5MExkJe/udEAneKeKp526', 'nar', '', '', 'staff', 'active', '2026-05-20 02:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `visit_logs`
--

CREATE TABLE `visit_logs` (
  `log_id` int(11) NOT NULL,
  `house_id` int(11) DEFAULT NULL,
  `visitor_ip` varchar(45) DEFAULT NULL,
  `visitor_device` varchar(255) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visit_logs`
--

INSERT INTO `visit_logs` (`log_id`, `house_id`, `visitor_ip`, `visitor_device`, `visit_date`, `visit_time`) VALUES
(3, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22', '19:16:18'),
(4, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22', '19:16:57'),
(5, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-22', '19:22:38'),
(50, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20', '04:57:48'),
(51, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-20', '09:27:22'),
(52, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:33:22'),
(53, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:37:31'),
(54, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:37:31'),
(55, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:46:52'),
(56, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:46:53'),
(57, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '17:46:53'),
(58, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:00:14'),
(59, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:00:14'),
(60, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:00:15'),
(61, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:01:00'),
(62, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:02:53'),
(63, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:02:53'),
(64, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:03:36'),
(65, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:06:18'),
(66, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:08:00'),
(67, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:08:00'),
(68, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:10:35'),
(69, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:11:21'),
(70, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:11:38'),
(71, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:19:42'),
(72, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:20:00'),
(73, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:22:43'),
(74, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:23:25'),
(75, 28, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-07', '18:23:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `heritage_categories`
--
ALTER TABLE `heritage_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `heritage_houses`
--
ALTER TABLE `heritage_houses`
  ADD PRIMARY KEY (`house_id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`);

--
-- Indexes for table `heritage_images`
--
ALTER TABLE `heritage_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `heritage_images_ibfk_1` (`house_id`);

--
-- Indexes for table `house_categories`
--
ALTER TABLE `house_categories`
  ADD PRIMARY KEY (`house_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `visit_logs`
--
ALTER TABLE `visit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `house_id` (`house_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `heritage_categories`
--
ALTER TABLE `heritage_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `heritage_houses`
--
ALTER TABLE `heritage_houses`
  MODIFY `house_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `heritage_images`
--
ALTER TABLE `heritage_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `visit_logs`
--
ALTER TABLE `visit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `heritage_images`
--
ALTER TABLE `heritage_images`
  ADD CONSTRAINT `heritage_images_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `heritage_houses` (`house_id`) ON DELETE CASCADE;

--
-- Constraints for table `house_categories`
--
ALTER TABLE `house_categories`
  ADD CONSTRAINT `house_categories_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `heritage_houses` (`house_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `house_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `heritage_categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_logs`
--
ALTER TABLE `visit_logs`
  ADD CONSTRAINT `visit_logs_ibfk_1` FOREIGN KEY (`house_id`) REFERENCES `heritage_houses` (`house_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
