-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `prefix_analyse`;
CREATE TABLE `prefix_analyse` (
  `id_analyse` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `requete` text COLLATE utf8_unicode_ci NOT NULL,
  `id_type_analyse` int(10) unsigned NOT NULL,
  `options` text COLLATE utf8_unicode_ci NOT NULL,
  `indicator` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Nombre',
  `percent` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `colonne` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ligne` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flag_accueil` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `ordre` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `grid` tinyint(3) unsigned NOT NULL DEFAULT '6',
  `date_cre` datetime NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_maj` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_analyse`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  KEY `id_type_analyse` (`id_type_analyse`),
  CONSTRAINT `analyse_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_analyse_ibfk_1` FOREIGN KEY (`id_type_analyse`) REFERENCES `prefix_type_analyse` (`id_type_analyse`),
  CONSTRAINT `prefix_analyse_ibfk_3` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `prefix_document`;
CREATE TABLE `prefix_document` (
  `id_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fichier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_document` int(11) unsigned NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_document`),
  KEY `FK_document_id_type_document` (`id_type_document`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `FK_document_id_type_document` FOREIGN KEY (`id_type_document`) REFERENCES `type_document` (`id_type_document`),
  CONSTRAINT `prefix_document_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_document_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `prefix_etat`;
CREATE TABLE `prefix_etat` (
  `id_etat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_etat`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_etat_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_etat_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_etat` (`id_etat`, `libelle`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'En attente',	1,	'2017-11-06 12:49:15',	NULL,	NULL),
(2,	'En cours',	1,	'2017-11-22 13:37:33',	NULL,	NULL),
(3,	'Terminé',	1,	'2017-11-22 13:37:41',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_groupe`;
CREATE TABLE `prefix_groupe` (
  `id_groupe` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_groupe`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_groupe_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_groupe_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_groupe` (`id_groupe`, `nom_groupe`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Utilisateur',	1,	'2017-10-24 17:13:53',	NULL,	NULL),
(2,	'Gestionnaire',	1,	'2017-10-24 17:13:53',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_groupe_item`;
CREATE TABLE `prefix_groupe_item` (
  `id_groupe` tinyint(3) unsigned NOT NULL,
  `alias` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `create` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `update` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delete` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`alias`),
  KEY `alias` (`alias`),
  KEY `id_groupe` (`id_groupe`),
  CONSTRAINT `prefix_groupe_item_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `prefix_groupe` (`id_groupe`) ON DELETE CASCADE,
  CONSTRAINT `groupe_item_alias_fk` FOREIGN KEY (`alias`) REFERENCES `prefix_item` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_groupe_item` (`id_groupe`, `alias`, `create`, `read`, `update`, `delete`) VALUES
(2,	'analyse',	1,	1,	1,	1),
(2,	'document',	1,	1,	1,	1),
(2,	'home',	0,	1,	0,	0),
(2,	'logout',	0,	1,	0,	0),
(1,	'analyse',	0,	1,	0,	0),
(1,	'document',	0,	1,	0,	0),
(1,	'home',	0,	1,	0,	0),
(1,	'logout',	0,	1,	0,	0);

DROP TABLE IF EXISTS `prefix_item`;
CREATE TABLE `prefix_item` (
  `id_item` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `static` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `variant` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `menu` tinyint(3) unsigned DEFAULT '0',
  `menu_order` tinyint(3) unsigned DEFAULT '0',
  `icon` varchar(35) COLLATE utf8_unicode_ci DEFAULT 'star',
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`alias`),
  UNIQUE KEY `id` (`id_item`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `item_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `item_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_item` (`id_item`, `alias`, `nom`, `description`, `static`, `variant`, `active`, `admin`, `menu`, `menu_order`, `icon`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(50,	'analyse',	'Analyses',	'Les différentes extractions possibles',	0,	1,	1,	0,	1,	200,	'chart-bar',	1,	'2018-10-29 18:20:27',	1,	'2018-10-31 17:24:48'),
(40,	'document',	'Documents',	'Les documents liés aux différents éléments',	0,	1,	1,	0,	0,	0,	'file-alt',	1,	'2017-11-06 11:11:49',	1,	'2017-11-13 15:18:25'),
(32,	'etat',	'Etats',	'Les états d\'inscription possibles',	0,	0,	1,	1,	5,	3,	'check',	1,	'2017-11-02 16:55:34',	1,	'2017-11-02 17:22:06'),
(26,	'groupe',	'Groupes',	'Groupes d\'utilisateurs',	0,	1,	1,	1,	9,	5,	'users',	1,	'2017-10-24 07:47:06',	1,	'2018-11-05 09:37:48'),
(4,	'home',	'Accueil',	'Les comptes en bref',	1,	0,	1,	0,	1,	10,	'home',	1,	'2017-10-24 07:47:06',	1,	'2018-10-31 17:38:33'),
(5,	'item',	'Eléments',	'Les éléments de construction du site',	0,	0,	1,	1,	9,	3,	'list-alt',	1,	'2017-10-24 07:47:06',	1,	'2017-10-24 13:56:49'),
(6,	'logout',	'Déconnexion',	'Déconnexion',	1,	0,	1,	0,	10,	2,	'sign-out-alt',	1,	'2017-10-24 07:47:06',	1,	'2018-10-29 14:58:51'),
(8,	'option',	'Configuration',	'Les options du site',	0,	0,	1,	1,	9,	4,	'cog',	1,	'2017-10-24 07:47:06',	1,	'2017-10-24 13:56:42'),
(27,	'register',	'Enregistrement',	'Formulaire de création de compte',	1,	0,	1,	1,	0,	0,	'check',	1,	'2017-10-25 14:02:39',	1,	'2018-10-29 14:59:08'),
(12,	'utilisateur',	'Utilisateurs',	'Les utilisateurs de l\'application',	0,	1,	1,	1,	9,	6,	'user',	1,	'2017-10-24 07:47:06',	NULL,	NULL),
(51,	'exemple',	'Exemples',	'Un objet exemple',	0,	0,	1,	0,	1,	20,	'question',	1,	'2018-11-09 18:20:35',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_option`;
CREATE TABLE `prefix_option` (
  `id_option` smallint(6) NOT NULL AUTO_INCREMENT,
  `alias` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `valeur` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_option`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `option_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `option_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_option` (`id_option`, `alias`, `valeur`, `description`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'keywords',	'',	'Liste des mots clé de la balise meta',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:22'),
(3,	'sitedesc',	'',	'Description du site',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:04'),
(5,	'sitetitle',	'Framework',	'Titre du site',	1,	'2017-10-24 07:47:06',	1,	'2018-11-05 10:26:15'),
(6,	'hometext',	' ',	'Contenu de la page d\'accueil',	1,	'2017-10-24 07:47:06',	1,	'2018-10-29 18:10:20'),
(8,	'nbparpage',	'10',	'Nombre d\'éléments à afficher par page (0 = tous)',	1,	'2017-10-24 07:47:06',	1,	'2017-11-03 10:26:59'),
(10,	'sitemail',	'contact@mydomain.com',	'Email du site',	1,	'2017-10-25 14:53:21',	1,	'2018-03-10 15:46:47');

DROP TABLE IF EXISTS `prefix_type_analyse`;
CREATE TABLE `prefix_type_analyse` (
  `id_type_analyse` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_type_analyse`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_type_analyse_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_type_analyse_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_type_analyse` (`id_type_analyse`, `libelle`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Tableau droit',	1,	'2018-01-03 11:47:07',	NULL,	NULL),
(2,	'Tableau croisé',	1,	'2018-01-03 11:47:22',	NULL,	NULL),
(3,	'Valeur unique',	1,	'2018-01-03 11:47:22',	NULL,	NULL),
(4,	'Graphique',	1,	'2018-11-02 15:24:27',	NULL,	NULL);

DROP TABLE IF EXISTS `prefix_type_document`;
CREATE TABLE `prefix_type_document` (
  `id_type_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_type_document`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_type_document_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_type_document_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `prefix_utilisateur`;
CREATE TABLE `prefix_utilisateur` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_groupe` tinyint(3) unsigned DEFAULT '1',
  `token` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valide` tinyint(3) unsigned DEFAULT '0',
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `login` (`identifiant`),
  KEY `id_groupe` (`id_groupe`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `utilisateur_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_groupe_fk` FOREIGN KEY (`id_groupe`) REFERENCES `prefix_groupe` (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prefix_utilisateur` (`id_utilisateur`, `identifiant`, `password`, `email`, `admin`, `id_groupe`, `token`, `valide`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'admin',	'$2y$10$2CrYdHmPrL1wqiWsGCNoguvVTra.ALW5/6vjH3ofodG7JW1QBEXrK',	'admin@mydomain.com',	1,	2,	'',	1,	1,	'2017-10-24 07:47:06',	1,	'2018-11-08 13:37:37');


DROP TABLE IF EXISTS `prefix_exemple`;
CREATE TABLE `prefix_exemple` (
  `id_exemple` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL DEFAULT '0',
  `image` varchar(60) DEFAULT NULL,
  `fichier` varchar(60) DEFAULT NULL,
  `id_etat` int(11) unsigned NOT NULL DEFAULT '1',
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_exemple`),
  KEY `id_etat` (`id_etat`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `prefix_exemple_ibfk_1` FOREIGN KEY (`id_etat`) REFERENCES `prefix_etat` (`id_etat`),
  CONSTRAINT `prefix_exemple_ibfk_2` FOREIGN KEY (`user_cre`) REFERENCES `prefix_utilisateur` (`id_utilisateur`),
  CONSTRAINT `prefix_exemple_ibfk_3` FOREIGN KEY (`user_maj`) REFERENCES `prefix_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 2018-11-08 12:37:51
