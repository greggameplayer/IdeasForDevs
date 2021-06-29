-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 15 juin 2021 à 09:52
-- Version du serveur :  5.7.31
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ifd`
--

-- --------------------------------------------------------

--
-- Structure de la table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `id_mongo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscribe_date` datetime NOT NULL,
  `is_activated` tinyint(1) NOT NULL,
  `skills` json DEFAULT NULL,
  `role` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `apply`
--

DROP TABLE IF EXISTS `apply`;
CREATE TABLE IF NOT EXISTS `apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_account_id` int(11) NOT NULL,
  `id_project_id` int(11) NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BD2F8C1F3EE1DF6D` (`id_account_id`),
  KEY `IDX_BD2F8C1FB3E79F4B` (`id_project_id`),
  KEY `IDX_BD2F8C1F995975B0` (`role_project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ch_cookieconsent_log`
--

DROP TABLE IF EXISTS `ch_cookieconsent_log`;
CREATE TABLE IF NOT EXISTS `ch_cookieconsent_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_consent_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cookie_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentary`
--

DROP TABLE IF EXISTS `commentary`;
CREATE TABLE IF NOT EXISTS `commentary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_project_id` int(11) NOT NULL,
  `id_account_id` int(11) DEFAULT NULL,
  `comment` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_comment` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1CAC12CAB3E79F4B` (`id_project_id`),
  KEY `IDX_1CAC12CA3EE1DF6D` (`id_account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20210614192938', '2021-06-14 19:29:44', 414),
('DoctrineMigrations\\Version20210614193853', '2021-06-14 19:38:59', 122),
('DoctrineMigrations\\Version20210614194910', '2021-06-14 19:49:15', 108),
('DoctrineMigrations\\Version20210614203224', '2021-06-14 20:32:27', 111),
('DoctrineMigrations\\Version20210615092528', '2021-06-15 09:25:41', 264),
('DoctrineMigrations\\Version20210615094738', '2021-06-15 09:47:44', 165);

-- --------------------------------------------------------

--
-- Structure de la table `is_for`
--

DROP TABLE IF EXISTS `is_for`;
CREATE TABLE IF NOT EXISTS `is_for` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_account_id` int(11) NOT NULL,
  `id_project_id` int(11) NOT NULL,
  `evaluation` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A311AA303EE1DF6D` (`id_account_id`),
  KEY `IDX_A311AA30B3E79F4B` (`id_project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `job`
--

INSERT INTO `job` (`id`, `name`) VALUES
(1, 'Développeur Web'),
(2, 'Développeur Mobile'),
(3, 'UX Designer'),
(4, 'Graphiste'),
(5, 'Développeur d\'applications'),
(6, 'Développeur Fullstack'),
(7, 'Développeur back-end'),
(8, 'Développeur front-end');

-- --------------------------------------------------------

--
-- Structure de la table `jobs_account`
--

DROP TABLE IF EXISTS `jobs_account`;
CREATE TABLE IF NOT EXISTS `jobs_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_AFC63159B6B5FBA` (`account_id`),
  KEY `IDX_AFC6315BE04EA9` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `repo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `id_mongo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL,
  `skills_needed` json DEFAULT NULL,
  `job_needed` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `report_comment`
--

DROP TABLE IF EXISTS `report_comment`;
CREATE TABLE IF NOT EXISTS `report_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commentary_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_report` datetime NOT NULL,
  `is_treated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F4ED2F6C5DED49AA` (`commentary_id`),
  KEY `IDX_F4ED2F6C9B6B5FBA` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `report_project`
--

DROP TABLE IF EXISTS `report_project`;
CREATE TABLE IF NOT EXISTS `report_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `date_report` datetime NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_treated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4F2AADEE166D1F9C` (`project_id`),
  KEY `IDX_4F2AADEE9B6B5FBA` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `report_user`
--

DROP TABLE IF EXISTS `report_user`;
CREATE TABLE IF NOT EXISTS `report_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reporter_id` int(11) NOT NULL,
  `reported_id` int(11) NOT NULL,
  `date_report` datetime NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_treated` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FEBF3BB2E1CFE6F5` (`reporter_id`),
  KEY `IDX_FEBF3BB294BDEEB6` (`reported_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role_project`
--

DROP TABLE IF EXISTS `role_project`;
CREATE TABLE IF NOT EXISTS `role_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_project`
--

INSERT INTO `role_project` (`id`, `name`) VALUES
(1, 'En attente'),
(2, 'Refusé'),
(3, 'Membre'),
(4, 'Administrateur');

-- --------------------------------------------------------

--
-- Structure de la table `skill`
--

DROP TABLE IF EXISTS `skill`;
CREATE TABLE IF NOT EXISTS `skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `skill`
--

INSERT INTO `skill` (`id`, `name`) VALUES
(1, 'Développeur PHP'),
(2, 'Développeur JavaScript'),
(3, 'Développeur C#'),
(4, 'Développeur Java'),
(7, 'UX Design'),
(8, 'Marketing Digital');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `apply`
--
ALTER TABLE `apply`
  ADD CONSTRAINT `FK_BD2F8C1F3EE1DF6D` FOREIGN KEY (`id_account_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_BD2F8C1F995975B0` FOREIGN KEY (`role_project_id`) REFERENCES `role_project` (`id`),
  ADD CONSTRAINT `FK_BD2F8C1FB3E79F4B` FOREIGN KEY (`id_project_id`) REFERENCES `project` (`id`);

--
-- Contraintes pour la table `commentary`
--
ALTER TABLE `commentary`
  ADD CONSTRAINT `FK_1CAC12CA3EE1DF6D` FOREIGN KEY (`id_account_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_1CAC12CAB3E79F4B` FOREIGN KEY (`id_project_id`) REFERENCES `project` (`id`);

--
-- Contraintes pour la table `is_for`
--
ALTER TABLE `is_for`
  ADD CONSTRAINT `FK_A311AA303EE1DF6D` FOREIGN KEY (`id_account_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_A311AA30B3E79F4B` FOREIGN KEY (`id_project_id`) REFERENCES `project` (`id`);

--
-- Contraintes pour la table `jobs_account`
--
ALTER TABLE `jobs_account`
  ADD CONSTRAINT `FK_AFC63159B6B5FBA` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_AFC6315BE04EA9` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`);

--
-- Contraintes pour la table `report_comment`
--
ALTER TABLE `report_comment`
  ADD CONSTRAINT `FK_F4ED2F6C5DED49AA` FOREIGN KEY (`commentary_id`) REFERENCES `commentary` (`id`),
  ADD CONSTRAINT `FK_F4ED2F6C9B6B5FBA` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`);

--
-- Contraintes pour la table `report_project`
--
ALTER TABLE `report_project`
  ADD CONSTRAINT `FK_4F2AADEE166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  ADD CONSTRAINT `FK_4F2AADEE9B6B5FBA` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`);

--
-- Contraintes pour la table `report_user`
--
ALTER TABLE `report_user`
  ADD CONSTRAINT `FK_FEBF3BB294BDEEB6` FOREIGN KEY (`reported_id`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `FK_FEBF3BB2E1CFE6F5` FOREIGN KEY (`reporter_id`) REFERENCES `account` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
