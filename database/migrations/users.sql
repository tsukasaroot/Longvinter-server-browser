-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           10.3.31-MariaDB-0ubuntu0.20.04.1 - Ubuntu 20.04
-- SE du serveur:                debian-linux-gnu
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

-- Listage de la structure de la table accountdb_2800. accountinfo
CREATE TABLE IF NOT EXISTS `users` (
    `accountDBID` int(11) NOT NULL AUTO_INCREMENT,
    `userName` varchar(64) NOT NULL,
    `passWord` varchar(555) NOT NULL,
    `RMB` int(11) NOT NULL DEFAULT 0,
    `charCount` int(11) NOT NULL DEFAULT 0,
    `authKey` varchar(128) DEFAULT NULL,
    `registerTime` timestamp NOT NULL DEFAULT current_timestamp(),
    `lastLoginTime` int(11) NOT NULL DEFAULT 0,
    `lastLoginIP` varchar(64) DEFAULT NULL,
    `playTimeLast` int(11) NOT NULL DEFAULT 0,
    `playTimeTotal` bigint(20) NOT NULL DEFAULT 0,
    `playCount` int(11) NOT NULL DEFAULT 0,
    `isBlocked` int(11) NOT NULL DEFAULT 0,
    `privilege` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`accountDBID`,`userName`)
    ) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- Modified for test
-- Les données exportées n'étaient pas sélectionnées.
