-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 19, 2022 at 04:28 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `costs`
--

CREATE TABLE `costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `balance` int(10) UNSIGNED NOT NULL,
  `billing_month` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `costs`
--

INSERT INTO `costs` (`id`, `amount`, `balance`, `billing_month`, `created_at`, `updated_at`) VALUES
(142, 5600, 5600, '2022-02-28', '2022-08-14 00:03:48', '2022-08-17 18:27:38'),
(189, 5, 9, '2022-04-17', '2022-08-17 07:44:25', '2022-08-17 18:02:02'),
(191, 4577, 4598, '2022-01-18', '2022-08-17 18:27:03', '2022-08-17 18:27:03'),
(192, 6530, 3144, '2022-03-18', '2022-08-17 18:29:14', '2022-08-17 18:29:14'),
(193, 5760, 5671, '2022-05-18', '2022-08-17 18:30:54', '2022-08-17 18:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(10) UNSIGNED NOT NULL,
  `paid_on` date NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `amount`, `paid_on`, `project_id`, `created_at`, `updated_at`) VALUES
(52, 5, '2022-03-31', 50, '2022-08-14 00:33:56', '2022-08-14 00:36:47'),
(55, 549600, '2022-02-18', 50, '2022-08-17 18:13:02', '2022-08-17 18:13:02'),
(56, 9820, '2022-04-18', 50, '2022-08-17 18:14:41', '2022-08-17 18:15:21'),
(57, 9996010, '2022-01-18', 50, '2022-08-17 18:29:49', '2022-08-17 18:29:49'),
(59, 3037, '2022-05-18', 50, '2022-08-17 18:31:14', '2022-08-17 18:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_04_21_154125_create_projects_table', 1),
(6, '2022_04_25_062103_create_project_members_pivot_table', 1),
(7, '2022_04_26_141738_create_invoices_table', 1),
(8, '2022_04_29_231056_create_costs_table', 1),
(9, '2022_04_30_112034_create_settings_table', 1);

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
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_project_firebase` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `budget` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `workload` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `concept` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `development` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `documentation` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `commissioning` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `phase` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('invoiced','paid','overdue','start','finished') COLLATE utf8mb4_unicode_ci NOT NULL,
  `leader_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `totalinvoice` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `id_project_firebase`, `name`, `progress`, `budget`, `workload`, `concept`, `development`, `documentation`, `commissioning`, `phase`, `status`, `leader_id`, `created_at`, `updated_at`, `totalinvoice`) VALUES
(50, 'lumina62f364fc639af4.84490370', 'Praesentium et', 0, 2720, 0, 0, 0, 0, 0, 'Provident iste eius cum quidem fugit iusto nihil.', 'invoiced', 12, '2022-08-10 00:57:49', '2022-08-17 18:31:17', 10458472);

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('member','leader','ba','da','ld') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'variables', '{\"span\":\"1\",\"safe\":\"2\",\"fee_theta\":\"50\"}', '2022-07-02 23:48:44', '2022-07-29 06:45:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `uid`, `email`, `phone`, `dob`, `role`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(12, 'Reinhold Powlowski', 'rziqTchWAvW868lpPaNoDYlN9oN2', 'pwiza@example.org', '+13202831448', NULL, 'user', '$2y$10$9YyZPgCn3REZuvAEarnululqDuyloVwAPnptRoWA84Ujnmta6jUvG', NULL, '2022-07-10 19:12:45', '2022-07-10 19:12:45'),
(19, 'Leonard Oberbrunner', 'V4CDa7CZOOeGHziWrkRMHyY8QqT2', 'wcruickshank@example.net', '+14694142090', NULL, 'user', '$2y$10$X0d3fVy0jKaElifsTMDCveU53gTiqfKjzCYLkStl0DvSgYBzfu3PG', NULL, '2022-07-23 07:37:59', '2022-07-23 07:37:59'),
(20, 'Kelley West', '6dG0BVx6QZaGjFS2BK1E8z7WkO82', 'calista.lesch@example.org', '+12039013924', NULL, 'user', '$2y$10$h3Oh98jf6PA9cUaog8HLduvmLAt8bnMbBzhKm89AJVpgDc0BKJvBm', NULL, '2022-07-27 21:27:43', '2022-07-27 21:27:43'),
(21, 'Abdi Ahsan', '8HG8IRwx6wUQc7KIJ4y0G5LVCW72', 'abdi.ahsan@gmail.com', NULL, NULL, 'admin', '$2y$10$q8WIpUNw4VEt36IVjjdtx.9SG4GS5rWSINiLCPOn9QU1dhrMOBQiK', 'BUCoH3vTVY1qTPSjJ43oKKmfNw5IMVWFCL5dXrYynjurXCa6kIDhMCQuSrE3', '2022-07-27 21:32:43', '2022-07-27 21:32:43'),
(24, 'Kitereative Luminaku', 'OOsT3bEQxhbYcVLC9qHEVeNs0w02', 'sip@example.net', '+17376870569', '2022-08-31', 'admin', '$2y$10$9.R0UDfqVtc6F9v4N6RdceBIO0K8dBFTdXnCmyjmdC.dawb1Ghmvq', 'tMuAIo9YnbDqKWAypCCl1PE4IplvMwciADj1bK3ZP6UEFDUB1gaKqEXvATbc', '2022-07-31 23:39:04', '2022-08-01 00:02:10'),
(25, 'kiterative', 'pOGT9MRrJgbjXdmHrLPhKgxifGw2', 'kiterative@example.com', '+13858349945', NULL, 'admin', '$2y$10$O5B4D923BL9kxYmnl3Jps.5gORGoFd/JxWObFdwi1PPZdJERnZV52', 'FhXYbsz0QqFDFLZZLmvHvtnKqk4JiU3T0eyR7rqgimDpo6DvMIVWJkz74n0p', '2022-07-31 23:49:18', '2022-07-31 23:49:18'),
(27, 'Miss Deanna Casper', 'mo0hrgL4l0UBxX7woY3umFF9fPi2', 'mona.gerlach@example.com', '+14195916792', NULL, 'user', '$2y$10$eoMhAyTTxPYfk0TZ5tzIHebPPzqalSyWqj4pI7WXraqqhXBRwVmdC', NULL, '2022-08-01 21:21:43', '2022-08-01 21:21:43'),
(28, 'Prof. Domingo Jast V', 'FcdNvLLQ0ActKW67KSgWnbDmx8E2', 'lmohr@example.net', '+14148233128', NULL, 'user', '$2y$10$fGPO2kytU0JaC6sqVzdZ1OQeDQJootYB3O73gZ6CiyOroN4BVXBby', NULL, '2022-08-01 21:27:24', '2022-08-01 21:27:24'),
(29, 'Berta Bins', 'YjiUNeD7MCRRGgqltvPrRSq8hP72', 'jacynthe.grimes@example.net', '+16416982998', NULL, 'user', '$2y$10$CEXOkxyhUqVkWOwgShrXPe9Gf12a430e0o3PBGbxkydt.qqJsUBeW', NULL, '2022-08-01 21:32:45', '2022-08-01 21:32:45'),
(30, 'Prof. Emely Russel V', 'ly1jB31IOGaAf8VUdqw4yonqn4O2', 'scottie.balistreri@example.net', '+17406967902', NULL, 'user', '$2y$10$gAPvJX9PD.Rprj/EJBXECuPsBu3kWnZkoTXWesxP9Kk3VTCDcQdOy', NULL, '2022-08-10 00:48:56', '2022-08-10 00:48:56'),
(31, 'Sabrina Spinka', '9LkZwJW8OaYaCti6yd4qvjL5pYp1', 'ywelch@example.com', '+15344881528', NULL, 'user', '$2y$10$5xbJRaDnydNCvMymhGiCauEst72kRmX08/X1S3wy3J8arpQ6hciBO', NULL, '2022-08-10 00:49:04', '2022-08-10 00:49:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `costs`
--
ALTER TABLE `costs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `costs_billing_month_unique` (`billing_month`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_project_id_foreign` (`project_id`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_leader_id_foreign` (`leader_id`);
ALTER TABLE `projects` ADD FULLTEXT KEY `projects_phase_fulltext` (`phase`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_members_project_id_user_id_role_unique` (`project_id`,`user_id`,`role`),
  ADD KEY `project_members_user_id_foreign` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_uid_unique` (`uid`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `costs`
--
ALTER TABLE `costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_leader_id_foreign` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
