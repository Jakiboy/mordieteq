-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 02 Mai 2015 à 18:49
-- Version du serveur :  5.6.16
-- Version de PHP :  5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `testdb`
--

-- --------------------------------------------------------

--
-- Structure de la table `persons`
--

CREATE TABLE IF NOT EXISTS `persons` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Firstname` varchar(32) DEFAULT NULL,
  `Lastname` varchar(32) DEFAULT NULL,
  `Sex` char(1) DEFAULT NULL,
  `Age` tinyint(3) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `persons`
--

INSERT INTO `persons` (`Id`, `Firstname`, `Lastname`, `Sex`, `Age`) VALUES
(1, 'Johny', 'Doe', 'M', 19),
(2, 'Bob', 'Black', 'M', 40),
(3, 'Zoe', 'Chan', 'F', 21),
(4, 'Sekito', 'Khan', 'M', 19),
(5, 'Kader', 'Khan', 'M', 56);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
