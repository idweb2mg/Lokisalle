-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 24 Février 2017 à 12:39
-- Version du serveur :  10.1.19-MariaDB
-- Version de PHP :  5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `lokisalle`
--

-- --------------------------------------------------------

--
-- Structure de la table `salle`
--

CREATE TABLE `salle` (
  `id_salle` int(3) NOT NULL,
  `categorie` enum('r','b','f') NOT NULL,
  `reference` varchar(10) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `url_photo` varchar(200) NOT NULL,
  `pays` varchar(20) NOT NULL,
  `ville` varchar(20) NOT NULL,
  `code_postal` int(5) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `capacite` int(3) NOT NULL,
  `superficie` int(3) NOT NULL,
  `prix` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `salle`
--

INSERT INTO `salle` (`id_salle`, `categorie`, `reference`, `titre`, `description`, `url_photo`, `pays`, `ville`, `code_postal`, `adresse`, `capacite`, `superficie`, `prix`) VALUES
(1, 'r', 'r-moz', 'Mozart', 'salle reception1', '1reception.jpg', 'France', 'Paris', 75001, '1 allée des Bruyères', 200, 120, 1400),
(2, 'f', 'f-beeth', 'Beethoven', 'salle de formation1', '1formation.jpg', 'France', 'Marseille', 13002, '2 allée de la formation1', 15, 70, 1200),
(6, '', 'fdsn', 'fdsnnfs', 'fsdnfnf', 'default.jpg', 'fdsnfsn', 'fnfns', 0, '', 0, 0, 0),
(7, '', 'fdsn', 'fdsnnfs', 'fsdnfnf', 'default.jpg', 'fdsnfsn', 'fnfns', 0, '', 0, 0, 0),
(8, 'f', 'b', 'bf', 'b', 'default.jpg', 'b', '', 0, '', 0, 0, 0),
(11, '', 'kkkkkkkkkk', 'Llllllllllllllllll', 'uuuuuuuuuuuuuuuuuuuuuuuuu', '_3bureau.jpg', 'espagne', 'uuuuuuuuuuuuuuuuuuu', 142544, 'fgggggggggggggggg', 10, 1111, 2222),
(12, '', 'dddddddddd', 'dddddddddddddddddd', 'fgffffffffffffffffffffffffffffffffffffffffffffffff', '_default.jpg', 'france', 'Paris', 45555, 'z', 123, 1444, 12345);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `salle`
--
ALTER TABLE `salle`
  ADD PRIMARY KEY (`id_salle`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `salle`
--
ALTER TABLE `salle`
  MODIFY `id_salle` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
