CREATE TABLE IF NOT EXISTS `heures_sup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_employe` int(11) NOT NULL,
  `date_heure` date NOT NULL,
  `duree_minutes` int(11) NOT NULL,
  `motif` text DEFAULT NULL,
  `valide` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_employe` (`id_employe`),
  CONSTRAINT `fk_heures_sup_employe` FOREIGN KEY (`id_employe`) REFERENCES `employes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
