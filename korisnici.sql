-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 01:13 AM
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
-- Database: `korisnici`
--

-- --------------------------------------------------------

--
-- Table structure for table `dodijeljeni_zadaci`
--

CREATE TABLE `dodijeljeni_zadaci` (
  `id` int(11) NOT NULL,
  `korisnik_id` int(11) NOT NULL,
  `opis` text NOT NULL,
  `vrijeme_pocetka` datetime DEFAULT NULL,
  `rok` datetime DEFAULT NULL,
  `datum` date NOT NULL DEFAULT curdate(),
  `vrijeme_zapocinjanja` datetime DEFAULT NULL,
  `vrijeme_zavrsetka` datetime DEFAULT NULL,
  `zavrsetak` datetime DEFAULT NULL,
  `stvarni_pocetak` datetime DEFAULT NULL,
  `napomena` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dodijeljeni_zadaci`
--

INSERT INTO `dodijeljeni_zadaci` (`id`, `korisnik_id`, `opis`, `vrijeme_pocetka`, `rok`, `datum`, `vrijeme_zapocinjanja`, `vrijeme_zavrsetka`, `zavrsetak`, `stvarni_pocetak`, `napomena`) VALUES
(1, 5, 'nova proba', '2025-05-25 23:00:00', '2025-05-25 23:50:00', '2025-05-13', NULL, NULL, '2025-05-13 21:23:21', '2025-05-14 10:00:10', 'samo isprobano'),
(2, 5, 'izradi web stranicu', '2025-05-25 21:25:00', '2025-05-25 21:30:00', '2025-05-13', NULL, NULL, '2025-05-13 21:25:50', '2025-05-14 10:00:19', NULL),
(3, 5, 'nastavi raditi na web stranici', '2025-05-25 09:15:00', '2025-05-25 15:00:00', '2025-05-14', NULL, NULL, '2025-05-14 16:14:32', '2025-05-14 10:00:14', 'urađena login forma\n[2025-05-14 16:13:28] sredina prva sgtranica\n[2025-05-14 16:13:43] napravljen web shop\n[2025-05-14 16:39:08] stranica završena'),
(4, 5, 'izradi aplikaciju za spremanje troškova', '2025-05-25 09:00:00', '2025-05-25 15:00:00', '2025-05-15', NULL, NULL, NULL, '2025-05-23 18:16:02', '[2025-05-23 18:16:17] napravio 20%\n[2025-05-24 02:07:47] 40%'),
(5, 6, 'napravi weeb stranicu', '2025-05-25 18:15:00', '2025-05-25 18:15:00', '2025-05-23', NULL, NULL, NULL, '2025-05-29 12:45:35', '[2025-05-29 12:46:31] riješena početna, index stranica'),
(6, 5, 'novi zadatak', '2025-05-25 22:05:00', '2025-05-25 22:05:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(7, 5, 'novi', '2025-05-25 22:05:00', '2025-05-25 22:05:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(8, 5, 'test vremena', '2025-05-25 22:24:00', '2025-05-25 22:24:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(9, 5, 'novi zadatak', '2025-05-25 22:29:00', '2025-05-25 22:29:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(10, 5, 'zadnji zadatak', '2025-05-25 22:36:00', '2025-05-25 22:36:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(11, 5, 'još jedan', '2025-05-25 22:39:00', '2025-05-25 22:39:00', '2025-05-25', NULL, NULL, NULL, NULL, NULL),
(12, 5, 'jel sada dobro prikazuje?', '2025-05-27 01:43:00', '2025-05-28 22:43:00', '2025-05-25', NULL, NULL, NULL, NULL, '[2025-05-25 22:43:45] da'),
(13, 5, 'dodajem', '2025-05-30 23:39:00', '2025-06-01 23:39:00', '2025-05-26', NULL, NULL, NULL, NULL, NULL),
(14, 5, 'novi zadatak', '2025-06-01 23:39:00', '2025-06-02 23:39:00', '2025-05-26', NULL, NULL, NULL, NULL, '[2025-05-28 00:50:17] nisam još krenio'),
(15, 5, 'još jedan zadatak', '2025-05-27 23:51:00', '2025-06-01 23:51:00', '2025-05-26', NULL, NULL, NULL, NULL, NULL),
(16, 5, 'web aplikacija', '2025-05-29 23:51:00', '2025-06-05 23:51:00', '2025-05-26', NULL, NULL, NULL, NULL, NULL),
(17, 5, 'jel bolje izgleda?', '2025-05-30 05:05:00', '2025-06-03 00:05:00', '2025-05-27', NULL, NULL, NULL, NULL, NULL),
(18, 7, 'Za tvrtku Firma d.o.o. prilagodi aplikaciju za praćenje zaduženja voznog parka. Ubaci njihov logo, boju tvrtke itd. sve pojedinosti se nalaze na serveru', '2025-06-02 08:30:00', '2025-06-06 15:00:00', '2025-05-29', NULL, NULL, NULL, '2025-06-10 08:35:47', '[2025-06-10 08:35:53] edrgde\n[2025-06-10 08:35:59] rješenje\n[2025-06-10 12:18:34] kljdfsjaklfdg'),
(19, 8, 'Dokuči mi pivo ', '2025-05-29 21:48:00', '2025-05-30 03:01:00', '2025-05-29', NULL, NULL, NULL, '2025-05-29 21:50:26', '[2025-05-29 21:50:35] nema hladnoga\n[2025-05-29 21:51:13] ipak sam našla, stiže'),
(20, 9, 'Zavari 5 cijevi ', '2025-06-04 15:00:00', '2025-06-04 18:00:00', '2025-06-04', NULL, NULL, '2025-06-05 01:51:59', '2025-06-04 14:53:37', '[2025-06-04 14:53:53] fali mi cijevi'),
(21, 5, 'kjdfklaj', '2025-06-11 15:00:00', '2025-06-19 15:00:00', '2025-06-10', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `korisnicko_ime` varchar(50) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `uloga` enum('admin','zaposlenik') DEFAULT 'zaposlenik',
  `ime` varchar(50) NOT NULL,
  `prezime` varchar(50) NOT NULL,
  `aktivan` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `korisnicko_ime`, `lozinka`, `uloga`, `ime`, `prezime`, `aktivan`) VALUES
(1, 'admin', '$2y$10$aOGoZrO3ODbqieotyrxAJ.laXaV6H5xivo862Dqi/NhNVldhA8Wj.', 'admin', '', '', 1),
(5, 'admin1', '$2y$10$cLIW9t9BPLFHzZuRYpS3keZljZnSenGSFEiRRFMGfFhRVyvspeDf2', 'zaposlenik', 'Petra', 'Crni', 1),
(6, 'zaposlenik', '$2y$10$Ew/SOL7veLX3DBe5wLyyBeWM4wJ2zoJlRA2OLTYMJ8Ve5kfvMtaXK', 'zaposlenik', 'Pero', 'Perić', 1),
(7, 'tradovanović', '$2y$10$zI0FhNnec3h6.e7Uq1kFv.zMuOUIfdm2RtbS/8y2pHLDM4DTwa.Zu', 'zaposlenik', 'Tihomir', 'Radovanović', 1),
(8, 'vradovanović', '$2y$10$7eW8/RbbBxnTdNp1tMtsz.IcK4d3GBabmfsDYbBZTcCbFlgFMKnmS', 'zaposlenik', 'Valerija', 'Radovanović', 1),
(9, 'mradovanović', '$2y$10$Uvv7tS5yRhK5pkaMSAGmhOHgYfrtwkW6bqWMc3.tspwxH9JHsKPKq', 'zaposlenik', 'Mato', 'Radovanović', 0),
(10, 'fdfvyf', '$2y$10$/DxsQyGGr9lM.xLEGcwUDe8geRpcSfn3QmCyRsEk.N.79UF3RvgxS', 'zaposlenik', 'fsdgs', 'dfvyf', 1),
(11, 'ssajlfjdsa', '$2y$10$tJ/QMg6s0gfgJpFn4iBzH.v5PLo5o9e419tUfMvabI0y3O0IoqsDu', 'zaposlenik', 'sdfdslj', 'sajlfjdsa', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pauze`
--

CREATE TABLE `pauze` (
  `id` int(11) NOT NULL,
  `korisnik_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `pocetak` datetime NOT NULL,
  `kraj` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pauze`
--

INSERT INTO `pauze` (`id`, `korisnik_id`, `datum`, `pocetak`, `kraj`) VALUES
(6, 5, '2025-05-15', '2025-05-15 13:05:17', '2025-05-15 13:06:18'),
(7, 5, '2025-05-16', '2025-05-16 13:50:20', '2025-05-16 14:31:02'),
(8, 5, '2025-05-16', '2025-05-16 14:49:21', '2025-05-16 14:53:40'),
(9, 5, '2025-05-16', '2025-05-16 15:01:12', '2025-05-16 15:02:40'),
(10, 5, '2025-05-16', '2025-05-16 15:09:05', '2025-05-16 15:09:06'),
(11, 5, '2025-05-19', '2025-05-19 13:39:54', '2025-05-19 13:48:06'),
(12, 5, '2025-05-19', '2025-05-19 13:59:18', '2025-05-19 13:59:34'),
(13, 5, '2025-05-19', '2025-05-19 13:59:38', '2025-05-19 14:03:53'),
(14, 5, '2025-05-20', '2025-05-20 21:25:03', '2025-05-20 21:29:31'),
(15, 5, '2025-05-20', '2025-05-20 21:36:57', '2025-05-20 21:41:10'),
(16, 5, '2025-05-20', '2025-05-20 22:12:50', '2025-05-20 22:16:53'),
(17, 5, '2025-05-20', '2025-05-20 22:35:17', '2025-05-20 22:38:09'),
(18, 5, '2025-05-20', '2025-05-20 22:40:49', '2025-05-20 22:40:57'),
(19, 5, '2025-05-20', '2025-05-20 22:51:38', '2025-05-20 22:51:46'),
(20, 6, '2025-05-20', '2025-05-20 23:05:44', '2025-05-20 23:22:49'),
(21, 6, '2025-05-20', '2025-05-20 23:23:38', '2025-05-20 23:25:09'),
(23, 5, '2025-05-22', '2025-05-22 22:34:30', '2025-05-22 22:37:11'),
(24, 5, '2025-05-22', '2025-05-22 22:41:36', '2025-05-22 22:42:01'),
(25, 5, '2025-05-22', '2025-05-22 22:50:16', '2025-05-22 22:53:49'),
(29, 5, '2025-05-24', '2025-05-24 13:12:20', '2025-05-24 13:34:46'),
(30, 5, '2025-05-24', '2025-05-24 13:35:11', '2025-05-24 13:35:55'),
(31, 5, '2025-05-24', '2025-05-24 13:38:18', '2025-05-24 13:38:48'),
(32, 5, '2025-05-24', '2025-05-25 21:46:34', '2025-05-25 21:46:41'),
(33, 5, '2025-05-26', '2025-05-26 22:34:37', '2025-05-26 23:49:43'),
(34, 5, '2025-05-27', '2025-05-27 23:03:08', '2025-05-27 23:52:44'),
(35, 5, '2025-05-27', '2025-05-28 01:18:04', '2025-05-28 01:22:01'),
(36, 5, '2025-05-27', '2025-05-28 01:22:09', '2025-05-28 01:22:12'),
(37, 5, '2025-05-27', '2025-05-28 02:01:54', '2025-05-28 02:01:56'),
(38, 7, '2025-05-28', '2025-05-28 02:03:17', '2025-05-28 13:18:06'),
(39, 5, '2025-05-27', '2025-05-28 13:58:45', '2025-05-28 14:05:33'),
(40, 5, '2025-05-27', '2025-05-28 14:06:22', '2025-05-29 12:16:54'),
(41, 5, '2025-05-27', '2025-05-29 12:21:23', '2025-05-29 12:21:28'),
(42, 8, '2025-05-29', '2025-05-29 21:49:54', '2025-06-03 21:49:51'),
(43, 9, '2025-06-04', '2025-06-04 14:51:36', '2025-06-04 14:52:00'),
(44, 7, '2025-05-29', '2025-06-05 13:09:21', '2025-06-10 08:34:00'),
(45, 7, '2025-06-10', '2025-06-10 08:34:10', '2025-06-10 08:35:11'),
(46, 7, '2025-06-10', '2025-06-10 12:18:03', '2025-06-10 12:18:20'),
(47, 5, '2025-06-09', '2025-06-12 23:28:13', '2025-06-13 00:45:59');

-- --------------------------------------------------------

--
-- Table structure for table `radni_sati`
--

CREATE TABLE `radni_sati` (
  `id` int(11) NOT NULL,
  `korisnik_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `pocetak` datetime NOT NULL,
  `kraj` datetime DEFAULT NULL,
  `status` enum('aktivno','pauza','zavrseno') DEFAULT 'aktivno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `radni_sati`
--

INSERT INTO `radni_sati` (`id`, `korisnik_id`, `datum`, `pocetak`, `kraj`, `status`) VALUES
(1, 5, '2025-05-11', '2025-05-27 22:35:16', '2025-05-24 22:38:51', 'zavrseno'),
(2, 5, '2025-05-12', '2025-05-27 08:31:30', '2025-05-24 15:14:56', 'zavrseno'),
(3, 5, '2025-05-13', '2025-05-27 13:09:13', '2025-05-24 21:38:53', 'zavrseno'),
(4, 5, '2025-05-14', '2025-05-27 08:51:58', '2025-05-24 21:31:22', 'zavrseno'),
(5, 5, '2025-05-15', '2025-05-27 08:50:53', '2025-05-24 22:42:42', 'zavrseno'),
(8, 5, '2025-05-20', '2025-05-27 21:22:37', '2025-05-24 23:25:44', 'zavrseno'),
(9, 6, '2025-05-20', '2025-05-27 22:58:20', '2025-05-24 23:25:23', 'zavrseno'),
(28, 5, '2025-05-24', '2025-05-27 13:12:06', '2025-05-25 21:46:49', 'zavrseno'),
(29, 5, '2025-05-26', '2025-05-27 22:04:37', '2025-05-27 22:33:46', 'zavrseno'),
(30, 5, '2025-05-27', '2025-05-27 22:35:55', '2025-05-29 12:21:34', 'zavrseno'),
(31, 7, '2025-05-28', '2025-05-28 01:53:15', '2025-05-29 12:39:32', 'zavrseno'),
(32, 5, '2025-05-29', '2025-05-29 12:23:59', '2025-06-03 21:50:08', 'zavrseno'),
(33, 7, '2025-05-29', '2025-05-29 12:39:35', '2025-06-10 08:34:00', 'zavrseno'),
(34, 6, '2025-05-29', '2025-05-29 12:45:11', NULL, 'aktivno'),
(35, 8, '2025-05-29', '2025-05-29 21:47:23', '2025-06-03 21:49:53', 'zavrseno'),
(36, 5, '2025-06-03', '2025-06-03 21:50:10', '2025-06-09 13:44:15', 'zavrseno'),
(37, 9, '2025-06-04', '2025-06-04 14:51:09', NULL, 'aktivno'),
(38, 5, '2025-06-09', '2025-06-09 13:44:18', NULL, 'aktivno'),
(39, 7, '2025-06-10', '2025-06-10 08:34:01', NULL, 'aktivno');

-- --------------------------------------------------------

--
-- Table structure for table `zadaci`
--

CREATE TABLE `zadaci` (
  `id` int(11) NOT NULL,
  `naziv_zadatka` varchar(255) NOT NULL,
  `vrijeme_pocetka` datetime NOT NULL,
  `vrijeme_zavrsetka` datetime DEFAULT NULL,
  `status` enum('na čekanju','u tijeku','završeno') DEFAULT 'na čekanju',
  `datum_dodjele` date DEFAULT curdate(),
  `rok_izvrsenja` time DEFAULT NULL,
  `korisnik_id` int(11) NOT NULL,
  `opis` text DEFAULT NULL,
  `rok` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zadaci`
--

INSERT INTO `zadaci` (`id`, `naziv_zadatka`, `vrijeme_pocetka`, `vrijeme_zavrsetka`, `status`, `datum_dodjele`, `rok_izvrsenja`, `korisnik_id`, `opis`, `rok`) VALUES
(3, '', '2025-05-12 14:30:00', NULL, 'na čekanju', '2025-05-12', '15:00:00', 5, 'napravi web stranicu', NULL),
(4, '', '2025-05-13 08:00:00', '2025-05-12 16:11:35', 'na čekanju', '2025-05-12', '15:00:00', 5, 'nastavi raditi na web stranici', NULL),
(5, '', '2025-05-14 08:00:00', NULL, 'na čekanju', '2025-05-12', '15:00:00', 5, 'radi', NULL),
(6, '', '2025-05-13 14:00:00', NULL, 'na čekanju', '2025-05-13', '16:00:00', 5, 'nastavi izradu web stranice', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dodijeljeni_zadaci`
--
ALTER TABLE `dodijeljeni_zadaci`
  ADD PRIMARY KEY (`id`),
  ADD KEY `korisnik_id` (`korisnik_id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `pauze`
--
ALTER TABLE `pauze`
  ADD PRIMARY KEY (`id`),
  ADD KEY `korisnik_id` (`korisnik_id`);

--
-- Indexes for table `radni_sati`
--
ALTER TABLE `radni_sati`
  ADD PRIMARY KEY (`id`),
  ADD KEY `korisnik_id` (`korisnik_id`);

--
-- Indexes for table `zadaci`
--
ALTER TABLE `zadaci`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dodijeljeni_zadaci`
--
ALTER TABLE `dodijeljeni_zadaci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pauze`
--
ALTER TABLE `pauze`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `radni_sati`
--
ALTER TABLE `radni_sati`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `zadaci`
--
ALTER TABLE `zadaci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dodijeljeni_zadaci`
--
ALTER TABLE `dodijeljeni_zadaci`
  ADD CONSTRAINT `dodijeljeni_zadaci_ibfk_1` FOREIGN KEY (`korisnik_id`) REFERENCES `korisnici` (`id`);

--
-- Constraints for table `pauze`
--
ALTER TABLE `pauze`
  ADD CONSTRAINT `pauze_ibfk_2` FOREIGN KEY (`korisnik_id`) REFERENCES `korisnici` (`id`);

--
-- Constraints for table `radni_sati`
--
ALTER TABLE `radni_sati`
  ADD CONSTRAINT `radni_sati_ibfk_1` FOREIGN KEY (`korisnik_id`) REFERENCES `korisnici` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
