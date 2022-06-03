create table `user`(
`id` int auto_increment,
`name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`mailadd` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`passwd` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`rank` int(1) DEFAULT 0,
`memo` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`login` datetime NOT NULL,
`regist` datetime NOT NULL,
`status` int(1) default 0,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `information`(
`id` int auto_increment,
`value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`rank` int(1),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

create table `admin_info`(
`id` int auto_increment,
`mailadd` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
`passwd` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `admin_info` (`id`,`mailadd`,`passwd`) values(1,'hoge@hoge.com','1234');



CREATE TABLE IF NOT EXISTS `analysis_pv` (
  `aid` int(11) NOT NULL auto_increment,
  `asp_id` int(11) default NULL,
  `pid` int(11) default NULL,
  `type` int(11) default NULL,
  `ip` text character set utf8 collate utf8_unicode_ci,
  `regist` datetime NOT NULL,
  `w` tinyint(1) NOT NULL,
  PRIMARY KEY  (`aid`),
  KEY `pid` (`pid`),
  KEY `asp_id` (`asp_id`),
  KEY `w` (`w`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `analysis_cl` (
  `cid` int(11) NOT NULL auto_increment,
  `asp_id` int(11) default NULL,
  `pid` int(11) default NULL,
  `type` int(11) default NULL,
  `ip` text character set utf8 collate utf8_unicode_ci,
  `regist` datetime NOT NULL,
  `w` tinyint(1) NOT NULL,
  PRIMARY KEY  (`cid`),
  KEY `asp_id` (`asp_id`),
  KEY `pid` (`pid`),
  KEY `w` (`w`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `category_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL,
  `item_name` text COLLATE utf8_unicode_ci,
  `img` text COLLATE utf8_unicode_ci,
  `brand` text COLLATE utf8_unicode_ci,
  `vote` text COLLATE utf8_unicode_ci,
  `asin` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jan` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `us_price` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `saiyasu_price` int(11) DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` text COLLATE utf8_unicode_ci,
  `ranking` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memver` int(11) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`),
  KEY `type` (`type`),
  KEY `jan` (`jan`),
  KEY `asin` (`asin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `name` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `plan_name` text COLLATE utf8_unicode_ci,
  `keyword` text COLLATE utf8_unicode_ci,
  `url` text COLLATE utf8_unicode_ci,
  `regist` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`gid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `plan_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `item_name` text COLLATE utf8_unicode_ci,
  `img` text COLLATE utf8_unicode_ci,
  `brand` text COLLATE utf8_unicode_ci,
  `vote` text COLLATE utf8_unicode_ci,
  `asin` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jan` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `us_price` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `saiyasu_price` int(11) DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` text COLLATE utf8_unicode_ci,
  `ranking` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memver` int(11) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `asin` (`asin`),
  KEY `jan` (`jan`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `seller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_name` text COLLATE utf8_unicode_ci,
  `shop_name` text COLLATE utf8_unicode_ci,
  `seller_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `seller_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) DEFAULT NULL,
  `item_name` text COLLATE utf8_unicode_ci,
  `img` text COLLATE utf8_unicode_ci,
  `brand` text COLLATE utf8_unicode_ci,
  `vote` text COLLATE utf8_unicode_ci,
  `asin` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jan` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `us_price` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `saiyasu_price` int(11) DEFAULT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` text COLLATE utf8_unicode_ci,
  `ranking` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memver` int(11) DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `asin` (`asin`),
  KEY `jan` (`jan`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;





