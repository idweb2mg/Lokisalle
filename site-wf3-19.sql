-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 08 Février 2017 à 10:28
-- Version du serveur :  10.1.13-MariaDB
-- Version de PHP :  5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `site`
--

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(3) NOT NULL,
  `id_membre` int(3) DEFAULT NULL,
  `montant` int(3) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `etat` enum('en cours de traitement','envoyé','livré') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `id_membre`, `montant`, `date_enregistrement`, `etat`) VALUES
(1, 4, 301, '2015-07-10 14:44:46', 'en cours de traitement'),
(2, 5, 77, '2017-02-07 11:19:59', 'en cours de traitement');

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_details_commande` int(3) NOT NULL,
  `id_commande` int(3) DEFAULT NULL,
  `id_produit` int(3) DEFAULT NULL,
  `quantite` int(3) NOT NULL,
  `prix` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `details_commande`
--

INSERT INTO `details_commande` (`id_details_commande`, `id_commande`, `id_produit`, `quantite`, `prix`) VALUES
(1, 1, 2, 1, 15),
(2, 1, 6, 1, 49),
(3, 1, 8, 3, 79),
(4, 2, 4, 1, 52),
(5, 2, 5, 1, 25);

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id_membre` int(3) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `mdp` varchar(128) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `civilite` enum('m','f') NOT NULL,
  `ville` varchar(20) NOT NULL,
  `code_postal` int(5) UNSIGNED ZEROFILL NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  `photo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `membre`
--

INSERT INTO `membre` (`id_membre`, `pseudo`, `mdp`, `nom`, `prenom`, `email`, `civilite`, `ville`, `code_postal`, `adresse`, `statut`, `photo`) VALUES
(1, 'juju', '23206deb7eba65b3fbc80a2ffbc53c28', 'Cottete', 'Julien', 'julien.cottet@gmail.com', 'm', 'Paris', 75015, '300 rue de vaugirard', 0, 'default.jpg'),
(2, 'lamarie', 'e24755cbd680d6baa5c51dca46dee1a9', 'thoyer', 'marie', 'marie.thoyer@yahoo.fr', 'f', 'Lyon', 69003, '10 rue paul bert', 0, 'default.jpg'),
(3, 'fab', '3ec049f667072f4bba034438abe6f0c4', 'grand', 'fabrice', 'fabrice.grand@gmail.com', 'm', 'Marseille', 13009, '70 rue de la r&eacute;publique', 0, 'default.jpg'),
(4, 'membre', '5a99c8cac333affeed05a24fe0d6f61c', 'membre', 'membre', 'membre@exemple.com', 'f', 'Toulouse', 31000, '55 rue bayard', 0, 'default.jpg'),
(5, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'admin', 'admin@exemple.com', 'm', 'Paris', 75015, '33 rue mademoiselle', 1, 'default.jpg'),
(6, 'test', '098f6bcd4621d373cade4e832627b4f6', 'Test', 'test', 'test@testtest.com', 'm', 'test', 02315, 'test test test', 0, 'default.jpg'),
(7, 'Yakine', '098f6bcd4621d373cade4e832627b4f6', 'HAMIDA', 'Yakine', 'admin@exemple.fr', 'm', 'Paris', 75019, '152 boulevard MACDONALD', 0, 'default.jpg'),
(8, 'Yakine22', '21232f297a57a5a743894a0e4a801fc3', 'HAMIDA', 'Yakine', 'yakine.hamida@evogue.fr', 'm', 'Paris', 75019, '152 boulevard MACDONALD', 0, 'default.jpg'),
(9, 'Yakine29', '098f6bcd4621d373cade4e832627b4f6', 'dqdqsd', 'qsdqsd', 'qsdqsd', 'm', 'dqsdqs', 123654, 'dqsdqsddqdsqsd', 1, 'default.jpg'),
(10, 'dfsfs', '35a322a37e6fb34b2aaea6f4ed30aa7f', 'dsfdsf', 'dsfdsf', 'dfsdfds', 'm', 'dfsdf', 00000, 'fsfdsfs', 0, 'default.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `id_note` int(5) NOT NULL,
  `id_membre` int(3) NOT NULL,
  `id_produit` int(3) NOT NULL,
  `note` enum('1','2','3','4','5') NOT NULL,
  `date_enregistrement` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `note`
--

INSERT INTO `note` (`id_note`, `id_membre`, `id_produit`, `note`, `date_enregistrement`) VALUES
(1, 5, 4, '4', '2017-02-07 14:42:18'),
(2, 4, 5, '3', '2017-02-07 14:43:45'),
(3, 4, 4, '5', '2017-02-07 14:45:20'),
(4, 1, 4, '1', '2017-02-07 15:50:58');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(3) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `categorie` varchar(20) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `couleur` varchar(20) NOT NULL,
  `taille` varchar(5) NOT NULL,
  `public` enum('m','f','mixte') NOT NULL,
  `photo` varchar(250) NOT NULL,
  `prix` int(3) NOT NULL,
  `stock` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `reference`, `categorie`, `titre`, `description`, `couleur`, `taille`, `public`, `photo`, `prix`, `stock`) VALUES
(4, '55-b-38', 'tshirt', 'Tshirt jaune', 'le jaune reviens à la mode, non? :-)', 'jaune', 'S', 'm', '55-b-38_BAI01003,bailey,chapeau-paille-bailey.jpg', 52, -1),
(5, '31-p-33', 'tshirt', 'Tshirt noir original', 'voici un tshirt noir très original :p', 'noir', 'XL', 'm', '31-p-33_noir.jpg', 25, 0),
(6, '56-a-65', 'chemise', 'Chemise Blanche', 'Les chemises c''est bien mieux que les tshirts', 'blanc', 'L', 'm', '56-a-65_chemiseblanchem.jpg', 49, 71),
(7, '63-s-63', 'chemise', 'Chemise Noir', 'Comme vous pouvez le voir c''est une chemise noir...', 'noir', 'M', 'm', '63-s-63_chemisenoirm.jpg', 59, 118),
(8, '77-p-79', 'pull', 'Pull gris', 'Pull gris pour l''hiver', 'gris', 'XL', 'f', '77-p-79_pullgrism2.jpg', 85, -2);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_details_commande`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id_membre`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id_note`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `reference` (`reference`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_details_commande` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id_membre` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `id_note` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
