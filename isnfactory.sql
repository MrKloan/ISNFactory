-- phpMyAdmin SQL Dump
-- version 4.1.9
-- http://www.phpmyadmin.net
--
-- Client :  mysql51-123.perso
-- Généré le :  Lun 29 Septembre 2014 à 22:50
-- Version du serveur :  5.1.73-1.1+squeeze+build0+1-log
-- Version de PHP :  5.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `isfactor`
--

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `maintenance` tinyint(1) NOT NULL DEFAULT '0',
  `allow_register` tinyint(1) NOT NULL DEFAULT '0',
  `shield_attempts` tinyint(2) unsigned NOT NULL DEFAULT '10',
  `shield_duracy` tinyint(2) unsigned NOT NULL DEFAULT '2',
  `calendar` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `homeworks` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `notes` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `informations` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `links` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `courses` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `codiad` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `ftp` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `projets` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `faq` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `mails` tinyint(1) unsigned NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `config`
--

INSERT INTO `config` (`id`, `maintenance`, `allow_register`, `shield_attempts`, `shield_duracy`, `calendar`, `homeworks`, `notes`, `informations`, `links`, `courses`, `codiad`, `ftp`, `projets`, `faq`, `mails`) VALUES
(1, 0, 1, 5, 5, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `chapter` tinyint(1) unsigned DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `files_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `courses`
--

INSERT INTO `courses` (`id`, `chapter`, `title`, `description`, `files_url`) VALUES
(1, 1, 'Langage C', '', 'YToxOntpOjA7czo2NDoiL0ZpbGVzL0NvdXJzZXMvUElFUlFVRVQvVGVybWluYWxlL0xlIExhbmdhZ2UgQyBOb3JtZSBBTlNJIGVuLnBkZiI7fQ=='),
(2, 1, 'HTML/CSS', '', 'YToyOntpOjA7czo0MToiL0ZpbGVzL0NvdXJzZXMvUElFUlFVRVQvUHJlbWllcmUvSFRNTC5wZGYiO2k6MTtzOjQwOiIvRmlsZXMvQ291cnNlcy9QSUVSUVVFVC9QcmVtaWVyZS9jc3MucGRmIjt9');

-- --------------------------------------------------------

--
-- Structure de la table `courses_for`
--

CREATE TABLE IF NOT EXISTS `courses_for` (
  `course` int(7) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`course`,`grade`),
  KEY `FK_courses_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `courses_for`
--

INSERT INTO `courses_for` (`course`, `grade`, `enabled`) VALUES
(1, 2, 1),
(2, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) DEFAULT NULL,
  `answer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `faq_for`
--

CREATE TABLE IF NOT EXISTS `faq_for` (
  `faq` smallint(5) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  PRIMARY KEY (`faq`,`grade`),
  KEY `FK_faq_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `final_projects`
--

CREATE TABLE IF NOT EXISTS `final_projects` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `group` int(7) unsigned NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text,
  `progress` tinyint(1) unsigned DEFAULT '0',
  `changelog` text,
  PRIMARY KEY (`id`),
  KEY `FK_final_projects_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `forgot`
--

CREATE TABLE IF NOT EXISTS `forgot` (
  `user` int(7) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

CREATE TABLE IF NOT EXISTS `grades` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `grade` varchar(20) DEFAULT NULL,
  `teacher` int(7) unsigned DEFAULT NULL,
  `allow_register` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_grades_teacher` (`teacher`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `grades`
--

INSERT INTO `grades` (`id`, `grade`, `teacher`, `allow_register`) VALUES
(1, 'Première S1', 3, 0),
(2, 'Terminale S1', 3, 0);

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `project` int(7) unsigned DEFAULT NULL,
  `chief` int(7) unsigned DEFAULT NULL,
  `locked` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_groups_project` (`project`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `project`, `chief`, `locked`) VALUES
(1, 1, 4, 1),
(2, 2, 5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `groups_members`
--

CREATE TABLE IF NOT EXISTS `groups_members` (
  `group` int(7) unsigned NOT NULL,
  `user` int(7) unsigned NOT NULL,
  PRIMARY KEY (`group`,`user`),
  KEY `FK_groups_members_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `group_invitations`
--

CREATE TABLE IF NOT EXISTS `group_invitations` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `group` int(7) unsigned DEFAULT NULL,
  `to` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_group_invitations_group` (`group`),
  KEY `FK_group_invitations_user` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `informations`
--

CREATE TABLE IF NOT EXISTS `informations` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT 'information',
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `informations`
--

INSERT INTO `informations` (`id`, `title`, `type`, `content`) VALUES
(1, 'Nouvel Plateforme d''enseignement', 'Extranet', '<p>Voici votre nouvelle plateforme extranet en ISN. Vous pourrez ainsi retrouver vos cours, vos notes ainsi que vos diff&eacute;rents projets tout au long de l''ann&eacute;e. Des outils de gestion de projets sont mis &agrave; votre disposition (To-Do List, Web IDE, Service mail)</p>');

-- --------------------------------------------------------

--
-- Structure de la table `informations_for`
--

CREATE TABLE IF NOT EXISTS `informations_for` (
  `information` int(7) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  PRIMARY KEY (`information`,`grade`),
  KEY `FK_informations_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `informations_for`
--

INSERT INTO `informations_for` (`information`, `grade`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `links`
--

INSERT INTO `links` (`id`, `title`, `description`, `url`) VALUES
(1, 'Sublime Text', 'Sublime Text is a sophisticated text editor for code, markup and prose.', 'http://www.sublimetext.com/'),
(2, 'Netbeans', '&quot;Les bons codent avec Eclipse, les génies utilisent NetBeans&quot; - M.Sananes décembre 2013', 'https://netbeans.org/'),
(3, 'CodeBlocks', 'Code::Blocks is a free C, C++ and Fortran IDE built to meet the most demanding needs of its users.', ' http://www.codeblocks.org/ '),
(4, 'WampServer', 'WampServer est une plate-forme de développement Web sous Windows pour des applications Web dynamiques à l’aide du serveur Apache2, du langage de scripts PHP et d’une base de données MySQL', 'http://www.wampserver.com/'),
(5, 'MySQLWorkbench', 'MySQL Workbench is a unified visual tool for database architects, developers, and DBAs.', 'http://www.mysql.fr/products/workbench/'),
(6, 'GIT', 'Git is a free and open source distributed version control system designed to handle everything from small to very large projects with speed and efficiency.', 'http://git-scm.com/'),
(7, 'BitBucket', 'Alternative à GitHub, permettant de créer des repos privés accessibles seulement à certaines personnes.', 'https://bitbucket.org/'),
(8, 'StarUML', 'Outil de modélisation UML', 'http://staruml.io');

-- --------------------------------------------------------

--
-- Structure de la table `links_for`
--

CREATE TABLE IF NOT EXISTS `links_for` (
  `link` smallint(5) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  PRIMARY KEY (`link`,`grade`),
  KEY `FK_links_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `links_for`
--

INSERT INTO `links_for` (`link`, `grade`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2);

-- --------------------------------------------------------

--
-- Structure de la table `login_security`
--

CREATE TABLE IF NOT EXISTS `login_security` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `attempts` tinyint(2) unsigned DEFAULT '0',
  `blocked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `login_security`
--

INSERT INTO `login_security` (`id`, `ip`, `account`, `attempts`, `blocked_at`) VALUES
(1, '82.227.21.221', 'admin@isnfactory.loc', 0, NULL),
(4, '82.227.21.221', 'pierquet@isnfactory.loc', 0, NULL),
(5, '82.227.21.221', 'mathieu@isnfactory.loc', 0, NULL),
(6, '82.227.21.221', 'valentin@isnfactory.loc', 0, NULL),
(8, '83.142.147.16', 'mathieu@isnfactory.loc', 0, NULL),
(9, '83.142.147.16', 'pierquet@isnfactory.loc', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `mails`
--

CREATE TABLE IF NOT EXISTS `mails` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `from` int(7) unsigned DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text,
  `sended_at` datetime DEFAULT NULL,
  `readed_at` datetime DEFAULT NULL,
  `response_to` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `mails`
--

INSERT INTO `mails` (`id`, `from`, `subject`, `content`, `sended_at`, `readed_at`, `response_to`) VALUES
(1, 4, 'Test de mail', '&lt;p&gt;Test&lt;/p&gt;', '2014-09-28 14:33:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `mails_for`
--

CREATE TABLE IF NOT EXISTS `mails_for` (
  `mail` int(7) unsigned NOT NULL,
  `user` int(7) unsigned NOT NULL,
  PRIMARY KEY (`mail`,`user`),
  KEY `FK_mails_for_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `mails_for`
--

INSERT INTO `mails_for` (`mail`, `user`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `student` int(7) unsigned DEFAULT NULL,
  `work` int(7) unsigned DEFAULT NULL,
  `note` decimal(4,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_notes_work` (`work`),
  KEY `FK_notes_student` (`student`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `information` varchar(255) DEFAULT NULL,
  `grade` int(7) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `information`, `grade`) VALUES
(1, 'E-Mail', 'Nouvel E-Mail de Mathieu BOISNARD de Terminale S1.', 2);

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `number` tinyint(1) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `files_url` varchar(255) DEFAULT NULL,
  `group_size` tinyint(1) unsigned DEFAULT '1',
  `codiad_enable` tinyint(1) DEFAULT '0',
  `todo_enable` tinyint(1) DEFAULT '1',
  `upload_enable` tinyint(1) DEFAULT '1',
  `final_project` tinyint(1) DEFAULT '0',
  `public_at_end` tinyint(1) DEFAULT '0',
  `date_start` date DEFAULT NULL,
  `date_group` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `projects`
--

INSERT INTO `projects` (`id`, `number`, `title`, `description`, `files_url`, `group_size`, `codiad_enable`, `todo_enable`, `upload_enable`, `final_project`, `public_at_end`, `date_start`, `date_group`, `date_end`) VALUES
(1, 1, 'Inventaire C', '&lt;p&gt;Cr&amp;eacute;ez un programme permettant de r&amp;eacute;aliser l&#039;inventaire des produits vendus dans un supermarch&amp;eacute;.&lt;/p&gt;', NULL, 1, 0, 1, 1, 0, 0, '2014-09-28', NULL, '2014-10-31'),
(2, 1, 'Page personnelle', '&lt;p&gt;Cr&amp;eacute;ez une page internet illustant votre CV (exp&amp;eacute;rience, projets, activit&amp;eacute;s, ...) en utilisant les langages appris en cours : HTML5/CSS3&lt;/p&gt;', NULL, 1, 1, 1, 0, 0, 0, '2014-09-28', NULL, '2014-10-20');

-- --------------------------------------------------------

--
-- Structure de la table `projects_for`
--

CREATE TABLE IF NOT EXISTS `projects_for` (
  `project` int(7) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  `enabled` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`project`,`grade`),
  KEY `FK_projects_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `projects_for`
--

INSERT INTO `projects_for` (`project`, `grade`, `enabled`) VALUES
(1, 2, 1),
(2, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `todo_lists`
--

CREATE TABLE IF NOT EXISTS `todo_lists` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `group` int(7) unsigned DEFAULT NULL,
  `title` varchar(20) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `color` enum('yellow','blue','green') DEFAULT 'yellow',
  `position` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_todo_lists_group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `todo_lists`
--

INSERT INTO `todo_lists` (`id`, `group`, `title`, `description`, `name`, `deadline`, `color`, `position`) VALUES
(1, 1, NULL, 'Créer la base SQL', 'Mathieu', '2014-10-02', 'yellow', '501x200x5'),
(2, 2, NULL, 'Mon parcours', 'Valentin', '2014-10-02', 'yellow', '533x160x2'),
(3, 2, NULL, 'Mes activités', 'Valentin', '2014-10-08', 'yellow', '100x100x1'),
(4, 2, NULL, 'Mes projets', 'Valentin', '2014-10-17', 'yellow', '112x267x1');

-- --------------------------------------------------------

--
-- Structure de la table `trimesters`
--

CREATE TABLE IF NOT EXISTS `trimesters` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `teacher` int(7) unsigned DEFAULT NULL,
  `trimester1` date DEFAULT NULL,
  `trimester2` date DEFAULT NULL,
  `trimester3` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_trimesters_teacher` (`teacher`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `trimesters`
--

INSERT INTO `trimesters` (`id`, `teacher`, `trimester1`, `trimester2`, `trimester3`) VALUES
(1, 3, '2014-09-01', '2014-12-01', '2015-03-01');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(25) DEFAULT NULL,
  `lastname` varchar(25) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `grade` int(7) unsigned DEFAULT NULL,
  `role` varchar(20) DEFAULT '0',
  `register_date` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(255) DEFAULT NULL,
  `nb_connection` smallint(5) unsigned DEFAULT '0',
  `validated` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_users_grade` (`grade`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `grade`, `role`, `register_date`, `last_login`, `last_ip`, `nb_connection`, `validated`) VALUES
(1, 'Admin', 'ADMIN', 'admin@isnfactory.loc', 'a417238e26bfcbc5bb883ec81c444a72cae8314d', NULL, 'role_admin', '2014-09-28 11:31:54', '2014-09-28 15:53:27', '82.227.21.221', 7, 1),
(3, 'Christian', 'PIERQUET', 'pierquet@isnfactory.loc', 'ad7bf954a6cee2d6c04619125541dfdb1dd27c89', NULL, 'role_professor', '2014-09-28 14:05:29', '2014-09-29 15:30:00', '83.142.147.16', 11, 1),
(4, 'Mathieu', 'BOISNARD', 'mathieu@isnfactory.loc', 'ad7bf954a6cee2d6c04619125541dfdb1dd27c89', 2, 'role_student', '2014-09-28 14:13:12', '2014-09-29 15:27:50', '83.142.147.16', 7, 1),
(5, 'Valentin', 'FRIES', 'valentin@isnfactory.loc', 'ad7bf954a6cee2d6c04619125541dfdb1dd27c89', 1, 'role_student', '2014-09-28 14:22:00', '2014-09-28 16:18:32', '82.227.21.221', 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `works`
--

CREATE TABLE IF NOT EXISTS `works` (
  `id` int(7) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `type` enum('devoir','controle','projet') DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `coeff` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `works`
--

INSERT INTO `works` (`id`, `title`, `type`, `date_end`, `coeff`) VALUES
(1, 'Inventaire C', 'projet', '2014-10-31', 2),
(2, 'Page personnelle', 'projet', '2014-10-20', 1);

-- --------------------------------------------------------

--
-- Structure de la table `works_for`
--

CREATE TABLE IF NOT EXISTS `works_for` (
  `work` int(7) unsigned NOT NULL,
  `grade` int(7) unsigned NOT NULL,
  PRIMARY KEY (`work`,`grade`),
  KEY `FK_works_for_grade` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `works_for`
--

INSERT INTO `works_for` (`work`, `grade`) VALUES
(2, 1),
(1, 2);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `courses_for`
--
ALTER TABLE `courses_for`
  ADD CONSTRAINT `FK_courses_for_course` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_courses_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `faq_for`
--
ALTER TABLE `faq_for`
  ADD CONSTRAINT `FK_faq_for_faq` FOREIGN KEY (`faq`) REFERENCES `faq` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_faq_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `final_projects`
--
ALTER TABLE `final_projects`
  ADD CONSTRAINT `FK_final_projects_group` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `forgot`
--
ALTER TABLE `forgot`
  ADD CONSTRAINT `FK_forgot_user` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `FK_grades_teacher` FOREIGN KEY (`teacher`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `FK_groups_project` FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `groups_members`
--
ALTER TABLE `groups_members`
  ADD CONSTRAINT `FK_groups_members_group` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_groups_members_user` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `group_invitations`
--
ALTER TABLE `group_invitations`
  ADD CONSTRAINT `FK_group_invitations_group` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_group_invitations_user` FOREIGN KEY (`to`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `informations_for`
--
ALTER TABLE `informations_for`
  ADD CONSTRAINT `FK_informations_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_informations_for_info` FOREIGN KEY (`information`) REFERENCES `informations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `links_for`
--
ALTER TABLE `links_for`
  ADD CONSTRAINT `FK_links_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_links_for_link` FOREIGN KEY (`link`) REFERENCES `links` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mails_for`
--
ALTER TABLE `mails_for`
  ADD CONSTRAINT `FK_mails_for_mail` FOREIGN KEY (`mail`) REFERENCES `mails` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_mails_for_user` FOREIGN KEY (`user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `FK_notes_student` FOREIGN KEY (`student`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_notes_work` FOREIGN KEY (`work`) REFERENCES `works` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `projects_for`
--
ALTER TABLE `projects_for`
  ADD CONSTRAINT `FK_projects_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_projects_for_project` FOREIGN KEY (`project`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `todo_lists`
--
ALTER TABLE `todo_lists`
  ADD CONSTRAINT `FK_todo_lists_group` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `trimesters`
--
ALTER TABLE `trimesters`
  ADD CONSTRAINT `FK_trimesters_teacher` FOREIGN KEY (`teacher`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_users_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `works_for`
--
ALTER TABLE `works_for`
  ADD CONSTRAINT `FK_works_for_grade` FOREIGN KEY (`grade`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_works_for_homework` FOREIGN KEY (`work`) REFERENCES `works` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
