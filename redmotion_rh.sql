-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Dec 10, 2025 at 02:25 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `redmotion_rh`
--

-- --------------------------------------------------------

--cccccccccccccccccccc

--

-- --------------------------------------------------------

--
-- Table structure for table `candidats`
--

CREATE TABLE `candidats` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `poste_vise` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` enum('Nouveau','Entretien','Embauché','Rejeté') COLLATE utf8mb4_general_ci DEFAULT 'Nouveau',
  `date_candidature` datetime DEFAULT CURRENT_TIMESTAMP,
  `notes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidats`
--

INSERT INTO `candidats` (`id`, `nom`, `prenom`, `email`, `telephone`, `poste_vise`, `cv_path`, `statut`, `date_candidature`, `notes`) VALUES
(1, 'Benali', 'System', 'khaldi@gmail.com', '+212661234567', 'ddd', 'uploads/cv/69395b1cc450e.png', 'Entretien', '2025-11-29 17:40:54', ''),
(2, 'Benali', 'System', 'khaldi@gmail.com', '+212661234567', 'ddd', 'uploads/cv/692b221694979.png', 'Embauché', '2025-11-29 17:40:54', 'jjjjjjjj');

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--
-- Table structure for table `conges`
--

CREATE TABLE `conges` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `type_conge` enum('Annuel','Maladie','Sans Solde','Maternité','Autre') COLLATE utf8mb4_general_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `motif` text COLLATE utf8mb4_general_ci,
  `statut` enum('En attente','Approuvé','Refusé') COLLATE utf8mb4_general_ci DEFAULT 'En attente',
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `commentaire_admin` text COLLATE utf8mb4_general_ci,
  `justificatif` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conges`
--

INSERT INTO `conges` (`id`, `id_employe`, `type_conge`, `date_debut`, `date_fin`, `motif`, `statut`, `date_demande`, `commentaire_admin`, `justificatif`) VALUES
(2, 4, 'Maladie', '2025-11-06', '2025-11-29', 'test', 'Approuvé', '2025-11-30 15:06:28', '', NULL),
(3, 2, 'Annuel', '2025-11-29', '2025-12-27', 'hhhh', 'Approuvé', '2025-11-30 15:16:22', '', NULL),
(4, 2, 'Annuel', '2025-11-29', '2025-12-31', 'hhhhjhh', 'Approuvé', '2025-11-30 15:25:01', '', NULL),
(5, 3, 'Annuel', '2025-11-29', '2025-12-26', 'jjjjjjjjjjjjjjjjjj', 'Approuvé', '2025-11-30 15:27:11', 'Auto-approuvé par Admin', NULL),
(6, 5, 'Annuel', '2025-11-30', '2025-12-30', 'hhhhhh', 'Approuvé', '2025-11-30 15:31:15', '', NULL),
(7, 4, 'Annuel', '2025-12-01', '2025-12-24', 'ddddddd', 'Approuvé', '2025-12-01 10:22:44', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `conge_type`
--

CREATE TABLE `conge_type` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `duree` int DEFAULT '0' COMMENT 'Durée en jours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conge_type`
--

INSERT INTO `conge_type` (`id`, `nom`, `description`, `duree`) VALUES
(1, 'Congé 1', '6 Jours', 4),
(2, 'Congé 2', '', 10),
(3, 'Congé 3', '', 20);

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `motif` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_deduction` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departements`
--

CREATE TABLE `departements` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departements`
--

INSERT INTO `departements` (`id`, `nom`, `description`, `date_creation`) VALUES
(1, 'Administration', 'Gestion administrative et financière', '2025-11-29 16:14:39'),
(2, 'Cuisine', 'Production et préparation', '2025-11-29 16:14:39'),
(3, 'Vente', 'Service client et vente en boutique', '2025-11-29 16:14:39'),
(4, 'Logistique', 'Livraison et stocks', '2025-11-29 16:14:39'),
(5, 'DGS', 'DGS', '2025-12-10 10:32:37');

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `documents_employe`
--

CREATE TABLE `documents_employe` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `employes`
--

CREATE TABLE `employes` (
  `id` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `id_poste` int DEFAULT NULL,
  `statut` enum('Actif','Congé','Terminé') COLLATE utf8mb4_general_ci DEFAULT 'Actif',
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_general_ci,
  `cin` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cnss` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `situation_familiale` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nombre_enfants` int DEFAULT '0',
  `type_contrat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rib` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `solde_conge` float DEFAULT '18'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employes`
--

INSERT INTO `employes` (`id`, `nom`, `prenom`, `email`, `telephone`, `date_naissance`, `date_embauche`, `salaire`, `id_poste`, `statut`, `photo`, `adresse`, `cin`, `cnss`, `date_creation`, `situation_familiale`, `nombre_enfants`, `type_contrat`, `rib`, `solde_conge`) VALUES
(1, 'Lahlou', 'Ahmed', 'ahmed.lahlou@patisserie.com', '0611223344', '1980-05-15', '2020-01-01', 15000.00, 1, 'Actif', NULL, 'Casablanca', 'AB123456', '112233445', '2025-11-29 17:03:01', NULL, 0, NULL, NULL, 18),
(2, 'Benali', 'Fatima', 'fatima.benali@patisserie.com', '0622334455', '1992-08-20', '2021-03-10', 8000.00, 2, 'Actif', 'uploads/employes/692b1a90cc7d9.jpg', 'Casablanca', 'CD789012', '223344556', '2025-11-29 17:03:01', NULL, 0, NULL, '', 18),
(3, 'Idrissi', 'Karim', 'karim.idrissi@patisserie.com', '0633445566', '1995-11-30', '2022-06-15', 4000.00, 3, 'Congé', NULL, 'Casablanca', 'EF345678', '334455667', '2025-11-29 17:03:01', NULL, 0, NULL, NULL, 18),
(4, 'Chraibi', 'Sara', 'sara.chraibi@patisserie.com', '0644556677', '1998-02-14', '2023-01-20', 4000.00, 3, 'Congé', NULL, 'Casablanca', 'GH901234', '445566778', '2025-11-29 17:03:01', NULL, 0, NULL, NULL, 18),
(5, 'Tazi', 'Omar', 'omar.tazi@patisserie.com', '0655667788', '1990-07-07', '2021-11-05', 3500.00, 4, 'Actif', NULL, 'Casablanca', 'IJ567890', '556677889', '2025-11-29 17:03:01', NULL, 0, NULL, NULL, 18),
(6, 'reda', 'azizi', 'redaazizi@hotmail.com', '0666038126', '1999-07-02', '2010-01-01', 10000.00, 5, 'Actif', 'uploads/employes/69396742586fc.jpeg', 'ANASSI 32 ENTR2E 12', 'BB123987', '4587226', '2025-12-10 12:27:46', 'Célibataire', 2, 'CDD', '5641646546546546', 18);

-- --------------------------------------------------------

--
-- Table structure for table `employe_documents`
--

CREATE TABLE `employe_documents` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `file_data` longblob,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `fichier` varchar(255) DEFAULT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `type` enum('reunion','evenement','autre') COLLATE utf8mb4_general_ci DEFAULT 'reunion',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `titre`, `description`, `date_debut`, `date_fin`, `type`, `created_at`) VALUES
(1, '_iiiii', 'iiiiiiiiii', '2025-11-30 21:00:00', '2025-12-03 19:58:00', 'reunion', '2025-11-30 18:58:40'),
(2, 'Restaurant App with Admin & Restaurant Panel', '', '2025-12-02 01:00:00', '2025-12-03 01:00:00', 'autre', '2025-12-01 10:54:10'),
(3, 'khalid', '', '2025-12-26 01:00:00', '2025-12-27 01:00:00', 'evenement', '2025-12-01 11:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `couleur` varchar(20) DEFAULT '#E63946',
  `icon` varchar(50) DEFAULT 'bx-calendar-event'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `nom`, `couleur`, `icon`) VALUES
(1, 'reunion', '#3B82F6', 'bx-calendar-event'),
(2, 'evenement', '#EF4444', 'bx-calendar-event'),
(3, 'autre', '#6B7280', 'bx-calendar-event');

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `heures_sup`
--

CREATE TABLE `heures_sup` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `date_heure` date NOT NULL,
  `duree_minutes` int NOT NULL,
  `motif` text COLLATE utf8mb4_general_ci,
  `valide` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `heures_sup`
--

INSERT INTO `heures_sup` (`id`, `id_employe`, `date_heure`, `duree_minutes`, `motif`, `valide`, `created_at`) VALUES
(1, 4, '2025-11-30', 30, '', 1, '2025-11-30 21:55:21'),
(2, 5, '2025-11-30', 40, 'hhhh', 1, '2025-11-30 22:11:19'),
(3, 2, '2025-12-01', 30, 'jhjj', 1, '2025-12-01 09:19:07'),
(4, 5, '2025-12-01', 90, 'jhjj', 1, '2025-12-01 09:19:55'),
(5, 2, '2025-12-02', 34, '', 1, '2025-12-02 19:06:28'),
(6, 1, '2025-12-02', 30, '', 1, '2025-12-02 19:09:40'),
(7, 2, '2025-12-10', 30, 'ggg', 1, '2025-12-10 12:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `historique_actions`
--

CREATE TABLE `historique_actions` (
  `id` int NOT NULL,
  `type_action` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_reference` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `date_action` datetime NOT NULL,
  `commentaire` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `historique_actions`
--

INSERT INTO `historique_actions` (`id`, `type_action`, `id_reference`, `id_utilisateur`, `date_action`, `commentaire`) VALUES
(1, 'annulation_vente', 122, 1, '2025-05-20 11:50:49', 'Annulation de la vente'),
(2, 'annulation_vente', 121, 1, '2025-05-20 11:53:57', 'Annulation de la vente'),
(3, 'annulation_vente', 119, 1, '2025-05-20 11:57:00', 'Annulation de la vente');

-- --------------------------------------------------------

--
-- Table structure for table `jours_feries`
--

CREATE TABLE `jours_feries` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `date_ferie` date NOT NULL,
  `recurrent` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jours_feries`
--

INSERT INTO `jours_feries` (`id`, `nom`, `date_ferie`, `recurrent`) VALUES
(1, 'Aid 1', '2025-12-12', 1);

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `position` int NOT NULL,
  `role_required` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `label`, `link`, `icon`, `position`, `role_required`, `is_active`) VALUES
(1, 'Utilisateurs', 'utilisateur.php', 'bx bx-user', 2, 'admin', 1),
(2, 'Dashboard', 'dashboard.php', 'bx bx-grid-alt', 1, NULL, 1),
(5, 'Clients', 'client.php', 'bx bx-user', 3, NULL, 0),
(10, 'Employés', 'employes.php', 'bx bx-user-pin', 4, NULL, 1),
(11, 'Recrutement', 'recrutement.php', 'bx bx-briefcase', 6, NULL, 1),
(12, 'Organigramme', 'organigramme.php', 'bx bx-sitemap', 5, NULL, 1),
(13, 'Finance & Paie', 'paie.php', 'bx bx-money', 6, 'admin', 1),
(14, 'Congés', 'conges.php', 'bx bx-calendar-event', 10, NULL, 1),
(15, 'Planning', 'planning.php', 'bx bx-calendar', 99, 'admin', 1),
(16, 'Retards', 'retards.php', 'bx bx-timer', 5, 'admin', 1),
(17, 'Paramétrage', 'parametrage.php', 'bx bx-cog', 100, 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mission`
--

CREATE TABLE `mission` (
  `id` int NOT NULL,
  `voiture_id` int NOT NULL,
  `chauffeur_id` int DEFAULT NULL,
  `client_id` int NOT NULL,
  `type_service` enum('Transfert','Mise à disposition') COLLATE utf8mb4_general_ci NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu_depart` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lieu_arrivee` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `statut` enum('Planifiée','En cours','Terminée','Annulée') COLLATE utf8mb4_general_ci DEFAULT 'Planifiée',
  `observation` text COLLATE utf8mb4_general_ci,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `km_depart` int DEFAULT '0',
  `km_arrivee` int DEFAULT '0',
  `carburant_depart` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `carburant_arrivee` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mission`
--

INSERT INTO `mission` (`id`, `voiture_id`, `chauffeur_id`, `client_id`, `type_service`, `date_debut`, `date_fin`, `lieu_depart`, `lieu_arrivee`, `prix`, `statut`, `observation`, `deleted`, `created_at`, `updated_at`, `km_depart`, `km_arrivee`, `carburant_depart`, `carburant_arrivee`) VALUES
(1, 1, 1, 343, 'Transfert', '2025-11-08 02:07:00', '2025-11-29 02:07:00', 'Aéroport', 'Casablanca - Retour Rabat', 15000.00, 'En cours', 'ddddd', 0, '2025-11-22 01:07:30', '2025-11-22 16:27:16', 0, 0, NULL, NULL);

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `paiements`
--

CREATE TABLE `paiements` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `mois` int NOT NULL,
  `annee` int NOT NULL,
  `salaire_base` decimal(10,2) NOT NULL,
  `primes` decimal(10,2) DEFAULT '0.00',
  `deductions` decimal(10,2) DEFAULT '0.00',
  `net_a_payer` decimal(10,2) NOT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Payé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paiements_salaire`
--

CREATE TABLE `paiements_salaire` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `mois` int NOT NULL,
  `annee` int NOT NULL,
  `salaire_base` decimal(10,2) NOT NULL,
  `total_primes` decimal(10,2) DEFAULT '0.00',
  `total_deductions` decimal(10,2) DEFAULT '0.00',
  `salaire_net` decimal(10,2) NOT NULL,
  `date_paiement` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Payé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paiements_salaire`
--

INSERT INTO `paiements_salaire` (`id`, `id_employe`, `mois`, `annee`, `salaire_base`, `total_primes`, `total_deductions`, `salaire_net`, `date_paiement`, `statut`) VALUES
(1, 2, 11, 2025, 8000.00, 0.00, 500.00, 7500.00, '2025-11-29 18:22:10', 'Payé'),
(2, 4, 11, 2025, 4000.00, 0.00, 0.00, 4000.00, '2025-11-29 18:22:42', 'Payé'),
(3, 2, 11, 2024, 8000.00, 0.00, 0.00, 8000.00, '2025-11-29 19:20:42', 'Payé'),
(4, 3, 11, 2025, 4000.00, 120.00, 200.00, 3920.00, '2025-11-29 19:57:06', 'Payé'),
(5, 1, 9, 2025, 15000.00, 0.00, 0.00, 15000.00, '2025-11-29 20:13:10', 'Payé'),
(6, 1, 11, 2025, 15000.00, 0.00, 0.00, 15000.00, '2025-11-30 14:29:30', 'Payé'),
(7, 2, 12, 2025, 8000.00, 0.00, 0.00, 8000.00, '2025-11-30 16:55:52', 'Payé'),
(8, 2, 6, 2025, 8000.00, 0.00, 0.00, 8000.00, '2025-11-30 16:56:13', 'Payé'),
(9, 2, 7, 2025, 8000.00, 0.00, 0.00, 8000.00, '2025-11-30 16:56:22', 'Payé'),
(10, 5, 11, 2025, 3500.00, NULL, NULL, 3507.21, '2025-11-30 23:15:06', 'Payé'),
(11, 5, 12, 2025, 3500.00, NULL, NULL, 3407.48, '2025-12-01 10:20:51', 'Payé');

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--
-- Table structure for table `planning_presence`
--

CREATE TABLE `planning_presence` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `date_planning` date NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postes`
--

CREATE TABLE `postes` (
  `id` int NOT NULL,
  `titre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_departement` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `parent_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postes`
--

INSERT INTO `postes` (`id`, `titre`, `id_departement`, `description`, `date_creation`, `parent_id`) VALUES
(1, 'Gérant', 1, 'Responsable général', '2025-11-29 16:14:39', NULL),
(2, 'Chef Pâtissier', 2, 'Responsable de la production', '2025-11-29 16:14:39', NULL),
(3, 'Vendeur', 3, 'Accueil et vente', '2025-11-29 16:14:39', NULL),
(4, 'Livreur', 4, 'Livraison des commandes', '2025-11-29 16:14:39', NULL),
(5, 'Plongeur', 1, 'Nettoyage et entretien', '2025-11-29 17:03:01', NULL),
(6, 'DESIGNER', 4, '', '2025-12-10 10:41:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `poste_type`
--

CREATE TABLE `poste_type` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poste_type`
--

INSERT INTO `poste_type` (`id`, `nom`, `description`) VALUES
(1, 'Designer', 'MOTION DESIGN');

-- --------------------------------------------------------

--
-- Table structure for table `primes`
--

CREATE TABLE `primes` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `motif` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date_prime` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retards`
--

CREATE TABLE `retards` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `date_retard` date NOT NULL,
  `duree_minutes` int NOT NULL,
  `motif` text COLLATE utf8mb4_general_ci,
  `justifie` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retards`
--

INSERT INTO `retards` (`id`, `id_employe`, `date_retard`, `duree_minutes`, `motif`, `justifie`, `created_at`) VALUES
(1, 2, '2025-11-30', 30, 'dddd', 0, '2025-11-30 20:49:10'),
(2, 2, '2025-11-30', 30, '', 0, '2025-11-30 20:49:29'),
(3, 2, '2025-11-30', 50, '', 0, '2025-11-30 20:49:37'),
(4, 5, '2025-11-30', 90, 'rrrr', 0, '2025-11-30 21:38:57'),
(7, 2, '2025-12-01', 30, 'jjj', 0, '2025-12-01 09:18:29'),
(8, 5, '2025-12-01', 90, 'hhhhhhhh', 0, '2025-12-01 09:19:45'),
(9, 2, '2025-12-02', 23, '', 0, '2025-12-02 19:05:58'),
(10, 2, '2025-12-02', 32, '', 0, '2025-12-02 19:06:06'),
(11, 1, '2025-12-02', 23, '', 0, '2025-12-02 19:09:26'),
(12, 1, '2025-12-02', 23, '', 0, '2025-12-02 19:09:26'),
(13, 1, '2025-12-02', 23, '', 0, '2025-12-02 19:09:26'),
(14, 2, '2025-12-10', 30, 'jij', 0, '2025-12-10 12:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `date_creation`) VALUES
(1, 'admin', 'Administrateur avec accès complet', '2025-12-10 00:25:27'),
(2, 'utilisateur', 'Utilisateur standard avec accès limité', '2025-12-10 00:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `role_menu_access`
--

CREATE TABLE `role_menu_access` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `menu_item_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `societe_info`
--

CREATE TABLE `societe_info` (
  `id` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `adresse` text,
  `telephone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rc` varchar(100) DEFAULT NULL,
  `nif` varchar(100) DEFAULT NULL,
  `nis` varchar(100) DEFAULT NULL,
  `art` varchar(100) DEFAULT NULL,
  `cnss` varchar(100) DEFAULT NULL,
  `if_fiscal` varchar(100) DEFAULT NULL,
  `ice` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `cachet_path` varchar(255) DEFAULT NULL,
  `jours_conge_annuel` int DEFAULT '18',
  `weekend_days` varchar(50) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `societe_info`
--

INSERT INTO `societe_info` (`id`, `nom`, `adresse`, `telephone`, `email`, `rc`, `nif`, `nis`, `art`, `cnss`, `if_fiscal`, `ice`, `logo_path`, `cachet_path`, `jours_conge_annuel`, `weekend_days`) VALUES
(1, 'RED MOTION', 'Adresse par défaut', '0000000000', 'contact@redmotion.com', '', NULL, NULL, '', '', '', '', '../public/uploads/societe/logo_1765324632_114f61ce-4e8e-4815-9a0a-da84a303c7be.jpeg', '../public/uploads/societe/cachet_1765324632_Untitled design (1).png', 18, '6,0');

-- --------------------------------------------------------

--
-- Table structure for table `suivi_absence`
--

CREATE TABLE `suivi_absence` (
  `id` int NOT NULL,
  `id_employe` int NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `type_absence` enum('Maladie','Congé Sans Solde','Absence Injustifiée','Autre') COLLATE utf8mb4_general_ci NOT NULL,
  `motif` text COLLATE utf8mb4_general_ci,
  `justifie` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suivi_absence`
--

INSERT INTO `suivi_absence` (`id`, `id_employe`, `date_debut`, `date_fin`, `type_absence`, `motif`, `justifie`, `created_at`) VALUES
(2, 2, '2025-12-02', '2025-12-02', 'Maladie', '', 0, '2025-12-02 20:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','utilisateur') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'utilisateur',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `password`, `role`, `date_creation`) VALUES
(0, 'Admin', 'System', 'admin@patisserie.com', '0192023a7bbd73250516f069df18b500', 'admin', '2025-11-29 14:24:26'),
(1, 'admin', 'El khaldi', 'admin@khaldi.com', 'e2c2077ed0abe1512832b1997eb3aca7', 'admin', '0000-00-00 00:00:00'),
(4, 'Admin', 'System', 'aa@bb.com', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2025-05-21 10:20:36');

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

--

-- --------------------------------------------------------

--

-- --------------------------------------------------------

--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Indexes for table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categorie_article`
--
ALTER TABLE `categorie_article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chauffeur`
--
ALTER TABLE `chauffeur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnie` (`cnie`),
  ADD UNIQUE KEY `numero_permis` (`numero_permis`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conges`
--
ALTER TABLE `conges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `conge_type`
--
ALTER TABLE `conge_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `departements`
--
ALTER TABLE `departements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents_employe`
--
ALTER TABLE `documents_employe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `employes`
--
ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_poste` (`id_poste`);

--
-- Indexes for table `employe_documents`
--
ALTER TABLE `employe_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_types`
--
ALTER TABLE `event_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `heures_sup`
--
ALTER TABLE `heures_sup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `jours_feries`
--
ALTER TABLE `jours_feries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_voiture` (`id_voiture`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mission`
--
ALTER TABLE `mission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voiture_id` (`voiture_id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `paiements_salaire`
--
ALTER TABLE `paiements_salaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `paiement_location`
--
ALTER TABLE `paiement_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_location` (`id_location`);

--
-- Indexes for table `planning_presence`
--
ALTER TABLE `planning_presence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `postes`
--
ALTER TABLE `postes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_departement` (`id_departement`),
  ADD KEY `fk_postes_parent` (`parent_id`);

--
-- Indexes for table `poste_type`
--
ALTER TABLE `poste_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `primes`
--
ALTER TABLE `primes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `retards`
--
ALTER TABLE `retards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `role_menu_access`
--
ALTER TABLE `role_menu_access`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_menu` (`role_id`,`menu_item_id`);

--
-- Indexes for table `societe_info`
--
ALTER TABLE `societe_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suivi_absence`
--
ALTER TABLE `suivi_absence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_employe` (`id_employe`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vente`
--
ALTER TABLE `vente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_client` (`id_client`);

--
-- Indexes for table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categorie_article`
--
ALTER TABLE `categorie_article`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chauffeur`
--
ALTER TABLE `chauffeur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT for table `conges`
--
ALTER TABLE `conges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `conge_type`
--
ALTER TABLE `conge_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departements`
--
ALTER TABLE `departements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents_employe`
--
ALTER TABLE `documents_employe`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employes`
--
ALTER TABLE `employes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employe_documents`
--
ALTER TABLE `employe_documents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `heures_sup`
--
ALTER TABLE `heures_sup`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jours_feries`
--
ALTER TABLE `jours_feries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `mission`
--
ALTER TABLE `mission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paiements_salaire`
--
ALTER TABLE `paiements_salaire`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `paiement_location`
--
ALTER TABLE `paiement_location`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `planning_presence`
--
ALTER TABLE `planning_presence`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `postes`
--
ALTER TABLE `postes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `poste_type`
--
ALTER TABLE `poste_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `primes`
--
ALTER TABLE `primes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retards`
--
ALTER TABLE `retards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_menu_access`
--
ALTER TABLE `role_menu_access`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `societe_info`
--
ALTER TABLE `societe_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suivi_absence`
--
ALTER TABLE `suivi_absence`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vente`
--
ALTER TABLE `vente`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie_article` (`id`);

--
-- Constraints for table `conges`
--
ALTER TABLE `conges`
  ADD CONSTRAINT `conges_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deductions`
--
ALTER TABLE `deductions`
  ADD CONSTRAINT `deductions_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents_employe`
--
ALTER TABLE `documents_employe`
  ADD CONSTRAINT `documents_employe_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employes`
--
ALTER TABLE `employes`
  ADD CONSTRAINT `employes_ibfk_1` FOREIGN KEY (`id_poste`) REFERENCES `postes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employe_documents`
--
ALTER TABLE `employe_documents`
  ADD CONSTRAINT `employe_documents_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `heures_sup`
--
ALTER TABLE `heures_sup`
  ADD CONSTRAINT `fk_heures_sup_employe` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`),
  ADD CONSTRAINT `location_ibfk_2` FOREIGN KEY (`id_voiture`) REFERENCES `voiture` (`id`);

--
-- Constraints for table `mission`
--
ALTER TABLE `mission`
  ADD CONSTRAINT `mission_ibfk_1` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`id`),
  ADD CONSTRAINT `mission_ibfk_2` FOREIGN KEY (`chauffeur_id`) REFERENCES `chauffeur` (`id`),
  ADD CONSTRAINT `mission_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`);

--
-- Constraints for table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paiements_salaire`
--
ALTER TABLE `paiements_salaire`
  ADD CONSTRAINT `paiements_salaire_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `paiement_location`
--
ALTER TABLE `paiement_location`
  ADD CONSTRAINT `paiement_location_ibfk_1` FOREIGN KEY (`id_location`) REFERENCES `location` (`id`);

--
-- Constraints for table `planning_presence`
--
ALTER TABLE `planning_presence`
  ADD CONSTRAINT `planning_presence_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `postes`
--
ALTER TABLE `postes`
  ADD CONSTRAINT `fk_postes_parent` FOREIGN KEY (`parent_id`) REFERENCES `postes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `postes_ibfk_1` FOREIGN KEY (`id_departement`) REFERENCES `departements` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `primes`
--
ALTER TABLE `primes`
  ADD CONSTRAINT `primes_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `retards`
--
ALTER TABLE `retards`
  ADD CONSTRAINT `retards_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `suivi_absence`
--
ALTER TABLE `suivi_absence`
  ADD CONSTRAINT `suivi_absence_ibfk_1` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vente`
--
ALTER TABLE `vente`
  ADD CONSTRAINT `vente_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `vente_ibfk_2` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
