-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 21 mai 2025 à 16:56
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `courriel` varchar(40) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `courriel`, `mot_de_passe`, `date_creation`) VALUES
(1, 'jose.ecoride@gmail.com', '$2b$12$o5w.YmgAlsVdZhCDBpvaM.Aa.QGIIrzWX6Ip1Jt0pJBHAwdHE3jH.', '2025-05-21 12:58:06');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `id_trajet` int(10) UNSIGNED NOT NULL,
  `courriel_passager` varchar(40) NOT NULL,
  `courriel_conducteur` varchar(40) NOT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `est_valide` tinyint(1) DEFAULT 0,
  `date_avis` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `id_trajet`, `courriel_passager`, `courriel_conducteur`, `note`, `commentaire`, `est_valide`, `date_avis`) VALUES
(1, 19, 'test6@gmail.com', 'test4@gmail.com', 5, 'super', 1, '2025-05-20 23:56:27');

-- --------------------------------------------------------

--
-- Structure de la table `employes`
--

CREATE TABLE `employes` (
  `id` int(11) NOT NULL,
  `courriel` varchar(40) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `employes`
--

INSERT INTO `employes` (`id`, `courriel`, `mot_de_passe`, `nom`, `date_creation`) VALUES
(2, 'test5@gmail.com', '$2y$10$WVtUUah/Bk4Kri3sqBM.aOHxZMn9xslq/LHN4ZixNGwU1FPEaJnBi', NULL, '2025-05-21 14:23:38'),
(3, 'test10@gmail.com', '$2y$10$otguWiMxMdlZh5QKtf5hUu0gkrcpEHMqnltJ7w8oAHWZ5Btig8hA.', NULL, '2025-05-21 15:30:06');

-- --------------------------------------------------------

--
-- Structure de la table `logs_credits`
--

CREATE TABLE `logs_credits` (
  `id` int(11) NOT NULL,
  `date_prise` date NOT NULL,
  `credits_preleves` int(11) NOT NULL DEFAULT 2,
  `id_trajet` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(40) NOT NULL,
  `genre` varchar(10) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `courriel` varchar(40) NOT NULL,
  `adresse` text NOT NULL,
  `role` enum('conducteur','passager','les deux') NOT NULL DEFAULT 'passager',
  `photo` varchar(255) DEFAULT NULL,
  `note` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `profils`
--

INSERT INTO `profils` (`id`, `nom`, `genre`, `telephone`, `courriel`, `adresse`, `role`, `photo`, `note`) VALUES
(8, 'test', 'F', '0152654525', 'test@gmail.com', 'test', 'conducteur', NULL, NULL),
(10, 'test', 'Autre', '0152654525', 'test3@gmail.com', 'ttt', 'passager', NULL, NULL),
(11, 'test', 'H', '025555555', 'test4@gmail.com', 'test', 'conducteur', NULL, NULL),
(12, 'test', 'M', '22222222', 'test6@gmail.com', 'lll', 'passager', NULL, NULL),
(13, 'test', 'M', '22222222', 'test9@gmail.com', 'ml', 'passager', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reset_tokens`
--

CREATE TABLE `reset_tokens` (
  `id` int(11) NOT NULL,
  `courriel` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `trajets_confirmes`
--

CREATE TABLE `trajets_confirmes` (
  `id_confirmation` int(10) UNSIGNED NOT NULL,
  `courriel_passager` varchar(40) NOT NULL,
  `courriel_conducteur` varchar(40) NOT NULL,
  `date_trajet` date NOT NULL,
  `depart` varchar(40) NOT NULL,
  `destination` varchar(40) NOT NULL,
  `vehicule` varchar(40) NOT NULL,
  `places_reservees` int(10) UNSIGNED NOT NULL,
  `heure_depart` time NOT NULL,
  `statut` enum('confirmé','en_cours','terminé','annulé') DEFAULT 'confirmé',
  `gain_applique` tinyint(1) DEFAULT 0,
  `id_trajet` int(10) UNSIGNED DEFAULT NULL,
  `etat_trajet` enum('en_attente','en_cours','termine') DEFAULT 'en_attente',
  `avis_depose` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `trajets_confirmes`
--

INSERT INTO `trajets_confirmes` (`id_confirmation`, `courriel_passager`, `courriel_conducteur`, `date_trajet`, `depart`, `destination`, `vehicule`, `places_reservees`, `heure_depart`, `statut`, `gain_applique`, `id_trajet`, `etat_trajet`, `avis_depose`) VALUES
(9, 'test3@gmail.com', 'test@gmail.com', '2025-03-22', 'paris', 'marseille', '1', 1, '21:21:00', 'confirmé', 0, 16, 'en_attente', 0),
(10, 'test6@gmail.com', 'test4@gmail.com', '2025-05-22', 'lyon', 'nice', '4', 1, '22:22:00', 'confirmé', 0, 19, 'en_attente', 0),
(11, 'test9@gmail.com', 'test@gmail.com', '2025-05-22', 'lyon', 'nice', '2', 1, '11:11:00', 'confirmé', 0, 21, 'en_attente', 0);

-- --------------------------------------------------------

--
-- Structure de la table `trajets_proposes`
--

CREATE TABLE `trajets_proposes` (
  `id_trajet` int(10) UNSIGNED NOT NULL,
  `courriel_conducteur` varchar(40) NOT NULL,
  `date_trajet` date NOT NULL,
  `depart` varchar(40) NOT NULL,
  `destination` varchar(40) NOT NULL,
  `prix` decimal(6,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `places_disponibles` int(10) UNSIGNED NOT NULL,
  `places_totales` int(11) NOT NULL DEFAULT 0,
  `heure_depart` time NOT NULL,
  `heure_arrivee` time DEFAULT NULL,
  `vehicule` int(11) DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  `statut` enum('en_attente','en_cours','terminé') DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `trajets_proposes`
--

INSERT INTO `trajets_proposes` (`id_trajet`, `courriel_conducteur`, `date_trajet`, `depart`, `destination`, `prix`, `description`, `created_at`, `updated_at`, `places_disponibles`, `places_totales`, `heure_depart`, `heure_arrivee`, `vehicule`, `duree`, `statut`) VALUES
(16, 'test@gmail.com', '2025-03-22', 'paris', 'marseille', '50.00', '', '2025-05-20 11:04:33', '2025-05-20 16:07:55', 4, 5, '21:21:00', '22:02:00', 1, NULL, 'en_attente'),
(17, 'test@gmail.com', '2025-02-22', 'lille', 'marseille', '50.00', NULL, '2025-05-20 17:12:51', '2025-05-20 17:12:51', 1, 1, '23:25:00', '00:22:00', 3, NULL, 'en_attente'),
(19, 'test4@gmail.com', '2025-05-22', 'lyon', 'nice', '50.00', NULL, '2025-05-20 21:34:13', '2025-05-20 21:55:49', 3, 4, '22:22:00', '23:23:00', 4, NULL, 'terminé'),
(20, 'test4@gmail.com', '2025-05-22', 'lyon', 'nice', '50.00', NULL, '2025-05-20 22:01:57', '2025-05-20 22:01:57', 2, 2, '22:22:00', '00:00:00', 4, NULL, 'en_attente'),
(21, 'test@gmail.com', '2025-05-22', 'lyon', 'nice', '50.00', NULL, '2025-05-21 13:47:36', '2025-05-21 14:14:06', 0, 1, '11:11:00', '22:22:00', 2, NULL, 'en_attente');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `date_transaction` date NOT NULL,
  `credits_gagnes` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `date_transaction`, `credits_gagnes`, `type`) VALUES
(6, '2025-05-20', 2, 'trajet'),
(7, '2025-05-20', 2, 'trajet'),
(8, '2025-05-20', 2, 'trajet'),
(9, '2025-05-21', 2, 'trajet'),
(10, '2025-05-21', 2, 'trajet');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `courriel` varchar(40) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 20,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`courriel`, `mot_de_passe`, `credits`, `actif`) VALUES
('test3@gmail.com', '$2y$10$RjDqRuIdY1LWshKdf6RRC.Xvc6x4eS9nGoUhPVJvw1aluGt.a2KJK', 18, 1),
('test4@gmail.com', '$2y$10$r7GQQi0JKjVmrUfrdyFzJentlHWs4L7qcDWXjWT6upw8FdbeuAvm.', 14, 1),
('test6@gmail.com', '$2y$10$NPpRB3LTOrUb0p3/KYyOQun2yRW4Yknnut.SouAiIyuJt3ZWwwltm', 19, 1),
('test9@gmail.com', '$2y$10$2sigaQgRtUY6FTtWI/UxLu9Om13sPnmNPm9db6JTfyF76VzlR7rRS', 19, 1),
('test@gmail.com', '$2y$10$i1Gukp5yXf2GfmujSTRbvOVN85AY4kthD6FOD2fGCnQ3J0c5sKcGO', 16, 1);

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL,
  `courriel_conducteur` varchar(40) DEFAULT NULL,
  `marque` varchar(40) DEFAULT NULL,
  `modele` varchar(40) DEFAULT NULL,
  `energie` enum('essence','diesel','electrique','hybride') DEFAULT NULL,
  `couleur` varchar(30) DEFAULT NULL,
  `plaque` varchar(20) DEFAULT NULL,
  `date_immatriculation` date DEFAULT NULL,
  `preferences` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `courriel_conducteur`, `marque`, `modele`, `energie`, `couleur`, `plaque`, `date_immatriculation`, `preferences`) VALUES
(1, 'test@gmail.com', 'Renault', 'Zoe', 'electrique', 'blanc', 'AB-123-CD', '2022-01-01', 'Non-fumeur'),
(2, 'test@gmail.com', 'Renault', 'Zoe', 'electrique', 'blanc', 'AB-123-CD', '2022-01-01', 'Non-fumeur'),
(3, 'test@gmail.com', 'mercedez', 'e43', 'essence', 'bleue', 'eduuu1425', '2005-05-22', ''),
(4, 'test4@gmail.com', 'mercedez', '4555', 'essence', 'rouge', 'uesd45jh', '2000-02-22', '');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courriel` (`courriel`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trajet` (`id_trajet`),
  ADD KEY `courriel_passager` (`courriel_passager`),
  ADD KEY `courriel_conducteur` (`courriel_conducteur`);

--
-- Index pour la table `employes`
--
ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courriel` (`courriel`);

--
-- Index pour la table `logs_credits`
--
ALTER TABLE `logs_credits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_trajet` (`id_trajet`);

--
-- Index pour la table `profils`
--
ALTER TABLE `profils`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courriel_unique` (`courriel`);

--
-- Index pour la table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`);

--
-- Index pour la table `trajets_confirmes`
--
ALTER TABLE `trajets_confirmes`
  ADD PRIMARY KEY (`id_confirmation`),
  ADD KEY `fk_confirmes_passager` (`courriel_passager`),
  ADD KEY `fk_confirmes_conducteur` (`courriel_conducteur`),
  ADD KEY `fk_confirmes_trajet` (`id_trajet`);

--
-- Index pour la table `trajets_proposes`
--
ALTER TABLE `trajets_proposes`
  ADD PRIMARY KEY (`id_trajet`),
  ADD KEY `fk_trajets_proposes_utilisateurs` (`courriel_conducteur`),
  ADD KEY `idx_date_trajet` (`date_trajet`),
  ADD KEY `idx_depart` (`depart`),
  ADD KEY `idx_destination` (`destination`),
  ADD KEY `fk_trajet_vehicule` (`vehicule`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`courriel`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courriel_conducteur` (`courriel_conducteur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `employes`
--
ALTER TABLE `employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `logs_credits`
--
ALTER TABLE `logs_credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `profils`
--
ALTER TABLE `profils`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `reset_tokens`
--
ALTER TABLE `reset_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trajets_confirmes`
--
ALTER TABLE `trajets_confirmes`
  MODIFY `id_confirmation` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `trajets_proposes`
--
ALTER TABLE `trajets_proposes`
  MODIFY `id_trajet` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_trajet`) REFERENCES `trajets_proposes` (`id_trajet`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`courriel_passager`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_3` FOREIGN KEY (`courriel_conducteur`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE;

--
-- Contraintes pour la table `logs_credits`
--
ALTER TABLE `logs_credits`
  ADD CONSTRAINT `logs_credits_ibfk_1` FOREIGN KEY (`id_trajet`) REFERENCES `trajets_confirmes` (`id_confirmation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profils`
--
ALTER TABLE `profils`
  ADD CONSTRAINT `fk_profils_utilisateurs` FOREIGN KEY (`courriel`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `trajets_confirmes`
--
ALTER TABLE `trajets_confirmes`
  ADD CONSTRAINT `fk_confirmes_conducteur` FOREIGN KEY (`courriel_conducteur`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_confirmes_passager` FOREIGN KEY (`courriel_passager`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_confirmes_trajet` FOREIGN KEY (`id_trajet`) REFERENCES `trajets_proposes` (`id_trajet`) ON DELETE CASCADE;

--
-- Contraintes pour la table `trajets_proposes`
--
ALTER TABLE `trajets_proposes`
  ADD CONSTRAINT `fk_trajet_vehicule` FOREIGN KEY (`vehicule`) REFERENCES `vehicules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trajets_proposes_utilisateurs` FOREIGN KEY (`courriel_conducteur`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`courriel_conducteur`) REFERENCES `utilisateurs` (`courriel`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
