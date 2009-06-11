-- phpMyAdmin SQL Dump
-- version 3.1.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 11, 2009 at 06:22 PM
-- Server version: 5.1.34
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `flora`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `posts`
--
CREATE TABLE `posts` (
`id` int(10) unsigned
,`parent` int(10) unsigned
,`author` char(10)
,`ip` int(11)
,`toc` int(10) unsigned
,`date` timestamp
,`body` text
,`topic` int(10) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `post_data`
--

CREATE TABLE `post_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `body` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `post_info`
--

CREATE TABLE `post_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic` int(10) unsigned NOT NULL,
  `parent` int(10) unsigned DEFAULT NULL,
  `author` char(10) DEFAULT NULL,
  `toc` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`),
  KEY `ip/toc` (`ip`,`toc`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `topics`
--
CREATE TABLE `topics` (
`id` int(10) unsigned
,`post` int(10) unsigned
,`ip` int(11)
,`title` char(80)
,`is_sticky` tinyint(1) unsigned
,`last_post_id` int(10) unsigned
,`last_post` int(10) unsigned
,`last_post_date` timestamp
,`last_post_author` char(10)
,`author` char(10)
,`replies` int(10) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `topic_info`
--

CREATE TABLE `topic_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post` int(10) unsigned NOT NULL DEFAULT '0',
  `title` char(80) NOT NULL,
  `is_sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `replies` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `last_post_id` (`last_post_id`),
  KEY `thread` (`post`),
  KEY `is_sticky/last_post_id` (`is_sticky`,`last_post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure for view `posts`
--
DROP TABLE IF EXISTS `posts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `posts` AS select `post_info`.`id` AS `id`,`post_info`.`parent` AS `parent`,`post_info`.`author` AS `author`,`post_info`.`ip` AS `ip`,`post_info`.`toc` AS `toc`,`post_info`.`date` AS `date`,`post_data`.`body` AS `body`,`post_info`.`topic` AS `topic` from (`post_info` left join `post_data` on((`post_info`.`id` = `post_data`.`id`)));

-- --------------------------------------------------------

--
-- Structure for view `topics`
--
DROP TABLE IF EXISTS `topics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `topics` AS select `topic_info`.`id` AS `id`,`topic_info`.`post` AS `post`,`post_info`.`ip` AS `ip`,`topic_info`.`title` AS `title`,`topic_info`.`is_sticky` AS `is_sticky`,`topic_info`.`last_post_id` AS `last_post_id`,`last_post_info`.`toc` AS `last_post`,`last_post_info`.`date` AS `last_post_date`,`last_post_info`.`author` AS `last_post_author`,`post_info`.`author` AS `author`,`topic_info`.`replies` AS `replies` from ((`topic_info` left join `post_info` on((`topic_info`.`post` = `post_info`.`id`))) left join `post_info` `last_post_info` on((`topic_info`.`last_post_id` = `last_post_info`.`id`)));
