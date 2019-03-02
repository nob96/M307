-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Feb 2019 um 14:08
-- Server-Version: 10.1.37-MariaDB
-- PHP-Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `m307_noah`
--
CREATE DATABASE IF NOT EXISTS `m307_noah` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `m307_noah`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `noah_inventar`
--

CREATE TABLE `noah_inventar` (
  `Id` int(11) NOT NULL,
  `inventar_Geraetename` varchar(40) NOT NULL,
  `inventar_Inventarnummer` varchar(40) NOT NULL,
  `inventar_Kategorie` enum('Computer','Audio','Monitor') NOT NULL DEFAULT 'Computer',
  `inventar_Kaufdatum` date DEFAULT NULL,
  `inventar_Bemerkung` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `noah_inventar`
--

INSERT INTO `noah_inventar` (`Id`, `inventar_Geraetename`, `inventar_Inventarnummer`, `inventar_Kategorie`, `inventar_Kaufdatum`, `inventar_Bemerkung`) VALUES
(1, 'Apple MacBook Air 13.3', 'KL156', 'Computer', '0000-00-00', 'Bemerkung');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `noah_inventar`
--
ALTER TABLE `noah_inventar`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `noah_inventar`
--
ALTER TABLE `noah_inventar`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
