-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 28, 2009 at 11:11 PM
-- Server version: 5.1.31
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `flora`
--

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
  `ip` int(11) NOT NULL,
  `num_children` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`),
  KEY `ip/toc` (`ip`,`toc`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `topic_info`
--

CREATE TABLE `topic_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned NOT NULL DEFAULT '0',
  `title` char(80) NOT NULL,
  `is_sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `last_post_id` int(10) unsigned NOT NULL,
  `replies` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `last_post_id` (`last_post_id`),
  KEY `is_sticky` (`is_sticky`),
  KEY `thread` (`thread`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
