-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2025 at 10:42 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eramo`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `vendor_id`, `slug`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(10022, NULL, '64b7f89e-bb1d-4aa7-a1fb-9b7d7368a008', 1, NULL, '2025-11-02 11:58:07', '2025-11-02 11:58:07'),
(10023, NULL, 'f43c8887-2614-49d4-9569-034e1c291b4a', 1, NULL, '2025-11-02 11:58:16', '2025-11-02 11:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `activities_departments`
--

CREATE TABLE `activities_departments` (
  `id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `activity_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` bigint UNSIGNED NOT NULL,
  `attachable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `attachable_type`, `attachable_id`, `type`, `path`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 'image', 'departments/7/I1YD2pWacLrInfkhkhe5P8hMesuY9H5RStpZRlk0.png', NULL, '2025-10-27 10:55:17', '2025-10-27 10:55:17'),
(2, 'Modules\\CategoryManagment\\app\\Models\\Department', 2, 'image', 'departments/2/OPEjncMdFsBQz2r1B0wWvzykYvWDFlr3lY4a08fr.png', '2025-10-27 11:10:31', '2025-10-27 10:59:41', '2025-10-27 11:10:31'),
(3, 'Modules\\CategoryManagment\\app\\Models\\Department', 2, 'image', 'departments/2/Ixze0jd4kc0KM8Ix8wkffXdIy9STkSFkSraKW0g2.jpg', '2025-10-27 11:11:07', '2025-10-27 11:10:31', '2025-10-27 11:11:07'),
(4, 'Modules\\CategoryManagment\\app\\Models\\Department', 2, 'image', 'departments/2/APBTZEKPbP0GQ5evLBI8709LWcHHV7UmXF1TEqyJ.jpg', '2025-10-27 11:11:45', '2025-10-27 11:11:07', '2025-10-27 11:11:45'),
(5, 'Modules\\CategoryManagment\\app\\Models\\Department', 2, 'image', 'departments/2/cQ8wiGiHqlkYDKDIWxGuc44y5GObr2rJIuKgUlWO.jpg', '2025-10-29 11:44:30', '2025-10-27 11:11:45', '2025-10-29 11:44:30'),
(6, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 'image', 'categories/3/Yl4T7bXJ7Jojf6b1ZZNnnfakidaGBj2wwj5TIUUV.jpg', '2025-10-27 12:03:59', '2025-10-27 12:02:31', '2025-10-27 12:03:59'),
(7, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 'image', 'categories/3/JJ4DRA4ZCtpzdSfwNrkujByH71sJHKrJ35PwSJvG.jpg', '2025-10-27 12:04:42', '2025-10-27 12:03:59', '2025-10-27 12:04:42'),
(8, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 'image', 'categories/3/Cg5c3V3hqWES1jHBf6H8sqHzDVVBUKeEvPsaWuLE.jpg', NULL, '2025-10-27 12:04:42', '2025-10-27 12:04:42'),
(9, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 'image', 'subcategories/1/RgwNmsD3jJpTw2AMxfX30XHPBcr1lYeLII7Z8w96.jpg', '2025-10-28 04:12:03', '2025-10-28 04:11:14', '2025-10-28 04:12:03'),
(10, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 'image', 'subcategories/1/dc2al20w8PoK8TifpjkH2nyH5WnzdPLKet6f7cFL.jpg', NULL, '2025-10-28 04:12:03', '2025-10-28 04:12:03'),
(11, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 3, 'image', 'subcategories/3/LzC1011F2J4ZAugY9O2EiB3B6uIdmNfkUCY24K2t.jpg', NULL, '2025-10-28 08:12:12', '2025-10-28 08:12:12'),
(16, 'Modules\\Vendor\\app\\Models\\Vendor', 3, 'logo', 'vendors/3/logo/IA1XgfOFG6Ky7dueLDprsnzp7xC18ucfWqGTNqPw.jpg', NULL, '2025-10-28 10:35:10', '2025-10-28 10:35:10'),
(17, 'Modules\\Vendor\\app\\Models\\Vendor', 3, 'banner', 'vendors/3/banner/c1wMck3j3Cad73vRfm7VWIeYQmmj9fKQqrlWucb2.jpg', NULL, '2025-10-28 10:35:10', '2025-10-28 10:35:10'),
(18, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 'logo', 'vendors/4/logo/32OY258Y970xjOLLPZyk0K6DpRYdatqYPEOB3EI2.jpg', NULL, '2025-10-28 11:55:29', '2025-10-28 11:55:29'),
(19, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 'banner', 'vendors/4/banner/tqC20xEj5CWqtS317w4j0Q7eIzqeIURCMsYagCp7.png', NULL, '2025-10-28 11:55:30', '2025-10-28 11:55:30'),
(20, 'Modules\\Brands\\app\\Models\\Brand', 1, 'logo', 'brands/1/naq0trAH7fJ8k3WaHQd2NZubttNYkp0ZJ8b209wX.jpg', NULL, '2025-10-29 08:29:50', '2025-10-29 08:29:50'),
(21, 'Modules\\Brands\\app\\Models\\Brand', 1, 'cover', 'brands/1/jf95Nav8EKG6szEyRoC1ZkK4Iethjc3LbnD1gT6f.jpg', NULL, '2025-10-29 08:30:42', '2025-10-29 08:30:42'),
(22, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 'logo', 'brands/1/qI0GR23aK4MpjbSBvVn1nVULLRnJ0rhcMK6Gi9Sc.jpg', NULL, '2025-10-29 09:02:41', '2025-10-29 09:02:41'),
(23, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 'cover', 'brands/1/e5BLZIuDCSkpMTYvKwPcBBJk60ItHPPPYPOTxw8v.jpg', NULL, '2025-10-29 09:02:41', '2025-10-29 09:02:41'),
(24, 'Modules\\CategoryManagment\\app\\Models\\Department', 6, 'image', 'departments/6/f2RrV0ztVAmuj94ZC3MPJ1yRsEGaAD51m5mlEBys.png', NULL, '2025-10-29 11:43:39', '2025-10-29 11:43:39'),
(25, 'Modules\\CategoryManagment\\app\\Models\\Department', 8, 'image', 'departments/8/Mdot31CNPdrZwA392ZE3ryaWtG1682SeylLvU5dp.jpg', NULL, '2025-10-29 11:46:39', '2025-10-29 11:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facebook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pinterest_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `vendor_id`, `slug`, `facebook_url`, `linkedin_url`, `pinterest_url`, `twitter_url`, `instagram_url`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, '621c4cf0-82cf-4e79-9165-d946baf2cdd5', NULL, NULL, NULL, NULL, NULL, 1, '2025-10-29 05:39:21', '2025-10-29 09:02:41', NULL),
(2, NULL, 'e4d71973-d8ff-4393-a143-b9c53fc4060b', NULL, NULL, NULL, NULL, NULL, 1, '2025-10-29 08:18:58', '2025-10-29 08:31:53', '2025-10-29 08:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `department_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `vendor_id`, `slug`, `active`, `department_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 'e71fdc96-fa85-457d-86f3-8d9d367d57ad', 1, 6, NULL, '2025-10-27 11:48:05', '2025-10-29 11:58:30'),
(2, NULL, 'fbf9d5bf-1684-4f7e-a049-ca3baef9b213', 1, 7, NULL, '2025-10-27 11:59:40', '2025-10-29 11:58:10'),
(3, NULL, 'a296371b-552d-4207-afe0-46d90277ba31', 0, 7, NULL, '2025-10-27 12:02:31', '2025-11-02 12:52:09');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint UNSIGNED NOT NULL,
  `country_id` bigint UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, '2025-10-28 05:12:04', '2025-10-28 05:12:04'),
(2, 1, 1, NULL, '2025-10-28 05:25:05', '2025-10-28 05:25:05'),
(3, 3, 1, NULL, '2025-10-28 06:45:28', '2025-10-28 06:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `code`, `phone_code`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'ASD', '+asd', 1, NULL, '2025-10-27 08:39:37', '2025-10-27 08:39:37'),
(2, 'EG', '+20', 1, NULL, '2025-10-28 05:11:47', '2025-10-28 05:11:47'),
(3, 'SAD', '+20', 0, NULL, '2025-10-28 05:24:24', '2025-10-28 05:24:31'),
(4, 'SDA', '+dasd', 1, NULL, '2025-10-28 05:58:27', '2025-10-28 05:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `slug`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '27e08788-40ef-46c9-aeae-c3f11a8ef967', 1, '2025-10-27 09:28:07', '2025-10-27 09:25:20', '2025-10-27 09:28:07'),
(2, '09bd34cc-2d74-49dc-987c-b151015087c4', 0, '2025-10-29 11:44:30', '2025-10-27 09:26:55', '2025-10-29 11:44:30'),
(3, '462aa35f-a6bf-4806-a829-bba965cc2f50', 1, '2025-10-29 11:44:25', '2025-10-27 09:33:01', '2025-10-29 11:44:25'),
(4, 'b1d5f836-dc19-4910-ae83-c2b1c8ed04b0', 1, '2025-10-29 11:44:20', '2025-10-27 10:52:24', '2025-10-29 11:44:20'),
(5, '95a5effd-da2d-48ab-866a-d3292130fd85', 1, '2025-10-29 11:44:14', '2025-10-27 10:52:39', '2025-10-29 11:44:14'),
(6, '787f6f9d-395b-4723-ba8e-6ba184a9688a', 1, NULL, '2025-10-27 10:53:56', '2025-10-27 10:53:56'),
(7, 'a476d70e-0784-4362-a55a-35aa81d72963', 1, NULL, '2025-10-27 10:55:17', '2025-10-27 10:55:17'),
(8, '95aebe30-57b8-4483-b7cf-0bd4aeaf5212', 1, NULL, '2025-10-29 11:46:29', '2025-10-29 11:46:29');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rtl` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `region`, `rtl`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 'US', 0, '2025-10-27 08:23:57', '2025-10-27 08:23:57'),
(2, 'Arabic', 'ar', 'SA', 1, '2025-10-27 08:23:57', '2025-10-27 08:23:57');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(98, '2014_10_11_000000_users_types_table', 1),
(99, '2014_10_12_000000_create_users_table', 1),
(100, '2014_10_12_100000_create_password_resets_table', 1),
(101, '2019_08_19_000000_create_failed_jobs_table', 1),
(102, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(103, '2025_10_21_122102_create_languages_table', 1),
(104, '2025_10_21_122203_create_translations_table', 1),
(105, '2025_10_21_122354_create_permessions_table', 1),
(106, '2025_10_21_132200_create_roles_table', 1),
(107, '2025_10_21_135418_create_user_role_table', 1),
(108, '2025_10_22_113221_create_attachments_table', 1),
(109, '2025_10_22_131257_create_countries_table', 1),
(110, '2025_10_22_131802_create_cities_table', 1),
(111, '2025_10_22_131939_create_regions_table', 1),
(112, '2025_10_22_132004_create_subregions_table', 1),
(113, '2025_10_23_110553_create_activities_table', 1),
(114, '2025_10_23_110554_create_vendors_table', 1),
(115, '2025_10_23_110555_create_vendor_commission_table', 1),
(116, '2025_10_23_132038_create_vendors_activities_table', 1),
(117, '2025_10_27_100415_create_departments_table', 1),
(118, '2025_10_27_100442_create_categories_table', 1),
(119, '2025_10_27_100521_create_departments_categories_table', 1),
(120, '2025_10_27_100558_create_sub_categories_table', 1),
(121, '2025_10_27_122054_create_activities_departments_table', 2),
(122, '2025_10_27_110000_create_sub_categories_table', 3),
(123, '2025_10_27_132055_create_activities_categories_table', 3),
(124, '2025_10_29_073659_create_brands_table', 3),
(125, '2025_10_29_120519_create_taxes_table', 4),
(129, '2025_10_30_072311_create_variants_configurations_keys', 5),
(130, '2025_10_30_072312_create_variants_configurations', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permessions`
--

CREATE TABLE `permessions` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('admin','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permessions`
--

INSERT INTO `permessions` (`id`, `type`, `key`, `group_by`, `created_at`, `updated_at`) VALUES
(652, 'other', 'dashboard.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(653, 'admin', 'activities.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(654, 'admin', 'activities.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(655, 'admin', 'activities.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(656, 'admin', 'activities.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(657, 'admin', 'activities.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(658, 'other', 'departments.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(659, 'other', 'departments.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(660, 'other', 'departments.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(661, 'other', 'departments.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(662, 'other', 'departments.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(663, 'other', 'categories.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(664, 'other', 'categories.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(665, 'other', 'categories.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(666, 'other', 'categories.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(667, 'other', 'categories.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(668, 'other', 'sub_categories.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(669, 'other', 'sub_categories.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(670, 'other', 'sub_categories.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(671, 'other', 'sub_categories.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(672, 'other', 'sub_categories.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(673, 'other', 'products.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(674, 'other', 'products.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(675, 'other', 'products.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(676, 'other', 'products.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(677, 'other', 'products.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(678, 'other', 'products.in_stock.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(679, 'other', 'products.out_of_stock.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(680, 'other', 'product_setup.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(681, 'other', 'product_setup.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(682, 'other', 'product_setup.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(683, 'other', 'product_setup.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(684, 'other', 'product_reviews.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(685, 'other', 'product_reviews.accept', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(686, 'other', 'product_reviews.reject', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(687, 'other', 'product_reviews.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(688, 'other', 'taxes.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(689, 'other', 'taxes.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(690, 'other', 'taxes.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(691, 'other', 'taxes.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(692, 'other', 'taxes.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(693, 'other', 'offers.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(694, 'other', 'offers.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(695, 'other', 'offers.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(696, 'other', 'offers.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(697, 'other', 'offers.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(698, 'other', 'promocodes.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(699, 'other', 'promocodes.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(700, 'other', 'promocodes.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(701, 'other', 'promocodes.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(702, 'other', 'promocodes.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(703, 'other', 'roles.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(704, 'other', 'roles.view', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(705, 'other', 'roles.create', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(706, 'other', 'roles.edit', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(707, 'other', 'roles.delete', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(708, 'other', 'admins.index', NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(709, 'other', 'admins.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(710, 'other', 'admins.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(711, 'other', 'admins.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(712, 'other', 'admins.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(713, 'other', 'vendors.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(714, 'other', 'vendors.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(715, 'other', 'vendors.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(716, 'other', 'vendors.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(717, 'other', 'vendors.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(718, 'other', 'vendor_requests.new', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(719, 'other', 'vendor_requests.accept', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(720, 'other', 'vendor_requests.reject', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(721, 'other', 'orders.new', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(722, 'other', 'orders.inprogress', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(723, 'other', 'orders.delivered', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(724, 'other', 'orders.canceled', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(725, 'other', 'orders.refunded', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(726, 'other', 'orders.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(727, 'other', 'orders.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(728, 'other', 'order_stages.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(729, 'other', 'order_stages.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(730, 'other', 'order_stages.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(731, 'other', 'order_stages.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(732, 'other', 'order_stages.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(733, 'other', 'shipping_methods.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(734, 'other', 'shipping_methods.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(735, 'other', 'shipping_methods.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(736, 'other', 'shipping_methods.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(737, 'other', 'shipping_methods.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(738, 'other', 'points.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(739, 'other', 'points.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(740, 'other', 'points.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(741, 'other', 'points.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(742, 'other', 'points.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(743, 'other', 'advertisements.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(744, 'other', 'advertisements.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(745, 'other', 'advertisements.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(746, 'other', 'advertisements.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(747, 'other', 'advertisements.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(748, 'other', 'positions.index', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(749, 'other', 'positions.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(750, 'other', 'positions.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(751, 'other', 'positions.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(752, 'other', 'positions.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(753, 'other', 'notifications.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(754, 'other', 'notifications.send', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(755, 'other', 'notifications.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(756, 'other', 'accounting.overview.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(757, 'other', 'accounting.balance.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(758, 'other', 'accounting.expenses_keys.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(759, 'other', 'accounting.expenses_keys.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(760, 'other', 'accounting.expenses_keys.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(761, 'other', 'accounting.expenses_keys.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(762, 'other', 'accounting.expenses.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(763, 'other', 'accounting.expenses.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(764, 'other', 'accounting.expenses.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(765, 'other', 'accounting.expenses.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(766, 'other', 'withdraw.send_money.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(767, 'other', 'withdraw.send_money.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(768, 'other', 'withdraw.transactions.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(769, 'other', 'withdraw.vendor_requests.new.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(770, 'other', 'withdraw.vendor_requests.accept', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(771, 'other', 'withdraw.vendor_requests.reject', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(772, 'other', 'withdraw.vendor_requests.accepted.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(773, 'other', 'withdraw.vendor_requests.rejected.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(774, 'other', 'blog.categories.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(775, 'other', 'blog.categories.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(776, 'other', 'blog.categories.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(777, 'other', 'blog.categories.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(778, 'other', 'blog.posts.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(779, 'other', 'blog.posts.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(780, 'other', 'blog.posts.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(781, 'other', 'blog.posts.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(782, 'other', 'reports.registered_users.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(783, 'other', 'reports.area_users.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(784, 'other', 'reports.orders.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(785, 'other', 'reports.products.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(786, 'other', 'reports.points.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(787, 'other', 'system_log.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(788, 'other', 'area.country.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(789, 'other', 'area.country.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(790, 'other', 'area.country.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(791, 'other', 'area.country.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(792, 'other', 'area.city.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(793, 'other', 'area.city.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(794, 'other', 'area.city.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(795, 'other', 'area.city.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(796, 'other', 'area.region.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(797, 'other', 'area.region.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(798, 'other', 'area.region.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(799, 'other', 'area.region.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(800, 'other', 'area.subregion.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(801, 'other', 'area.subregion.create', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(802, 'other', 'area.subregion.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(803, 'other', 'area.subregion.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(804, 'other', 'settings.terms.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(805, 'other', 'settings.terms.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(806, 'other', 'settings.privacy.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(807, 'other', 'settings.privacy.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(808, 'other', 'settings.about.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(809, 'other', 'settings.about.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(810, 'other', 'settings.contact.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(811, 'other', 'settings.contact.edit', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(812, 'other', 'settings.messages.view', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(813, 'other', 'settings.messages.delete', NULL, '2025-11-03 07:03:40', '2025-11-03 07:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint UNSIGNED NOT NULL,
  `city_id` bigint UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `city_id`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, '2025-10-28 05:26:50', '2025-10-28 05:26:50'),
(2, 2, 0, NULL, '2025-10-28 05:27:04', '2025-10-28 05:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('admin','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', '2025-10-27 08:26:57', '2025-10-28 10:52:28', '2025-10-28 10:52:28'),
(2, 'admin', '2025-10-28 10:52:28', '2025-10-28 10:57:25', '2025-10-28 10:57:25'),
(3, 'admin', '2025-10-28 10:58:59', '2025-10-28 10:59:56', '2025-10-28 10:59:56'),
(4, 'admin', '2025-10-28 10:59:56', '2025-10-28 11:00:09', '2025-10-28 11:00:09'),
(5, 'admin', '2025-10-28 11:00:09', '2025-10-28 11:01:01', '2025-10-28 11:01:01'),
(6, 'admin', '2025-10-28 11:01:01', '2025-10-28 11:01:04', '2025-10-28 11:01:04'),
(7, 'admin', '2025-10-28 11:01:04', '2025-10-28 11:01:24', '2025-10-28 11:01:24'),
(8, 'admin', '2025-10-28 11:01:24', '2025-10-28 11:01:39', '2025-10-28 11:01:39'),
(9, 'admin', '2025-10-28 11:01:39', '2025-10-28 11:02:14', '2025-10-28 11:02:14'),
(10, 'admin', '2025-10-28 11:02:14', '2025-10-28 11:02:24', '2025-10-28 11:02:24'),
(11, 'admin', '2025-10-28 11:02:24', '2025-10-28 11:02:41', '2025-10-28 11:02:41'),
(12, 'admin', '2025-10-28 11:02:41', '2025-10-28 11:02:54', '2025-10-28 11:02:54'),
(13, 'admin', '2025-10-28 11:02:54', '2025-10-28 11:02:56', '2025-10-28 11:02:56'),
(14, 'admin', '2025-10-28 11:02:56', '2025-10-28 11:03:28', '2025-10-28 11:03:28'),
(15, 'admin', '2025-10-28 11:03:28', '2025-10-28 11:03:55', '2025-10-28 11:03:55'),
(16, 'admin', '2025-10-28 11:03:55', '2025-11-03 06:58:36', '2025-11-03 06:58:36'),
(17, 'admin', '2025-10-28 15:12:58', '2025-11-03 06:58:36', '2025-11-03 06:58:36'),
(18, 'admin', '2025-10-28 15:13:10', '2025-11-03 06:58:36', '2025-11-03 06:58:36'),
(19, 'admin', '2025-10-28 15:13:23', '2025-11-03 06:58:36', '2025-11-03 06:58:36'),
(20, 'admin', '2025-10-28 15:20:42', '2025-10-28 16:03:55', '2025-10-28 16:03:55'),
(21, 'admin', '2025-10-28 15:21:52', '2025-10-28 16:03:48', '2025-10-28 16:03:48'),
(22, 'admin', '2025-10-28 16:31:21', '2025-11-03 06:58:36', '2025-11-03 06:58:36'),
(23, 'admin', '2025-11-03 06:58:36', '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(24, 'other', '2025-11-03 06:58:36', '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(25, 'admin', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(26, 'other', '2025-11-03 07:03:41', '2025-11-03 07:03:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permession`
--

CREATE TABLE `role_permession` (
  `id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `permession_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permession`
--

INSERT INTO `role_permession` (`id`, `role_id`, `permession_id`, `created_at`, `updated_at`) VALUES
(539, 25, 652, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(540, 25, 653, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(541, 25, 654, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(542, 25, 655, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(543, 25, 656, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(544, 25, 657, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(545, 25, 658, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(546, 25, 659, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(547, 25, 660, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(548, 25, 661, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(549, 25, 662, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(550, 25, 663, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(551, 25, 664, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(552, 25, 665, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(553, 25, 666, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(554, 25, 667, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(555, 25, 668, '2025-11-03 07:03:40', '2025-11-03 07:03:40'),
(556, 25, 669, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(557, 25, 670, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(558, 25, 671, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(559, 25, 672, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(560, 25, 673, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(561, 25, 674, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(562, 25, 675, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(563, 25, 676, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(564, 25, 677, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(565, 25, 678, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(566, 25, 679, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(567, 25, 680, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(568, 25, 681, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(569, 25, 682, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(570, 25, 683, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(571, 25, 684, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(572, 25, 685, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(573, 25, 686, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(574, 25, 687, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(575, 25, 688, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(576, 25, 689, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(577, 25, 690, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(578, 25, 691, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(579, 25, 692, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(580, 25, 693, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(581, 25, 694, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(582, 25, 695, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(583, 25, 696, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(584, 25, 697, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(585, 25, 698, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(586, 25, 699, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(587, 25, 700, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(588, 25, 701, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(589, 25, 702, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(590, 25, 703, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(591, 25, 704, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(592, 25, 705, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(593, 25, 706, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(594, 25, 707, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(595, 25, 708, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(596, 25, 709, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(597, 25, 710, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(598, 25, 711, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(599, 25, 712, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(600, 25, 713, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(601, 25, 714, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(602, 25, 715, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(603, 25, 716, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(604, 25, 717, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(605, 25, 718, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(606, 25, 719, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(607, 25, 720, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(608, 25, 721, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(609, 25, 722, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(610, 25, 723, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(611, 25, 724, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(612, 25, 725, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(613, 25, 726, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(614, 25, 727, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(615, 25, 728, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(616, 25, 729, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(617, 25, 730, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(618, 25, 731, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(619, 25, 732, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(620, 25, 733, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(621, 25, 734, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(622, 25, 735, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(623, 25, 736, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(624, 25, 737, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(625, 25, 738, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(626, 25, 739, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(627, 25, 740, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(628, 25, 741, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(629, 25, 742, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(630, 25, 743, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(631, 25, 744, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(632, 25, 745, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(633, 25, 746, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(634, 25, 747, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(635, 25, 748, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(636, 25, 749, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(637, 25, 750, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(638, 25, 751, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(639, 25, 752, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(640, 25, 753, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(641, 25, 754, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(642, 25, 755, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(643, 25, 756, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(644, 25, 757, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(645, 25, 758, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(646, 25, 759, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(647, 25, 760, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(648, 25, 761, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(649, 25, 762, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(650, 25, 763, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(651, 25, 764, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(652, 25, 765, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(653, 25, 766, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(654, 25, 767, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(655, 25, 768, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(656, 25, 769, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(657, 25, 770, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(658, 25, 771, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(659, 25, 772, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(660, 25, 773, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(661, 25, 774, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(662, 25, 775, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(663, 25, 776, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(664, 25, 777, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(665, 25, 778, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(666, 25, 779, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(667, 25, 780, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(668, 25, 781, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(669, 25, 782, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(670, 25, 783, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(671, 25, 784, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(672, 25, 785, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(673, 25, 786, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(674, 25, 787, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(675, 25, 788, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(676, 25, 789, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(677, 25, 790, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(678, 25, 791, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(679, 25, 792, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(680, 25, 793, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(681, 25, 794, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(682, 25, 795, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(683, 25, 796, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(684, 25, 797, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(685, 25, 798, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(686, 25, 799, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(687, 25, 800, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(688, 25, 801, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(689, 25, 802, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(690, 25, 803, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(691, 25, 804, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(692, 25, 805, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(693, 25, 806, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(694, 25, 807, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(695, 25, 808, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(696, 25, 809, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(697, 25, 810, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(698, 25, 811, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(699, 25, 812, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(700, 25, 813, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(701, 26, 652, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(702, 26, 653, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(703, 26, 654, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(704, 26, 655, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(705, 26, 656, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(706, 26, 657, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(707, 26, 658, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(708, 26, 659, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(709, 26, 660, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(710, 26, 661, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(711, 26, 662, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(712, 26, 663, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(713, 26, 664, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(714, 26, 665, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(715, 26, 666, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(716, 26, 667, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(717, 26, 668, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(718, 26, 669, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(719, 26, 670, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(720, 26, 671, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(721, 26, 672, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(722, 26, 673, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(723, 26, 674, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(724, 26, 675, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(725, 26, 676, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(726, 26, 677, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(727, 26, 678, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(728, 26, 679, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(729, 26, 680, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(730, 26, 681, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(731, 26, 682, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(732, 26, 683, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(733, 26, 684, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(734, 26, 685, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(735, 26, 686, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(736, 26, 687, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(737, 26, 688, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(738, 26, 689, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(739, 26, 690, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(740, 26, 691, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(741, 26, 692, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(742, 26, 693, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(743, 26, 694, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(744, 26, 695, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(745, 26, 696, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(746, 26, 697, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(747, 26, 698, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(748, 26, 699, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(749, 26, 700, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(750, 26, 701, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(751, 26, 702, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(752, 26, 703, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(753, 26, 704, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(754, 26, 705, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(755, 26, 706, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(756, 26, 707, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(757, 26, 708, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(758, 26, 709, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(759, 26, 710, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(760, 26, 711, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(761, 26, 712, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(762, 26, 713, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(763, 26, 714, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(764, 26, 715, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(765, 26, 716, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(766, 26, 717, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(767, 26, 718, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(768, 26, 719, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(769, 26, 720, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(770, 26, 721, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(771, 26, 722, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(772, 26, 723, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(773, 26, 724, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(774, 26, 725, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(775, 26, 726, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(776, 26, 727, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(777, 26, 728, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(778, 26, 729, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(779, 26, 730, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(780, 26, 731, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(781, 26, 732, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(782, 26, 733, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(783, 26, 734, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(784, 26, 735, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(785, 26, 736, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(786, 26, 737, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(787, 26, 738, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(788, 26, 739, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(789, 26, 740, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(790, 26, 741, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(791, 26, 742, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(792, 26, 743, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(793, 26, 744, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(794, 26, 745, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(795, 26, 746, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(796, 26, 747, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(797, 26, 748, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(798, 26, 749, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(799, 26, 750, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(800, 26, 751, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(801, 26, 752, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(802, 26, 753, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(803, 26, 754, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(804, 26, 755, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(805, 26, 756, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(806, 26, 757, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(807, 26, 758, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(808, 26, 759, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(809, 26, 760, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(810, 26, 761, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(811, 26, 762, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(812, 26, 763, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(813, 26, 764, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(814, 26, 765, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(815, 26, 766, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(816, 26, 767, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(817, 26, 768, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(818, 26, 769, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(819, 26, 770, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(820, 26, 771, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(821, 26, 772, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(822, 26, 773, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(823, 26, 774, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(824, 26, 775, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(825, 26, 776, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(826, 26, 777, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(827, 26, 778, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(828, 26, 779, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(829, 26, 780, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(830, 26, 781, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(831, 26, 782, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(832, 26, 783, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(833, 26, 784, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(834, 26, 785, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(835, 26, 786, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(836, 26, 787, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(837, 26, 804, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(838, 26, 805, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(839, 26, 806, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(840, 26, 807, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(841, 26, 808, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(842, 26, 809, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(843, 26, 810, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(844, 26, 811, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(845, 26, 812, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(846, 26, 813, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(847, 26, 788, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(848, 26, 789, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(849, 26, 790, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(850, 26, 791, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(851, 26, 792, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(852, 26, 793, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(853, 26, 794, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(854, 26, 795, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(855, 26, 796, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(856, 26, 797, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(857, 26, 798, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(858, 26, 799, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(859, 26, 800, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(860, 26, 801, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(861, 26, 802, '2025-11-03 07:52:33', '2025-11-03 07:52:33'),
(862, 26, 803, '2025-11-03 07:52:33', '2025-11-03 07:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `subregions`
--

CREATE TABLE `subregions` (
  `id` bigint UNSIGNED NOT NULL,
  `region_id` bigint UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subregions`
--

INSERT INTO `subregions` (`id`, `region_id`, `active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, '2025-10-28 05:36:17', '2025-10-28 05:36:33'),
(2, 2, 0, NULL, '2025-10-28 05:36:27', '2025-10-28 05:36:27'),
(3, 1, 1, NULL, '2025-10-28 08:10:59', '2025-10-28 08:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `slug`, `active`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'd48a8f4d-f4fd-43b1-b922-2b9f3a0eba85', 1, 2, '2025-10-29 09:29:24', '2025-10-29 09:38:21', NULL),
(9, 'db0cf497-a763-4e16-b739-4e3669f53d20', 1, 1, '2025-11-02 12:52:43', '2025-11-02 12:52:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `slug`, `tax_rate`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '53103a96-94cc-48f2-90fb-2ce4131e5581', 10.00, 1, '2025-10-29 09:20:12', '2025-10-29 09:20:12', NULL),
(2, 'fbf620df-8725-4331-8d64-adfe699b7b1c', 20.00, 0, '2025-10-29 09:20:38', '2025-10-30 06:38:22', '2025-10-30 06:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` bigint UNSIGNED NOT NULL,
  `translatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translatable_id` bigint UNSIGNED NOT NULL,
  `lang_id` bigint UNSIGNED NOT NULL,
  `lang_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lang_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(20211, 'App\\Models\\Role', 13, 1, 'name', 'Vendor User', '2025-10-28 11:02:54', '2025-10-28 11:02:54', NULL),
(20212, 'App\\Models\\Role', 14, 1, 'name', 'Vendor User', '2025-10-28 11:02:56', '2025-10-28 11:02:56', NULL),
(20213, 'App\\Models\\Role', 15, 1, 'name', 'Vendor User', '2025-10-28 11:03:28', '2025-10-28 11:03:28', NULL),
(20214, 'App\\Models\\Role', 15, 2, 'name', 'مستخدم مورد', '2025-10-28 11:03:28', '2025-10-28 11:03:28', NULL),
(20215, 'App\\Models\\Permession', 331, 1, 'name', 'View Dashboard', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20216, 'App\\Models\\Permession', 331, 2, 'name', 'عرض لوحة التحكم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20217, 'App\\Models\\Permession', 331, 1, 'group_by', 'Dashboard', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20218, 'App\\Models\\Permession', 331, 2, 'group_by', 'لوحة التحكم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20219, 'App\\Models\\Permession', 332, 1, 'name', 'All Activities', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20220, 'App\\Models\\Permession', 332, 2, 'name', 'كل الانشطة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20221, 'App\\Models\\Permession', 332, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20222, 'App\\Models\\Permession', 332, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20223, 'App\\Models\\Permession', 333, 1, 'name', 'View Activities', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20224, 'App\\Models\\Permession', 333, 2, 'name', 'عرض الانشطة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20225, 'App\\Models\\Permession', 333, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20226, 'App\\Models\\Permession', 333, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20227, 'App\\Models\\Permession', 334, 1, 'name', 'Create Activities', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20228, 'App\\Models\\Permession', 334, 2, 'name', 'إنشاء الانشطة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20229, 'App\\Models\\Permession', 334, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20230, 'App\\Models\\Permession', 334, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20231, 'App\\Models\\Permession', 335, 1, 'name', 'Edit Activities', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20232, 'App\\Models\\Permession', 335, 2, 'name', 'تعديل الانشطة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20233, 'App\\Models\\Permession', 335, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20234, 'App\\Models\\Permession', 335, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20235, 'App\\Models\\Permession', 336, 1, 'name', 'Delete Activities', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20236, 'App\\Models\\Permession', 336, 2, 'name', 'ازالة الانشطة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20237, 'App\\Models\\Permession', 336, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20238, 'App\\Models\\Permession', 336, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20239, 'App\\Models\\Permession', 337, 1, 'name', 'All Departments', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20240, 'App\\Models\\Permession', 337, 2, 'name', 'كل الأقسام', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20241, 'App\\Models\\Permession', 337, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20242, 'App\\Models\\Permession', 337, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20243, 'App\\Models\\Permession', 338, 1, 'name', 'View Departments', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20244, 'App\\Models\\Permession', 338, 2, 'name', 'عرض الأقسام', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20245, 'App\\Models\\Permession', 338, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20246, 'App\\Models\\Permession', 338, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20247, 'App\\Models\\Permession', 339, 1, 'name', 'Create Department', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20248, 'App\\Models\\Permession', 339, 2, 'name', 'إنشاء قسم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20249, 'App\\Models\\Permession', 339, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20250, 'App\\Models\\Permession', 339, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20251, 'App\\Models\\Permession', 340, 1, 'name', 'Edit Department', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20252, 'App\\Models\\Permession', 340, 2, 'name', 'تعديل قسم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20253, 'App\\Models\\Permession', 340, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20254, 'App\\Models\\Permession', 340, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20255, 'App\\Models\\Permession', 341, 1, 'name', 'Delete Department', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20256, 'App\\Models\\Permession', 341, 2, 'name', 'حذف قسم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20257, 'App\\Models\\Permession', 341, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20258, 'App\\Models\\Permession', 341, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20259, 'App\\Models\\Permession', 342, 1, 'name', 'All Main Categories', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20260, 'App\\Models\\Permession', 342, 2, 'name', 'كل الأقسام الرئيسية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20261, 'App\\Models\\Permession', 342, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20262, 'App\\Models\\Permession', 342, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20263, 'App\\Models\\Permession', 343, 1, 'name', 'View Main Categories', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20264, 'App\\Models\\Permession', 343, 2, 'name', 'عرض الأقسام الرئيسية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20265, 'App\\Models\\Permession', 343, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20266, 'App\\Models\\Permession', 343, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20267, 'App\\Models\\Permession', 344, 1, 'name', 'Create Main Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20268, 'App\\Models\\Permession', 344, 2, 'name', 'إنشاء قسم رئيسية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20269, 'App\\Models\\Permession', 344, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20270, 'App\\Models\\Permession', 344, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20271, 'App\\Models\\Permession', 345, 1, 'name', 'Edit Main Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20272, 'App\\Models\\Permession', 345, 2, 'name', 'تعديل قسم رئيسية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20273, 'App\\Models\\Permession', 345, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20274, 'App\\Models\\Permession', 345, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20275, 'App\\Models\\Permession', 346, 1, 'name', 'Delete Main Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20276, 'App\\Models\\Permession', 346, 2, 'name', 'حذف قسم رئيسية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20277, 'App\\Models\\Permession', 346, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20278, 'App\\Models\\Permession', 346, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20279, 'App\\Models\\Permession', 347, 1, 'name', 'All Sub Categories', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20280, 'App\\Models\\Permession', 347, 2, 'name', 'كل الأقسام الفرعية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20281, 'App\\Models\\Permession', 347, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20282, 'App\\Models\\Permession', 347, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20283, 'App\\Models\\Permession', 348, 1, 'name', 'View Sub Categories', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20284, 'App\\Models\\Permession', 348, 2, 'name', 'عرض الأقسام الفرعية', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20285, 'App\\Models\\Permession', 348, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20286, 'App\\Models\\Permession', 348, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20287, 'App\\Models\\Permession', 349, 1, 'name', 'Create Sub Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20288, 'App\\Models\\Permession', 349, 2, 'name', 'إنشاء قسم فرعي', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20289, 'App\\Models\\Permession', 349, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20290, 'App\\Models\\Permession', 349, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20291, 'App\\Models\\Permession', 350, 1, 'name', 'Edit Sub Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20292, 'App\\Models\\Permession', 350, 2, 'name', 'تعديل قسم فرعي', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20293, 'App\\Models\\Permession', 350, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20294, 'App\\Models\\Permession', 350, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20295, 'App\\Models\\Permession', 351, 1, 'name', 'Delete Sub Category', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20296, 'App\\Models\\Permession', 351, 2, 'name', 'حذف قسم فرعي', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20297, 'App\\Models\\Permession', 351, 1, 'group_by', 'Catalog Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20298, 'App\\Models\\Permession', 351, 2, 'group_by', 'إدارة الكتالوج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20299, 'App\\Models\\Permession', 352, 1, 'name', 'All Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20300, 'App\\Models\\Permession', 352, 2, 'name', 'كل المنتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20301, 'App\\Models\\Permession', 352, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20302, 'App\\Models\\Permession', 352, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20303, 'App\\Models\\Permession', 353, 1, 'name', 'View Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20304, 'App\\Models\\Permession', 353, 2, 'name', 'عرض المنتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20305, 'App\\Models\\Permession', 353, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20306, 'App\\Models\\Permession', 353, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20307, 'App\\Models\\Permession', 354, 1, 'name', 'Create Product', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20308, 'App\\Models\\Permession', 354, 2, 'name', 'إنشاء منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20309, 'App\\Models\\Permession', 354, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20310, 'App\\Models\\Permession', 354, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20311, 'App\\Models\\Permession', 355, 1, 'name', 'Edit Product', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20312, 'App\\Models\\Permession', 355, 2, 'name', 'تعديل منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20313, 'App\\Models\\Permession', 355, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20314, 'App\\Models\\Permession', 355, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20315, 'App\\Models\\Permession', 356, 1, 'name', 'Delete Product', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20316, 'App\\Models\\Permession', 356, 2, 'name', 'حذف منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20317, 'App\\Models\\Permession', 356, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20318, 'App\\Models\\Permession', 356, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20319, 'App\\Models\\Permession', 357, 1, 'name', 'View In Stock Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20320, 'App\\Models\\Permession', 357, 2, 'name', 'عرض المنتجات في المخزون', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20321, 'App\\Models\\Permession', 357, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20322, 'App\\Models\\Permession', 357, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20323, 'App\\Models\\Permession', 358, 1, 'name', 'View Out of Stock Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20324, 'App\\Models\\Permession', 358, 2, 'name', 'عرض المنتجات غير في المخزون', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20325, 'App\\Models\\Permession', 358, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20326, 'App\\Models\\Permession', 358, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20327, 'App\\Models\\Permession', 359, 1, 'name', 'View Product Setup', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20328, 'App\\Models\\Permession', 359, 2, 'name', 'عرض إعداد المنتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20329, 'App\\Models\\Permession', 359, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20330, 'App\\Models\\Permession', 359, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20331, 'App\\Models\\Permession', 360, 1, 'name', 'Create Product Setup', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20332, 'App\\Models\\Permession', 360, 2, 'name', 'إنشاء إعداد منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20333, 'App\\Models\\Permession', 360, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20334, 'App\\Models\\Permession', 360, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20335, 'App\\Models\\Permession', 361, 1, 'name', 'Edit Product Setup', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20336, 'App\\Models\\Permession', 361, 2, 'name', 'تعديل إعداد منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20337, 'App\\Models\\Permession', 361, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20338, 'App\\Models\\Permession', 361, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20339, 'App\\Models\\Permession', 362, 1, 'name', 'Delete Product Setup', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20340, 'App\\Models\\Permession', 362, 2, 'name', 'حذف إعداد منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20341, 'App\\Models\\Permession', 362, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20342, 'App\\Models\\Permession', 362, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20343, 'App\\Models\\Permession', 363, 1, 'name', 'View Product Reviews', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20344, 'App\\Models\\Permession', 363, 2, 'name', 'عرض تقييم المنتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20345, 'App\\Models\\Permession', 363, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20346, 'App\\Models\\Permession', 363, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20347, 'App\\Models\\Permession', 364, 1, 'name', 'Accept Product Review', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20348, 'App\\Models\\Permession', 364, 2, 'name', 'قبول تقييم منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20349, 'App\\Models\\Permession', 364, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20350, 'App\\Models\\Permession', 364, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20351, 'App\\Models\\Permession', 365, 1, 'name', 'Reject Product Review', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20352, 'App\\Models\\Permession', 365, 2, 'name', 'رفض تقييم منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20353, 'App\\Models\\Permession', 365, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20354, 'App\\Models\\Permession', 365, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20355, 'App\\Models\\Permession', 366, 1, 'name', 'Delete Product Review', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20356, 'App\\Models\\Permession', 366, 2, 'name', 'حذف تقييم منتج', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20357, 'App\\Models\\Permession', 366, 1, 'group_by', 'Products', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20358, 'App\\Models\\Permession', 366, 2, 'group_by', 'منتجات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20359, 'App\\Models\\Permession', 367, 1, 'name', 'View Taxes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20360, 'App\\Models\\Permession', 367, 2, 'name', 'عرض الضرائب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20361, 'App\\Models\\Permession', 367, 1, 'group_by', 'Taxes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20362, 'App\\Models\\Permession', 367, 2, 'group_by', 'ضرائب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20363, 'App\\Models\\Permession', 368, 1, 'name', 'Create Tax', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20364, 'App\\Models\\Permession', 368, 2, 'name', 'إنشاء ضريبة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20365, 'App\\Models\\Permession', 368, 1, 'group_by', 'Taxes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20366, 'App\\Models\\Permession', 368, 2, 'group_by', 'ضرائب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20367, 'App\\Models\\Permession', 369, 1, 'name', 'Edit Tax', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20368, 'App\\Models\\Permession', 369, 2, 'name', 'تعديل ضريبة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20369, 'App\\Models\\Permession', 369, 1, 'group_by', 'Taxes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20370, 'App\\Models\\Permession', 369, 2, 'group_by', 'ضرائب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20371, 'App\\Models\\Permession', 370, 1, 'name', 'Delete Tax', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20372, 'App\\Models\\Permession', 370, 2, 'name', 'حذف ضريبة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20373, 'App\\Models\\Permession', 370, 1, 'group_by', 'Taxes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20374, 'App\\Models\\Permession', 370, 2, 'group_by', 'ضرائب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20375, 'App\\Models\\Permession', 371, 1, 'name', 'View Offers', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20376, 'App\\Models\\Permession', 371, 2, 'name', 'عرض العروض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20377, 'App\\Models\\Permession', 371, 1, 'group_by', 'Offers', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20378, 'App\\Models\\Permession', 371, 2, 'group_by', 'عروض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20379, 'App\\Models\\Permession', 372, 1, 'name', 'Create Offer', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20380, 'App\\Models\\Permession', 372, 2, 'name', 'إنشاء عرض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20381, 'App\\Models\\Permession', 372, 1, 'group_by', 'Offers', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20382, 'App\\Models\\Permession', 372, 2, 'group_by', 'عروض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20383, 'App\\Models\\Permession', 373, 1, 'name', 'Edit Offer', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20384, 'App\\Models\\Permession', 373, 2, 'name', 'تعديل عرض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20385, 'App\\Models\\Permession', 373, 1, 'group_by', 'Offers', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20386, 'App\\Models\\Permession', 373, 2, 'group_by', 'عروض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20387, 'App\\Models\\Permession', 374, 1, 'name', 'Delete Offer', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20388, 'App\\Models\\Permession', 374, 2, 'name', 'حذف عرض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20389, 'App\\Models\\Permession', 374, 1, 'group_by', 'Offers', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20390, 'App\\Models\\Permession', 374, 2, 'group_by', 'عروض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20391, 'App\\Models\\Permession', 375, 1, 'name', 'View Promocodes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20392, 'App\\Models\\Permession', 375, 2, 'name', 'عرض الكودات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20393, 'App\\Models\\Permession', 375, 1, 'group_by', 'Promocodes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20394, 'App\\Models\\Permession', 375, 2, 'group_by', 'كودات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20395, 'App\\Models\\Permession', 376, 1, 'name', 'Create Promocode', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20396, 'App\\Models\\Permession', 376, 2, 'name', 'إنشاء كود', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20397, 'App\\Models\\Permession', 376, 1, 'group_by', 'Promocodes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20398, 'App\\Models\\Permession', 376, 2, 'group_by', 'كودات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20399, 'App\\Models\\Permession', 377, 1, 'name', 'Edit Promocode', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20400, 'App\\Models\\Permession', 377, 2, 'name', 'تعديل كود', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20401, 'App\\Models\\Permession', 377, 1, 'group_by', 'Promocodes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20402, 'App\\Models\\Permession', 377, 2, 'group_by', 'كودات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20403, 'App\\Models\\Permession', 378, 1, 'name', 'Delete Promocode', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20404, 'App\\Models\\Permession', 378, 2, 'name', 'حذف كود', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20405, 'App\\Models\\Permession', 378, 1, 'group_by', 'Promocodes', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20406, 'App\\Models\\Permession', 378, 2, 'group_by', 'كودات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20407, 'App\\Models\\Permession', 379, 1, 'name', 'View Roles', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20408, 'App\\Models\\Permession', 379, 2, 'name', 'عرض الأدوار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20409, 'App\\Models\\Permession', 379, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20410, 'App\\Models\\Permession', 379, 2, 'group_by', 'إدارة الأدوار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20411, 'App\\Models\\Permession', 380, 1, 'name', 'Create Role', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20412, 'App\\Models\\Permession', 380, 2, 'name', 'إنشاء دور', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20413, 'App\\Models\\Permession', 380, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20414, 'App\\Models\\Permession', 380, 2, 'group_by', 'إدارة الأدوار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20415, 'App\\Models\\Permession', 381, 1, 'name', 'Edit Role', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20416, 'App\\Models\\Permession', 381, 2, 'name', 'تعديل دور', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20417, 'App\\Models\\Permession', 381, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20418, 'App\\Models\\Permession', 381, 2, 'group_by', 'إدارة الأدوار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20419, 'App\\Models\\Permession', 382, 1, 'name', 'Delete Role', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20420, 'App\\Models\\Permession', 382, 2, 'name', 'حذف دور', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20421, 'App\\Models\\Permession', 382, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20422, 'App\\Models\\Permession', 382, 2, 'group_by', 'إدارة الأدوار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20423, 'App\\Models\\Permession', 383, 1, 'name', 'View Admins', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20424, 'App\\Models\\Permession', 383, 2, 'name', 'عرض المسؤولين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20425, 'App\\Models\\Permession', 383, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20426, 'App\\Models\\Permession', 383, 2, 'group_by', 'إدارة المسؤولين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20427, 'App\\Models\\Permession', 384, 1, 'name', 'Create Admin', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20428, 'App\\Models\\Permession', 384, 2, 'name', 'إنشاء المسؤول', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20429, 'App\\Models\\Permession', 384, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20430, 'App\\Models\\Permession', 384, 2, 'group_by', 'إدارة المسؤولين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20431, 'App\\Models\\Permession', 385, 1, 'name', 'Edit Admin', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20432, 'App\\Models\\Permession', 385, 2, 'name', 'تعديل المسؤول', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20433, 'App\\Models\\Permession', 385, 1, 'group_by', 'Admin Management', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20434, 'App\\Models\\Permession', 385, 2, 'group_by', 'إدارة المسؤولين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20435, 'App\\Models\\Permession', 387, 1, 'name', 'View Vendors', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20436, 'App\\Models\\Permession', 387, 2, 'name', 'عرض الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20437, 'App\\Models\\Permession', 387, 1, 'group_by', 'Vendors', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20438, 'App\\Models\\Permession', 387, 2, 'group_by', 'الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20439, 'App\\Models\\Permession', 391, 1, 'name', 'View New Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20440, 'App\\Models\\Permession', 391, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20441, 'App\\Models\\Permession', 391, 1, 'group_by', 'Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20442, 'App\\Models\\Permession', 391, 2, 'group_by', 'طلبات الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20443, 'App\\Models\\Permession', 392, 1, 'name', 'Accept Vendor Request', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20444, 'App\\Models\\Permession', 392, 2, 'name', 'قبول طلب المورد', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20445, 'App\\Models\\Permession', 392, 1, 'group_by', 'Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20446, 'App\\Models\\Permession', 392, 2, 'group_by', 'طلبات الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20447, 'App\\Models\\Permession', 393, 1, 'name', 'Reject Vendor Request', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20448, 'App\\Models\\Permession', 393, 2, 'name', 'رفض طلب المورد', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20449, 'App\\Models\\Permession', 393, 1, 'group_by', 'Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20450, 'App\\Models\\Permession', 393, 2, 'group_by', 'طلبات الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20451, 'App\\Models\\Permession', 394, 1, 'name', 'View Accepted Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20452, 'App\\Models\\Permession', 394, 2, 'name', 'عرض طلبات الموردين المقبولة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20453, 'App\\Models\\Permession', 394, 1, 'group_by', 'Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20454, 'App\\Models\\Permession', 394, 2, 'group_by', 'طلبات الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20455, 'App\\Models\\Permession', 395, 1, 'name', 'View Rejected Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20456, 'App\\Models\\Permession', 395, 2, 'name', 'عرض طلبات الموردين الرفض', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20457, 'App\\Models\\Permession', 395, 1, 'group_by', 'Vendor Requests', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20458, 'App\\Models\\Permession', 395, 2, 'group_by', 'طلبات الموردين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20459, 'App\\Models\\Permession', 396, 1, 'name', 'View Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20460, 'App\\Models\\Permession', 396, 2, 'name', 'عرض المستخدمين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20461, 'App\\Models\\Permession', 396, 1, 'group_by', 'Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20462, 'App\\Models\\Permession', 396, 2, 'group_by', 'المستخدمين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20463, 'App\\Models\\Permession', 397, 1, 'name', 'Create User', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20464, 'App\\Models\\Permession', 397, 2, 'name', 'إنشاء مستخدم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20465, 'App\\Models\\Permession', 397, 1, 'group_by', 'Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20466, 'App\\Models\\Permession', 397, 2, 'group_by', 'المستخدمين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20467, 'App\\Models\\Permession', 398, 1, 'name', 'Edit User', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20468, 'App\\Models\\Permession', 398, 2, 'name', 'تعديل مستخدم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20469, 'App\\Models\\Permession', 398, 1, 'group_by', 'Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20470, 'App\\Models\\Permession', 398, 2, 'group_by', 'المستخدمين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20471, 'App\\Models\\Permession', 399, 1, 'name', 'Delete User', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20472, 'App\\Models\\Permession', 399, 2, 'name', 'حذف مستخدم', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20473, 'App\\Models\\Permession', 399, 1, 'group_by', 'Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20474, 'App\\Models\\Permession', 399, 2, 'group_by', 'المستخدمين', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20475, 'App\\Models\\Permession', 400, 1, 'name', 'View Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20476, 'App\\Models\\Permession', 400, 2, 'name', 'عرض الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20477, 'App\\Models\\Permession', 400, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20478, 'App\\Models\\Permession', 400, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20479, 'App\\Models\\Permession', 401, 1, 'name', 'View New Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20480, 'App\\Models\\Permession', 401, 2, 'name', 'عرض الطلبات الجديدة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20481, 'App\\Models\\Permession', 401, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20482, 'App\\Models\\Permession', 401, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20483, 'App\\Models\\Permession', 402, 1, 'name', 'View Inprogress Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20484, 'App\\Models\\Permession', 402, 2, 'name', 'عرض الطلبات المعلقة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20485, 'App\\Models\\Permession', 402, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20486, 'App\\Models\\Permession', 402, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20487, 'App\\Models\\Permession', 403, 1, 'name', 'View Delivered Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20488, 'App\\Models\\Permession', 403, 2, 'name', 'عرض الطلبات المكتملة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20489, 'App\\Models\\Permession', 403, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20490, 'App\\Models\\Permession', 403, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20491, 'App\\Models\\Permession', 404, 1, 'name', 'View Canceled Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20492, 'App\\Models\\Permession', 404, 2, 'name', 'عرض الطلبات الملغاة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20493, 'App\\Models\\Permession', 404, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20494, 'App\\Models\\Permession', 404, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20495, 'App\\Models\\Permession', 405, 1, 'name', 'View Refunded Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20496, 'App\\Models\\Permession', 405, 2, 'name', 'عرض الطلبات المدفوعة', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20497, 'App\\Models\\Permession', 405, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20498, 'App\\Models\\Permession', 405, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20499, 'App\\Models\\Permession', 406, 1, 'name', 'Edit Order', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20500, 'App\\Models\\Permession', 406, 2, 'name', 'تعديل طلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20501, 'App\\Models\\Permession', 406, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20502, 'App\\Models\\Permession', 406, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20503, 'App\\Models\\Permession', 407, 1, 'name', 'Delete Order', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20504, 'App\\Models\\Permession', 407, 2, 'name', 'حذف طلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20505, 'App\\Models\\Permession', 407, 1, 'group_by', 'Orders', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20506, 'App\\Models\\Permession', 407, 2, 'group_by', 'الطلبات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20507, 'App\\Models\\Permession', 408, 1, 'name', 'View Order Stages', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20508, 'App\\Models\\Permession', 408, 2, 'name', 'عرض خطوات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20509, 'App\\Models\\Permession', 408, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20510, 'App\\Models\\Permession', 408, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20511, 'App\\Models\\Permession', 409, 1, 'name', 'Create Order Stage', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20512, 'App\\Models\\Permession', 409, 2, 'name', 'إنشاء خطوة طلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20513, 'App\\Models\\Permession', 409, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20514, 'App\\Models\\Permession', 409, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20515, 'App\\Models\\Permession', 410, 1, 'name', 'Edit Order Stage', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20516, 'App\\Models\\Permession', 410, 2, 'name', 'تعديل خطوة طلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20517, 'App\\Models\\Permession', 410, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20518, 'App\\Models\\Permession', 410, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20519, 'App\\Models\\Permession', 411, 1, 'name', 'Delete Order Stage', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20520, 'App\\Models\\Permession', 411, 2, 'name', 'حذف خطوة طلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20521, 'App\\Models\\Permession', 411, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20522, 'App\\Models\\Permession', 411, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20523, 'App\\Models\\Permession', 412, 1, 'name', 'View Shipping Methods', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20524, 'App\\Models\\Permession', 412, 2, 'name', 'عرض طرق الشحن', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20525, 'App\\Models\\Permession', 412, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20526, 'App\\Models\\Permession', 412, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20527, 'App\\Models\\Permession', 413, 1, 'name', 'Create Shipping Method', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20528, 'App\\Models\\Permession', 413, 2, 'name', 'إنشاء طريقة شحن', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20529, 'App\\Models\\Permession', 413, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20530, 'App\\Models\\Permession', 413, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20531, 'App\\Models\\Permession', 414, 1, 'name', 'Edit Shipping Method', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20532, 'App\\Models\\Permession', 414, 2, 'name', 'تعديل طريقة شحن', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20533, 'App\\Models\\Permession', 414, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20534, 'App\\Models\\Permession', 414, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20535, 'App\\Models\\Permession', 415, 1, 'name', 'Delete Shipping Method', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20536, 'App\\Models\\Permession', 415, 2, 'name', 'حذف طريقة شحن', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20537, 'App\\Models\\Permession', 415, 1, 'group_by', 'Order Settings', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20538, 'App\\Models\\Permession', 415, 2, 'group_by', 'إعدادات الطلب', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20539, 'App\\Models\\Permession', 416, 1, 'name', 'View Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20540, 'App\\Models\\Permession', 416, 2, 'name', 'عرض نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20541, 'App\\Models\\Permession', 416, 1, 'group_by', 'Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20542, 'App\\Models\\Permession', 416, 2, 'group_by', 'نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20543, 'App\\Models\\Permession', 417, 1, 'name', 'Create Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20544, 'App\\Models\\Permession', 417, 2, 'name', 'إنشاء نظام نقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20545, 'App\\Models\\Permession', 417, 1, 'group_by', 'Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20546, 'App\\Models\\Permession', 417, 2, 'group_by', 'نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20547, 'App\\Models\\Permession', 418, 1, 'name', 'Edit Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20548, 'App\\Models\\Permession', 418, 2, 'name', 'تعديل نظام نقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20549, 'App\\Models\\Permession', 418, 1, 'group_by', 'Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20550, 'App\\Models\\Permession', 418, 2, 'group_by', 'نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20551, 'App\\Models\\Permession', 419, 1, 'name', 'Delete Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20552, 'App\\Models\\Permession', 419, 2, 'name', 'حذف نظام نقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20553, 'App\\Models\\Permession', 419, 1, 'group_by', 'Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20554, 'App\\Models\\Permession', 419, 2, 'group_by', 'نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20555, 'App\\Models\\Permession', 420, 1, 'name', 'View Points Users', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20556, 'App\\Models\\Permession', 420, 2, 'name', 'عرض مستخدمين النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20557, 'App\\Models\\Permession', 420, 1, 'group_by', 'Points System', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20558, 'App\\Models\\Permession', 420, 2, 'group_by', 'نظام النقاط', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20559, 'App\\Models\\Permession', 421, 1, 'name', 'View Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20560, 'App\\Models\\Permession', 421, 2, 'name', 'عرض الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20561, 'App\\Models\\Permession', 421, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20562, 'App\\Models\\Permession', 421, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20563, 'App\\Models\\Permession', 422, 1, 'name', 'Create Advertisement', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20564, 'App\\Models\\Permession', 422, 2, 'name', 'إنشاء إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20565, 'App\\Models\\Permession', 422, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20566, 'App\\Models\\Permession', 422, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20567, 'App\\Models\\Permession', 423, 1, 'name', 'Edit Advertisement', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20568, 'App\\Models\\Permession', 423, 2, 'name', 'تعديل إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20569, 'App\\Models\\Permession', 423, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20570, 'App\\Models\\Permession', 423, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20571, 'App\\Models\\Permession', 424, 1, 'name', 'Delete Advertisement', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20572, 'App\\Models\\Permession', 424, 2, 'name', 'حذف إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20573, 'App\\Models\\Permession', 424, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20574, 'App\\Models\\Permession', 424, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20575, 'App\\Models\\Permession', 425, 1, 'name', 'View Advertisement Positions', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20576, 'App\\Models\\Permession', 425, 2, 'name', 'عرض مواقع الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20577, 'App\\Models\\Permession', 425, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20578, 'App\\Models\\Permession', 425, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20579, 'App\\Models\\Permession', 426, 1, 'name', 'Create Advertisement Position', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20580, 'App\\Models\\Permession', 426, 2, 'name', 'إنشاء موقع إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20581, 'App\\Models\\Permession', 426, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20582, 'App\\Models\\Permession', 426, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20583, 'App\\Models\\Permession', 427, 1, 'name', 'Edit Advertisement Position', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20584, 'App\\Models\\Permession', 427, 2, 'name', 'تعديل موقع إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20585, 'App\\Models\\Permession', 427, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20586, 'App\\Models\\Permession', 427, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20587, 'App\\Models\\Permession', 428, 1, 'name', 'Delete Advertisement Position', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20588, 'App\\Models\\Permession', 428, 2, 'name', 'حذف موقع إعلان', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20589, 'App\\Models\\Permession', 428, 1, 'group_by', 'Advertisements', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20590, 'App\\Models\\Permession', 428, 2, 'group_by', 'الإعلانات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20591, 'App\\Models\\Permession', 429, 1, 'name', 'View Notifications', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20592, 'App\\Models\\Permession', 429, 2, 'name', 'عرض الإشعارات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20593, 'App\\Models\\Permession', 429, 1, 'group_by', 'Notifications', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20594, 'App\\Models\\Permession', 429, 2, 'group_by', 'الإشعارات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20595, 'App\\Models\\Permession', 430, 1, 'name', 'Send Notification', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20596, 'App\\Models\\Permession', 430, 2, 'name', 'إرسال إشعار', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20597, 'App\\Models\\Permession', 430, 1, 'group_by', 'Notifications', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20598, 'App\\Models\\Permession', 430, 2, 'group_by', 'الإشعارات', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20599, 'App\\Models\\Permession', 431, 1, 'name', 'Delete Notification', '2025-10-28 11:03:54', '2025-10-28 11:03:54', NULL),
(20600, 'App\\Models\\Permession', 431, 2, 'name', 'حذف إشعار', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20601, 'App\\Models\\Permession', 431, 1, 'group_by', 'Notifications', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20602, 'App\\Models\\Permession', 431, 2, 'group_by', 'الإشعارات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20603, 'App\\Models\\Permession', 432, 1, 'name', 'View Accounting Overview', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20604, 'App\\Models\\Permession', 432, 2, 'name', 'عرض ملخص المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20605, 'App\\Models\\Permession', 432, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20606, 'App\\Models\\Permession', 432, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20607, 'App\\Models\\Permession', 433, 1, 'name', 'View Accounting Balance', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20608, 'App\\Models\\Permession', 433, 2, 'name', 'عرض ميزانية المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20609, 'App\\Models\\Permession', 433, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20610, 'App\\Models\\Permession', 433, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20611, 'App\\Models\\Permession', 434, 1, 'name', 'View Accounting Expenses Keys', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20612, 'App\\Models\\Permession', 434, 2, 'name', 'عرض مفاتيح النفقات المالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20613, 'App\\Models\\Permession', 434, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20614, 'App\\Models\\Permession', 434, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20615, 'App\\Models\\Permession', 435, 1, 'name', 'Create Accounting Expenses Key', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20616, 'App\\Models\\Permession', 435, 2, 'name', 'إنشاء مفتاح نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20617, 'App\\Models\\Permession', 435, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20618, 'App\\Models\\Permession', 435, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20619, 'App\\Models\\Permession', 436, 1, 'name', 'Edit Accounting Expenses Key', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20620, 'App\\Models\\Permession', 436, 2, 'name', 'تعديل مفتاح نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20621, 'App\\Models\\Permession', 436, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL);
INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(20622, 'App\\Models\\Permession', 436, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20623, 'App\\Models\\Permession', 437, 1, 'name', 'Delete Accounting Expenses Key', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20624, 'App\\Models\\Permession', 437, 2, 'name', 'حذف مفتاح نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20625, 'App\\Models\\Permession', 437, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20626, 'App\\Models\\Permession', 437, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20627, 'App\\Models\\Permession', 438, 1, 'name', 'View Accounting Expenses', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20628, 'App\\Models\\Permession', 438, 2, 'name', 'عرض النفقات المالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20629, 'App\\Models\\Permession', 438, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20630, 'App\\Models\\Permession', 438, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20631, 'App\\Models\\Permession', 439, 1, 'name', 'Create Accounting Expense', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20632, 'App\\Models\\Permession', 439, 2, 'name', 'إنشاء نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20633, 'App\\Models\\Permession', 439, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20634, 'App\\Models\\Permession', 439, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20635, 'App\\Models\\Permession', 440, 1, 'name', 'Edit Accounting Expense', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20636, 'App\\Models\\Permession', 440, 2, 'name', 'تعديل نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20637, 'App\\Models\\Permession', 440, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20638, 'App\\Models\\Permession', 440, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20639, 'App\\Models\\Permession', 441, 1, 'name', 'Delete Accounting Expense', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20640, 'App\\Models\\Permession', 441, 2, 'name', 'حذف نفقات مالية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20641, 'App\\Models\\Permession', 441, 1, 'group_by', 'Accounting', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20642, 'App\\Models\\Permession', 441, 2, 'group_by', 'المالي', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20643, 'App\\Models\\Permession', 442, 1, 'name', 'View Send Money', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20644, 'App\\Models\\Permession', 442, 2, 'name', 'عرض إرسال المال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20645, 'App\\Models\\Permession', 442, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20646, 'App\\Models\\Permession', 442, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20647, 'App\\Models\\Permession', 443, 1, 'name', 'Create Send Money', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20648, 'App\\Models\\Permession', 443, 2, 'name', 'إنشاء إرسال المال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20649, 'App\\Models\\Permession', 443, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20650, 'App\\Models\\Permession', 443, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20651, 'App\\Models\\Permession', 444, 1, 'name', 'View Transactions', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20652, 'App\\Models\\Permession', 444, 2, 'name', 'عرض المعاملات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20653, 'App\\Models\\Permession', 444, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20654, 'App\\Models\\Permession', 444, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20655, 'App\\Models\\Permession', 445, 1, 'name', 'View New Vendor Requests', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20656, 'App\\Models\\Permession', 445, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20657, 'App\\Models\\Permession', 445, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20658, 'App\\Models\\Permession', 445, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20659, 'App\\Models\\Permession', 446, 1, 'name', 'Accept Vendor Request', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20660, 'App\\Models\\Permession', 446, 2, 'name', 'قبول طلب المورد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20661, 'App\\Models\\Permession', 446, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20662, 'App\\Models\\Permession', 446, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20663, 'App\\Models\\Permession', 447, 1, 'name', 'Reject Vendor Request', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20664, 'App\\Models\\Permession', 447, 2, 'name', 'رفض طلب المورد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20665, 'App\\Models\\Permession', 447, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20666, 'App\\Models\\Permession', 447, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20667, 'App\\Models\\Permession', 448, 1, 'name', 'View Accepted Vendor Requests', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20668, 'App\\Models\\Permession', 448, 2, 'name', 'عرض طلبات الموردين المقبولة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20669, 'App\\Models\\Permession', 448, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20670, 'App\\Models\\Permession', 448, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20671, 'App\\Models\\Permession', 449, 1, 'name', 'View Rejected Vendor Requests', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20672, 'App\\Models\\Permession', 449, 2, 'name', 'عرض طلبات الموردين الرفض', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20673, 'App\\Models\\Permession', 449, 1, 'group_by', 'Withdraw', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20674, 'App\\Models\\Permession', 449, 2, 'group_by', 'سحب', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20675, 'App\\Models\\Permession', 450, 1, 'name', 'View Blog Categories', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20676, 'App\\Models\\Permession', 450, 2, 'name', 'عرض مجموعات المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20677, 'App\\Models\\Permession', 450, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20678, 'App\\Models\\Permession', 450, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20679, 'App\\Models\\Permession', 451, 1, 'name', 'Create Blog Category', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20680, 'App\\Models\\Permession', 451, 2, 'name', 'إنشاء مجموعات المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20681, 'App\\Models\\Permession', 451, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20682, 'App\\Models\\Permession', 451, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20683, 'App\\Models\\Permession', 452, 1, 'name', 'Edit Blog Category', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20684, 'App\\Models\\Permession', 452, 2, 'name', 'تعديل مجموعات المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20685, 'App\\Models\\Permession', 452, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20686, 'App\\Models\\Permession', 452, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20687, 'App\\Models\\Permession', 453, 1, 'name', 'Delete Blog Category', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20688, 'App\\Models\\Permession', 453, 2, 'name', 'حذف مجموعات المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20689, 'App\\Models\\Permession', 453, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20690, 'App\\Models\\Permession', 453, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20691, 'App\\Models\\Permession', 454, 1, 'name', 'View Blog Posts', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20692, 'App\\Models\\Permession', 454, 2, 'name', 'عرض المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20693, 'App\\Models\\Permession', 454, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20694, 'App\\Models\\Permession', 454, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20695, 'App\\Models\\Permession', 455, 1, 'name', 'Create Blog Post', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20696, 'App\\Models\\Permession', 455, 2, 'name', 'إنشاء مقال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20697, 'App\\Models\\Permession', 455, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20698, 'App\\Models\\Permession', 455, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20699, 'App\\Models\\Permession', 456, 1, 'name', 'Edit Blog Post', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20700, 'App\\Models\\Permession', 456, 2, 'name', 'تعديل مقال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20701, 'App\\Models\\Permession', 456, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20702, 'App\\Models\\Permession', 456, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20703, 'App\\Models\\Permession', 457, 1, 'name', 'Delete Blog Post', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20704, 'App\\Models\\Permession', 457, 2, 'name', 'حذف مقال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20705, 'App\\Models\\Permession', 457, 1, 'group_by', 'Blog Management', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20706, 'App\\Models\\Permession', 457, 2, 'group_by', 'إدارة المقالات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20707, 'App\\Models\\Permession', 458, 1, 'name', 'View Registered Users', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20708, 'App\\Models\\Permession', 458, 2, 'name', 'عرض المستخدمين المسجلين', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20709, 'App\\Models\\Permession', 458, 1, 'group_by', 'Reports', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20710, 'App\\Models\\Permession', 458, 2, 'group_by', 'التقارير', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20711, 'App\\Models\\Permession', 459, 1, 'name', 'View Area Users', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20712, 'App\\Models\\Permession', 459, 2, 'name', 'عرض المستخدمين في المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20713, 'App\\Models\\Permession', 459, 1, 'group_by', 'Reports', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20714, 'App\\Models\\Permession', 459, 2, 'group_by', 'التقارير', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20715, 'App\\Models\\Permession', 460, 1, 'name', 'View Orders', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20716, 'App\\Models\\Permession', 460, 2, 'name', 'عرض الطلبات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20717, 'App\\Models\\Permession', 460, 1, 'group_by', 'Reports', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20718, 'App\\Models\\Permession', 460, 2, 'group_by', 'التقارير', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20719, 'App\\Models\\Permession', 461, 1, 'name', 'View Products', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20720, 'App\\Models\\Permession', 461, 2, 'name', 'عرض المنتجات', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20721, 'App\\Models\\Permession', 461, 1, 'group_by', 'Reports', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20722, 'App\\Models\\Permession', 461, 2, 'group_by', 'التقارير', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20723, 'App\\Models\\Permession', 462, 1, 'name', 'View Points', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20724, 'App\\Models\\Permession', 462, 2, 'name', 'عرض النقاط', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20725, 'App\\Models\\Permession', 462, 1, 'group_by', 'Reports', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20726, 'App\\Models\\Permession', 462, 2, 'group_by', 'التقارير', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20727, 'App\\Models\\Permession', 463, 1, 'name', 'View System Log', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20728, 'App\\Models\\Permession', 463, 2, 'name', 'عرض سجل النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20729, 'App\\Models\\Permession', 463, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20730, 'App\\Models\\Permession', 463, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20731, 'App\\Models\\Permession', 464, 1, 'name', 'View Country', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20732, 'App\\Models\\Permession', 464, 2, 'name', 'عرض البلد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20733, 'App\\Models\\Permession', 464, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20734, 'App\\Models\\Permession', 464, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20735, 'App\\Models\\Permession', 465, 1, 'name', 'Create Country', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20736, 'App\\Models\\Permession', 465, 2, 'name', 'إنشاء بلد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20737, 'App\\Models\\Permession', 465, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20738, 'App\\Models\\Permession', 465, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20739, 'App\\Models\\Permession', 466, 1, 'name', 'Edit Country', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20740, 'App\\Models\\Permession', 466, 2, 'name', 'تعديل بلد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20741, 'App\\Models\\Permession', 466, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20742, 'App\\Models\\Permession', 466, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20743, 'App\\Models\\Permession', 467, 1, 'name', 'Delete Country', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20744, 'App\\Models\\Permession', 467, 2, 'name', 'حذف بلد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20745, 'App\\Models\\Permession', 467, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20746, 'App\\Models\\Permession', 467, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20747, 'App\\Models\\Permession', 468, 1, 'name', 'View City', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20748, 'App\\Models\\Permession', 468, 2, 'name', 'عرض المدينة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20749, 'App\\Models\\Permession', 468, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20750, 'App\\Models\\Permession', 468, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20751, 'App\\Models\\Permession', 469, 1, 'name', 'Create City', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20752, 'App\\Models\\Permession', 469, 2, 'name', 'إنشاء مدينة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20753, 'App\\Models\\Permession', 469, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20754, 'App\\Models\\Permession', 469, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20755, 'App\\Models\\Permession', 470, 1, 'name', 'Edit City', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20756, 'App\\Models\\Permession', 470, 2, 'name', 'تعديل مدينة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20757, 'App\\Models\\Permession', 470, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20758, 'App\\Models\\Permession', 470, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20759, 'App\\Models\\Permession', 471, 1, 'name', 'Delete City', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20760, 'App\\Models\\Permession', 471, 2, 'name', 'حذف مدينة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20761, 'App\\Models\\Permession', 471, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20762, 'App\\Models\\Permession', 471, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20763, 'App\\Models\\Permession', 472, 1, 'name', 'View Region', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20764, 'App\\Models\\Permession', 472, 2, 'name', 'عرض المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20765, 'App\\Models\\Permession', 472, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20766, 'App\\Models\\Permession', 472, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20767, 'App\\Models\\Permession', 473, 1, 'name', 'Create Region', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20768, 'App\\Models\\Permession', 473, 2, 'name', 'إنشاء منطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20769, 'App\\Models\\Permession', 473, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20770, 'App\\Models\\Permession', 473, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20771, 'App\\Models\\Permession', 474, 1, 'name', 'Edit Region', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20772, 'App\\Models\\Permession', 474, 2, 'name', 'تعديل منطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20773, 'App\\Models\\Permession', 474, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20774, 'App\\Models\\Permession', 474, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20775, 'App\\Models\\Permession', 475, 1, 'name', 'Delete Region', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20776, 'App\\Models\\Permession', 475, 2, 'name', 'حذف منطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20777, 'App\\Models\\Permession', 475, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20778, 'App\\Models\\Permession', 475, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20779, 'App\\Models\\Permession', 476, 1, 'name', 'View Subregion', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20780, 'App\\Models\\Permession', 476, 2, 'name', 'عرض المنطقة الفرعية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20781, 'App\\Models\\Permession', 476, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20782, 'App\\Models\\Permession', 476, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20783, 'App\\Models\\Permession', 477, 1, 'name', 'Create Subregion', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20784, 'App\\Models\\Permession', 477, 2, 'name', 'إنشاء منطقة فرعية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20785, 'App\\Models\\Permession', 477, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20786, 'App\\Models\\Permession', 477, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20787, 'App\\Models\\Permession', 478, 1, 'name', 'Edit Subregion', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20788, 'App\\Models\\Permession', 478, 2, 'name', 'تعديل منطقة فرعية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20789, 'App\\Models\\Permession', 478, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20790, 'App\\Models\\Permession', 478, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20791, 'App\\Models\\Permession', 479, 1, 'name', 'Delete Subregion', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20792, 'App\\Models\\Permession', 479, 2, 'name', 'حذف منطقة فرعية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20793, 'App\\Models\\Permession', 479, 1, 'group_by', 'Area Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20794, 'App\\Models\\Permession', 479, 2, 'group_by', 'إعدادات المنطقة', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20795, 'App\\Models\\Permession', 480, 1, 'name', 'View Terms', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20796, 'App\\Models\\Permession', 480, 2, 'name', 'عرض الشروط', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20797, 'App\\Models\\Permession', 480, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20798, 'App\\Models\\Permession', 480, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20799, 'App\\Models\\Permession', 481, 1, 'name', 'Edit Terms', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20800, 'App\\Models\\Permession', 481, 2, 'name', 'تعديل الشروط', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20801, 'App\\Models\\Permession', 481, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20802, 'App\\Models\\Permession', 481, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20803, 'App\\Models\\Permession', 482, 1, 'name', 'View Privacy', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20804, 'App\\Models\\Permession', 482, 2, 'name', 'عرض الخصوصية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20805, 'App\\Models\\Permession', 482, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20806, 'App\\Models\\Permession', 482, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20807, 'App\\Models\\Permession', 483, 1, 'name', 'Edit Privacy', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20808, 'App\\Models\\Permession', 483, 2, 'name', 'تعديل الخصوصية', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20809, 'App\\Models\\Permession', 483, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20810, 'App\\Models\\Permession', 483, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20811, 'App\\Models\\Permession', 484, 1, 'name', 'View About', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20812, 'App\\Models\\Permession', 484, 2, 'name', 'عرض عن النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20813, 'App\\Models\\Permession', 484, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20814, 'App\\Models\\Permession', 484, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20815, 'App\\Models\\Permession', 485, 1, 'name', 'Edit About', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20816, 'App\\Models\\Permession', 485, 2, 'name', 'تعديل عن النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20817, 'App\\Models\\Permession', 485, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20818, 'App\\Models\\Permession', 485, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20819, 'App\\Models\\Permession', 486, 1, 'name', 'View Contact', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20820, 'App\\Models\\Permession', 486, 2, 'name', 'عرض الاتصال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20821, 'App\\Models\\Permession', 486, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20822, 'App\\Models\\Permession', 486, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20823, 'App\\Models\\Permession', 487, 1, 'name', 'Edit Contact', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20824, 'App\\Models\\Permession', 487, 2, 'name', 'تعديل الاتصال', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20825, 'App\\Models\\Permession', 487, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20826, 'App\\Models\\Permession', 487, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20827, 'App\\Models\\Permession', 488, 1, 'name', 'View Messages', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20828, 'App\\Models\\Permession', 488, 2, 'name', 'عرض الرسائل', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20829, 'App\\Models\\Permession', 488, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20830, 'App\\Models\\Permession', 488, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20831, 'App\\Models\\Permession', 489, 1, 'name', 'Delete Messages', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20832, 'App\\Models\\Permession', 489, 2, 'name', 'حذف الرسائل', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20833, 'App\\Models\\Permession', 489, 1, 'group_by', 'System Settings', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20834, 'App\\Models\\Permession', 489, 2, 'group_by', 'إعدادات النظام', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20835, 'App\\Models\\Role', 16, 1, 'name', 'Vendor User', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20836, 'App\\Models\\Role', 16, 2, 'name', 'مستخدم مورد', '2025-10-28 11:03:55', '2025-10-28 11:03:55', NULL),
(20837, 'Modules\\AreaSettings\\app\\Models\\Country', 1, 1, 'name', 'Egypt', '2025-10-28 11:52:39', '2025-10-28 11:52:39', NULL),
(20838, 'Modules\\AreaSettings\\app\\Models\\Country', 1, 2, 'name', 'مصر', '2025-10-28 11:52:39', '2025-10-28 11:52:39', NULL),
(20839, 'Modules\\AreaSettings\\app\\Models\\Country', 2, 1, 'name', 'somal', '2025-10-28 11:52:54', '2025-10-28 11:54:31', NULL),
(20840, 'Modules\\AreaSettings\\app\\Models\\Country', 2, 2, 'name', 'الصومال', '2025-10-28 11:52:54', '2025-10-28 11:52:54', NULL),
(20841, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-10-28 11:55:30', '2025-10-28 16:50:49', '2025-10-28 16:50:49'),
(20842, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-10-28 11:55:30', '2025-10-28 16:50:49', '2025-10-28 16:50:49'),
(20843, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-10-28 11:55:30', '2025-10-28 16:50:49', '2025-10-28 16:50:49'),
(20844, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-10-28 11:55:30', '2025-10-28 16:50:49', '2025-10-28 16:50:49'),
(20845, 'App\\Models\\Role', 21, 1, 'name', 'ssssssssssss', '2025-10-28 15:21:52', '2025-10-28 16:03:48', '2025-10-28 16:03:48'),
(20846, 'App\\Models\\Role', 21, 2, 'name', 'szxcxzdas', '2025-10-28 15:21:52', '2025-10-28 16:03:48', '2025-10-28 16:03:48'),
(20847, 'App\\Models\\Role', 19, 1, 'name', 'sadsad', '2025-10-28 16:19:59', '2025-10-28 16:19:59', NULL),
(20848, 'App\\Models\\Role', 19, 2, 'name', 'asdasdas', '2025-10-28 16:19:59', '2025-10-28 16:19:59', NULL),
(20849, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-10-28 16:50:49', '2025-10-28 16:53:15', '2025-10-28 16:53:15'),
(20850, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-10-28 16:50:49', '2025-10-28 16:53:15', '2025-10-28 16:53:15'),
(20851, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-10-28 16:50:49', '2025-10-28 16:53:15', '2025-10-28 16:53:15'),
(20852, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-10-28 16:50:49', '2025-10-28 16:53:15', '2025-10-28 16:53:15'),
(20853, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-10-28 16:53:15', '2025-10-28 17:01:49', '2025-10-28 17:01:49'),
(20854, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-10-28 16:53:15', '2025-10-28 17:01:49', '2025-10-28 17:01:49'),
(20855, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-10-28 16:53:15', '2025-10-28 17:01:49', '2025-10-28 17:01:49'),
(20856, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-10-28 16:53:15', '2025-10-28 17:01:49', '2025-10-28 17:01:49'),
(20857, 'Modules\\Vendor\\app\\Models\\Vendor', 3, 1, 'name', 'asdasdasd', '2025-10-28 17:00:50', '2025-10-28 17:00:50', NULL),
(20858, 'Modules\\Vendor\\app\\Models\\Vendor', 3, 2, 'name', 'zxczxczx', '2025-10-28 17:00:50', '2025-10-28 17:00:50', NULL),
(20859, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-10-28 17:01:49', '2025-11-02 10:18:27', '2025-11-02 10:18:27'),
(20860, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-10-28 17:01:49', '2025-11-02 10:18:27', '2025-11-02 10:18:27'),
(20861, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-10-28 17:01:49', '2025-11-02 10:18:27', '2025-11-02 10:18:27'),
(20862, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-10-28 17:01:49', '2025-11-02 10:18:27', '2025-11-02 10:18:27'),
(20863, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10021, 1, 'name', 'asdasd', '2025-10-29 04:21:29', '2025-10-29 04:21:29', NULL),
(20864, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10021, 1, 'description', 'asdasl;dl;', '2025-10-29 04:21:29', '2025-10-29 04:21:29', NULL),
(20865, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10021, 2, 'name', 'شسيمنشمسين', '2025-10-29 04:21:29', '2025-10-29 04:21:29', NULL),
(20866, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10021, 2, 'description', 'ldsalk;d', '2025-10-29 04:21:29', '2025-10-29 04:21:29', NULL),
(20867, 'Modules\\CategoryManagment\\app\\Models\\Activity', 9, 1, 'name', 'asdaskldl;', '2025-10-29 04:22:33', '2025-10-29 04:22:33', NULL),
(20868, 'Modules\\CategoryManagment\\app\\Models\\Activity', 9, 2, 'name', ';lkas;ldaskl;dklas;', '2025-10-29 04:22:33', '2025-10-29 04:22:33', NULL),
(20869, 'Modules\\Brands\\app\\Models\\Brand', 1, 1, 'name', 'asda', '2025-10-29 05:39:21', '2025-10-29 05:39:21', NULL),
(20870, 'Modules\\Brands\\app\\Models\\Brand', 1, 2, 'name', 'ِسمينشنسمكين', '2025-10-29 05:39:21', '2025-10-29 05:39:21', NULL),
(20871, 'Modules\\Brands\\app\\Models\\Brand', 2, 1, 'name', 'brand name', '2025-10-29 08:18:58', '2025-10-29 08:31:53', '2025-10-29 08:31:53'),
(20872, 'Modules\\Brands\\app\\Models\\Brand', 2, 1, 'description', 'asdl;sadlk;asld;k;l', '2025-10-29 08:18:58', '2025-10-29 08:31:53', '2025-10-29 08:31:53'),
(20873, 'Modules\\Brands\\app\\Models\\Brand', 2, 2, 'name', 'شسمنكيشسنمكيشكنمس', '2025-10-29 08:18:58', '2025-10-29 08:31:53', '2025-10-29 08:31:53'),
(20874, 'Modules\\Brands\\app\\Models\\Brand', 2, 2, 'description', 'ksal;dkl;as;lkdas', '2025-10-29 08:18:58', '2025-10-29 08:31:53', '2025-10-29 08:31:53'),
(20875, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 1, 'name', 'sssssssss', '2025-10-29 09:02:41', '2025-10-29 09:02:41', NULL),
(20876, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 1, 'description', 'as;ljdkljasdjklasjkldasjkldjlk', '2025-10-29 09:02:41', '2025-10-29 09:02:41', NULL),
(20877, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 2, 'name', 'شسميكنشنكمينكمشسنمكي', '2025-10-29 09:02:41', '2025-10-29 09:02:41', NULL),
(20878, 'Modules\\CatalogManagement\\app\\Models\\Brand', 1, 2, 'description', 'ئءةمؤمكءئنؤنمئءنمك', '2025-10-29 09:02:41', '2025-10-29 09:02:41', NULL),
(20879, 'Modules\\CatalogManagement\\app\\Models\\Tax', 1, 1, 'name', 'aklsdjklasd', '2025-10-29 09:20:12', '2025-10-29 09:20:12', NULL),
(20880, 'Modules\\CatalogManagement\\app\\Models\\Tax', 1, 2, 'name', 'شسمكنينمكشسينمكش', '2025-10-29 09:20:12', '2025-10-29 09:20:12', NULL),
(20881, 'Modules\\CatalogManagement\\app\\Models\\Tax', 2, 1, 'name', 'aaaaaaaaaaaa', '2025-10-29 09:20:38', '2025-10-30 06:38:22', '2025-10-30 06:38:22'),
(20882, 'Modules\\CatalogManagement\\app\\Models\\Tax', 2, 2, 'name', 'سشييشسي', '2025-10-29 09:20:38', '2025-10-30 06:38:22', '2025-10-30 06:38:22'),
(20883, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 1, 'name', 'a,sdaskdl;as', '2025-10-29 09:29:24', '2025-10-29 09:38:21', '2025-10-29 09:38:21'),
(20884, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 1, 'description', 'askdjaskl;d', '2025-10-29 09:29:24', '2025-10-29 09:38:21', '2025-10-29 09:38:21'),
(20885, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 2, 'name', 'شمنكسينمكشسنيمك', '2025-10-29 09:29:24', '2025-10-29 09:38:21', '2025-10-29 09:38:21'),
(20886, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 2, 'description', 'asdasd', '2025-10-29 09:29:24', '2025-10-29 09:38:21', '2025-10-29 09:38:21'),
(20887, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'name', 'asdasdasd', '2025-10-29 09:36:24', '2025-10-29 10:26:04', '2025-10-29 10:26:04'),
(20888, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'description', 'adaslkdaskdasdkalsdaskl;dlkjasdjlkaskljd', '2025-10-29 09:36:24', '2025-10-29 10:26:04', '2025-10-29 10:26:04'),
(20889, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'name', 'نشمسيمنكشينمكشنمكي', '2025-10-29 09:36:24', '2025-10-29 10:26:04', '2025-10-29 10:26:04'),
(20890, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'description', 'شسنمكيشسكمنينمكشسيكنمشسينكمشسكم', '2025-10-29 09:36:24', '2025-10-29 10:26:04', '2025-10-29 10:26:04'),
(20891, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'name', 'zxclk\'zxlk;', '2025-10-29 09:36:41', '2025-10-29 10:52:04', '2025-10-29 10:52:04'),
(20892, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'description', 'kasdlkjaksl', '2025-10-29 09:36:41', '2025-10-29 10:52:04', '2025-10-29 10:52:04'),
(20893, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'name', 'شسنمكيشمكنيشمك', '2025-10-29 09:36:41', '2025-10-29 10:52:04', '2025-10-29 10:52:04'),
(20894, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'description', 'كمنسشنميشس', '2025-10-29 09:36:41', '2025-10-29 10:52:04', '2025-10-29 10:52:04'),
(20895, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 1, 'name', 'a,sdaskdl;as', '2025-10-29 09:38:21', '2025-10-29 09:38:21', NULL),
(20896, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 1, 'description', 'askdjaskl;d', '2025-10-29 09:38:21', '2025-10-29 09:38:21', NULL),
(20897, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 2, 'name', 'شمنكسينمكشسنيمك', '2025-10-29 09:38:21', '2025-10-29 09:38:21', NULL),
(20898, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 1, 2, 'description', 'asdasd', '2025-10-29 09:38:21', '2025-10-29 09:38:21', NULL),
(20899, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 1, 'name', 'test', '2025-10-29 10:25:37', '2025-10-29 11:43:17', '2025-10-29 11:43:17'),
(20900, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 1, 'description', 'asdl;sakldjklasdk', '2025-10-29 10:25:37', '2025-10-29 11:43:17', '2025-10-29 11:43:17'),
(20901, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 2, 'name', 'تيست', '2025-10-29 10:25:37', '2025-10-29 11:43:17', '2025-10-29 11:43:17'),
(20902, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 2, 'description', 'شتنمسكيكمنشسنمكيسش', '2025-10-29 10:25:37', '2025-10-29 11:43:17', '2025-10-29 11:43:17'),
(20903, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'name', 'aaaaaaaaaaa', '2025-10-29 10:26:04', '2025-11-02 12:52:09', '2025-11-02 12:52:09'),
(20904, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'description', 'adaslkdaskdasdkalsdaskl;dlkjasdjlkaskljd', '2025-10-29 10:26:04', '2025-11-02 12:52:09', '2025-11-02 12:52:09'),
(20905, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'name', 'ءءءءءءءءءءءءءءءء', '2025-10-29 10:26:04', '2025-11-02 12:52:09', '2025-11-02 12:52:09'),
(20906, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'description', 'شسنمكيشسكمنينمكشسيكنمشسينكمشسكم', '2025-10-29 10:26:04', '2025-11-02 12:52:09', '2025-11-02 12:52:09'),
(20907, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'name', 'zxclk\'zxlk;', '2025-10-29 10:52:04', '2025-10-29 11:58:10', '2025-10-29 11:58:10'),
(20908, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'description', 'kasdlkjaksl', '2025-10-29 10:52:04', '2025-10-29 11:58:10', '2025-10-29 11:58:10'),
(20909, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'name', 'شسنمكيشمكنيشمك', '2025-10-29 10:52:04', '2025-10-29 11:58:10', '2025-10-29 11:58:10'),
(20910, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'description', 'كمنسشنميشس', '2025-10-29 10:52:04', '2025-10-29 11:58:10', '2025-10-29 11:58:10'),
(20911, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 1, 'name', 'first department', '2025-10-29 11:43:17', '2025-10-29 11:43:17', NULL),
(20912, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 1, 'description', 'asdl;sakldjklasdk', '2025-10-29 11:43:17', '2025-10-29 11:43:17', NULL),
(20913, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 2, 'name', 'اول ديبارتمينت', '2025-10-29 11:43:17', '2025-10-29 11:43:17', NULL),
(20914, 'Modules\\CategoryManagment\\app\\Models\\Department', 7, 2, 'description', 'شتنمسكيكمنشسنمكيسش', '2025-10-29 11:43:17', '2025-10-29 11:43:17', NULL),
(20915, 'Modules\\CategoryManagment\\app\\Models\\Department', 6, 1, 'name', 'second department', '2025-10-29 11:43:39', '2025-10-29 11:43:39', NULL),
(20916, 'Modules\\CategoryManagment\\app\\Models\\Department', 6, 2, 'name', 'تانى ديبارتمينت', '2025-10-29 11:43:39', '2025-10-29 11:43:39', NULL),
(20917, 'Modules\\CategoryManagment\\app\\Models\\Department', 5, 1, 'name', 'third department', '2025-10-29 11:43:57', '2025-10-29 11:44:14', '2025-10-29 11:44:14'),
(20918, 'Modules\\CategoryManagment\\app\\Models\\Department', 5, 2, 'name', 'تالت ديبارتمينت', '2025-10-29 11:43:57', '2025-10-29 11:44:14', '2025-10-29 11:44:14'),
(20919, 'Modules\\CategoryManagment\\app\\Models\\Department', 8, 1, 'name', 'third department', '2025-10-29 11:46:29', '2025-10-29 11:46:39', '2025-10-29 11:46:39'),
(20920, 'Modules\\CategoryManagment\\app\\Models\\Department', 8, 2, 'name', 'ثالث ديبارتمينت', '2025-10-29 11:46:29', '2025-10-29 11:46:39', '2025-10-29 11:46:39'),
(20921, 'Modules\\CategoryManagment\\app\\Models\\Department', 8, 1, 'name', 'third department', '2025-10-29 11:46:39', '2025-10-29 11:46:39', NULL),
(20922, 'Modules\\CategoryManagment\\app\\Models\\Department', 8, 2, 'name', 'ثالث ديبارتمينت', '2025-10-29 11:46:39', '2025-10-29 11:46:39', NULL),
(20923, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 1, 'name', 'Clothing', '2025-10-29 11:55:47', '2025-10-29 11:57:42', '2025-10-29 11:57:42'),
(20924, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 2, 'name', 'ملابس', '2025-10-29 11:55:47', '2025-10-29 11:57:42', '2025-10-29 11:57:42'),
(20925, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 1, 'name', 'Cheeses', '2025-10-29 11:57:42', '2025-10-29 11:58:30', '2025-10-29 11:58:30'),
(20926, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 2, 'name', 'جبن', '2025-10-29 11:57:42', '2025-10-29 11:58:30', '2025-10-29 11:58:30'),
(20927, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'name', 'milks', '2025-10-29 11:58:10', '2025-10-29 11:58:10', NULL),
(20928, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 1, 'description', 'kasdlkjaksl', '2025-10-29 11:58:10', '2025-10-29 11:58:10', NULL),
(20929, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'name', 'ألبان', '2025-10-29 11:58:10', '2025-10-29 11:58:10', NULL),
(20930, 'Modules\\CategoryManagment\\app\\Models\\Category', 2, 2, 'description', 'كمنسشنميشس', '2025-10-29 11:58:10', '2025-10-29 11:58:10', NULL),
(20931, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 1, 'name', 'butchers', '2025-10-29 11:58:30', '2025-10-29 11:58:30', NULL),
(20932, 'Modules\\CategoryManagment\\app\\Models\\Category', 1, 2, 'name', 'جزارة', '2025-10-29 11:58:30', '2025-10-29 11:58:30', NULL),
(20933, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 1, 1, 'name', 'height', '2025-10-30 05:18:40', '2025-10-30 09:31:04', NULL),
(20934, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 1, 2, 'name', 'الطول', '2025-10-30 05:18:40', '2025-10-30 09:31:04', NULL),
(20935, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 2, 1, 'name', 'ton', '2025-10-30 05:19:05', '2025-10-30 09:32:12', '2025-10-30 09:32:12'),
(20936, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 2, 2, 'name', 'طن', '2025-10-30 05:19:05', '2025-10-30 09:32:12', '2025-10-30 09:32:12'),
(20937, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 3, 1, 'name', 'Centemeter', '2025-10-30 05:19:19', '2025-10-30 07:32:36', '2025-10-30 07:32:36'),
(20938, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 3, 2, 'name', 'سنتى متر', '2025-10-30 05:19:19', '2025-10-30 07:32:36', '2025-10-30 07:32:36'),
(20939, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 4, 1, 'name', 'sss', '2025-10-30 06:28:19', '2025-10-30 06:53:19', '2025-10-30 06:53:19'),
(20940, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 4, 2, 'name', 'شسنمنشمسي', '2025-10-30 06:28:19', '2025-10-30 06:53:19', '2025-10-30 06:53:19'),
(20941, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 5, 1, 'name', 'شسيشس', '2025-10-30 06:28:25', '2025-10-30 06:53:14', '2025-10-30 06:53:14'),
(20942, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 5, 2, 'name', 'يشسيشسي', '2025-10-30 06:28:25', '2025-10-30 06:53:14', '2025-10-30 06:53:14'),
(20943, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 6, 1, 'name', 'Sizes', '2025-10-30 06:28:38', '2025-10-30 07:32:30', '2025-10-30 07:32:30'),
(20944, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 6, 2, 'name', 'المقاسات', '2025-10-30 06:28:38', '2025-10-30 07:32:30', '2025-10-30 07:32:30'),
(20945, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 7, 1, 'name', 'qqqqqqqq', '2025-10-30 06:28:47', '2025-10-30 06:40:17', '2025-10-30 06:40:17'),
(20946, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 7, 2, 'name', 'سيسشنمي', '2025-10-30 06:28:47', '2025-10-30 06:40:17', '2025-10-30 06:40:17'),
(20947, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 8, 1, 'name', 'asdasd', '2025-10-30 06:28:57', '2025-10-30 06:37:29', '2025-10-30 06:37:29'),
(20948, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 8, 2, 'name', 'ِسيشسيشسيس', '2025-10-30 06:28:57', '2025-10-30 06:37:29', '2025-10-30 06:37:29'),
(20949, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 9, 1, 'name', 'zxcxcads', '2025-10-30 06:29:05', '2025-10-30 06:36:37', '2025-10-30 06:36:37'),
(20950, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 9, 2, 'name', 'dsadasd', '2025-10-30 06:29:05', '2025-10-30 06:36:37', '2025-10-30 06:36:37'),
(20951, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 10, 1, 'name', 'asdasd', '2025-10-30 06:29:11', '2025-10-30 06:36:24', '2025-10-30 06:36:24'),
(20952, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 10, 2, 'name', 'asdasd', '2025-10-30 06:29:11', '2025-10-30 06:36:24', '2025-10-30 06:36:24'),
(20953, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 11, 1, 'name', 'asdasd', '2025-10-30 06:29:19', '2025-10-30 06:36:19', '2025-10-30 06:36:19'),
(20954, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 11, 2, 'name', 'asdasdsa', '2025-10-30 06:29:19', '2025-10-30 06:36:19', '2025-10-30 06:36:19'),
(20955, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 12, 1, 'name', 'xxxxxx', '2025-10-30 06:29:27', '2025-10-30 06:33:01', '2025-10-30 06:33:01'),
(20956, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 12, 2, 'name', 'asdsadsa', '2025-10-30 06:29:27', '2025-10-30 06:33:01', '2025-10-30 06:33:01'),
(20957, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 13, 1, 'name', 'asdas', '2025-10-30 06:37:36', '2025-10-30 06:40:11', '2025-10-30 06:40:11'),
(20958, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 13, 2, 'name', 'dasdasd', '2025-10-30 06:37:36', '2025-10-30 06:40:11', '2025-10-30 06:40:11'),
(20959, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 14, 1, 'name', 'meter', '2025-10-30 06:54:18', '2025-10-30 07:32:25', '2025-10-30 07:32:25'),
(20960, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 14, 2, 'name', 'متر', '2025-10-30 06:54:18', '2025-10-30 07:32:25', '2025-10-30 07:32:25'),
(20961, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 15, 1, 'name', 'meter moqaab', '2025-10-30 06:59:30', '2025-10-30 07:32:21', '2025-10-30 07:32:21'),
(20962, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 15, 2, 'name', 'متر مكعب', '2025-10-30 06:59:30', '2025-10-30 07:32:21', '2025-10-30 07:32:21'),
(20963, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 16, 1, 'name', 'center meter moqaab', '2025-10-30 06:59:58', '2025-10-30 07:32:16', '2025-10-30 07:32:16'),
(20964, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 16, 2, 'name', 'سنتي متر مكعب', '2025-10-30 06:59:58', '2025-10-30 07:32:16', '2025-10-30 07:32:16'),
(20965, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 17, 1, 'name', 'leter moqaab', '2025-10-30 07:00:16', '2025-10-30 07:32:11', '2025-10-30 07:32:11'),
(20966, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 17, 2, 'name', 'لتر مكعب', '2025-10-30 07:00:16', '2025-10-30 07:32:11', '2025-10-30 07:32:11'),
(20967, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 18, 1, 'name', 'color', '2025-10-30 07:33:07', '2025-10-30 09:33:14', '2025-10-30 09:33:14'),
(20968, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 18, 2, 'name', 'اللون', '2025-10-30 07:33:07', '2025-10-30 09:33:14', '2025-10-30 09:33:14'),
(20969, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 19, 1, 'name', 'Units', '2025-10-30 07:33:22', '2025-10-30 09:30:13', NULL),
(20970, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 19, 2, 'name', 'الوحدات', '2025-10-30 07:33:22', '2025-10-30 09:30:13', NULL),
(20971, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 20, 1, 'name', 'length unit', '2025-10-30 07:34:41', '2025-10-30 07:35:11', NULL),
(20972, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 20, 2, 'name', 'وحدة الطول', '2025-10-30 07:34:41', '2025-10-30 07:34:41', NULL),
(20973, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 21, 1, 'name', 'Thickness', '2025-10-30 07:35:50', '2025-10-30 07:35:50', NULL),
(20974, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 21, 2, 'name', 'السٌمك', '2025-10-30 07:35:50', '2025-10-30 07:35:50', NULL),
(20975, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 22, 1, 'name', 'Packaging', '2025-10-30 07:36:13', '2025-10-30 07:36:13', NULL),
(20976, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 22, 2, 'name', 'العبوة', '2025-10-30 07:36:13', '2025-10-30 07:36:13', NULL),
(20977, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 1, 1, 'name', 'red', '2025-10-30 08:38:22', '2025-10-30 08:38:22', NULL),
(20978, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 1, 2, 'name', 'احمر', '2025-10-30 08:38:22', '2025-10-30 08:38:22', NULL),
(20979, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 2, 1, 'name', 'green', '2025-10-30 08:43:13', '2025-10-30 08:43:13', NULL),
(20980, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 2, 2, 'name', 'اخضر', '2025-10-30 08:43:13', '2025-10-30 08:43:13', NULL),
(20981, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 3, 1, 'name', 'blue', '2025-10-30 08:43:32', '2025-10-30 08:43:32', NULL),
(20982, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 3, 2, 'name', 'ازرق', '2025-10-30 08:43:32', '2025-10-30 08:43:32', NULL),
(20983, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 23, 1, 'name', 'area', '2025-10-30 09:31:35', '2025-10-30 09:31:35', NULL),
(20984, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 23, 2, 'name', 'المساحة', '2025-10-30 09:31:35', '2025-10-30 09:31:35', NULL),
(20985, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 24, 1, 'name', 'Size', '2025-10-30 09:31:52', '2025-10-30 09:31:52', NULL),
(20986, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 24, 2, 'name', 'الحجم', '2025-10-30 09:31:52', '2025-10-30 09:31:52', NULL),
(20987, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 4, 1, 'name', 'meter', '2025-10-30 09:35:48', '2025-10-30 09:35:48', NULL),
(20988, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 4, 2, 'name', 'متر', '2025-10-30 09:35:48', '2025-10-30 09:35:48', NULL),
(20989, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 5, 1, 'name', 'Centemeter', '2025-10-30 09:39:23', '2025-10-30 09:39:23', NULL),
(20990, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 5, 2, 'name', 'سنتى متر', '2025-10-30 09:39:23', '2025-10-30 09:39:23', NULL);
INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(20991, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 6, 1, 'name', 'Large', '2025-10-30 10:30:46', '2025-10-30 10:30:46', NULL),
(20992, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 6, 2, 'name', 'لارج', '2025-10-30 10:30:46', '2025-10-30 10:30:46', NULL),
(20993, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 1, 'name', 'Mediummmma', '2025-10-30 10:31:04', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(20994, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 2, 'name', 'ميديم', '2025-10-30 10:31:04', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(20995, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 25, 1, 'name', 'sadas', '2025-10-30 11:11:02', '2025-10-30 11:11:48', '2025-10-30 11:11:48'),
(20996, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 25, 2, 'name', 'asdas', '2025-10-30 11:11:02', '2025-10-30 11:11:48', '2025-10-30 11:11:48'),
(20997, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 26, 1, 'name', 'aaaaaaaaa', '2025-10-30 11:11:21', '2025-10-30 11:11:42', '2025-10-30 11:11:42'),
(20998, 'Modules\\CatalogManagement\\app\\Models\\VariantConfigurationKey', 26, 2, 'name', 'xzczxc', '2025-10-30 11:11:21', '2025-10-30 11:11:42', '2025-10-30 11:11:42'),
(20999, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21000, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21001, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_title', 'title', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21002, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_description', 'description', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21003, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_keywords', 'asjk;sadklj;as', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21004, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21005, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21006, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_title', 'تايتل', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21007, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_description', 'ديبسكش', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21008, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_keywords', 'شسمنكيشسنكمينكمشسي', '2025-11-02 10:18:27', '2025-11-03 06:25:37', '2025-11-03 06:25:37'),
(21009, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10022, 1, 'name', 'asdasd', '2025-11-02 11:58:07', '2025-11-02 11:58:07', NULL),
(21010, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10022, 1, 'description', 'dasd', '2025-11-02 11:58:07', '2025-11-02 11:58:07', NULL),
(21011, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10022, 2, 'name', 'adas', '2025-11-02 11:58:07', '2025-11-02 11:58:07', NULL),
(21012, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10022, 2, 'description', 'asdas', '2025-11-02 11:58:07', '2025-11-02 11:58:07', NULL),
(21013, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10023, 1, 'name', 'wwwwwwwwwww', '2025-11-02 11:58:16', '2025-11-02 11:58:16', NULL),
(21014, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10023, 1, 'description', 'asdas', '2025-11-02 11:58:16', '2025-11-02 11:58:16', NULL),
(21015, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10023, 2, 'name', 'sadsad', '2025-11-02 11:58:16', '2025-11-02 11:58:16', NULL),
(21016, 'Modules\\CategoryManagment\\app\\Models\\Activity', 10023, 2, 'description', 'das', '2025-11-02 11:58:16', '2025-11-02 11:58:16', NULL),
(21017, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'name', 'aaaaaaaaaaa', '2025-11-02 12:52:09', '2025-11-02 12:52:09', NULL),
(21018, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 1, 'description', 'adaslkdaskdasdkalsdaskl;dlkjasdjlkaskljd', '2025-11-02 12:52:09', '2025-11-02 12:52:09', NULL),
(21019, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'name', 'ءءءءءءءءءءءءءءءء', '2025-11-02 12:52:09', '2025-11-02 12:52:09', NULL),
(21020, 'Modules\\CategoryManagment\\app\\Models\\Category', 3, 2, 'description', 'شسنمكيشسكمنينمكشسيكنمشسينكمشسكم', '2025-11-02 12:52:09', '2025-11-02 12:52:09', NULL),
(21021, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 2, 1, 'name', 'asd', '2025-11-02 12:52:43', '2025-11-02 12:52:43', NULL),
(21022, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 2, 1, 'description', 'asd', '2025-11-02 12:52:43', '2025-11-02 12:52:43', NULL),
(21023, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 2, 2, 'name', 'asdasd', '2025-11-02 12:52:43', '2025-11-02 12:52:43', NULL),
(21024, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 2, 2, 'description', 'asdsa', '2025-11-02 12:52:43', '2025-11-02 12:52:43', NULL),
(21025, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 9, 1, 'name', 'asdas', '2025-11-02 13:41:56', '2025-11-02 13:41:56', NULL),
(21026, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 9, 2, 'name', 'dasdas', '2025-11-02 13:41:56', '2025-11-02 13:41:56', NULL),
(21027, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 6, 1, 'name', 'qweqw', '2025-11-02 13:42:04', '2025-11-02 13:42:04', NULL),
(21028, 'Modules\\CategoryManagment\\app\\Models\\SubCategory', 6, 2, 'name', 'dasdsa', '2025-11-02 13:42:04', '2025-11-02 13:42:04', NULL),
(21029, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 1, 'name', 'Medium', '2025-11-02 13:47:09', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21030, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 2, 'name', 'ميديم', '2025-11-02 13:47:09', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21031, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 1, 'name', 'Mediumm', '2025-11-02 13:47:16', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21032, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 2, 'name', 'ميديم', '2025-11-02 13:47:16', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21033, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 1, 'name', 'Mediummmm', '2025-11-02 13:48:56', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21034, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 7, 2, 'name', 'ميديم', '2025-11-02 13:48:56', '2025-11-02 13:55:08', '2025-11-02 13:55:08'),
(21035, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 8, 1, 'name', 'medium', '2025-11-02 13:55:21', '2025-11-02 13:55:21', NULL),
(21036, 'Modules\\CatalogManagement\\app\\Models\\VariantsConfiguration', 8, 2, 'name', 'ميديم', '2025-11-02 13:55:21', '2025-11-02 13:55:21', NULL),
(21037, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'name', 'asdasdasd', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21038, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'description', 'dsada', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21039, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_title', 'title', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21040, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_description', 'description', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21041, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 1, 'meta_keywords', 'asjk;sadklj;as', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21042, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'name', 'asdasdas', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21043, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'description', 'dsa', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21044, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_title', 'تايتل', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21045, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_description', 'ديبسكش', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21046, 'Modules\\Vendor\\app\\Models\\Vendor', 4, 2, 'meta_keywords', 'شسمنكيشسنكمينكمشسي', '2025-11-03 06:25:37', '2025-11-03 06:25:37', NULL),
(21047, 'App\\Models\\Permession', 490, 1, 'name', 'View Dashboard', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21048, 'App\\Models\\Permession', 490, 2, 'name', 'عرض لوحة التحكم', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21049, 'App\\Models\\Permession', 490, 1, 'group_by', 'Dashboard', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21050, 'App\\Models\\Permession', 490, 2, 'group_by', 'لوحة التحكم', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21051, 'App\\Models\\Permession', 491, 1, 'name', 'All Activities', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21052, 'App\\Models\\Permession', 491, 2, 'name', 'كل الانشطة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21053, 'App\\Models\\Permession', 491, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21054, 'App\\Models\\Permession', 491, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21055, 'App\\Models\\Permession', 492, 1, 'name', 'View Activities', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21056, 'App\\Models\\Permession', 492, 2, 'name', 'عرض الانشطة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21057, 'App\\Models\\Permession', 492, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21058, 'App\\Models\\Permession', 492, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21059, 'App\\Models\\Permession', 493, 1, 'name', 'Create Activities', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21060, 'App\\Models\\Permession', 493, 2, 'name', 'إنشاء الانشطة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21061, 'App\\Models\\Permession', 493, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21062, 'App\\Models\\Permession', 493, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21063, 'App\\Models\\Permession', 494, 1, 'name', 'Edit Activities', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21064, 'App\\Models\\Permession', 494, 2, 'name', 'تعديل الانشطة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21065, 'App\\Models\\Permession', 494, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21066, 'App\\Models\\Permession', 494, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21067, 'App\\Models\\Permession', 495, 1, 'name', 'Delete Activities', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21068, 'App\\Models\\Permession', 495, 2, 'name', 'ازالة الانشطة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21069, 'App\\Models\\Permession', 495, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21070, 'App\\Models\\Permession', 495, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21071, 'App\\Models\\Permession', 496, 1, 'name', 'All Departments', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21072, 'App\\Models\\Permession', 496, 2, 'name', 'كل الأقسام', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21073, 'App\\Models\\Permession', 496, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21074, 'App\\Models\\Permession', 496, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21075, 'App\\Models\\Permession', 497, 1, 'name', 'View Departments', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21076, 'App\\Models\\Permession', 497, 2, 'name', 'عرض الأقسام', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21077, 'App\\Models\\Permession', 497, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21078, 'App\\Models\\Permession', 497, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21079, 'App\\Models\\Permession', 498, 1, 'name', 'Create Department', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21080, 'App\\Models\\Permession', 498, 2, 'name', 'إنشاء قسم', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21081, 'App\\Models\\Permession', 498, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21082, 'App\\Models\\Permession', 498, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21083, 'App\\Models\\Permession', 499, 1, 'name', 'Edit Department', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21084, 'App\\Models\\Permession', 499, 2, 'name', 'تعديل قسم', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21085, 'App\\Models\\Permession', 499, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21086, 'App\\Models\\Permession', 499, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21087, 'App\\Models\\Permession', 500, 1, 'name', 'Delete Department', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21088, 'App\\Models\\Permession', 500, 2, 'name', 'حذف قسم', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21089, 'App\\Models\\Permession', 500, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21090, 'App\\Models\\Permession', 500, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21091, 'App\\Models\\Permession', 501, 1, 'name', 'All Main Categories', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21092, 'App\\Models\\Permession', 501, 2, 'name', 'كل الأقسام الرئيسية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21093, 'App\\Models\\Permession', 501, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21094, 'App\\Models\\Permession', 501, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21095, 'App\\Models\\Permession', 502, 1, 'name', 'View Main Categories', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21096, 'App\\Models\\Permession', 502, 2, 'name', 'عرض الأقسام الرئيسية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21097, 'App\\Models\\Permession', 502, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21098, 'App\\Models\\Permession', 502, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21099, 'App\\Models\\Permession', 503, 1, 'name', 'Create Main Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21100, 'App\\Models\\Permession', 503, 2, 'name', 'إنشاء قسم رئيسية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21101, 'App\\Models\\Permession', 503, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21102, 'App\\Models\\Permession', 503, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21103, 'App\\Models\\Permession', 504, 1, 'name', 'Edit Main Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21104, 'App\\Models\\Permession', 504, 2, 'name', 'تعديل قسم رئيسية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21105, 'App\\Models\\Permession', 504, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21106, 'App\\Models\\Permession', 504, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21107, 'App\\Models\\Permession', 505, 1, 'name', 'Delete Main Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21108, 'App\\Models\\Permession', 505, 2, 'name', 'حذف قسم رئيسية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21109, 'App\\Models\\Permession', 505, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21110, 'App\\Models\\Permession', 505, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21111, 'App\\Models\\Permession', 506, 1, 'name', 'All Sub Categories', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21112, 'App\\Models\\Permession', 506, 2, 'name', 'كل الأقسام الفرعية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21113, 'App\\Models\\Permession', 506, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21114, 'App\\Models\\Permession', 506, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21115, 'App\\Models\\Permession', 507, 1, 'name', 'View Sub Categories', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21116, 'App\\Models\\Permession', 507, 2, 'name', 'عرض الأقسام الفرعية', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21117, 'App\\Models\\Permession', 507, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21118, 'App\\Models\\Permession', 507, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21119, 'App\\Models\\Permession', 508, 1, 'name', 'Create Sub Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21120, 'App\\Models\\Permession', 508, 2, 'name', 'إنشاء قسم فرعي', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21121, 'App\\Models\\Permession', 508, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21122, 'App\\Models\\Permession', 508, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21123, 'App\\Models\\Permession', 509, 1, 'name', 'Edit Sub Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21124, 'App\\Models\\Permession', 509, 2, 'name', 'تعديل قسم فرعي', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21125, 'App\\Models\\Permession', 509, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21126, 'App\\Models\\Permession', 509, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21127, 'App\\Models\\Permession', 510, 1, 'name', 'Delete Sub Category', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21128, 'App\\Models\\Permession', 510, 2, 'name', 'حذف قسم فرعي', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21129, 'App\\Models\\Permession', 510, 1, 'group_by', 'Catalog Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21130, 'App\\Models\\Permession', 510, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21131, 'App\\Models\\Permession', 511, 1, 'name', 'All Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21132, 'App\\Models\\Permession', 511, 2, 'name', 'كل المنتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21133, 'App\\Models\\Permession', 511, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21134, 'App\\Models\\Permession', 511, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21135, 'App\\Models\\Permession', 512, 1, 'name', 'View Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21136, 'App\\Models\\Permession', 512, 2, 'name', 'عرض المنتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21137, 'App\\Models\\Permession', 512, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21138, 'App\\Models\\Permession', 512, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21139, 'App\\Models\\Permession', 513, 1, 'name', 'Create Product', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21140, 'App\\Models\\Permession', 513, 2, 'name', 'إنشاء منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21141, 'App\\Models\\Permession', 513, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21142, 'App\\Models\\Permession', 513, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21143, 'App\\Models\\Permession', 514, 1, 'name', 'Edit Product', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21144, 'App\\Models\\Permession', 514, 2, 'name', 'تعديل منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21145, 'App\\Models\\Permession', 514, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21146, 'App\\Models\\Permession', 514, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21147, 'App\\Models\\Permession', 515, 1, 'name', 'Delete Product', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21148, 'App\\Models\\Permession', 515, 2, 'name', 'حذف منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21149, 'App\\Models\\Permession', 515, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21150, 'App\\Models\\Permession', 515, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21151, 'App\\Models\\Permession', 516, 1, 'name', 'View In Stock Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21152, 'App\\Models\\Permession', 516, 2, 'name', 'عرض المنتجات في المخزون', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21153, 'App\\Models\\Permession', 516, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21154, 'App\\Models\\Permession', 516, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21155, 'App\\Models\\Permession', 517, 1, 'name', 'View Out of Stock Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21156, 'App\\Models\\Permession', 517, 2, 'name', 'عرض المنتجات غير في المخزون', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21157, 'App\\Models\\Permession', 517, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21158, 'App\\Models\\Permession', 517, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21159, 'App\\Models\\Permession', 518, 1, 'name', 'View Product Setup', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21160, 'App\\Models\\Permession', 518, 2, 'name', 'عرض إعداد المنتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21161, 'App\\Models\\Permession', 518, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21162, 'App\\Models\\Permession', 518, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21163, 'App\\Models\\Permession', 519, 1, 'name', 'Create Product Setup', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21164, 'App\\Models\\Permession', 519, 2, 'name', 'إنشاء إعداد منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21165, 'App\\Models\\Permession', 519, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21166, 'App\\Models\\Permession', 519, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21167, 'App\\Models\\Permession', 520, 1, 'name', 'Edit Product Setup', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21168, 'App\\Models\\Permession', 520, 2, 'name', 'تعديل إعداد منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21169, 'App\\Models\\Permession', 520, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21170, 'App\\Models\\Permession', 520, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21171, 'App\\Models\\Permession', 521, 1, 'name', 'Delete Product Setup', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21172, 'App\\Models\\Permession', 521, 2, 'name', 'حذف إعداد منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21173, 'App\\Models\\Permession', 521, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21174, 'App\\Models\\Permession', 521, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21175, 'App\\Models\\Permession', 522, 1, 'name', 'View Product Reviews', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21176, 'App\\Models\\Permession', 522, 2, 'name', 'عرض تقييم المنتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21177, 'App\\Models\\Permession', 522, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21178, 'App\\Models\\Permession', 522, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21179, 'App\\Models\\Permession', 523, 1, 'name', 'Accept Product Review', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21180, 'App\\Models\\Permession', 523, 2, 'name', 'قبول تقييم منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21181, 'App\\Models\\Permession', 523, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21182, 'App\\Models\\Permession', 523, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21183, 'App\\Models\\Permession', 524, 1, 'name', 'Reject Product Review', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21184, 'App\\Models\\Permession', 524, 2, 'name', 'رفض تقييم منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21185, 'App\\Models\\Permession', 524, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21186, 'App\\Models\\Permession', 524, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21187, 'App\\Models\\Permession', 525, 1, 'name', 'Delete Product Review', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21188, 'App\\Models\\Permession', 525, 2, 'name', 'حذف تقييم منتج', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21189, 'App\\Models\\Permession', 525, 1, 'group_by', 'Products', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21190, 'App\\Models\\Permession', 525, 2, 'group_by', 'منتجات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21191, 'App\\Models\\Permession', 526, 1, 'name', 'All Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21192, 'App\\Models\\Permession', 526, 2, 'name', 'كل الضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21193, 'App\\Models\\Permession', 526, 1, 'group_by', 'Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21194, 'App\\Models\\Permession', 526, 2, 'group_by', 'ضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21195, 'App\\Models\\Permession', 527, 1, 'name', 'View Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21196, 'App\\Models\\Permession', 527, 2, 'name', 'عرض الضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21197, 'App\\Models\\Permession', 527, 1, 'group_by', 'Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21198, 'App\\Models\\Permession', 527, 2, 'group_by', 'ضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21199, 'App\\Models\\Permession', 528, 1, 'name', 'Create Tax', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21200, 'App\\Models\\Permession', 528, 2, 'name', 'إنشاء ضريبة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21201, 'App\\Models\\Permession', 528, 1, 'group_by', 'Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21202, 'App\\Models\\Permession', 528, 2, 'group_by', 'ضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21203, 'App\\Models\\Permession', 529, 1, 'name', 'Edit Tax', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21204, 'App\\Models\\Permession', 529, 2, 'name', 'تعديل ضريبة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21205, 'App\\Models\\Permession', 529, 1, 'group_by', 'Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21206, 'App\\Models\\Permession', 529, 2, 'group_by', 'ضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21207, 'App\\Models\\Permession', 530, 1, 'name', 'Delete Tax', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21208, 'App\\Models\\Permession', 530, 2, 'name', 'حذف ضريبة', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21209, 'App\\Models\\Permession', 530, 1, 'group_by', 'Taxes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21210, 'App\\Models\\Permession', 530, 2, 'group_by', 'ضرائب', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21211, 'App\\Models\\Permession', 531, 1, 'name', 'All Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21212, 'App\\Models\\Permession', 531, 2, 'name', 'كل العروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21213, 'App\\Models\\Permession', 531, 1, 'group_by', 'Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21214, 'App\\Models\\Permession', 531, 2, 'group_by', 'عروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21215, 'App\\Models\\Permession', 532, 1, 'name', 'View Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21216, 'App\\Models\\Permession', 532, 2, 'name', 'عرض العروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21217, 'App\\Models\\Permession', 532, 1, 'group_by', 'Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21218, 'App\\Models\\Permession', 532, 2, 'group_by', 'عروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21219, 'App\\Models\\Permession', 533, 1, 'name', 'Create Offer', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21220, 'App\\Models\\Permession', 533, 2, 'name', 'إنشاء عرض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21221, 'App\\Models\\Permession', 533, 1, 'group_by', 'Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21222, 'App\\Models\\Permession', 533, 2, 'group_by', 'عروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21223, 'App\\Models\\Permession', 534, 1, 'name', 'Edit Offer', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21224, 'App\\Models\\Permession', 534, 2, 'name', 'تعديل عرض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21225, 'App\\Models\\Permession', 534, 1, 'group_by', 'Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21226, 'App\\Models\\Permession', 534, 2, 'group_by', 'عروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21227, 'App\\Models\\Permession', 535, 1, 'name', 'Delete Offer', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21228, 'App\\Models\\Permession', 535, 2, 'name', 'حذف عرض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21229, 'App\\Models\\Permession', 535, 1, 'group_by', 'Offers', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21230, 'App\\Models\\Permession', 535, 2, 'group_by', 'عروض', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21231, 'App\\Models\\Permession', 536, 1, 'name', 'All Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21232, 'App\\Models\\Permession', 536, 2, 'name', 'كل الكودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21233, 'App\\Models\\Permession', 536, 1, 'group_by', 'Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21234, 'App\\Models\\Permession', 536, 2, 'group_by', 'كودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21235, 'App\\Models\\Permession', 537, 1, 'name', 'View Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21236, 'App\\Models\\Permession', 537, 2, 'name', 'عرض الكودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21237, 'App\\Models\\Permession', 537, 1, 'group_by', 'Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21238, 'App\\Models\\Permession', 537, 2, 'group_by', 'كودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21239, 'App\\Models\\Permession', 538, 1, 'name', 'Create Promocode', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21240, 'App\\Models\\Permession', 538, 2, 'name', 'إنشاء كود', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21241, 'App\\Models\\Permession', 538, 1, 'group_by', 'Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21242, 'App\\Models\\Permession', 538, 2, 'group_by', 'كودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21243, 'App\\Models\\Permession', 539, 1, 'name', 'Edit Promocode', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21244, 'App\\Models\\Permession', 539, 2, 'name', 'تعديل كود', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21245, 'App\\Models\\Permession', 539, 1, 'group_by', 'Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21246, 'App\\Models\\Permession', 539, 2, 'group_by', 'كودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21247, 'App\\Models\\Permession', 540, 1, 'name', 'Delete Promocode', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21248, 'App\\Models\\Permession', 540, 2, 'name', 'حذف كود', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21249, 'App\\Models\\Permession', 540, 1, 'group_by', 'Promocodes', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21250, 'App\\Models\\Permession', 540, 2, 'group_by', 'كودات', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21251, 'App\\Models\\Permession', 541, 1, 'name', 'All Roles', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21252, 'App\\Models\\Permession', 541, 2, 'name', 'كل الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21253, 'App\\Models\\Permession', 541, 1, 'group_by', 'Roles Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21254, 'App\\Models\\Permession', 541, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21255, 'App\\Models\\Permession', 542, 1, 'name', 'View Roles', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21256, 'App\\Models\\Permession', 542, 2, 'name', 'عرض الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21257, 'App\\Models\\Permession', 542, 1, 'group_by', 'Roles Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21258, 'App\\Models\\Permession', 542, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21259, 'App\\Models\\Permession', 543, 1, 'name', 'Create Role', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21260, 'App\\Models\\Permession', 543, 2, 'name', 'إنشاء دور', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21261, 'App\\Models\\Permession', 543, 1, 'group_by', 'Roles Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21262, 'App\\Models\\Permession', 543, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21263, 'App\\Models\\Permession', 544, 1, 'name', 'Edit Role', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21264, 'App\\Models\\Permession', 544, 2, 'name', 'تعديل دور', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21265, 'App\\Models\\Permession', 544, 1, 'group_by', 'Roles Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21266, 'App\\Models\\Permession', 544, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21267, 'App\\Models\\Permession', 545, 1, 'name', 'Delete Role', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21268, 'App\\Models\\Permession', 545, 2, 'name', 'حذف دور', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21269, 'App\\Models\\Permession', 545, 1, 'group_by', 'Roles Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21270, 'App\\Models\\Permession', 545, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21271, 'App\\Models\\Permession', 546, 1, 'name', 'All Admins', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21272, 'App\\Models\\Permession', 546, 2, 'name', 'كل المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21273, 'App\\Models\\Permession', 546, 1, 'group_by', 'Admin Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21274, 'App\\Models\\Permession', 546, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21275, 'App\\Models\\Permession', 547, 1, 'name', 'View Admins', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21276, 'App\\Models\\Permession', 547, 2, 'name', 'عرض المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21277, 'App\\Models\\Permession', 547, 1, 'group_by', 'Admin Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21278, 'App\\Models\\Permession', 547, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21279, 'App\\Models\\Permession', 548, 1, 'name', 'Create Admin', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21280, 'App\\Models\\Permession', 548, 2, 'name', 'إنشاء المسؤول', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21281, 'App\\Models\\Permession', 548, 1, 'group_by', 'Admin Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21282, 'App\\Models\\Permession', 548, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21283, 'App\\Models\\Permession', 549, 1, 'name', 'Edit Admin', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21284, 'App\\Models\\Permession', 549, 2, 'name', 'تعديل المسؤول', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21285, 'App\\Models\\Permession', 549, 1, 'group_by', 'Admin Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21286, 'App\\Models\\Permession', 549, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21287, 'App\\Models\\Permession', 550, 1, 'name', 'Delete Admin', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21288, 'App\\Models\\Permession', 550, 2, 'name', 'ازالة المسؤول', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21289, 'App\\Models\\Permession', 550, 1, 'group_by', 'Admin Management', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21290, 'App\\Models\\Permession', 550, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21291, 'App\\Models\\Permession', 551, 1, 'name', 'All Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21292, 'App\\Models\\Permession', 551, 2, 'name', 'كل الموردين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21293, 'App\\Models\\Permession', 551, 1, 'group_by', 'Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21294, 'App\\Models\\Permession', 551, 2, 'group_by', 'الموردين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21295, 'App\\Models\\Permession', 552, 1, 'name', 'View Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21296, 'App\\Models\\Permession', 552, 2, 'name', 'عرض الموردين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21297, 'App\\Models\\Permession', 552, 1, 'group_by', 'Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21298, 'App\\Models\\Permession', 552, 2, 'group_by', 'الموردين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21299, 'App\\Models\\Permession', 553, 1, 'name', 'Create Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21300, 'App\\Models\\Permession', 553, 2, 'name', 'انشاء الموردين', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21301, 'App\\Models\\Permession', 553, 1, 'group_by', 'Vendors', '2025-11-03 06:58:35', '2025-11-03 06:58:35', NULL),
(21302, 'App\\Models\\Permession', 553, 2, 'group_by', 'الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21303, 'App\\Models\\Permession', 554, 1, 'name', 'Edit Vendors', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21304, 'App\\Models\\Permession', 554, 2, 'name', 'تعديل الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21305, 'App\\Models\\Permession', 554, 1, 'group_by', 'Vendors', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21306, 'App\\Models\\Permession', 554, 2, 'group_by', 'الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21307, 'App\\Models\\Permession', 555, 1, 'name', 'Remove Vendors', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21308, 'App\\Models\\Permession', 555, 2, 'name', 'ازالة الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21309, 'App\\Models\\Permession', 555, 1, 'group_by', 'Vendors', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21310, 'App\\Models\\Permession', 555, 2, 'group_by', 'الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21311, 'App\\Models\\Permession', 556, 1, 'name', 'View New Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21312, 'App\\Models\\Permession', 556, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21313, 'App\\Models\\Permession', 556, 1, 'group_by', 'Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21314, 'App\\Models\\Permession', 556, 2, 'group_by', 'طلبات الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21315, 'App\\Models\\Permession', 557, 1, 'name', 'Accept Vendor Request', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21316, 'App\\Models\\Permession', 557, 2, 'name', 'قبول طلب المورد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21317, 'App\\Models\\Permession', 557, 1, 'group_by', 'Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21318, 'App\\Models\\Permession', 557, 2, 'group_by', 'طلبات الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21319, 'App\\Models\\Permession', 558, 1, 'name', 'Reject Vendor Request', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21320, 'App\\Models\\Permession', 558, 2, 'name', 'رفض طلب المورد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21321, 'App\\Models\\Permession', 558, 1, 'group_by', 'Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21322, 'App\\Models\\Permession', 558, 2, 'group_by', 'طلبات الموردين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21323, 'App\\Models\\Permession', 559, 1, 'name', 'View New Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21324, 'App\\Models\\Permession', 559, 2, 'name', 'عرض الطلبات الجديدة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21325, 'App\\Models\\Permession', 559, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21326, 'App\\Models\\Permession', 559, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21327, 'App\\Models\\Permession', 560, 1, 'name', 'View Inprogress Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21328, 'App\\Models\\Permession', 560, 2, 'name', 'عرض الطلبات المعلقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21329, 'App\\Models\\Permession', 560, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21330, 'App\\Models\\Permession', 560, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21331, 'App\\Models\\Permession', 561, 1, 'name', 'View Delivered Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21332, 'App\\Models\\Permession', 561, 2, 'name', 'عرض الطلبات المكتملة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21333, 'App\\Models\\Permession', 561, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21334, 'App\\Models\\Permession', 561, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21335, 'App\\Models\\Permession', 562, 1, 'name', 'View Canceled Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21336, 'App\\Models\\Permession', 562, 2, 'name', 'عرض الطلبات الملغاة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21337, 'App\\Models\\Permession', 562, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21338, 'App\\Models\\Permession', 562, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21339, 'App\\Models\\Permession', 563, 1, 'name', 'View Refunded Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21340, 'App\\Models\\Permession', 563, 2, 'name', 'عرض الطلبات المدفوعة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21341, 'App\\Models\\Permession', 563, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21342, 'App\\Models\\Permession', 563, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21343, 'App\\Models\\Permession', 564, 1, 'name', 'Edit Order', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21344, 'App\\Models\\Permession', 564, 2, 'name', 'تعديل طلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21345, 'App\\Models\\Permession', 564, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21346, 'App\\Models\\Permession', 564, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21347, 'App\\Models\\Permession', 565, 1, 'name', 'Delete Order', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21348, 'App\\Models\\Permession', 565, 2, 'name', 'حذف طلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21349, 'App\\Models\\Permession', 565, 1, 'group_by', 'Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21350, 'App\\Models\\Permession', 565, 2, 'group_by', 'الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21351, 'App\\Models\\Permession', 566, 1, 'name', 'All Order Stages', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21352, 'App\\Models\\Permession', 566, 2, 'name', 'كل خطوات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21353, 'App\\Models\\Permession', 566, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21354, 'App\\Models\\Permession', 566, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21355, 'App\\Models\\Permession', 567, 1, 'name', 'View Order Stages', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21356, 'App\\Models\\Permession', 567, 2, 'name', 'عرض خطوات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21357, 'App\\Models\\Permession', 567, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21358, 'App\\Models\\Permession', 567, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21359, 'App\\Models\\Permession', 568, 1, 'name', 'Create Order Stage', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21360, 'App\\Models\\Permession', 568, 2, 'name', 'إنشاء خطوة طلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21361, 'App\\Models\\Permession', 568, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21362, 'App\\Models\\Permession', 568, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21363, 'App\\Models\\Permession', 569, 1, 'name', 'Edit Order Stage', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21364, 'App\\Models\\Permession', 569, 2, 'name', 'تعديل خطوة طلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21365, 'App\\Models\\Permession', 569, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21366, 'App\\Models\\Permession', 569, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21367, 'App\\Models\\Permession', 570, 1, 'name', 'Delete Order Stage', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21368, 'App\\Models\\Permession', 570, 2, 'name', 'حذف خطوة طلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21369, 'App\\Models\\Permession', 570, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21370, 'App\\Models\\Permession', 570, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21371, 'App\\Models\\Permession', 571, 1, 'name', 'All Shipping Methods', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21372, 'App\\Models\\Permession', 571, 2, 'name', 'كل طرق الشحن', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21373, 'App\\Models\\Permession', 571, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21374, 'App\\Models\\Permession', 571, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21375, 'App\\Models\\Permession', 572, 1, 'name', 'View Shipping Methods', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21376, 'App\\Models\\Permession', 572, 2, 'name', 'عرض طرق الشحن', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21377, 'App\\Models\\Permession', 572, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21378, 'App\\Models\\Permession', 572, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21379, 'App\\Models\\Permession', 573, 1, 'name', 'Create Shipping Method', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21380, 'App\\Models\\Permession', 573, 2, 'name', 'إنشاء طريقة شحن', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21381, 'App\\Models\\Permession', 573, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21382, 'App\\Models\\Permession', 573, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21383, 'App\\Models\\Permession', 574, 1, 'name', 'Edit Shipping Method', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21384, 'App\\Models\\Permession', 574, 2, 'name', 'تعديل طريقة شحن', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21385, 'App\\Models\\Permession', 574, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21386, 'App\\Models\\Permession', 574, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21387, 'App\\Models\\Permession', 575, 1, 'name', 'Delete Shipping Method', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21388, 'App\\Models\\Permession', 575, 2, 'name', 'حذف طريقة شحن', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21389, 'App\\Models\\Permession', 575, 1, 'group_by', 'Order Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21390, 'App\\Models\\Permession', 575, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL);
INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(21391, 'App\\Models\\Permession', 576, 1, 'name', 'All Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21392, 'App\\Models\\Permession', 576, 2, 'name', 'كل نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21393, 'App\\Models\\Permession', 576, 1, 'group_by', 'Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21394, 'App\\Models\\Permession', 576, 2, 'group_by', 'نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21395, 'App\\Models\\Permession', 577, 1, 'name', 'View Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21396, 'App\\Models\\Permession', 577, 2, 'name', 'عرض نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21397, 'App\\Models\\Permession', 577, 1, 'group_by', 'Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21398, 'App\\Models\\Permession', 577, 2, 'group_by', 'نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21399, 'App\\Models\\Permession', 578, 1, 'name', 'Create Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21400, 'App\\Models\\Permession', 578, 2, 'name', 'إنشاء نظام نقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21401, 'App\\Models\\Permession', 578, 1, 'group_by', 'Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21402, 'App\\Models\\Permession', 578, 2, 'group_by', 'نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21403, 'App\\Models\\Permession', 579, 1, 'name', 'Edit Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21404, 'App\\Models\\Permession', 579, 2, 'name', 'تعديل نظام نقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21405, 'App\\Models\\Permession', 579, 1, 'group_by', 'Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21406, 'App\\Models\\Permession', 579, 2, 'group_by', 'نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21407, 'App\\Models\\Permession', 580, 1, 'name', 'Delete Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21408, 'App\\Models\\Permession', 580, 2, 'name', 'حذف نظام نقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21409, 'App\\Models\\Permession', 580, 1, 'group_by', 'Points System', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21410, 'App\\Models\\Permession', 580, 2, 'group_by', 'نظام النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21411, 'App\\Models\\Permession', 581, 1, 'name', 'All Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21412, 'App\\Models\\Permession', 581, 2, 'name', 'كل الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21413, 'App\\Models\\Permession', 581, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21414, 'App\\Models\\Permession', 581, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21415, 'App\\Models\\Permession', 582, 1, 'name', 'View Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21416, 'App\\Models\\Permession', 582, 2, 'name', 'عرض الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21417, 'App\\Models\\Permession', 582, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21418, 'App\\Models\\Permession', 582, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21419, 'App\\Models\\Permession', 583, 1, 'name', 'Create Advertisement', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21420, 'App\\Models\\Permession', 583, 2, 'name', 'إنشاء إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21421, 'App\\Models\\Permession', 583, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21422, 'App\\Models\\Permession', 583, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21423, 'App\\Models\\Permession', 584, 1, 'name', 'Edit Advertisement', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21424, 'App\\Models\\Permession', 584, 2, 'name', 'تعديل إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21425, 'App\\Models\\Permession', 584, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21426, 'App\\Models\\Permession', 584, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21427, 'App\\Models\\Permession', 585, 1, 'name', 'Delete Advertisement', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21428, 'App\\Models\\Permession', 585, 2, 'name', 'حذف إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21429, 'App\\Models\\Permession', 585, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21430, 'App\\Models\\Permession', 585, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21431, 'App\\Models\\Permession', 586, 1, 'name', 'All Advertisement Positions', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21432, 'App\\Models\\Permession', 586, 2, 'name', 'كل مواقع الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21433, 'App\\Models\\Permession', 586, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21434, 'App\\Models\\Permession', 586, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21435, 'App\\Models\\Permession', 587, 1, 'name', 'View Advertisement Positions', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21436, 'App\\Models\\Permession', 587, 2, 'name', 'عرض مواقع الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21437, 'App\\Models\\Permession', 587, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21438, 'App\\Models\\Permession', 587, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21439, 'App\\Models\\Permession', 588, 1, 'name', 'Create Advertisement Position', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21440, 'App\\Models\\Permession', 588, 2, 'name', 'إنشاء موقع إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21441, 'App\\Models\\Permession', 588, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21442, 'App\\Models\\Permession', 588, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21443, 'App\\Models\\Permession', 589, 1, 'name', 'Edit Advertisement Position', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21444, 'App\\Models\\Permession', 589, 2, 'name', 'تعديل موقع إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21445, 'App\\Models\\Permession', 589, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21446, 'App\\Models\\Permession', 589, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21447, 'App\\Models\\Permession', 590, 1, 'name', 'Delete Advertisement Position', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21448, 'App\\Models\\Permession', 590, 2, 'name', 'حذف موقع إعلان', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21449, 'App\\Models\\Permession', 590, 1, 'group_by', 'Advertisements', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21450, 'App\\Models\\Permession', 590, 2, 'group_by', 'الإعلانات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21451, 'App\\Models\\Permession', 591, 1, 'name', 'View Notifications', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21452, 'App\\Models\\Permession', 591, 2, 'name', 'عرض الإشعارات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21453, 'App\\Models\\Permession', 591, 1, 'group_by', 'Notifications', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21454, 'App\\Models\\Permession', 591, 2, 'group_by', 'الإشعارات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21455, 'App\\Models\\Permession', 592, 1, 'name', 'Send Notification', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21456, 'App\\Models\\Permession', 592, 2, 'name', 'إرسال إشعار', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21457, 'App\\Models\\Permession', 592, 1, 'group_by', 'Notifications', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21458, 'App\\Models\\Permession', 592, 2, 'group_by', 'الإشعارات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21459, 'App\\Models\\Permession', 593, 1, 'name', 'Delete Notification', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21460, 'App\\Models\\Permession', 593, 2, 'name', 'حذف إشعار', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21461, 'App\\Models\\Permession', 593, 1, 'group_by', 'Notifications', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21462, 'App\\Models\\Permession', 593, 2, 'group_by', 'الإشعارات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21463, 'App\\Models\\Permession', 594, 1, 'name', 'View Accounting Overview', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21464, 'App\\Models\\Permession', 594, 2, 'name', 'عرض ملخص المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21465, 'App\\Models\\Permession', 594, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21466, 'App\\Models\\Permession', 594, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21467, 'App\\Models\\Permession', 595, 1, 'name', 'View Accounting Balance', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21468, 'App\\Models\\Permession', 595, 2, 'name', 'عرض ميزانية المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21469, 'App\\Models\\Permession', 595, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21470, 'App\\Models\\Permession', 595, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21471, 'App\\Models\\Permession', 596, 1, 'name', 'View Accounting Expenses Keys', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21472, 'App\\Models\\Permession', 596, 2, 'name', 'عرض مفاتيح النفقات المالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21473, 'App\\Models\\Permession', 596, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21474, 'App\\Models\\Permession', 596, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21475, 'App\\Models\\Permession', 597, 1, 'name', 'Create Accounting Expenses Key', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21476, 'App\\Models\\Permession', 597, 2, 'name', 'إنشاء مفتاح نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21477, 'App\\Models\\Permession', 597, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21478, 'App\\Models\\Permession', 597, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21479, 'App\\Models\\Permession', 598, 1, 'name', 'Edit Accounting Expenses Key', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21480, 'App\\Models\\Permession', 598, 2, 'name', 'تعديل مفتاح نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21481, 'App\\Models\\Permession', 598, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21482, 'App\\Models\\Permession', 598, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21483, 'App\\Models\\Permession', 599, 1, 'name', 'Delete Accounting Expenses Key', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21484, 'App\\Models\\Permession', 599, 2, 'name', 'حذف مفتاح نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21485, 'App\\Models\\Permession', 599, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21486, 'App\\Models\\Permession', 599, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21487, 'App\\Models\\Permession', 600, 1, 'name', 'View Accounting Expenses', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21488, 'App\\Models\\Permession', 600, 2, 'name', 'عرض النفقات المالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21489, 'App\\Models\\Permession', 600, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21490, 'App\\Models\\Permession', 600, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21491, 'App\\Models\\Permession', 601, 1, 'name', 'Create Accounting Expense', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21492, 'App\\Models\\Permession', 601, 2, 'name', 'إنشاء نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21493, 'App\\Models\\Permession', 601, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21494, 'App\\Models\\Permession', 601, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21495, 'App\\Models\\Permession', 602, 1, 'name', 'Edit Accounting Expense', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21496, 'App\\Models\\Permession', 602, 2, 'name', 'تعديل نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21497, 'App\\Models\\Permession', 602, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21498, 'App\\Models\\Permession', 602, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21499, 'App\\Models\\Permession', 603, 1, 'name', 'Delete Accounting Expense', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21500, 'App\\Models\\Permession', 603, 2, 'name', 'حذف نفقات مالية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21501, 'App\\Models\\Permession', 603, 1, 'group_by', 'Accounting', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21502, 'App\\Models\\Permession', 603, 2, 'group_by', 'المالي', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21503, 'App\\Models\\Permession', 604, 1, 'name', 'View Send Money', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21504, 'App\\Models\\Permession', 604, 2, 'name', 'عرض إرسال المال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21505, 'App\\Models\\Permession', 604, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21506, 'App\\Models\\Permession', 604, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21507, 'App\\Models\\Permession', 605, 1, 'name', 'Create Send Money', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21508, 'App\\Models\\Permession', 605, 2, 'name', 'إنشاء إرسال المال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21509, 'App\\Models\\Permession', 605, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21510, 'App\\Models\\Permession', 605, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21511, 'App\\Models\\Permession', 606, 1, 'name', 'View Transactions', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21512, 'App\\Models\\Permession', 606, 2, 'name', 'عرض المعاملات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21513, 'App\\Models\\Permession', 606, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21514, 'App\\Models\\Permession', 606, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21515, 'App\\Models\\Permession', 607, 1, 'name', 'View New Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21516, 'App\\Models\\Permession', 607, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21517, 'App\\Models\\Permession', 607, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21518, 'App\\Models\\Permession', 607, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21519, 'App\\Models\\Permession', 608, 1, 'name', 'Accept Vendor Request', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21520, 'App\\Models\\Permession', 608, 2, 'name', 'قبول طلب المورد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21521, 'App\\Models\\Permession', 608, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21522, 'App\\Models\\Permession', 608, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21523, 'App\\Models\\Permession', 609, 1, 'name', 'Reject Vendor Request', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21524, 'App\\Models\\Permession', 609, 2, 'name', 'رفض طلب المورد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21525, 'App\\Models\\Permession', 609, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21526, 'App\\Models\\Permession', 609, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21527, 'App\\Models\\Permession', 610, 1, 'name', 'View Accepted Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21528, 'App\\Models\\Permession', 610, 2, 'name', 'عرض طلبات الموردين المقبولة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21529, 'App\\Models\\Permession', 610, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21530, 'App\\Models\\Permession', 610, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21531, 'App\\Models\\Permession', 611, 1, 'name', 'View Rejected Vendor Requests', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21532, 'App\\Models\\Permession', 611, 2, 'name', 'عرض طلبات الموردين الرفض', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21533, 'App\\Models\\Permession', 611, 1, 'group_by', 'Withdraw', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21534, 'App\\Models\\Permession', 611, 2, 'group_by', 'سحب', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21535, 'App\\Models\\Permession', 612, 1, 'name', 'View Blog Categories', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21536, 'App\\Models\\Permession', 612, 2, 'name', 'عرض مجموعات المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21537, 'App\\Models\\Permession', 612, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21538, 'App\\Models\\Permession', 612, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21539, 'App\\Models\\Permession', 613, 1, 'name', 'Create Blog Category', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21540, 'App\\Models\\Permession', 613, 2, 'name', 'إنشاء مجموعات المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21541, 'App\\Models\\Permession', 613, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21542, 'App\\Models\\Permession', 613, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21543, 'App\\Models\\Permession', 614, 1, 'name', 'Edit Blog Category', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21544, 'App\\Models\\Permession', 614, 2, 'name', 'تعديل مجموعات المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21545, 'App\\Models\\Permession', 614, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21546, 'App\\Models\\Permession', 614, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21547, 'App\\Models\\Permession', 615, 1, 'name', 'Delete Blog Category', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21548, 'App\\Models\\Permession', 615, 2, 'name', 'حذف مجموعات المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21549, 'App\\Models\\Permession', 615, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21550, 'App\\Models\\Permession', 615, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21551, 'App\\Models\\Permession', 616, 1, 'name', 'View Blog Posts', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21552, 'App\\Models\\Permession', 616, 2, 'name', 'عرض المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21553, 'App\\Models\\Permession', 616, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21554, 'App\\Models\\Permession', 616, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21555, 'App\\Models\\Permession', 617, 1, 'name', 'Create Blog Post', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21556, 'App\\Models\\Permession', 617, 2, 'name', 'إنشاء مقال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21557, 'App\\Models\\Permession', 617, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21558, 'App\\Models\\Permession', 617, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21559, 'App\\Models\\Permession', 618, 1, 'name', 'Edit Blog Post', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21560, 'App\\Models\\Permession', 618, 2, 'name', 'تعديل مقال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21561, 'App\\Models\\Permession', 618, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21562, 'App\\Models\\Permession', 618, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21563, 'App\\Models\\Permession', 619, 1, 'name', 'Delete Blog Post', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21564, 'App\\Models\\Permession', 619, 2, 'name', 'حذف مقال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21565, 'App\\Models\\Permession', 619, 1, 'group_by', 'Blog Management', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21566, 'App\\Models\\Permession', 619, 2, 'group_by', 'إدارة المقالات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21567, 'App\\Models\\Permession', 620, 1, 'name', 'View Registered Users', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21568, 'App\\Models\\Permession', 620, 2, 'name', 'عرض المستخدمين المسجلين', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21569, 'App\\Models\\Permession', 620, 1, 'group_by', 'Reports', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21570, 'App\\Models\\Permession', 620, 2, 'group_by', 'التقارير', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21571, 'App\\Models\\Permession', 621, 1, 'name', 'View Area Users', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21572, 'App\\Models\\Permession', 621, 2, 'name', 'عرض المستخدمين في المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21573, 'App\\Models\\Permession', 621, 1, 'group_by', 'Reports', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21574, 'App\\Models\\Permession', 621, 2, 'group_by', 'التقارير', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21575, 'App\\Models\\Permession', 622, 1, 'name', 'View Orders', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21576, 'App\\Models\\Permession', 622, 2, 'name', 'عرض الطلبات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21577, 'App\\Models\\Permession', 622, 1, 'group_by', 'Reports', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21578, 'App\\Models\\Permession', 622, 2, 'group_by', 'التقارير', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21579, 'App\\Models\\Permession', 623, 1, 'name', 'View Products', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21580, 'App\\Models\\Permession', 623, 2, 'name', 'عرض المنتجات', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21581, 'App\\Models\\Permession', 623, 1, 'group_by', 'Reports', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21582, 'App\\Models\\Permession', 623, 2, 'group_by', 'التقارير', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21583, 'App\\Models\\Permession', 624, 1, 'name', 'View Points', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21584, 'App\\Models\\Permession', 624, 2, 'name', 'عرض النقاط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21585, 'App\\Models\\Permession', 624, 1, 'group_by', 'Reports', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21586, 'App\\Models\\Permession', 624, 2, 'group_by', 'التقارير', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21587, 'App\\Models\\Permession', 625, 1, 'name', 'View System Log', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21588, 'App\\Models\\Permession', 625, 2, 'name', 'عرض سجل النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21589, 'App\\Models\\Permession', 625, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21590, 'App\\Models\\Permession', 625, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21591, 'App\\Models\\Permession', 626, 1, 'name', 'View Country', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21592, 'App\\Models\\Permession', 626, 2, 'name', 'عرض البلد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21593, 'App\\Models\\Permession', 626, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21594, 'App\\Models\\Permession', 626, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21595, 'App\\Models\\Permession', 627, 1, 'name', 'Create Country', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21596, 'App\\Models\\Permession', 627, 2, 'name', 'إنشاء بلد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21597, 'App\\Models\\Permession', 627, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21598, 'App\\Models\\Permession', 627, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21599, 'App\\Models\\Permession', 628, 1, 'name', 'Edit Country', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21600, 'App\\Models\\Permession', 628, 2, 'name', 'تعديل بلد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21601, 'App\\Models\\Permession', 628, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21602, 'App\\Models\\Permession', 628, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21603, 'App\\Models\\Permession', 629, 1, 'name', 'Delete Country', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21604, 'App\\Models\\Permession', 629, 2, 'name', 'حذف بلد', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21605, 'App\\Models\\Permession', 629, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21606, 'App\\Models\\Permession', 629, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21607, 'App\\Models\\Permession', 630, 1, 'name', 'View City', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21608, 'App\\Models\\Permession', 630, 2, 'name', 'عرض المدينة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21609, 'App\\Models\\Permession', 630, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21610, 'App\\Models\\Permession', 630, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21611, 'App\\Models\\Permession', 631, 1, 'name', 'Create City', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21612, 'App\\Models\\Permession', 631, 2, 'name', 'إنشاء مدينة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21613, 'App\\Models\\Permession', 631, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21614, 'App\\Models\\Permession', 631, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21615, 'App\\Models\\Permession', 632, 1, 'name', 'Edit City', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21616, 'App\\Models\\Permession', 632, 2, 'name', 'تعديل مدينة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21617, 'App\\Models\\Permession', 632, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21618, 'App\\Models\\Permession', 632, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21619, 'App\\Models\\Permession', 633, 1, 'name', 'Delete City', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21620, 'App\\Models\\Permession', 633, 2, 'name', 'حذف مدينة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21621, 'App\\Models\\Permession', 633, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21622, 'App\\Models\\Permession', 633, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21623, 'App\\Models\\Permession', 634, 1, 'name', 'View Region', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21624, 'App\\Models\\Permession', 634, 2, 'name', 'عرض المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21625, 'App\\Models\\Permession', 634, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21626, 'App\\Models\\Permession', 634, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21627, 'App\\Models\\Permession', 635, 1, 'name', 'Create Region', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21628, 'App\\Models\\Permession', 635, 2, 'name', 'إنشاء منطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21629, 'App\\Models\\Permession', 635, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21630, 'App\\Models\\Permession', 635, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21631, 'App\\Models\\Permession', 636, 1, 'name', 'Edit Region', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21632, 'App\\Models\\Permession', 636, 2, 'name', 'تعديل منطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21633, 'App\\Models\\Permession', 636, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21634, 'App\\Models\\Permession', 636, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21635, 'App\\Models\\Permession', 637, 1, 'name', 'Delete Region', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21636, 'App\\Models\\Permession', 637, 2, 'name', 'حذف منطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21637, 'App\\Models\\Permession', 637, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21638, 'App\\Models\\Permession', 637, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21639, 'App\\Models\\Permession', 638, 1, 'name', 'View Subregion', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21640, 'App\\Models\\Permession', 638, 2, 'name', 'عرض المنطقة الفرعية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21641, 'App\\Models\\Permession', 638, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21642, 'App\\Models\\Permession', 638, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21643, 'App\\Models\\Permession', 639, 1, 'name', 'Create Subregion', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21644, 'App\\Models\\Permession', 639, 2, 'name', 'إنشاء منطقة فرعية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21645, 'App\\Models\\Permession', 639, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21646, 'App\\Models\\Permession', 639, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21647, 'App\\Models\\Permession', 640, 1, 'name', 'Edit Subregion', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21648, 'App\\Models\\Permession', 640, 2, 'name', 'تعديل منطقة فرعية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21649, 'App\\Models\\Permession', 640, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21650, 'App\\Models\\Permession', 640, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21651, 'App\\Models\\Permession', 641, 1, 'name', 'Delete Subregion', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21652, 'App\\Models\\Permession', 641, 2, 'name', 'حذف منطقة فرعية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21653, 'App\\Models\\Permession', 641, 1, 'group_by', 'Area Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21654, 'App\\Models\\Permession', 641, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21655, 'App\\Models\\Permession', 642, 1, 'name', 'View Terms', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21656, 'App\\Models\\Permession', 642, 2, 'name', 'عرض الشروط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21657, 'App\\Models\\Permession', 642, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21658, 'App\\Models\\Permession', 642, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21659, 'App\\Models\\Permession', 643, 1, 'name', 'Edit Terms', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21660, 'App\\Models\\Permession', 643, 2, 'name', 'تعديل الشروط', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21661, 'App\\Models\\Permession', 643, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21662, 'App\\Models\\Permession', 643, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21663, 'App\\Models\\Permession', 644, 1, 'name', 'View Privacy', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21664, 'App\\Models\\Permession', 644, 2, 'name', 'عرض الخصوصية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21665, 'App\\Models\\Permession', 644, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21666, 'App\\Models\\Permession', 644, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21667, 'App\\Models\\Permession', 645, 1, 'name', 'Edit Privacy', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21668, 'App\\Models\\Permession', 645, 2, 'name', 'تعديل الخصوصية', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21669, 'App\\Models\\Permession', 645, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21670, 'App\\Models\\Permession', 645, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21671, 'App\\Models\\Permession', 646, 1, 'name', 'View About', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21672, 'App\\Models\\Permession', 646, 2, 'name', 'عرض عن النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21673, 'App\\Models\\Permession', 646, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21674, 'App\\Models\\Permession', 646, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21675, 'App\\Models\\Permession', 647, 1, 'name', 'Edit About', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21676, 'App\\Models\\Permession', 647, 2, 'name', 'تعديل عن النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21677, 'App\\Models\\Permession', 647, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21678, 'App\\Models\\Permession', 647, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21679, 'App\\Models\\Permession', 648, 1, 'name', 'View Contact', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21680, 'App\\Models\\Permession', 648, 2, 'name', 'عرض الاتصال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21681, 'App\\Models\\Permession', 648, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21682, 'App\\Models\\Permession', 648, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21683, 'App\\Models\\Permession', 649, 1, 'name', 'Edit Contact', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21684, 'App\\Models\\Permession', 649, 2, 'name', 'تعديل الاتصال', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21685, 'App\\Models\\Permession', 649, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21686, 'App\\Models\\Permession', 649, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21687, 'App\\Models\\Permession', 650, 1, 'name', 'View Messages', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21688, 'App\\Models\\Permession', 650, 2, 'name', 'عرض الرسائل', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21689, 'App\\Models\\Permession', 650, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21690, 'App\\Models\\Permession', 650, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21691, 'App\\Models\\Permession', 651, 1, 'name', 'Delete Messages', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21692, 'App\\Models\\Permession', 651, 2, 'name', 'حذف الرسائل', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21693, 'App\\Models\\Permession', 651, 1, 'group_by', 'System Settings', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21694, 'App\\Models\\Permession', 651, 2, 'group_by', 'إعدادات النظام', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21695, 'App\\Models\\Role', 23, 1, 'name', 'Super Admin Eramo', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21696, 'App\\Models\\Role', 23, 2, 'name', 'سوبر ادمن ايرامو', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21697, 'App\\Models\\Role', 24, 1, 'name', 'Admin Eramo', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21698, 'App\\Models\\Role', 24, 2, 'name', 'ادمن ايرامو', '2025-11-03 06:58:36', '2025-11-03 06:58:36', NULL),
(21699, 'App\\Models\\Permession', 652, 1, 'name', 'View Dashboard', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21700, 'App\\Models\\Permession', 652, 2, 'name', 'عرض لوحة التحكم', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21701, 'App\\Models\\Permession', 652, 1, 'group_by', 'Dashboard', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21702, 'App\\Models\\Permession', 652, 2, 'group_by', 'لوحة التحكم', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21703, 'App\\Models\\Permession', 653, 1, 'name', 'All Activities', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21704, 'App\\Models\\Permession', 653, 2, 'name', 'كل الانشطة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21705, 'App\\Models\\Permession', 653, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21706, 'App\\Models\\Permession', 653, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21707, 'App\\Models\\Permession', 654, 1, 'name', 'View Activities', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21708, 'App\\Models\\Permession', 654, 2, 'name', 'عرض الانشطة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21709, 'App\\Models\\Permession', 654, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21710, 'App\\Models\\Permession', 654, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21711, 'App\\Models\\Permession', 655, 1, 'name', 'Create Activities', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21712, 'App\\Models\\Permession', 655, 2, 'name', 'إنشاء الانشطة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21713, 'App\\Models\\Permession', 655, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21714, 'App\\Models\\Permession', 655, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21715, 'App\\Models\\Permession', 656, 1, 'name', 'Edit Activities', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21716, 'App\\Models\\Permession', 656, 2, 'name', 'تعديل الانشطة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21717, 'App\\Models\\Permession', 656, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21718, 'App\\Models\\Permession', 656, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21719, 'App\\Models\\Permession', 657, 1, 'name', 'Delete Activities', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21720, 'App\\Models\\Permession', 657, 2, 'name', 'ازالة الانشطة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21721, 'App\\Models\\Permession', 657, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21722, 'App\\Models\\Permession', 657, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21723, 'App\\Models\\Permession', 658, 1, 'name', 'All Departments', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21724, 'App\\Models\\Permession', 658, 2, 'name', 'كل الأقسام', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21725, 'App\\Models\\Permession', 658, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21726, 'App\\Models\\Permession', 658, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21727, 'App\\Models\\Permession', 659, 1, 'name', 'View Departments', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21728, 'App\\Models\\Permession', 659, 2, 'name', 'عرض الأقسام', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21729, 'App\\Models\\Permession', 659, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21730, 'App\\Models\\Permession', 659, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21731, 'App\\Models\\Permession', 660, 1, 'name', 'Create Department', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21732, 'App\\Models\\Permession', 660, 2, 'name', 'إنشاء قسم', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21733, 'App\\Models\\Permession', 660, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21734, 'App\\Models\\Permession', 660, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21735, 'App\\Models\\Permession', 661, 1, 'name', 'Edit Department', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21736, 'App\\Models\\Permession', 661, 2, 'name', 'تعديل قسم', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21737, 'App\\Models\\Permession', 661, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21738, 'App\\Models\\Permession', 661, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21739, 'App\\Models\\Permession', 662, 1, 'name', 'Delete Department', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21740, 'App\\Models\\Permession', 662, 2, 'name', 'حذف قسم', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21741, 'App\\Models\\Permession', 662, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21742, 'App\\Models\\Permession', 662, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21743, 'App\\Models\\Permession', 663, 1, 'name', 'All Main Categories', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21744, 'App\\Models\\Permession', 663, 2, 'name', 'كل الأقسام الرئيسية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21745, 'App\\Models\\Permession', 663, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21746, 'App\\Models\\Permession', 663, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21747, 'App\\Models\\Permession', 664, 1, 'name', 'View Main Categories', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21748, 'App\\Models\\Permession', 664, 2, 'name', 'عرض الأقسام الرئيسية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21749, 'App\\Models\\Permession', 664, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21750, 'App\\Models\\Permession', 664, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21751, 'App\\Models\\Permession', 665, 1, 'name', 'Create Main Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21752, 'App\\Models\\Permession', 665, 2, 'name', 'إنشاء قسم رئيسية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21753, 'App\\Models\\Permession', 665, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21754, 'App\\Models\\Permession', 665, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21755, 'App\\Models\\Permession', 666, 1, 'name', 'Edit Main Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21756, 'App\\Models\\Permession', 666, 2, 'name', 'تعديل قسم رئيسية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21757, 'App\\Models\\Permession', 666, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21758, 'App\\Models\\Permession', 666, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21759, 'App\\Models\\Permession', 667, 1, 'name', 'Delete Main Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21760, 'App\\Models\\Permession', 667, 2, 'name', 'حذف قسم رئيسية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21761, 'App\\Models\\Permession', 667, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21762, 'App\\Models\\Permession', 667, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21763, 'App\\Models\\Permession', 668, 1, 'name', 'All Sub Categories', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21764, 'App\\Models\\Permession', 668, 2, 'name', 'كل الأقسام الفرعية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21765, 'App\\Models\\Permession', 668, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21766, 'App\\Models\\Permession', 668, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21767, 'App\\Models\\Permession', 669, 1, 'name', 'View Sub Categories', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21768, 'App\\Models\\Permession', 669, 2, 'name', 'عرض الأقسام الفرعية', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21769, 'App\\Models\\Permession', 669, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21770, 'App\\Models\\Permession', 669, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21771, 'App\\Models\\Permession', 670, 1, 'name', 'Create Sub Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21772, 'App\\Models\\Permession', 670, 2, 'name', 'إنشاء قسم فرعي', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21773, 'App\\Models\\Permession', 670, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21774, 'App\\Models\\Permession', 670, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21775, 'App\\Models\\Permession', 671, 1, 'name', 'Edit Sub Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21776, 'App\\Models\\Permession', 671, 2, 'name', 'تعديل قسم فرعي', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21777, 'App\\Models\\Permession', 671, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21778, 'App\\Models\\Permession', 671, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21779, 'App\\Models\\Permession', 672, 1, 'name', 'Delete Sub Category', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21780, 'App\\Models\\Permession', 672, 2, 'name', 'حذف قسم فرعي', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21781, 'App\\Models\\Permession', 672, 1, 'group_by', 'Catalog Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21782, 'App\\Models\\Permession', 672, 2, 'group_by', 'إدارة الكتالوج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21783, 'App\\Models\\Permession', 673, 1, 'name', 'All Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21784, 'App\\Models\\Permession', 673, 2, 'name', 'كل المنتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21785, 'App\\Models\\Permession', 673, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21786, 'App\\Models\\Permession', 673, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21787, 'App\\Models\\Permession', 674, 1, 'name', 'View Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21788, 'App\\Models\\Permession', 674, 2, 'name', 'عرض المنتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21789, 'App\\Models\\Permession', 674, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21790, 'App\\Models\\Permession', 674, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21791, 'App\\Models\\Permession', 675, 1, 'name', 'Create Product', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21792, 'App\\Models\\Permession', 675, 2, 'name', 'إنشاء منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21793, 'App\\Models\\Permession', 675, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21794, 'App\\Models\\Permession', 675, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21795, 'App\\Models\\Permession', 676, 1, 'name', 'Edit Product', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21796, 'App\\Models\\Permession', 676, 2, 'name', 'تعديل منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21797, 'App\\Models\\Permession', 676, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21798, 'App\\Models\\Permession', 676, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL);
INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(21799, 'App\\Models\\Permession', 677, 1, 'name', 'Delete Product', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21800, 'App\\Models\\Permession', 677, 2, 'name', 'حذف منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21801, 'App\\Models\\Permession', 677, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21802, 'App\\Models\\Permession', 677, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21803, 'App\\Models\\Permession', 678, 1, 'name', 'View In Stock Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21804, 'App\\Models\\Permession', 678, 2, 'name', 'عرض المنتجات في المخزون', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21805, 'App\\Models\\Permession', 678, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21806, 'App\\Models\\Permession', 678, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21807, 'App\\Models\\Permession', 679, 1, 'name', 'View Out of Stock Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21808, 'App\\Models\\Permession', 679, 2, 'name', 'عرض المنتجات غير في المخزون', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21809, 'App\\Models\\Permession', 679, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21810, 'App\\Models\\Permession', 679, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21811, 'App\\Models\\Permession', 680, 1, 'name', 'View Product Setup', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21812, 'App\\Models\\Permession', 680, 2, 'name', 'عرض إعداد المنتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21813, 'App\\Models\\Permession', 680, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21814, 'App\\Models\\Permession', 680, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21815, 'App\\Models\\Permession', 681, 1, 'name', 'Create Product Setup', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21816, 'App\\Models\\Permession', 681, 2, 'name', 'إنشاء إعداد منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21817, 'App\\Models\\Permession', 681, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21818, 'App\\Models\\Permession', 681, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21819, 'App\\Models\\Permession', 682, 1, 'name', 'Edit Product Setup', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21820, 'App\\Models\\Permession', 682, 2, 'name', 'تعديل إعداد منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21821, 'App\\Models\\Permession', 682, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21822, 'App\\Models\\Permession', 682, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21823, 'App\\Models\\Permession', 683, 1, 'name', 'Delete Product Setup', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21824, 'App\\Models\\Permession', 683, 2, 'name', 'حذف إعداد منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21825, 'App\\Models\\Permession', 683, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21826, 'App\\Models\\Permession', 683, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21827, 'App\\Models\\Permession', 684, 1, 'name', 'View Product Reviews', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21828, 'App\\Models\\Permession', 684, 2, 'name', 'عرض تقييم المنتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21829, 'App\\Models\\Permession', 684, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21830, 'App\\Models\\Permession', 684, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21831, 'App\\Models\\Permession', 685, 1, 'name', 'Accept Product Review', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21832, 'App\\Models\\Permession', 685, 2, 'name', 'قبول تقييم منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21833, 'App\\Models\\Permession', 685, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21834, 'App\\Models\\Permession', 685, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21835, 'App\\Models\\Permession', 686, 1, 'name', 'Reject Product Review', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21836, 'App\\Models\\Permession', 686, 2, 'name', 'رفض تقييم منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21837, 'App\\Models\\Permession', 686, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21838, 'App\\Models\\Permession', 686, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21839, 'App\\Models\\Permession', 687, 1, 'name', 'Delete Product Review', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21840, 'App\\Models\\Permession', 687, 2, 'name', 'حذف تقييم منتج', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21841, 'App\\Models\\Permession', 687, 1, 'group_by', 'Products', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21842, 'App\\Models\\Permession', 687, 2, 'group_by', 'منتجات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21843, 'App\\Models\\Permession', 688, 1, 'name', 'All Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21844, 'App\\Models\\Permession', 688, 2, 'name', 'كل الضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21845, 'App\\Models\\Permession', 688, 1, 'group_by', 'Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21846, 'App\\Models\\Permession', 688, 2, 'group_by', 'ضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21847, 'App\\Models\\Permession', 689, 1, 'name', 'View Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21848, 'App\\Models\\Permession', 689, 2, 'name', 'عرض الضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21849, 'App\\Models\\Permession', 689, 1, 'group_by', 'Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21850, 'App\\Models\\Permession', 689, 2, 'group_by', 'ضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21851, 'App\\Models\\Permession', 690, 1, 'name', 'Create Tax', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21852, 'App\\Models\\Permession', 690, 2, 'name', 'إنشاء ضريبة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21853, 'App\\Models\\Permession', 690, 1, 'group_by', 'Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21854, 'App\\Models\\Permession', 690, 2, 'group_by', 'ضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21855, 'App\\Models\\Permession', 691, 1, 'name', 'Edit Tax', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21856, 'App\\Models\\Permession', 691, 2, 'name', 'تعديل ضريبة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21857, 'App\\Models\\Permession', 691, 1, 'group_by', 'Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21858, 'App\\Models\\Permession', 691, 2, 'group_by', 'ضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21859, 'App\\Models\\Permession', 692, 1, 'name', 'Delete Tax', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21860, 'App\\Models\\Permession', 692, 2, 'name', 'حذف ضريبة', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21861, 'App\\Models\\Permession', 692, 1, 'group_by', 'Taxes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21862, 'App\\Models\\Permession', 692, 2, 'group_by', 'ضرائب', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21863, 'App\\Models\\Permession', 693, 1, 'name', 'All Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21864, 'App\\Models\\Permession', 693, 2, 'name', 'كل العروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21865, 'App\\Models\\Permession', 693, 1, 'group_by', 'Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21866, 'App\\Models\\Permession', 693, 2, 'group_by', 'عروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21867, 'App\\Models\\Permession', 694, 1, 'name', 'View Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21868, 'App\\Models\\Permession', 694, 2, 'name', 'عرض العروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21869, 'App\\Models\\Permession', 694, 1, 'group_by', 'Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21870, 'App\\Models\\Permession', 694, 2, 'group_by', 'عروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21871, 'App\\Models\\Permession', 695, 1, 'name', 'Create Offer', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21872, 'App\\Models\\Permession', 695, 2, 'name', 'إنشاء عرض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21873, 'App\\Models\\Permession', 695, 1, 'group_by', 'Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21874, 'App\\Models\\Permession', 695, 2, 'group_by', 'عروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21875, 'App\\Models\\Permession', 696, 1, 'name', 'Edit Offer', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21876, 'App\\Models\\Permession', 696, 2, 'name', 'تعديل عرض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21877, 'App\\Models\\Permession', 696, 1, 'group_by', 'Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21878, 'App\\Models\\Permession', 696, 2, 'group_by', 'عروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21879, 'App\\Models\\Permession', 697, 1, 'name', 'Delete Offer', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21880, 'App\\Models\\Permession', 697, 2, 'name', 'حذف عرض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21881, 'App\\Models\\Permession', 697, 1, 'group_by', 'Offers', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21882, 'App\\Models\\Permession', 697, 2, 'group_by', 'عروض', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21883, 'App\\Models\\Permession', 698, 1, 'name', 'All Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21884, 'App\\Models\\Permession', 698, 2, 'name', 'كل الكودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21885, 'App\\Models\\Permession', 698, 1, 'group_by', 'Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21886, 'App\\Models\\Permession', 698, 2, 'group_by', 'كودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21887, 'App\\Models\\Permession', 699, 1, 'name', 'View Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21888, 'App\\Models\\Permession', 699, 2, 'name', 'عرض الكودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21889, 'App\\Models\\Permession', 699, 1, 'group_by', 'Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21890, 'App\\Models\\Permession', 699, 2, 'group_by', 'كودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21891, 'App\\Models\\Permession', 700, 1, 'name', 'Create Promocode', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21892, 'App\\Models\\Permession', 700, 2, 'name', 'إنشاء كود', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21893, 'App\\Models\\Permession', 700, 1, 'group_by', 'Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21894, 'App\\Models\\Permession', 700, 2, 'group_by', 'كودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21895, 'App\\Models\\Permession', 701, 1, 'name', 'Edit Promocode', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21896, 'App\\Models\\Permession', 701, 2, 'name', 'تعديل كود', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21897, 'App\\Models\\Permession', 701, 1, 'group_by', 'Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21898, 'App\\Models\\Permession', 701, 2, 'group_by', 'كودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21899, 'App\\Models\\Permession', 702, 1, 'name', 'Delete Promocode', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21900, 'App\\Models\\Permession', 702, 2, 'name', 'حذف كود', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21901, 'App\\Models\\Permession', 702, 1, 'group_by', 'Promocodes', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21902, 'App\\Models\\Permession', 702, 2, 'group_by', 'كودات', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21903, 'App\\Models\\Permession', 703, 1, 'name', 'All Roles', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21904, 'App\\Models\\Permession', 703, 2, 'name', 'كل الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21905, 'App\\Models\\Permession', 703, 1, 'group_by', 'Roles Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21906, 'App\\Models\\Permession', 703, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21907, 'App\\Models\\Permession', 704, 1, 'name', 'View Roles', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21908, 'App\\Models\\Permession', 704, 2, 'name', 'عرض الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21909, 'App\\Models\\Permession', 704, 1, 'group_by', 'Roles Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21910, 'App\\Models\\Permession', 704, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21911, 'App\\Models\\Permession', 705, 1, 'name', 'Create Role', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21912, 'App\\Models\\Permession', 705, 2, 'name', 'إنشاء دور', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21913, 'App\\Models\\Permession', 705, 1, 'group_by', 'Roles Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21914, 'App\\Models\\Permession', 705, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21915, 'App\\Models\\Permession', 706, 1, 'name', 'Edit Role', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21916, 'App\\Models\\Permession', 706, 2, 'name', 'تعديل دور', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21917, 'App\\Models\\Permession', 706, 1, 'group_by', 'Roles Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21918, 'App\\Models\\Permession', 706, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21919, 'App\\Models\\Permession', 707, 1, 'name', 'Delete Role', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21920, 'App\\Models\\Permession', 707, 2, 'name', 'حذف دور', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21921, 'App\\Models\\Permession', 707, 1, 'group_by', 'Roles Management', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21922, 'App\\Models\\Permession', 707, 2, 'group_by', 'إدارة الأدوار', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21923, 'App\\Models\\Permession', 708, 1, 'name', 'All Admins', '2025-11-03 07:03:39', '2025-11-03 07:03:39', NULL),
(21924, 'App\\Models\\Permession', 708, 2, 'name', 'كل المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21925, 'App\\Models\\Permession', 708, 1, 'group_by', 'Admin Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21926, 'App\\Models\\Permession', 708, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21927, 'App\\Models\\Permession', 709, 1, 'name', 'View Admins', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21928, 'App\\Models\\Permession', 709, 2, 'name', 'عرض المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21929, 'App\\Models\\Permession', 709, 1, 'group_by', 'Admin Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21930, 'App\\Models\\Permession', 709, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21931, 'App\\Models\\Permession', 710, 1, 'name', 'Create Admin', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21932, 'App\\Models\\Permession', 710, 2, 'name', 'إنشاء المسؤول', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21933, 'App\\Models\\Permession', 710, 1, 'group_by', 'Admin Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21934, 'App\\Models\\Permession', 710, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21935, 'App\\Models\\Permession', 711, 1, 'name', 'Edit Admin', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21936, 'App\\Models\\Permession', 711, 2, 'name', 'تعديل المسؤول', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21937, 'App\\Models\\Permession', 711, 1, 'group_by', 'Admin Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21938, 'App\\Models\\Permession', 711, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21939, 'App\\Models\\Permession', 712, 1, 'name', 'Delete Admin', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21940, 'App\\Models\\Permession', 712, 2, 'name', 'ازالة المسؤول', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21941, 'App\\Models\\Permession', 712, 1, 'group_by', 'Admin Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21942, 'App\\Models\\Permession', 712, 2, 'group_by', 'إدارة المسؤولين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21943, 'App\\Models\\Permession', 713, 1, 'name', 'All Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21944, 'App\\Models\\Permession', 713, 2, 'name', 'كل الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21945, 'App\\Models\\Permession', 713, 1, 'group_by', 'Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21946, 'App\\Models\\Permession', 713, 2, 'group_by', 'الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21947, 'App\\Models\\Permession', 714, 1, 'name', 'View Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21948, 'App\\Models\\Permession', 714, 2, 'name', 'عرض الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21949, 'App\\Models\\Permession', 714, 1, 'group_by', 'Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21950, 'App\\Models\\Permession', 714, 2, 'group_by', 'الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21951, 'App\\Models\\Permession', 715, 1, 'name', 'Create Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21952, 'App\\Models\\Permession', 715, 2, 'name', 'انشاء الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21953, 'App\\Models\\Permession', 715, 1, 'group_by', 'Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21954, 'App\\Models\\Permession', 715, 2, 'group_by', 'الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21955, 'App\\Models\\Permession', 716, 1, 'name', 'Edit Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21956, 'App\\Models\\Permession', 716, 2, 'name', 'تعديل الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21957, 'App\\Models\\Permession', 716, 1, 'group_by', 'Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21958, 'App\\Models\\Permession', 716, 2, 'group_by', 'الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21959, 'App\\Models\\Permession', 717, 1, 'name', 'Remove Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21960, 'App\\Models\\Permession', 717, 2, 'name', 'ازالة الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21961, 'App\\Models\\Permession', 717, 1, 'group_by', 'Vendors', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21962, 'App\\Models\\Permession', 717, 2, 'group_by', 'الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21963, 'App\\Models\\Permession', 718, 1, 'name', 'View New Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21964, 'App\\Models\\Permession', 718, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21965, 'App\\Models\\Permession', 718, 1, 'group_by', 'Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21966, 'App\\Models\\Permession', 718, 2, 'group_by', 'طلبات الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21967, 'App\\Models\\Permession', 719, 1, 'name', 'Accept Vendor Request', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21968, 'App\\Models\\Permession', 719, 2, 'name', 'قبول طلب المورد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21969, 'App\\Models\\Permession', 719, 1, 'group_by', 'Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21970, 'App\\Models\\Permession', 719, 2, 'group_by', 'طلبات الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21971, 'App\\Models\\Permession', 720, 1, 'name', 'Reject Vendor Request', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21972, 'App\\Models\\Permession', 720, 2, 'name', 'رفض طلب المورد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21973, 'App\\Models\\Permession', 720, 1, 'group_by', 'Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21974, 'App\\Models\\Permession', 720, 2, 'group_by', 'طلبات الموردين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21975, 'App\\Models\\Permession', 721, 1, 'name', 'View New Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21976, 'App\\Models\\Permession', 721, 2, 'name', 'عرض الطلبات الجديدة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21977, 'App\\Models\\Permession', 721, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21978, 'App\\Models\\Permession', 721, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21979, 'App\\Models\\Permession', 722, 1, 'name', 'View Inprogress Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21980, 'App\\Models\\Permession', 722, 2, 'name', 'عرض الطلبات المعلقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21981, 'App\\Models\\Permession', 722, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21982, 'App\\Models\\Permession', 722, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21983, 'App\\Models\\Permession', 723, 1, 'name', 'View Delivered Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21984, 'App\\Models\\Permession', 723, 2, 'name', 'عرض الطلبات المكتملة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21985, 'App\\Models\\Permession', 723, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21986, 'App\\Models\\Permession', 723, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21987, 'App\\Models\\Permession', 724, 1, 'name', 'View Canceled Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21988, 'App\\Models\\Permession', 724, 2, 'name', 'عرض الطلبات الملغاة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21989, 'App\\Models\\Permession', 724, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21990, 'App\\Models\\Permession', 724, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21991, 'App\\Models\\Permession', 725, 1, 'name', 'View Refunded Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21992, 'App\\Models\\Permession', 725, 2, 'name', 'عرض الطلبات المدفوعة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21993, 'App\\Models\\Permession', 725, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21994, 'App\\Models\\Permession', 725, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21995, 'App\\Models\\Permession', 726, 1, 'name', 'Edit Order', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21996, 'App\\Models\\Permession', 726, 2, 'name', 'تعديل طلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21997, 'App\\Models\\Permession', 726, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21998, 'App\\Models\\Permession', 726, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(21999, 'App\\Models\\Permession', 727, 1, 'name', 'Delete Order', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22000, 'App\\Models\\Permession', 727, 2, 'name', 'حذف طلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22001, 'App\\Models\\Permession', 727, 1, 'group_by', 'Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22002, 'App\\Models\\Permession', 727, 2, 'group_by', 'الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22003, 'App\\Models\\Permession', 728, 1, 'name', 'All Order Stages', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22004, 'App\\Models\\Permession', 728, 2, 'name', 'كل خطوات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22005, 'App\\Models\\Permession', 728, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22006, 'App\\Models\\Permession', 728, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22007, 'App\\Models\\Permession', 729, 1, 'name', 'View Order Stages', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22008, 'App\\Models\\Permession', 729, 2, 'name', 'عرض خطوات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22009, 'App\\Models\\Permession', 729, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22010, 'App\\Models\\Permession', 729, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22011, 'App\\Models\\Permession', 730, 1, 'name', 'Create Order Stage', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22012, 'App\\Models\\Permession', 730, 2, 'name', 'إنشاء خطوة طلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22013, 'App\\Models\\Permession', 730, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22014, 'App\\Models\\Permession', 730, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22015, 'App\\Models\\Permession', 731, 1, 'name', 'Edit Order Stage', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22016, 'App\\Models\\Permession', 731, 2, 'name', 'تعديل خطوة طلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22017, 'App\\Models\\Permession', 731, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22018, 'App\\Models\\Permession', 731, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22019, 'App\\Models\\Permession', 732, 1, 'name', 'Delete Order Stage', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22020, 'App\\Models\\Permession', 732, 2, 'name', 'حذف خطوة طلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22021, 'App\\Models\\Permession', 732, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22022, 'App\\Models\\Permession', 732, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22023, 'App\\Models\\Permession', 733, 1, 'name', 'All Shipping Methods', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22024, 'App\\Models\\Permession', 733, 2, 'name', 'كل طرق الشحن', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22025, 'App\\Models\\Permession', 733, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22026, 'App\\Models\\Permession', 733, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22027, 'App\\Models\\Permession', 734, 1, 'name', 'View Shipping Methods', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22028, 'App\\Models\\Permession', 734, 2, 'name', 'عرض طرق الشحن', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22029, 'App\\Models\\Permession', 734, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22030, 'App\\Models\\Permession', 734, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22031, 'App\\Models\\Permession', 735, 1, 'name', 'Create Shipping Method', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22032, 'App\\Models\\Permession', 735, 2, 'name', 'إنشاء طريقة شحن', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22033, 'App\\Models\\Permession', 735, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22034, 'App\\Models\\Permession', 735, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22035, 'App\\Models\\Permession', 736, 1, 'name', 'Edit Shipping Method', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22036, 'App\\Models\\Permession', 736, 2, 'name', 'تعديل طريقة شحن', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22037, 'App\\Models\\Permession', 736, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22038, 'App\\Models\\Permession', 736, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22039, 'App\\Models\\Permession', 737, 1, 'name', 'Delete Shipping Method', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22040, 'App\\Models\\Permession', 737, 2, 'name', 'حذف طريقة شحن', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22041, 'App\\Models\\Permession', 737, 1, 'group_by', 'Order Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22042, 'App\\Models\\Permession', 737, 2, 'group_by', 'إعدادات الطلب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22043, 'App\\Models\\Permession', 738, 1, 'name', 'All Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22044, 'App\\Models\\Permession', 738, 2, 'name', 'كل نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22045, 'App\\Models\\Permession', 738, 1, 'group_by', 'Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22046, 'App\\Models\\Permession', 738, 2, 'group_by', 'نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22047, 'App\\Models\\Permession', 739, 1, 'name', 'View Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22048, 'App\\Models\\Permession', 739, 2, 'name', 'عرض نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22049, 'App\\Models\\Permession', 739, 1, 'group_by', 'Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22050, 'App\\Models\\Permession', 739, 2, 'group_by', 'نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22051, 'App\\Models\\Permession', 740, 1, 'name', 'Create Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22052, 'App\\Models\\Permession', 740, 2, 'name', 'إنشاء نظام نقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22053, 'App\\Models\\Permession', 740, 1, 'group_by', 'Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22054, 'App\\Models\\Permession', 740, 2, 'group_by', 'نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22055, 'App\\Models\\Permession', 741, 1, 'name', 'Edit Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22056, 'App\\Models\\Permession', 741, 2, 'name', 'تعديل نظام نقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22057, 'App\\Models\\Permession', 741, 1, 'group_by', 'Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22058, 'App\\Models\\Permession', 741, 2, 'group_by', 'نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22059, 'App\\Models\\Permession', 742, 1, 'name', 'Delete Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22060, 'App\\Models\\Permession', 742, 2, 'name', 'حذف نظام نقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22061, 'App\\Models\\Permession', 742, 1, 'group_by', 'Points System', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22062, 'App\\Models\\Permession', 742, 2, 'group_by', 'نظام النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22063, 'App\\Models\\Permession', 743, 1, 'name', 'All Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22064, 'App\\Models\\Permession', 743, 2, 'name', 'كل الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22065, 'App\\Models\\Permession', 743, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22066, 'App\\Models\\Permession', 743, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22067, 'App\\Models\\Permession', 744, 1, 'name', 'View Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22068, 'App\\Models\\Permession', 744, 2, 'name', 'عرض الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22069, 'App\\Models\\Permession', 744, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22070, 'App\\Models\\Permession', 744, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22071, 'App\\Models\\Permession', 745, 1, 'name', 'Create Advertisement', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22072, 'App\\Models\\Permession', 745, 2, 'name', 'إنشاء إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22073, 'App\\Models\\Permession', 745, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22074, 'App\\Models\\Permession', 745, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22075, 'App\\Models\\Permession', 746, 1, 'name', 'Edit Advertisement', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22076, 'App\\Models\\Permession', 746, 2, 'name', 'تعديل إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22077, 'App\\Models\\Permession', 746, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22078, 'App\\Models\\Permession', 746, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22079, 'App\\Models\\Permession', 747, 1, 'name', 'Delete Advertisement', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22080, 'App\\Models\\Permession', 747, 2, 'name', 'حذف إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22081, 'App\\Models\\Permession', 747, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22082, 'App\\Models\\Permession', 747, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22083, 'App\\Models\\Permession', 748, 1, 'name', 'All Advertisement Positions', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22084, 'App\\Models\\Permession', 748, 2, 'name', 'كل مواقع الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22085, 'App\\Models\\Permession', 748, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22086, 'App\\Models\\Permession', 748, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22087, 'App\\Models\\Permession', 749, 1, 'name', 'View Advertisement Positions', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22088, 'App\\Models\\Permession', 749, 2, 'name', 'عرض مواقع الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22089, 'App\\Models\\Permession', 749, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22090, 'App\\Models\\Permession', 749, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22091, 'App\\Models\\Permession', 750, 1, 'name', 'Create Advertisement Position', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22092, 'App\\Models\\Permession', 750, 2, 'name', 'إنشاء موقع إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22093, 'App\\Models\\Permession', 750, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22094, 'App\\Models\\Permession', 750, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22095, 'App\\Models\\Permession', 751, 1, 'name', 'Edit Advertisement Position', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22096, 'App\\Models\\Permession', 751, 2, 'name', 'تعديل موقع إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22097, 'App\\Models\\Permession', 751, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22098, 'App\\Models\\Permession', 751, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22099, 'App\\Models\\Permession', 752, 1, 'name', 'Delete Advertisement Position', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22100, 'App\\Models\\Permession', 752, 2, 'name', 'حذف موقع إعلان', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22101, 'App\\Models\\Permession', 752, 1, 'group_by', 'Advertisements', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22102, 'App\\Models\\Permession', 752, 2, 'group_by', 'الإعلانات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22103, 'App\\Models\\Permession', 753, 1, 'name', 'View Notifications', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22104, 'App\\Models\\Permession', 753, 2, 'name', 'عرض الإشعارات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22105, 'App\\Models\\Permession', 753, 1, 'group_by', 'Notifications', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22106, 'App\\Models\\Permession', 753, 2, 'group_by', 'الإشعارات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22107, 'App\\Models\\Permession', 754, 1, 'name', 'Send Notification', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22108, 'App\\Models\\Permession', 754, 2, 'name', 'إرسال إشعار', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22109, 'App\\Models\\Permession', 754, 1, 'group_by', 'Notifications', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22110, 'App\\Models\\Permession', 754, 2, 'group_by', 'الإشعارات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22111, 'App\\Models\\Permession', 755, 1, 'name', 'Delete Notification', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22112, 'App\\Models\\Permession', 755, 2, 'name', 'حذف إشعار', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22113, 'App\\Models\\Permession', 755, 1, 'group_by', 'Notifications', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22114, 'App\\Models\\Permession', 755, 2, 'group_by', 'الإشعارات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22115, 'App\\Models\\Permession', 756, 1, 'name', 'View Accounting Overview', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22116, 'App\\Models\\Permession', 756, 2, 'name', 'عرض ملخص المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22117, 'App\\Models\\Permession', 756, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22118, 'App\\Models\\Permession', 756, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22119, 'App\\Models\\Permession', 757, 1, 'name', 'View Accounting Balance', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22120, 'App\\Models\\Permession', 757, 2, 'name', 'عرض ميزانية المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22121, 'App\\Models\\Permession', 757, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22122, 'App\\Models\\Permession', 757, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22123, 'App\\Models\\Permession', 758, 1, 'name', 'View Accounting Expenses Keys', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22124, 'App\\Models\\Permession', 758, 2, 'name', 'عرض مفاتيح النفقات المالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22125, 'App\\Models\\Permession', 758, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22126, 'App\\Models\\Permession', 758, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22127, 'App\\Models\\Permession', 759, 1, 'name', 'Create Accounting Expenses Key', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22128, 'App\\Models\\Permession', 759, 2, 'name', 'إنشاء مفتاح نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22129, 'App\\Models\\Permession', 759, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22130, 'App\\Models\\Permession', 759, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22131, 'App\\Models\\Permession', 760, 1, 'name', 'Edit Accounting Expenses Key', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22132, 'App\\Models\\Permession', 760, 2, 'name', 'تعديل مفتاح نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22133, 'App\\Models\\Permession', 760, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22134, 'App\\Models\\Permession', 760, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22135, 'App\\Models\\Permession', 761, 1, 'name', 'Delete Accounting Expenses Key', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22136, 'App\\Models\\Permession', 761, 2, 'name', 'حذف مفتاح نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22137, 'App\\Models\\Permession', 761, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22138, 'App\\Models\\Permession', 761, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22139, 'App\\Models\\Permession', 762, 1, 'name', 'View Accounting Expenses', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22140, 'App\\Models\\Permession', 762, 2, 'name', 'عرض النفقات المالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22141, 'App\\Models\\Permession', 762, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22142, 'App\\Models\\Permession', 762, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22143, 'App\\Models\\Permession', 763, 1, 'name', 'Create Accounting Expense', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22144, 'App\\Models\\Permession', 763, 2, 'name', 'إنشاء نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22145, 'App\\Models\\Permession', 763, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22146, 'App\\Models\\Permession', 763, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22147, 'App\\Models\\Permession', 764, 1, 'name', 'Edit Accounting Expense', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22148, 'App\\Models\\Permession', 764, 2, 'name', 'تعديل نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22149, 'App\\Models\\Permession', 764, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22150, 'App\\Models\\Permession', 764, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22151, 'App\\Models\\Permession', 765, 1, 'name', 'Delete Accounting Expense', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22152, 'App\\Models\\Permession', 765, 2, 'name', 'حذف نفقات مالية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22153, 'App\\Models\\Permession', 765, 1, 'group_by', 'Accounting', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22154, 'App\\Models\\Permession', 765, 2, 'group_by', 'المالي', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22155, 'App\\Models\\Permession', 766, 1, 'name', 'View Send Money', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22156, 'App\\Models\\Permession', 766, 2, 'name', 'عرض إرسال المال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22157, 'App\\Models\\Permession', 766, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22158, 'App\\Models\\Permession', 766, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22159, 'App\\Models\\Permession', 767, 1, 'name', 'Create Send Money', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22160, 'App\\Models\\Permession', 767, 2, 'name', 'إنشاء إرسال المال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22161, 'App\\Models\\Permession', 767, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22162, 'App\\Models\\Permession', 767, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22163, 'App\\Models\\Permession', 768, 1, 'name', 'View Transactions', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22164, 'App\\Models\\Permession', 768, 2, 'name', 'عرض المعاملات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22165, 'App\\Models\\Permession', 768, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22166, 'App\\Models\\Permession', 768, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22167, 'App\\Models\\Permession', 769, 1, 'name', 'View New Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22168, 'App\\Models\\Permession', 769, 2, 'name', 'عرض طلبات الموردين الجديدة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22169, 'App\\Models\\Permession', 769, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22170, 'App\\Models\\Permession', 769, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22171, 'App\\Models\\Permession', 770, 1, 'name', 'Accept Vendor Request', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22172, 'App\\Models\\Permession', 770, 2, 'name', 'قبول طلب المورد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22173, 'App\\Models\\Permession', 770, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22174, 'App\\Models\\Permession', 770, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22175, 'App\\Models\\Permession', 771, 1, 'name', 'Reject Vendor Request', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22176, 'App\\Models\\Permession', 771, 2, 'name', 'رفض طلب المورد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22177, 'App\\Models\\Permession', 771, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22178, 'App\\Models\\Permession', 771, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22179, 'App\\Models\\Permession', 772, 1, 'name', 'View Accepted Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22180, 'App\\Models\\Permession', 772, 2, 'name', 'عرض طلبات الموردين المقبولة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22181, 'App\\Models\\Permession', 772, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22182, 'App\\Models\\Permession', 772, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22183, 'App\\Models\\Permession', 773, 1, 'name', 'View Rejected Vendor Requests', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22184, 'App\\Models\\Permession', 773, 2, 'name', 'عرض طلبات الموردين الرفض', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22185, 'App\\Models\\Permession', 773, 1, 'group_by', 'Withdraw', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22186, 'App\\Models\\Permession', 773, 2, 'group_by', 'سحب', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22187, 'App\\Models\\Permession', 774, 1, 'name', 'View Blog Categories', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22188, 'App\\Models\\Permession', 774, 2, 'name', 'عرض مجموعات المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22189, 'App\\Models\\Permession', 774, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22190, 'App\\Models\\Permession', 774, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22191, 'App\\Models\\Permession', 775, 1, 'name', 'Create Blog Category', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22192, 'App\\Models\\Permession', 775, 2, 'name', 'إنشاء مجموعات المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22193, 'App\\Models\\Permession', 775, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22194, 'App\\Models\\Permession', 775, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22195, 'App\\Models\\Permession', 776, 1, 'name', 'Edit Blog Category', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22196, 'App\\Models\\Permession', 776, 2, 'name', 'تعديل مجموعات المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22197, 'App\\Models\\Permession', 776, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22198, 'App\\Models\\Permession', 776, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22199, 'App\\Models\\Permession', 777, 1, 'name', 'Delete Blog Category', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22200, 'App\\Models\\Permession', 777, 2, 'name', 'حذف مجموعات المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22201, 'App\\Models\\Permession', 777, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22202, 'App\\Models\\Permession', 777, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22203, 'App\\Models\\Permession', 778, 1, 'name', 'View Blog Posts', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22204, 'App\\Models\\Permession', 778, 2, 'name', 'عرض المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22205, 'App\\Models\\Permession', 778, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22206, 'App\\Models\\Permession', 778, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22207, 'App\\Models\\Permession', 779, 1, 'name', 'Create Blog Post', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22208, 'App\\Models\\Permession', 779, 2, 'name', 'إنشاء مقال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22209, 'App\\Models\\Permession', 779, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL);
INSERT INTO `translations` (`id`, `translatable_type`, `translatable_id`, `lang_id`, `lang_key`, `lang_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(22210, 'App\\Models\\Permession', 779, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22211, 'App\\Models\\Permession', 780, 1, 'name', 'Edit Blog Post', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22212, 'App\\Models\\Permession', 780, 2, 'name', 'تعديل مقال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22213, 'App\\Models\\Permession', 780, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22214, 'App\\Models\\Permession', 780, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22215, 'App\\Models\\Permession', 781, 1, 'name', 'Delete Blog Post', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22216, 'App\\Models\\Permession', 781, 2, 'name', 'حذف مقال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22217, 'App\\Models\\Permession', 781, 1, 'group_by', 'Blog Management', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22218, 'App\\Models\\Permession', 781, 2, 'group_by', 'إدارة المقالات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22219, 'App\\Models\\Permession', 782, 1, 'name', 'View Registered Users', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22220, 'App\\Models\\Permession', 782, 2, 'name', 'عرض المستخدمين المسجلين', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22221, 'App\\Models\\Permession', 782, 1, 'group_by', 'Reports', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22222, 'App\\Models\\Permession', 782, 2, 'group_by', 'التقارير', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22223, 'App\\Models\\Permession', 783, 1, 'name', 'View Area Users', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22224, 'App\\Models\\Permession', 783, 2, 'name', 'عرض المستخدمين في المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22225, 'App\\Models\\Permession', 783, 1, 'group_by', 'Reports', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22226, 'App\\Models\\Permession', 783, 2, 'group_by', 'التقارير', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22227, 'App\\Models\\Permession', 784, 1, 'name', 'View Orders', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22228, 'App\\Models\\Permession', 784, 2, 'name', 'عرض الطلبات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22229, 'App\\Models\\Permession', 784, 1, 'group_by', 'Reports', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22230, 'App\\Models\\Permession', 784, 2, 'group_by', 'التقارير', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22231, 'App\\Models\\Permession', 785, 1, 'name', 'View Products', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22232, 'App\\Models\\Permession', 785, 2, 'name', 'عرض المنتجات', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22233, 'App\\Models\\Permession', 785, 1, 'group_by', 'Reports', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22234, 'App\\Models\\Permession', 785, 2, 'group_by', 'التقارير', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22235, 'App\\Models\\Permession', 786, 1, 'name', 'View Points', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22236, 'App\\Models\\Permession', 786, 2, 'name', 'عرض النقاط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22237, 'App\\Models\\Permession', 786, 1, 'group_by', 'Reports', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22238, 'App\\Models\\Permession', 786, 2, 'group_by', 'التقارير', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22239, 'App\\Models\\Permession', 787, 1, 'name', 'View System Log', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22240, 'App\\Models\\Permession', 787, 2, 'name', 'عرض سجل النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22241, 'App\\Models\\Permession', 787, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22242, 'App\\Models\\Permession', 787, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22243, 'App\\Models\\Permession', 788, 1, 'name', 'View Country', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22244, 'App\\Models\\Permession', 788, 2, 'name', 'عرض البلد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22245, 'App\\Models\\Permession', 788, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22246, 'App\\Models\\Permession', 788, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22247, 'App\\Models\\Permession', 789, 1, 'name', 'Create Country', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22248, 'App\\Models\\Permession', 789, 2, 'name', 'إنشاء بلد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22249, 'App\\Models\\Permession', 789, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22250, 'App\\Models\\Permession', 789, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22251, 'App\\Models\\Permession', 790, 1, 'name', 'Edit Country', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22252, 'App\\Models\\Permession', 790, 2, 'name', 'تعديل بلد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22253, 'App\\Models\\Permession', 790, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22254, 'App\\Models\\Permession', 790, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22255, 'App\\Models\\Permession', 791, 1, 'name', 'Delete Country', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22256, 'App\\Models\\Permession', 791, 2, 'name', 'حذف بلد', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22257, 'App\\Models\\Permession', 791, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22258, 'App\\Models\\Permession', 791, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22259, 'App\\Models\\Permession', 792, 1, 'name', 'View City', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22260, 'App\\Models\\Permession', 792, 2, 'name', 'عرض المدينة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22261, 'App\\Models\\Permession', 792, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22262, 'App\\Models\\Permession', 792, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22263, 'App\\Models\\Permession', 793, 1, 'name', 'Create City', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22264, 'App\\Models\\Permession', 793, 2, 'name', 'إنشاء مدينة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22265, 'App\\Models\\Permession', 793, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22266, 'App\\Models\\Permession', 793, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22267, 'App\\Models\\Permession', 794, 1, 'name', 'Edit City', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22268, 'App\\Models\\Permession', 794, 2, 'name', 'تعديل مدينة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22269, 'App\\Models\\Permession', 794, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22270, 'App\\Models\\Permession', 794, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22271, 'App\\Models\\Permession', 795, 1, 'name', 'Delete City', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22272, 'App\\Models\\Permession', 795, 2, 'name', 'حذف مدينة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22273, 'App\\Models\\Permession', 795, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22274, 'App\\Models\\Permession', 795, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22275, 'App\\Models\\Permession', 796, 1, 'name', 'View Region', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22276, 'App\\Models\\Permession', 796, 2, 'name', 'عرض المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22277, 'App\\Models\\Permession', 796, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22278, 'App\\Models\\Permession', 796, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22279, 'App\\Models\\Permession', 797, 1, 'name', 'Create Region', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22280, 'App\\Models\\Permession', 797, 2, 'name', 'إنشاء منطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22281, 'App\\Models\\Permession', 797, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22282, 'App\\Models\\Permession', 797, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22283, 'App\\Models\\Permession', 798, 1, 'name', 'Edit Region', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22284, 'App\\Models\\Permession', 798, 2, 'name', 'تعديل منطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22285, 'App\\Models\\Permession', 798, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22286, 'App\\Models\\Permession', 798, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22287, 'App\\Models\\Permession', 799, 1, 'name', 'Delete Region', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22288, 'App\\Models\\Permession', 799, 2, 'name', 'حذف منطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22289, 'App\\Models\\Permession', 799, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22290, 'App\\Models\\Permession', 799, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22291, 'App\\Models\\Permession', 800, 1, 'name', 'View Subregion', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22292, 'App\\Models\\Permession', 800, 2, 'name', 'عرض المنطقة الفرعية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22293, 'App\\Models\\Permession', 800, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22294, 'App\\Models\\Permession', 800, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22295, 'App\\Models\\Permession', 801, 1, 'name', 'Create Subregion', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22296, 'App\\Models\\Permession', 801, 2, 'name', 'إنشاء منطقة فرعية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22297, 'App\\Models\\Permession', 801, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22298, 'App\\Models\\Permession', 801, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22299, 'App\\Models\\Permession', 802, 1, 'name', 'Edit Subregion', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22300, 'App\\Models\\Permession', 802, 2, 'name', 'تعديل منطقة فرعية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22301, 'App\\Models\\Permession', 802, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22302, 'App\\Models\\Permession', 802, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22303, 'App\\Models\\Permession', 803, 1, 'name', 'Delete Subregion', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22304, 'App\\Models\\Permession', 803, 2, 'name', 'حذف منطقة فرعية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22305, 'App\\Models\\Permession', 803, 1, 'group_by', 'Area Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22306, 'App\\Models\\Permession', 803, 2, 'group_by', 'إعدادات المنطقة', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22307, 'App\\Models\\Permession', 804, 1, 'name', 'View Terms', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22308, 'App\\Models\\Permession', 804, 2, 'name', 'عرض الشروط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22309, 'App\\Models\\Permession', 804, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22310, 'App\\Models\\Permession', 804, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22311, 'App\\Models\\Permession', 805, 1, 'name', 'Edit Terms', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22312, 'App\\Models\\Permession', 805, 2, 'name', 'تعديل الشروط', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22313, 'App\\Models\\Permession', 805, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22314, 'App\\Models\\Permession', 805, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22315, 'App\\Models\\Permession', 806, 1, 'name', 'View Privacy', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22316, 'App\\Models\\Permession', 806, 2, 'name', 'عرض الخصوصية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22317, 'App\\Models\\Permession', 806, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22318, 'App\\Models\\Permession', 806, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22319, 'App\\Models\\Permession', 807, 1, 'name', 'Edit Privacy', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22320, 'App\\Models\\Permession', 807, 2, 'name', 'تعديل الخصوصية', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22321, 'App\\Models\\Permession', 807, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22322, 'App\\Models\\Permession', 807, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22323, 'App\\Models\\Permession', 808, 1, 'name', 'View About', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22324, 'App\\Models\\Permession', 808, 2, 'name', 'عرض عن النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22325, 'App\\Models\\Permession', 808, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22326, 'App\\Models\\Permession', 808, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22327, 'App\\Models\\Permession', 809, 1, 'name', 'Edit About', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22328, 'App\\Models\\Permession', 809, 2, 'name', 'تعديل عن النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22329, 'App\\Models\\Permession', 809, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22330, 'App\\Models\\Permession', 809, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22331, 'App\\Models\\Permession', 810, 1, 'name', 'View Contact', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22332, 'App\\Models\\Permession', 810, 2, 'name', 'عرض الاتصال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22333, 'App\\Models\\Permession', 810, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22334, 'App\\Models\\Permession', 810, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22335, 'App\\Models\\Permession', 811, 1, 'name', 'Edit Contact', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22336, 'App\\Models\\Permession', 811, 2, 'name', 'تعديل الاتصال', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22337, 'App\\Models\\Permession', 811, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22338, 'App\\Models\\Permession', 811, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22339, 'App\\Models\\Permession', 812, 1, 'name', 'View Messages', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22340, 'App\\Models\\Permession', 812, 2, 'name', 'عرض الرسائل', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22341, 'App\\Models\\Permession', 812, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22342, 'App\\Models\\Permession', 812, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22343, 'App\\Models\\Permession', 813, 1, 'name', 'Delete Messages', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22344, 'App\\Models\\Permession', 813, 2, 'name', 'حذف الرسائل', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22345, 'App\\Models\\Permession', 813, 1, 'group_by', 'System Settings', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22346, 'App\\Models\\Permession', 813, 2, 'group_by', 'إعدادات النظام', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22347, 'App\\Models\\Role', 25, 1, 'name', 'Super Admin Eramo', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22348, 'App\\Models\\Role', 25, 2, 'name', 'سوبر ادمن ايرامو', '2025-11-03 07:03:40', '2025-11-03 07:03:40', NULL),
(22349, 'App\\Models\\Role', 26, 1, 'name', 'Admin Eramo', '2025-11-03 07:03:41', '2025-11-03 07:03:41', NULL),
(22350, 'App\\Models\\Role', 26, 2, 'name', 'ادمن ايرامو', '2025-11-03 07:03:41', '2025-11-03 07:03:41', NULL),
(22351, 'App\\Models\\User', 14, 1, 'name', 'hamdy', '2025-11-03 07:57:20', '2025-11-03 07:57:20', NULL),
(22352, 'App\\Models\\User', 14, 2, 'name', 'حمدى عماد', '2025-11-03 07:57:20', '2025-11-03 07:57:20', NULL),
(22353, 'App\\Models\\User', 15, 1, 'name', 'mahmoud', '2025-11-03 07:57:53', '2025-11-03 07:58:17', '2025-11-03 07:58:17'),
(22354, 'App\\Models\\User', 15, 2, 'name', 'محمود', '2025-11-03 07:57:53', '2025-11-03 07:58:17', '2025-11-03 07:58:17'),
(22355, 'App\\Models\\User', 15, 1, 'name', 'mahmoud', '2025-11-03 07:58:17', '2025-11-03 08:35:10', '2025-11-03 08:35:10'),
(22356, 'App\\Models\\User', 15, 2, 'name', 'محمود', '2025-11-03 07:58:17', '2025-11-03 08:35:10', '2025-11-03 08:35:10'),
(22357, 'App\\Models\\User', 15, 1, 'name', 'mahmoud', '2025-11-03 08:35:10', '2025-11-03 08:35:10', NULL),
(22358, 'App\\Models\\User', 15, 2, 'name', 'محمود', '2025-11-03 08:35:10', '2025-11-03 08:35:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `user_type_id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint DEFAULT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_code_timestamp` timestamp NULL DEFAULT NULL,
  `reset_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` int NOT NULL DEFAULT '0',
  `block` int NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type_id`, `vendor_id`, `uuid`, `email`, `reset_code_timestamp`, `reset_code`, `email_verified_at`, `password`, `active`, `block`, `remember_token`, `deleted_at`, `created_at`, `updated_at`) VALUES
(13, 1, NULL, '2312f930-86df-4d90-b613-1940ff30ee50', 'super_admin@gmail.com', NULL, NULL, NULL, '$2y$10$BdNUvUDaHyWCVEA.u8ko8.h2m5ZCx53FldTilVP3xIAVkXAhaRi3K', 1, 1, NULL, NULL, '2025-11-03 07:03:39', '2025-11-03 07:03:39'),
(14, 2, NULL, 'd9023a09-c2c3-403c-a3f8-6ed848d4c557', 'test@test.com', NULL, NULL, NULL, '$2y$10$1EEMRfNJ6pj5cdmdj0ZX5.6e.pnvBZLMfPi087JxdORqTssW2YuAG', 1, 0, NULL, NULL, '2025-11-03 07:57:20', '2025-11-03 07:57:20'),
(15, 2, NULL, 'a18f2259-cb60-4718-b638-a24c872c6f3d', 'test2@test.com', NULL, NULL, NULL, '$2y$10$7rdpCLjySGWV5XLPxEaCuu2Jqdfw20ofHGHVLSyK/VCO2KxFAn4uu', 1, 0, NULL, NULL, '2025-11-03 07:57:53', '2025-11-03 07:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `users_types`
--

CREATE TABLE `users_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_types`
--

INSERT INTO `users_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', '2025-10-27 08:23:57', '2025-10-27 08:23:57'),
(2, 'admin', '2025-10-27 08:23:57', '2025-10-27 08:23:57'),
(3, 'vendor', '2025-10-27 08:23:57', '2025-10-27 08:23:57'),
(4, 'vendor_users', '2025-10-27 08:23:57', '2025-10-27 08:23:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(2, 13, 25, '2025-11-03 07:03:41', '2025-11-03 07:03:41'),
(3, 14, 26, '2025-11-03 07:57:20', '2025-11-03 07:57:20'),
(4, 15, 26, '2025-11-03 07:57:53', '2025-11-03 07:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `variants_configurations`
--

CREATE TABLE `variants_configurations` (
  `id` bigint UNSIGNED NOT NULL,
  `type` enum('text','color') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_id` bigint UNSIGNED DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variants_configurations_keys`
--

CREATE TABLE `variants_configurations_keys` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_key_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `country_id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('product','booking','product_booking') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'product',
  `active` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors_activities`
--

CREATE TABLE `vendors_activities` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `activity_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_commission`
--

CREATE TABLE `vendor_commission` (
  `id` bigint UNSIGNED NOT NULL,
  `vendor_id` bigint UNSIGNED NOT NULL,
  `commission` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities_departments`
--
ALTER TABLE `activities_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_departments_department_id_foreign` (`department_id`),
  ADD KEY `activities_departments_activity_id_foreign` (`activity_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_country_id_foreign` (`country_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `languages_name_unique` (`name`),
  ADD UNIQUE KEY `languages_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permessions`
--
ALTER TABLE `permessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permessions_key_unique` (`key`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `regions_city_id_foreign` (`city_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permession`
--
ALTER TABLE `role_permession`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permession_role_id_foreign` (`role_id`),
  ADD KEY `role_permession_permession_id_foreign` (`permession_id`);

--
-- Indexes for table `subregions`
--
ALTER TABLE `subregions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subregions_region_id_foreign` (`region_id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `taxes_slug_unique` (`slug`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translations_translatable_type_translatable_id_index` (`translatable_type`,`translatable_id`),
  ADD KEY `translations_lang_id_foreign` (`lang_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_user_type_id_foreign` (`user_type_id`);

--
-- Indexes for table `users_types`
--
ALTER TABLE `users_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `variants_configurations`
--
ALTER TABLE `variants_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variants_configurations_key_id_foreign` (`key_id`),
  ADD KEY `variants_configurations_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `variants_configurations_keys`
--
ALTER TABLE `variants_configurations_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variants_configurations_keys_parent_key_id_foreign` (`parent_key_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_slug_unique` (`slug`),
  ADD KEY `vendors_country_id_foreign` (`country_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vendors_activities`
--
ALTER TABLE `vendors_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendors_activities_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendors_activities_activity_id_foreign` (`activity_id`);

--
-- Indexes for table `vendor_commission`
--
ALTER TABLE `vendor_commission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_commission_vendor_id_foreign` (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10024;

--
-- AUTO_INCREMENT for table `activities_departments`
--
ALTER TABLE `activities_departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `permessions`
--
ALTER TABLE `permessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=814;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `role_permession`
--
ALTER TABLE `role_permession`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=863;

--
-- AUTO_INCREMENT for table `subregions`
--
ALTER TABLE `subregions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22359;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users_types`
--
ALTER TABLE `users_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `variants_configurations`
--
ALTER TABLE `variants_configurations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variants_configurations_keys`
--
ALTER TABLE `variants_configurations_keys`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendors_activities`
--
ALTER TABLE `vendors_activities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendor_commission`
--
ALTER TABLE `vendor_commission`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities_departments`
--
ALTER TABLE `activities_departments`
  ADD CONSTRAINT `activities_departments_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_departments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `regions`
--
ALTER TABLE `regions`
  ADD CONSTRAINT `regions_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permession`
--
ALTER TABLE `role_permession`
  ADD CONSTRAINT `role_permession_permession_id_foreign` FOREIGN KEY (`permession_id`) REFERENCES `permessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permession_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subregions`
--
ALTER TABLE `subregions`
  ADD CONSTRAINT `subregions_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_lang_id_foreign` FOREIGN KEY (`lang_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `users_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variants_configurations`
--
ALTER TABLE `variants_configurations`
  ADD CONSTRAINT `variants_configurations_key_id_foreign` FOREIGN KEY (`key_id`) REFERENCES `variants_configurations_keys` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variants_configurations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `variants_configurations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variants_configurations_keys`
--
ALTER TABLE `variants_configurations_keys`
  ADD CONSTRAINT `variants_configurations_keys_parent_key_id_foreign` FOREIGN KEY (`parent_key_id`) REFERENCES `variants_configurations_keys` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendors_activities`
--
ALTER TABLE `vendors_activities`
  ADD CONSTRAINT `vendors_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendors_activities_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_commission`
--
ALTER TABLE `vendor_commission`
  ADD CONSTRAINT `vendor_commission_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
