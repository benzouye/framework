-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `col_document`;
CREATE TABLE `col_document` (
  `id_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fichier` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_maj` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  `id_type_document` int(11) unsigned NOT NULL,
  `id_inscription` int(11) unsigned NOT NULL,
  `email_sent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `organisme_sent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_document`),
  KEY `FK_document_id_type_document` (`id_type_document`),
  KEY `FK_document_id_inscription` (`id_inscription`),
  CONSTRAINT `FK_document_id_inscription` FOREIGN KEY (`id_inscription`) REFERENCES `col_inscription` (`id_inscription`),
  CONSTRAINT `FK_document_id_type_document` FOREIGN KEY (`id_type_document`) REFERENCES `col_type_document` (`id_type_document`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `col_etat`;
CREATE TABLE `col_etat` (
  `id_etat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_maj` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_etat`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_etat` (`id_etat`, `libelle`, `date_cre`, `user_cre`, `date_maj`, `user_maj`) VALUES
(1,	'En attente',	'2017-11-06 12:49:15',	1,	NULL,	NULL),
(2,	'En cours',	'2017-11-22 13:37:33',	1,	NULL,	NULL),
(3,	'Terminé',	'2017-11-22 13:37:41',	1,	NULL,	NULL);

DROP TABLE IF EXISTS `col_groupe`;
CREATE TABLE `col_groupe` (
  `id_groupe` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_groupe`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `groupe_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `col_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_groupe` (`id_groupe`, `nom_groupe`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(2,	'Gestionnaire',	1,	'2017-10-24 17:13:53',	NULL,	NULL);

DROP TABLE IF EXISTS `col_groupe_item`;
CREATE TABLE `col_groupe_item` (
  `id_groupe` tinyint(3) unsigned NOT NULL,
  `alias` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `create` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `update` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `delete` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_groupe`,`alias`),
  KEY `alias` (`alias`),
  KEY `id_groupe` (`id_groupe`),
  CONSTRAINT `groupe_item_alias_fk` FOREIGN KEY (`alias`) REFERENCES `col_item` (`alias`),
  CONSTRAINT `groupe_item_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `col_groupe` (`id_groupe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_groupe_item` (`id_groupe`, `alias`, `create`, `read`, `update`, `delete`) VALUES
(2,	'document',	1,	1,	1,	1),
(2,	'home',	0,	1,	0,	0),
(2,	'logout',	0,	1,	0,	0);

DROP TABLE IF EXISTS `col_item`;
CREATE TABLE `col_item` (
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
  `glyphicon` varchar(35) COLLATE utf8_unicode_ci DEFAULT 'star',
  `user_cre` int(11) DEFAULT '1',
  `date_cre` datetime DEFAULT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`alias`),
  UNIQUE KEY `id` (`id_item`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `item_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `item_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `col_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_item` (`id_item`, `alias`, `nom`, `description`, `static`, `variant`, `active`, `admin`, `menu`, `menu_order`, `glyphicon`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(40,	'document',	'Documents',	'Les documents liés aux différents éléments',	0,	1,	1,	0,	0,	0,	'envelope',	1,	'2017-11-06 11:11:49',	1,	'2017-11-13 15:18:25'),
(32,	'etat',	'Etats',	'Les états d\'inscription possibles',	0,	0,	1,	1,	5,	3,	'ok',	1,	'2017-11-02 16:55:34',	1,	'2017-11-02 17:22:06'),
(26,	'groupe',	'Groupes',	'Groupes d\'utilisateurs',	0,	1,	1,	1,	9,	5,	'th',	1,	'2017-10-24 07:47:06',	NULL,	NULL),
(4,	'home',	'Accueil',	'Bienvenue sur la page d'accueil',	1,	0,	1,	0,	1,	10,	'home',	1,	'2017-10-24 07:47:06',	1,	'2017-11-13 15:37:49'),
(5,	'item',	'Eléments',	'Les éléments de construction du site',	0,	0,	1,	1,	9,	3,	'list-alt',	1,	'2017-10-24 07:47:06',	1,	'2017-10-24 13:56:49'),
(6,	'logout',	'Déconnexion',	'Déconnexion',	1,	0,	1,	0,	10,	2,	'log-out',	1,	'2017-10-24 07:47:06',	1,	'2017-10-27 16:09:36'),
(8,	'option',	'Configuration',	'Les options du site',	0,	0,	1,	1,	9,	4,	'cog',	1,	'2017-10-24 07:47:06',	1,	'2017-10-24 13:56:42'),
(27,	'register',	'Enregistrement',	'Formulaire de création de compte',	1,	0,	1,	1,	10,	0,	'check',	1,	'2017-10-25 14:02:39',	1,	'2017-10-27 11:12:53'),
(48,	'type_analyse',	'Types de tableau',	'Les types de tableaux possibles pour les analyses',	0,	0,	1,	1,	9,	3,	'list',	1,	'2018-01-03 11:45:55',	NULL,	NULL),
(39,	'type_document',	'Types de document',	'Les types de documents possibles',	0,	0,	1,	1,	5,	4,	'list-alt',	1,	'2017-11-06 11:04:14',	1,	'2017-11-06 11:04:39'),
(12,	'utilisateur',	'Utilisateurs',	'Les utilisateurs de l\'application',	0,	1,	1,	1,	9,	6,	'user',	1,	'2017-10-24 07:47:06',	NULL,	NULL);

DROP TABLE IF EXISTS `col_option`;
CREATE TABLE `col_option` (
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
  CONSTRAINT `option_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `option_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `col_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_option` (`id_option`, `alias`, `valeur`, `description`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'keywords',	'',	'Liste des mots clé de la balise meta ',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:22'),
(3,	'sitedesc',	'',	'Description du site',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 12:00:04'),
(4,	'siteicon',	'grain',	'L\'alias de la glyphicon à utiliser dans le bandeau titre du site',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 11:59:25'),
(5,	'sitetitle',	'',	'Titre du site',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 11:59:15'),
(6,	'hometext',	'<p><strong>Bienvenue</strong></p>',	'Contenu de la page d\'accueil',	1,	'2017-10-24 07:47:06',	1,	'2017-10-25 16:16:10'),
(8,	'nbparpage',	'10',	'Nombre d\'éléments à afficher par page (0 = tous)',	1,	'2017-10-24 07:47:06',	1,	'2017-11-03 10:26:59'),
(10,	'sitemail',	'contact@mydomain.com',	'Email du site',	1,	'2017-10-25 14:53:21',	1,	'2018-03-10 15:46:47');

DROP TABLE IF EXISTS `col_type_analyse`;
CREATE TABLE `col_type_analyse` (
  `id_type_analyse` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `classe` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `user_cre` int(11) NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_maj` int(11) DEFAULT NULL,
  `date_maj` datetime DEFAULT NULL,
  PRIMARY KEY (`id_type_analyse`),
  KEY `user_cre` (`user_cre`),
  KEY `user_maj` (`user_maj`),
  CONSTRAINT `col_type_analyse_ibfk_1` FOREIGN KEY (`user_cre`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `col_type_analyse_ibfk_2` FOREIGN KEY (`user_maj`) REFERENCES `col_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_type_analyse` (`id_type_analyse`, `libelle`, `classe`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'Tableau droit',	'SimpleTable',	1,	'2018-01-03 11:47:07',	NULL,	NULL),
(2,	'Tableau croisé',	'PivotTable',	1,	'2018-01-03 11:47:22',	NULL,	NULL);

DROP TABLE IF EXISTS `col_type_document`;
CREATE TABLE `col_type_document` (
  `id_type_document` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `date_cre` datetime NOT NULL,
  `user_cre` int(11) unsigned NOT NULL,
  `date_maj` datetime DEFAULT NULL,
  `user_maj` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_type_document`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `col_utilisateur`;
CREATE TABLE `col_utilisateur` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_groupe` tinyint(3) unsigned DEFAULT '3',
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
  CONSTRAINT `utilisateur_createur_fk` FOREIGN KEY (`user_cre`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_editeur_fk` FOREIGN KEY (`user_maj`) REFERENCES `col_utilisateur` (`id_utilisateur`),
  CONSTRAINT `utilisateur_groupe_fk` FOREIGN KEY (`id_groupe`) REFERENCES `col_groupe` (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `col_utilisateur` (`id_utilisateur`, `identifiant`, `password`, `email`, `admin`, `id_groupe`, `token`, `valide`, `user_cre`, `date_cre`, `user_maj`, `date_maj`) VALUES
(1,	'admin',	'$2y$10$NzL0l3deoMiZEPWAge5E5Oh4BcGo7Nx/Voox7qJYt9Y90Jw/FpVyO',	'admin@mydomain.com',	1,	2,	'',	1,	1,	'2017-10-24 07:47:06',	1,	'2018-09-29 12:10:50');

-- 2018-09-29 10:11:38
