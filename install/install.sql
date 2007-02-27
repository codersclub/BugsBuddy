-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
--
-- Host: localhost:21212
-- Generation Time: Jun 26, 2006 at 03:16 PM
-- Server version: 5.0.21
-- PHP Version: 5.1.4
--
-- Database: `bugsbuddy`
--

-- --------------------------------------------------------

--
-- Table structure for table `bbtags`
--

CREATE TABLE `bbtags` (
  `id` int(11) NOT NULL auto_increment,
  `bbcode` text,
  `htmlcode` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bbtags`
--


-- --------------------------------------------------------

--
-- Table structure for table `bugcategory`
--

CREATE TABLE `bugcategory` (
  `id` int(11) NOT NULL auto_increment,
  `category` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bugcategory`
--


-- --------------------------------------------------------

--
-- Table structure for table `bugpriority`
--

CREATE TABLE `bugpriority` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bugpriority`
--

INSERT INTO `bugpriority` (`id`, `name`) VALUES (1, 'default');

-- --------------------------------------------------------

--
-- Table structure for table `bugs`
--

CREATE TABLE `bugs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `description` text,
  `user_id` int(11) default NULL,
  `submitdate` int(11) default NULL,
  `productversion_id` int(11) default NULL,
  `productversionfixed_id` int(11) default NULL,
  `priority_id` int(11) default NULL,
  `status_id` int(11) default NULL,
  `category1_id` int(11) NOT NULL default '0',
  `category2_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bugs`
--


-- --------------------------------------------------------

--
-- Table structure for table `bugstatus`
--

CREATE TABLE `bugstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bugstatus`
--

INSERT INTO `bugstatus` (`id`, `name`) VALUES (1, 'default');

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL auto_increment,
  `setting` text,
  `value` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `setting`, `value`) VALUES
(1, 'mailfrom', 'Bug&#32;Tracker&#32;&#60;bugtracker&#64;example&#46;com&#62;'),
(2, 'webmastermail', 'webmaster&#64;example&#46;com');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL auto_increment,
  `bug_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `submitdate` int(11) default NULL,
  `message` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `message`
--


-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL auto_increment,
  `page` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page`) VALUES
(1, 'buglist'),
(2, 'changepassword'),
(3, 'errorpage'),
(4, 'forgotpassword'),
(5, 'home'),
(6, 'pagenotfound'),
(7, 'register'),
(8, 'submitbug'),
(9, 'viewbug'),
(10, 'download'),
(11, 'login'),
(12, 'logout'),
(13, 'project');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL auto_increment,
  `level_id` int(11) default NULL,
  `setting` text,
  `value` text,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `level_id`, `setting`, `value`, `description`) VALUES
(1, 3, 'mayview_admin', 'true', 'This user group can view the admin page'),
(2, 3, 'mayview_admin_bbtags', 'true', 'This user group can view the admin page &quot;BBTags&quot;'),
(3, 3, 'mayview_admin_bugpriority', 'true', 'This user group can view the admin page &quot;Bug Priority&quot;'),
(4, 3, 'mayview_admin_bugstatus', 'true', 'This user group can view the admin page &quot;Bug Status&quot;'),
(5, 3, 'mayview_admin_editconfig', 'true', 'This user group can view the admin page &quot;Edit Config&quot;'),
(6, 3, 'mayview_admin_permissions', 'true', 'This user group can view the admin page &quot;Permissions&quot;'),
(7, 3, 'mayview_admin_project', 'true', 'This user group can view the admin page &quot;Projects&quot;'),
(8, 3, 'mayview_admin_users', 'true', 'This user group can view the admin page &quot;Users&quot;'),
(10, 3, 'mayview_admin_projectstatus', 'true', 'This user group can view the admin page &quot;Project Statuses&quot;'),
(11, 2, 'mayadd_viewbug_comment', 'true', 'This user group can add a bug comment'),
(12, 3, 'mayview_admin_categories', 'true', 'This user group can view the admin page &quot;Bug Categories&quot;');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  `projectstatus_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `project`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectstatus`
--

CREATE TABLE `projectstatus` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projectstatus`
--

INSERT INTO `projectstatus` (`id`, `name`) VALUES
(1, 'public'),
(2, 'private');

-- --------------------------------------------------------

--
-- Table structure for table `projectusers`
--

CREATE TABLE `projectusers` (
  `project_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projectusers`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectversion`
--

CREATE TABLE `projectversion` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL default '0',
  `version` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projectversion`
--


-- --------------------------------------------------------

--
-- Table structure for table `usergroups`
--

CREATE TABLE `usergroups` (
  `id` int(11) NOT NULL auto_increment,
  `name` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usergroups`
--

INSERT INTO `usergroups` (`id`, `name`) VALUES
(1, 'User'),
(2, 'Moderator'),
(3, 'Admin'),
(5, 'Guest');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `passwordhash` varchar(32) default NULL,
  `email` varchar(64) default NULL,
  `group_id` int(11) NOT NULL default '1',
  `last_login` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

