-- Adminer 4.8.1 MySQL 8.0.43-0ubuntu0.24.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `colleges`;
CREATE TABLE `colleges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `percentile_between` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `college_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `overall_percentile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fees` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `highest_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deadline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `colleges` (`id`, `percentile_between`, `college_name`, `overall_percentile`, `fees`, `highest_package`, `average_package`, `deadline`, `whatsapp_no`, `created_at`, `updated_at`) VALUES
(1,	'97-99',	'JBIMS - Jamnalal Bajaj Institute of Management Studies',	'99+',	'6.035 Lakhs',	'87.12 LPA',	'26.12 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(2,	'97-99',	'Indian Institute of Management, Calcutta',	'99+',	'31-33 Lakhs',	'01.20 CPA',	'35.07 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(3,	'97-99',	'Indian Institute of Management, Bangalore',	'99+',	'24.05 Lakhs',	'01.10 CPA',	'35.92 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(4,	'97-99',	'Indian Institute of Management, Ahmedabad',	'99+',	'27.50 Lakhs',	'01.15 CPA',	'34.63-36.2 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(5,	'97-99',	'Indian Institute of Management, Indore',	'98+',	'21.17 Lakhs',	'40 LPA',	'25 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(6,	'97-99',	'Indian Institute of Management, Lucknow',	'98+',	'20.75 Lakhs',	'123 LPA',	'30-32.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(7,	'97-99',	'IIFT',	'98+',	'21.9 Lakhs',	'85.4 LPA',	'27.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(8,	'97-99',	'Indian Institute of Technology (IIT), Bombay (Shailesh J Mehta)',	'98+',	'14.00 Lakhs',	'49.00 LPA',	'29.44 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(9,	'97-99',	'Faculty of Management Studies, Delhi',	'98+',	'2.32 Lakhs',	'123 LPA',	'34.1 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(10,	'97-99',	'Indian Institute of Management, Kozhikode',	'97+',	'20.05 Lakhs',	'72.02 LPA',	'28.05 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(11,	'97-99',	'Indian Institute Of Management Rohtak',	'97+',	'19.04 Lakhs',	'36 LPA',	'18.73 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(12,	'95-97',	'Indian Institute of Technology (IIT), Delhi',	'96-97+',	'12.4 Lakhs',	'41.13 LPA',	'23.40 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(13,	'95-97',	'TISS',	'95+',	'24.59 Lakhs',	'36.25 LPA',	'28.20 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(14,	'95-97',	'IIT Kharagpur, Vinod Gupta School of Management (VGSOM)',	'95+',	'12.64 Lakhs',	'37.07 LPA',	'20.83 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(15,	'95-97',	'Management Development Institute (MDI), Gurgaon',	'95+',	'25.99 Lakhs',	'53.6 LPA',	'26.20 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(16,	'95-97',	'SP. Jain Institute of Management and Research (SPJIMR)',	'95+',	'26.50 Lakhs',	'81 LPA',	'33 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(17,	'95-97',	'Indian Institute of Management, Mumbai (NITIE)',	'95-97+',	'21.00 Lakhs',	'49.00 LPA',	'33.84 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(18,	'90-95',	'Xavier Institute of Management (XIMB)',	'94+',	'25 Lakhs',	'30 LPA',	'19.53 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(19,	'90-95',	'Indian Institute of Management, Sirmaur',	'94+',	'16.80 Lakhs',	'64.12 LPA',	'13.30 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(20,	'90-95',	'Indian Institute of Management, Sambalpur',	'94+',	'21.00 Lakhs',	'48.60 LPA',	'15.65 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(21,	'90-95',	'IIndian Institute of Management, Nagpur',	'94+',	'18.90 Lakhs',	'69.57 LPA',	'18.07 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(22,	'90-95',	'Indian Institute of Management, Jammu',	'94+',	'18.86 Lakhs',	'31.75 LPA',	'15.48 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(23,	'90-95',	'Indian Institute of Management, Bodh Gaya',	'94+',	'17.96 Lakhs',	'32.5 LPA',	'13.9 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(24,	'90-95',	'Indian Institute of Technology (IIT), Roorkee',	'93+',	'09.68 Lakhs',	'26 LPA',	'18.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(25,	'90-95',	'Indian Institute of Technology (IIT), Kanpur',	'90+',	'04.35 Lakhs',	'24.00 LPA',	'18.2 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(26,	'90-95',	'Indian Institute of Management, Lucknow',	'90+',	'20.07 Lakhs',	'123 LPA',	'32.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(27,	'90-95',	'GIM',	'90+',	'20.04 Lakhs',	'32.20 LPA',	'15.13 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(28,	'90-95',	'Masters’ Union',	'90-95+',	'29.3 Lakhs',	'61.80 LPA',	'28.52 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(29,	'80-90',	'FORE School of Management, New Delhi',	'85+',	'20.77 Lakhs',	'70 LPA',	'16.01 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(30,	'80-90',	'K J Somaiya Institute of Management',	'85+',	'20.87 Lakhs',	'28.5 LPA',	'Top 100-17.34 LPA|Top 200-15.8 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(31,	'80-90',	'Lal Bahadur Shastri Institute of Management, New Delhi',	'83+',	'17.25 Lakhs',	'20 LPA',	'12.24 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(32,	'80-90',	'NIBM, Pune',	'85+',	'16 Lakhs',	'26.51 LPA',	'14.23 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(33,	'80-90',	'XISS, Ranchi',	'85+',	'8-8.9 Lakhs',	'21 LPA',	'9.36 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(34,	'80-90',	'Welingkar Institute of Management, Mumbai',	'85+',	'14 Lakhs',	'40 LPA',	'11.67 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(35,	'80-90',	'SDA Bocconi Asia Center',	'85+',	'20.75 Lakhs',	'36.5 LPA',	'15.01 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(36,	'80-90',	'NIT, Trichy',	'85+',	'2.87 Lakhs',	'19 LPA',	'9.40 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(37,	'80-90',	'BIM Trichy: Bharathidasan Institute of Management',	'85+',	'17.68 Lakhs',	'35.26 LPA',	'10.54 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(38,	'80-90',	'Indian Institute of Management, Amritsar',	'85+',	'21.00 Lakhs',	'58.52 LPA',	'19.73 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(39,	'80-90',	'Indian Institute of Management, Visakhapatnam',	'82+',	'18.38 Lakhs',	'32.65 LPA',	'16.61 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(40,	'80-90',	'XIME Bangalore',	'80+',	'12.5 Lakhs',	'22 LPA',	'11.2 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(41,	'80-90',	'SOIL Institute of Management',	'80+',	'15.3 Lakhs',	'22 LPA',	'11.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(42,	'80-90',	'NIRMA',	'80+',	'12.09 Lakhs',	'70 LPA',	'11.2 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(43,	'80-90',	'National Insurance Academy Pune',	'80+',	'11 Lakhs',	'22 LPA',	'12.3 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(44,	'80-90',	'LIBA, Chennai',	'80+',	'29 Lakhs',	'21 LPA',	'-',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(45,	'80-90',	'IBA Bangalore',	'80+',	'10.3 Lakhs',	'22.6 LPA',	'7.90 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(46,	'70-80',	'Welingkar Bangalore',	'75+',	'14 Lakhs',	'17.63 LPA',	'11.11 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(47,	'70-80',	'Lal Bahadur Shastri Institute of Management, New Delhi',	'75+',	'16.5 Lakhs',	'16.67 LPA',	'12.24 LPA',	'-',	'8850712170',	'2025-10-27 01:44:19',	'2025-10-27 02:15:45'),
(48,	'70-80',	'ISME Bangalore',	'70-80+',	'10.45 Lakhs',	'14 LPA',	'8 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(49,	'70-80',	'BML Munjal',	'70-80+',	'14.6 Lakhs',	'14.86 LPA',	'9.2 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(50,	'70-80',	'IILM',	'70-80+',	'12.3 Lakhs Fees',	'24 LPA',	'8.6 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(51,	'70-80',	'MYRA School of Business',	'70-80+',	'11 Lakhs',	'16 LPA',	'8.64 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(52,	'70-80',	'RCB Bangalore',	'70-80+',	'-',	'-',	'-',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(53,	'70-80',	'ABBS (Acharya, Bangalore)',	'70-75+',	'8.9 Lakhs',	'22.5 LPA',	'7.5 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(54,	'70-80',	'IIEBM Pune',	'75-80+',	'12.9 Lakhs',	'30 LPA',	'10 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(55,	'70-80',	'SDMIMD Mysore',	'75-80+',	'12-13 Lakhs',	'14 LPA',	'10 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(56,	'70-80',	'Woxsen University, Hyderabad',	'75-80+',	'20.14 Lakhs',	'19 LPA',	'9.05 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(57,	'70-80',	'Alliance University, Bangalore',	'75-80+',	'15 Lakhs',	'38.05 LPA',	'8.5 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(58,	'70-80',	'ISBR Bangalore',	'75-80+',	'11 Lakhs',	'16.42 LPA',	'8 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(59,	'70-80',	'ISBR Bangalore',	'75+',	'11 Lakhs',	'16.42 LPA',	'8 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(60,	'70-80',	'Birla Institute of Technology & Science (BITS) Pilani',	'70-75',	'11.40 Lakhs',	'22 LPA',	'15.9 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(61,	'70-80',	'Aditya School of Business Management',	'70-75',	'11 Lakhs',	'25 LPA',	'8.64 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(62,	'70-80',	'National Insurance Academy Pune',	'70-75',	'11 Lakhs',	'22 LPA',	'12.3 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(63,	'70-80',	'UPES Dehradun',	'70-75',	'14.39 Lakhs',	'27.83 LPA',	'18 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(64,	'70-80',	'RCB Bangalore',	'70-80',	'-',	'-',	'-',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(65,	'60-70',	'Bharatiya Vidya Bhavan\'s Usha & Lakshmi Mittal Institute of Management (BULMIM)',	'60-65',	'6 Lakhs',	'15.20 LPA',	'4.50 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(66,	'60-70',	'Gitam School of International Business (GSIB) Visakhapatnam',	'60-65',	'10.19 Lakhs',	'14 LPA',	'6.5 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(67,	'60-70',	'J K Business School Gurgaon',	'60-65',	'7.99 Lakhs',	'Not Available',	'7 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(68,	'60-70',	'Vedica Scholars Programme for Women',	'60-70',	'12.98 Lakhs',	'22 LPA',	'10 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(69,	'60-70',	'Fortune Institute of International Business (FIIB) New Delhi',	'65-70',	'10.3 Lakhs',	'25 LPA',	'10.4 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(70,	'60-70',	'GITAM Hyderabad Business School (GHBS)',	'65-70',	'10.19 Lakhs',	'13.59 LPA',	'4.34 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(71,	'60-70',	'International Institute of Management Studies (IIMS) Pune',	'65-70',	'9.27 Lakhs',	'26.10 LPA',	'7.50 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(72,	'60-70',	'Lexicon Management Institute of Leadership and Excellence (MILE) Pune',	'65-70',	'15.6 Lakhs',	'Not Available',	'8.27 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(73,	'60-70',	'Siva Sivani Institute of Management (SSIM) Secunderabad',	'65-70',	'6.9 Lakhs',	'13 LPA',	'7.44 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(74,	'60-70',	'St. Joseph Institute of Management, Bangalore',	'65-70',	'9.3 Lakhs',	'Not Available',	'8 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(75,	'60-70',	'Vishwa Vishwani Institute of Systems and Management Hyderabad',	'65-70',	'1.8 - 11 Lakhs',	'Not Available',	'6 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(76,	'50-60',	'Adarsh Institute of Management and Information Technology (AIMIT) Bangalore',	'55-60',	'6 Lakhs',	'Not Available',	'Not Available',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(77,	'50-60',	'Apeejay School of Management (ASM) New Delhi',	'55-60',	'8.7 Lakhs',	'12.43 LPA',	'Not Available',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(78,	'50-60',	'CII School of Logistics, Amity University, Noida',	'55-60',	'10.5 Lakhs',	'16.50 LPA',	'8.50 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(79,	'50-60',	'EMPI Business School New Delhi',	'55-60',	'8.3 Lakhs',	'20 LPA',	'8.50 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(80,	'50-60',	'GIBS Business School Bangalore',	'55-60',	'9 Lakhs',	'16.15 LPA',	'7.40 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(81,	'50-60',	'IIBS Bangalore',	'55-60',	'9 Lakhs',	'51 LPA',	'8.60 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(82,	'50-60',	'Jaypee Business School, Noida',	'55-60',	'5.83 - 12.11 Lakhs',	'10.5 LPA',	'7.5 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(83,	'50-60',	'MITCON Institute of Management Pune',	'55-60',	'6.7 - 7.65 Lakhs',	'18 LPA',	'7.50 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01'),
(84,	'50-60',	'RSB Chennai: Rajalakshmi School of Business',	'55-60',	'7.25 Lakhs',	'10 LPA',	'5 LPA',	'-',	'8850712170',	'2025-11-06 10:12:01',	'2025-11-06 10:12:01');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'0001_01_01_000000_create_users_table',	1),
(2,	'0001_01_01_000001_create_cache_table',	1),
(3,	'0001_01_01_000002_create_jobs_table',	1);

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('HdLh1kPl5WXkoFJb9b685K9OHmPGog179wHdaS08',	1,	'127.0.0.1',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',	'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUHRtZ0ZLZ0tQQjJYdmxGYXRuWkY1bTNPMFVHaUFsS004aVJOSHliaiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iYWNrZW5kL3N0dWRlbnQtcmVzdWx0L2V4cG9ydC8xMjYiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',	1762158288),
('Hr2S5qm2ExCpdmSRJxHrDRJ3cyD58Fi2LqpXddSf',	NULL,	'127.0.0.1',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHRZcUVKWkwxRTlpNmQ0dVdGcDR0QTVUUHV1TVlNaDBXU2o5SUhHSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iYWNrZW5kL2NvbGxlZ2VzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',	1762424119),
('HrEVyZccx74UnNdRVjJriHE5DkbpEWvxzvi6Dt8e',	1,	'127.0.0.1',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36',	'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFVmRXZrRmNTWFJuTWNqZ0NqMlp5a2hnZzh6d3RoaHBnMjFHUlBLbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9iYWNrZW5kL2NvbGxlZ2VzL2NyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',	1762414763);

DROP TABLE IF EXISTS `user_cat_results`;
CREATE TABLE `user_cat_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_cat_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_cat_results` (`id`, `user_id`, `url`, `data`, `created_at`, `updated_at`) VALUES
(127,	2,	'https://cdn.digialm.com//per/g01/pub/756/touchstone/AssessmentQPHTMLMode1//CAT231/CAT231S1D4413/17013483282856980/23052551_CAT231S1D4413E1.html',	'{\"details\": {\"Shift\": 1, \"Subject\": \"CAT 2023\", \"Test Date\": \"26/11/2023\", \"Test Time\": \"8:30 AM - 10:30 AM\", \"Application No\": \"23052551\", \"Candidate Name\": \"YADAV TANYA JIVRAJ\", \"Test Center Name\": \"Eklavya Academy Udaipur\"}, \"percentile\": \"50%tile - 55%tile\", \"total_marks\": 198, \"obtain_marks\": 22, \"sections_marks\": [{\"name\": \"VARC\", \"total_marks\": 72, \"obtain_marks\": 11, \"wrong_answers\": 7, \"correct_answers\": 5, \"total_questions\": 24, \"attempt_questions\": 12}, {\"name\": \"DILR\", \"total_marks\": 60, \"obtain_marks\": 1, \"wrong_answers\": 6, \"correct_answers\": 2, \"total_questions\": 20, \"attempt_questions\": 8}, {\"name\": \"QA\", \"total_marks\": 66, \"obtain_marks\": 10, \"wrong_answers\": 2, \"correct_answers\": 4, \"total_questions\": 22, \"attempt_questions\": 6}]}',	'2025-11-06 02:07:19',	'2025-11-06 02:07:19');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `whatsapp_number`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'admin',	'chhavi@njgraphica.com',	NULL,	'$2y$12$HtPZcT9jUa7VWVIeDTI9oOnrFzTuRRH/RLUe37yeJhQ8WVddTH/Ti',	NULL,	NULL,	'2025-10-24 10:59:10',	'2025-10-24 05:29:42'),
(2,	'Test',	'test@gmail.com',	NULL,	NULL,	'9632587410',	NULL,	'2025-11-03 00:03:30',	'2025-11-03 00:03:30'),
(3,	'aavya',	'hello@njgraphica.com',	NULL,	NULL,	'8969659680',	NULL,	'2025-11-06 10:56:07',	'2025-11-06 10:56:07');

-- 2025-11-07 04:43:37
