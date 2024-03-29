SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `prefix_groupe`;
CREATE TABLE `prefix_groupe` (
  `id_groupe` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_cre` int(11) unsigned DEFAULT 1,
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_groupe`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_groupe_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_groupe_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_groupe` (`id_groupe`, `nom_groupe`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Utilisateur',	1,	'2017-10-24 17:13:53',	NULL,	NULL),
(2,	'Gestionnaire',	1,	'2017-10-24 17:13:53',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_utilisateur`;
CREATE TABLE `prefix_utilisateur` (
  `id_utilisateur` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `id_groupe` tinyint(3) unsigned DEFAULT 1,
  `token` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valide` tinyint(3) unsigned DEFAULT 0,
  `user_cre` int(11) unsigned DEFAULT 1,
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `login` (`identifiant`),
  KEY `id_groupe` (`id_groupe`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `utilisateur_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_groupe_fk` FOREIGN KEY (`id_groupe`) REFERENCES `prefix_groupe` (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_utilisateur` (`id_utilisateur`, `identifiant`, `password`, `email`, `admin`, `id_groupe`, `token`, `valide`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'admin',	'$2y$10$2CrYdHmPrL1wqiWsGCNoguvVTra.ALW5/6vjH3ofodG7JW1QBEXrK',	'admin@mydomain.com',	1,	2,	'',	1,	1,	'2017-10-24 07:47:06',	1,	'2018-11-08 13:37:37');

DROP TABLE IF EXISTS `prefix_affectation`;
CREATE TABLE `prefix_affectation` (
  `id_affectation` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_cre` int(11) UNSIGNED NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) UNSIGNED DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_affectation`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_affectation_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_affectation_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_affectation` (`id_affectation`, `libelle`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Affectation principale',	1,	'2021-06-25 13:54:25',	NULL,	NULL),
(2,	'Affectation Secondaire',	1,	'2021-06-25 13:54:25',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_utilisateur_affectation`;
CREATE TABLE `prefix_utilisateur_affectation` (
  `id_utilisateur` int(11) unsigned NOT NULL,
  `id_affectation` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_affectation`),
  KEY `id_affectation` (`id_affectation`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_utilisateur_affectation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_utilisateur_affectation_ibfk_2` FOREIGN KEY (`id_affectation`) REFERENCES `prefix_affectation` (`id_affectation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prefix_analyse`;
CREATE TABLE `prefix_analyse` (
  `id_analyse` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requete` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_type_analyse` int(10) unsigned NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `indicator` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nombre',
  `percent` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `flag_total` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `colonne` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comptage` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ligne` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flag_accueil` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `flag_affect` tinyint(3) unsigned NOT NULL,
  `alias_affect` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `grid` tinyint(3) unsigned NOT NULL DEFAULT 6,
  `date_cre` datetime NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_maj` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_analyse`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  KEY `id_type_analyse` (`id_type_analyse`),
  CONSTRAINT `analyse_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_analyse_ibfk_1` FOREIGN KEY (`id_type_analyse`) REFERENCES `prefix_type_analyse` (`id_type_analyse`),
  CONSTRAINT `prefix_analyse_ibfk_3` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `prefix_analyse_item`;
CREATE TABLE `prefix_analyse_item` (
  `alias` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_analyse` int(11) unsigned NOT NULL,
  PRIMARY KEY (`alias`,`id_analyse`),
  KEY `id_analyse` (`id_analyse`),
  KEY `alias` (`alias`),
  CONSTRAINT `prefix_analyse_item_ibfk_1` FOREIGN KEY (`alias`) REFERENCES `prefix_item` (`alias`),
  CONSTRAINT `prefix_analyse_item_ibfk_2` FOREIGN KEY (`id_analyse`) REFERENCES `prefix_analyse` (`id_analyse`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `prefix_document`;
CREATE TABLE `prefix_document` (
  `id_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fichier` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_type_document` int(11) unsigned NOT NULL,
  `id_exemple` int(10) unsigned NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_document`),
  KEY `FK_document_id_type_document` (`id_type_document`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  KEY `id_exemple` (`id_exemple`),
  CONSTRAINT `prefix_document_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_document_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_document_ibfk_3` FOREIGN KEY (`id_exemple`) REFERENCES `prefix_exemple` (`id_exemple`),
  CONSTRAINT `prefix_document_ibfk_4` FOREIGN KEY (`id_type_document`) REFERENCES `prefix_type_document` (`id_type_document`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prefix_colorscheme`;
CREATE TABLE `prefix_colorscheme` (
  `id_colorscheme` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dark` tinyint(3) unsigned NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_colorscheme`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_colorscheme_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_colorscheme_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_colorscheme` (`id_colorscheme`, `libelle`, `dark`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'primary',	0,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(2,	'secondary',	0,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(3,	'success',	0,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(4,	'danger',	0,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(5,	'warning',	1,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(6,	'info',	1,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(7,	'light',	1,	1,	'2021-09-02 14:10:20',	NULL,	NULL),
(8,	'dark',	0,	1,	'2021-09-02 14:10:20',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_etat`;
CREATE TABLE `prefix_etat` (
  `id_etat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_colorscheme` int(10) unsigned DEFAULT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_etat`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  KEY `id_colorscheme` (`id_colorscheme`),
  CONSTRAINT `prefix_etat_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_etat_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_etat_ibfk_3` FOREIGN KEY (`id_colorscheme`) REFERENCES `prefix_colorscheme` (`id_colorscheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_etat` (`id_etat`, `libelle`, `id_colorscheme`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'En attente',	5,	1,	'2017-11-06 12:49:15',	1,	'2021-09-02 14:12:10'),
(2,	'En cours',	1,	1,	'2017-11-22 13:37:33',	1,	'2021-09-02 14:12:27'),
(3,	'Terminé',	3,	1,	'2017-11-22 13:37:41',	1,	'2021-09-02 14:12:33');

DROP TABLE IF EXISTS `prefix_exemple`;
CREATE TABLE `prefix_exemple` (
  `id_exemple` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_affectation` int(10) unsigned NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fichier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localisation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_etat` int(11) unsigned NOT NULL DEFAULT 1,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_exemple`),
  KEY `id_etat` (`id_etat`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  KEY `id_affectation` (`id_affectation`),
  CONSTRAINT `prefix_exemple_ibfk_1` FOREIGN KEY (`id_etat`) REFERENCES `prefix_etat` (`id_etat`),
  CONSTRAINT `prefix_exemple_ibfk_2` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_exemple_ibfk_3` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_exemple_ibfk_4` FOREIGN KEY (`id_affectation`) REFERENCES `prefix_affectation` (`id_affectation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prefix_groupe_item`;
CREATE TABLE `prefix_groupe_item` (
  `id_groupe` tinyint(3) unsigned NOT NULL,
  `alias` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `read` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `update` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `delete` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `all` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_groupe`,`alias`),
  KEY `alias` (`alias`),
  KEY `id_groupe` (`id_groupe`),
  CONSTRAINT `groupe_item_alias_fk` FOREIGN KEY (`alias`) REFERENCES `prefix_item` (`alias`),
  CONSTRAINT `prefix_groupe_item_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `prefix_groupe` (`id_groupe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_groupe_item` (`id_groupe`, `alias`, `create`, `read`, `update`, `delete`, `all`) VALUES
(1,	'document',	0,	1,	0,	0,	0),
(1,	'exemple',	1,	1,	1,	1,	0),
(1,	'home',	0,	1,	0,	0,	0),
(1,	'logout',	0,	1,	0,	0,	0),
(1,	'analyse',	1,	1,	1,	1,	0),
(2,	'analyse',	1,	1,	1,	1,	1),
(2,	'document',	1,	1,	1,	1,	1),
(2,	'exemple',	1,	1,	1,	1,	1),
(2,	'home',	0,	1,	0,	0,	0),
(2,	'logout',	0,	1,	0,	0,	0),
(2,	'type_document',	1,	1,	1,	1,	1);

DROP TABLE IF EXISTS `prefix_historique`;
CREATE TABLE `prefix_historique` (
  `id_historique` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_cre` datetime NOT NULL,
  `user_cre` int(10) unsigned NOT NULL,
  `item` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `action` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_historique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `prefix_item`;
CREATE TABLE `prefix_item` (
  `id_item` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `static` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `variant` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `menu` tinyint(3) unsigned DEFAULT 0,
  `menu_order` tinyint(3) unsigned DEFAULT 0,
  `icon` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT 'star',
  `user_cre` int(11) unsigned DEFAULT 1,
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`alias`),
  UNIQUE KEY `id` (`id_item`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `item_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `item_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_item` (`id_item`, `alias`, `nom`, `description`, `static`, `variant`, `active`, `admin`, `menu`, `menu_order`, `icon`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'analyse',	'Analyses',	'Les différentes extractions possibles',	0,	1,	1,	0,	1,	30,	'bar-chart',	1,	'2018-10-29 18:20:27',	1,	'2021-08-11 18:12:34'),
(2,	'document',	'Documents',	'Les documents liés aux différents éléments',	0,	1,	1,	0,	1,	20,	'file-earmark',	1,	'2017-11-06 11:11:49',	1,	'2021-08-11 18:12:42'),
(3,	'etat',	'Etats',	'Les états d\'inscription possibles',	0,	0,	1,	1,	5,	10,	'check',	1,	'2017-11-02 16:55:34',	1,	'2021-08-11 18:10:19'),
(4,	'exemple',	'Exemples',	'Un objet exemple',	0,	1,	1,	0,	1,	10,	'bookmark-star',	1,	'2018-11-09 18:20:35',	1,	'2021-08-11 18:09:12'),
(5,	'groupe',	'Groupes',	'Groupes d\'utilisateurs',	0,	1,	1,	1,	10,	30,	'people',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:11:05'),
(6,	'home',	'Accueil',	'Bienvenue dans le framework !',	1,	0,	1,	0,	1,	0,	'house',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:09:03'),
(7,	'item',	'Eléments',	'Les éléments de construction du site',	0,	0,	1,	1,	10,	10,	'list-task',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:10:41'),
(8,	'logout',	'Déconnexion',	'Déconnexion',	1,	0,	1,	0,	100,	1,	'power',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:08:26'),
(9,	'option',	'Configuration',	'Les options du site',	0,	0,	1,	1,	10,	20,	'gear',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:10:59'),
(10,	'register',	'Enregistrement',	'Formulaire de création de compte',	1,	0,	1,	1,	0,	0,	'box-arrow-in-right',	1,	'2017-10-25 14:02:39',	1,	'2021-08-09 16:44:00'),
(11,	'type_document',	'Types de document',	'Les différents type de document possibles',	0,	0,	1,	0,	5,	20,	'file-earmark-code',	1,	'2018-11-19 10:01:11',	1,	'2021-08-11 18:10:34'),
(12,	'utilisateur',	'Utilisateurs',	'Les utilisateurs de l\'application',	0,	1,	1,	1,	10,	40,	'person',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:11:12'),
(13,	'affectation',	'Affectations',	'Les affectations de l\'application',	0,	0,	1,	1,	10,	50,	'building',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 18:11:33'),
(14,	'type_analyse',	'Type d\'analyse',	'Les types de tableau d\'analyse possibles',	0,	0,	1,	1,	10,	60,	'file-bar-graph',	1,	'2021-08-11 18:21:27',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_option`;
CREATE TABLE `prefix_option` (
  `id_option` smallint(6) NOT NULL AUTO_INCREMENT,
  `alias` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_cre` int(11) unsigned DEFAULT 1,
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_option`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `option_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `option_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_option` (`id_option`, `alias`, `valeur`, `description`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'keywords',	'',	'Liste des mots clé de la balise meta',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:22'),
(2,	'sitedesc',	'',	'Description du site',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:04'),
(3,	'sitetitle',	'Framework',	'Titre du site',	1,	'2017-10-24 07:47:06',	1,	'2018-11-05 10:26:15'),
(4,	'homeText',	' ',	'Contenu de la page d\'accueil',	1,	'2017-10-24 07:47:06',	1,	'2018-10-29 18:10:20'),
(5,	'nbparpage',	'20',	'Nombre d\'éléments à afficher par page (0 = tous)',	1,	'2017-10-24 07:47:06',	1,	'2021-08-11 11:53:23'),
(6,	'sitemail',	'contact@mydomain.com',	'Email du site',	1,	'2017-10-25 14:53:21',	1,	'2018-03-10 15:46:47'),
(7,	'leafletOptions',	'{ \"center_lat\": 45.438705905866,\"center_lng\": 4.371288760839, \"zoom\": 14,\"min_zoom\": 2,\"max_zoom\": 18,\"lat\": 0,\"lng\": 0}',	'Configuration Leaflet par défaut JSON',	1,	'2018-11-20 10:49:13',	1,	'2018-11-20 11:01:46'),
(8,	'allowregister',	'0',	'Autoriser ou non l\'enregistrement de nouveaux utilisateurs',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:22'),
(9,	'siteicon',	'gear',	'Code bootstrap icon pour icone site',	1,	'2020-10-22 11:28:00',	1,	'2021-08-09 16:18:06'),
(10,	'colorschema',	'primary',	'Schéma de couleur Boostrap général',	1,	'2020-10-22 11:28:00',	1,	'2021-08-09 16:18:06'),
(11,	'hcolorschema',	'info',	'Schéma de couleur Boostrap header',	1,	'2020-10-22 11:28:00',	1,	'2021-08-09 16:18:06');

DROP TABLE IF EXISTS `prefix_type_analyse`;
CREATE TABLE `prefix_type_analyse` (
  `id_type_analyse` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_type_analyse`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_type_analyse_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_type_analyse_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_type_analyse` (`id_type_analyse`, `libelle`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Tableau droit',	1,	'2018-01-03 11:47:07',	NULL,	NULL),
(2,	'Tableau croisé',	1,	'2018-01-03 11:47:22',	NULL,	NULL),
(3,	'Valeur unique',	1,	'2018-01-03 11:47:22',	NULL,	NULL),
(4,	'Graphique',	1,	'2018-11-02 15:24:27',	NULL,	NULL),
(5,	'Carte',	1,	'2018-11-19 16:34:10',	NULL,	NULL),
(6,	'Calendrier',	1,	'2018-11-19 16:34:10',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_type_document`;
CREATE TABLE `prefix_type_document` (
  `id_type_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_type_document`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_type_document_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_type_document_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `prefix_type_document` (`id_type_document`, `libelle`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Type exemple',	1,	'2021-06-25 13:51:03',	NULL,	NULL);

SET foreign_key_checks = 1;
