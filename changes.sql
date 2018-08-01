CREATE TABLE `Node` (
  `node_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_id` int(11) unsigned NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  `contentType` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) NOT NULL DEFAULT '',
  `creator_id` int(11) unsigned NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` tinytext NOT NULL,
  `published` bit(1) NOT NULL,
  `trashed` bit(1) NOT NULL,
  `featured` bit(1) NOT NULL,
  `permalink` varchar(255) NOT NULL DEFAULT '',
  `ordinal` int(11) unsigned NOT NULL,

  PRIMARY KEY (`node_id`),
  KEY(`parent_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Content` (
  `content_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `File` (
  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(30) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Form` (
  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fields` text NOT NULL,
  PRIMARY KEY (`form_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Submission` (
  `submission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `form_id` int(11) NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ua` varchar(255) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  PRIMARY KEY (`submission_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL DEFAULT '',
  `template_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Tag` (
  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `label` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Node_Tag` (
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tag_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,

  PRIMARY KEY (`tag_id`,`node_id`)
);

CREATE TABLE `Revision` (
  `revision_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `revisedModel` varchar(255) NOT NULL DEFAULT '',
  `revisedID` int(11) unsigned NOT NULL,
  `editor_id` int(11) unsigned NOT NULL,
  `changes` text NOT NULL,
  PRIMARY KEY (`revision_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Site` (
  `site_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `theme_id` int(11) unsigned NOT NULL,
  `defaultPage_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`site_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Template` (
  `template_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `css` text NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY(`updated_dts`)
);

CREATE TABLE `Theme` (
  `theme_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_uts` int(11) unsigned NOT NULL,
  `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `css` text NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  PRIMARY KEY (`theme_id`),
  KEY(`updated_dts`)
);
