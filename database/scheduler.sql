-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Sam 11 Août 2012 à 19:50
-- Version du serveur: 5.1.62
-- Version de PHP: 5.3.6-13ubuntu3.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `scheduler`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=100 ;

-- --------------------------------------------------------

--
-- Structure de la table `events_map`
--

CREATE TABLE IF NOT EXISTS `events_map` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,
  `lng` float(10,6) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=287 ;

-- --------------------------------------------------------

--
-- Structure de la table `events_ms`
--

CREATE TABLE IF NOT EXISTS `events_ms` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Structure de la table `events_rec`
--

CREATE TABLE IF NOT EXISTS `events_rec` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) NOT NULL,
  `rec_type` varchar(64) NOT NULL,
  `event_pid` int(11) NOT NULL,
  `event_length` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Structure de la table `events_shared`
--

CREATE TABLE IF NOT EXISTS `events_shared` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `event_type` int(11) NOT NULL DEFAULT '0',
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Structure de la table `events_tt`
--

CREATE TABLE IF NOT EXISTS `events_tt` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `details` text NOT NULL,
  `section_id` int(11) NOT NULL,
  `section2_id` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=94 ;

-- --------------------------------------------------------

--
-- Structure de la table `event_fruit`
--

CREATE TABLE IF NOT EXISTS `event_fruit` (
  `event_fruit_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `fruit_id` int(11) NOT NULL,
  PRIMARY KEY (`event_fruit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Structure de la table `event_user`
--

CREATE TABLE IF NOT EXISTS `event_user` (
  `event_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`event_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=92 ;

-- --------------------------------------------------------

--
-- Structure de la table `fruit`
--

CREATE TABLE IF NOT EXISTS `fruit` (
  `fruit_id` int(11) NOT NULL AUTO_INCREMENT,
  `fruit_name` varchar(64) NOT NULL,
  PRIMARY KEY (`fruit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Structure de la table `tevents`
--

CREATE TABLE IF NOT EXISTS `tevents` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(127) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Structure de la table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `typeid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
