--
-- Current Schema CREATE statements go here
--

 -- \Stationer\Pencil\models\Article
DROP TABLE IF EXISTS `Article`;
CREATE TABLE IF NOT EXISTS `Article` (
    `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `release_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `author_id` int(10) unsigned NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL DEFAULT '',
    `body` mediumtext NOT NULL DEFAULT '',
    KEY (`updated_dts`),
    PRIMARY KEY(`article_id`)
);


 -- \Stationer\Pencil\models\Asset
DROP TABLE IF EXISTS `Asset`;
CREATE TABLE IF NOT EXISTS `Asset` (
    `asset_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` varchar(30) NOT NULL,
    `path` varchar(255) NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`asset_id`)
);


 -- \Stationer\Pencil\models\Form
DROP TABLE IF EXISTS `Form`;
CREATE TABLE IF NOT EXISTS `Form` (
    `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `fields` text NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`form_id`)
);


 -- \Stationer\Pencil\models\Node
DROP TABLE IF EXISTS `Node`;
CREATE TABLE IF NOT EXISTS `Node` (
    `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `parent_id` int(10) unsigned NOT NULL DEFAULT 0,
    `content_id` int(10) unsigned NOT NULL DEFAULT 0,
    `contentType` varchar(255) NOT NULL,
    `label` varchar(255) NOT NULL,
    `creator_id` int(10) unsigned NOT NULL DEFAULT 0,
    `keywords` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL,
    `published` bit(1) NOT NULL DEFAULT b'0',
    `trashed` bit(1) NOT NULL DEFAULT b'0',
    `featured` bit(1) NOT NULL DEFAULT b'0',
    `pathAlias` varchar(255) NOT NULL,
    `ordinal` smallint(5) unsigned NOT NULL DEFAULT 0,
    `path` text NOT NULL DEFAULT '',
    `left_index` int(10) unsigned NOT NULL DEFAULT 0,
    `right_index` int(10) unsigned NOT NULL DEFAULT 0,
    KEY (`left_index`,`right_index`),
    KEY (`right_index`,`left_index`),
    KEY (`path`(64)),
    KEY (`updated_dts`),
    UNIQUE KEY (`parent_id`,`label`),
    PRIMARY KEY(`node_id`)
);


 -- \Stationer\Pencil\models\Page
DROP TABLE IF EXISTS `Page`;
CREATE TABLE IF NOT EXISTS `Page` (
    `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `title` varchar(255) NOT NULL,
    `body` mediumtext NOT NULL DEFAULT '',
    `template_id` int(10) unsigned NOT NULL DEFAULT 0,
    KEY (`updated_dts`),
    PRIMARY KEY(`page_id`)
);


 -- \Stationer\Pencil\models\Revision
DROP TABLE IF EXISTS `Revision`;
CREATE TABLE IF NOT EXISTS `Revision` (
    `revision_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `revisedModel` varchar(255) NOT NULL,
    `revised_id` int(10) unsigned NOT NULL DEFAULT 0,
    `editor_id` int(10) unsigned NOT NULL DEFAULT 0,
    `changes` mediumtext NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`revision_id`)
);


 -- \Stationer\Pencil\models\Site
DROP TABLE IF EXISTS `Site`;
CREATE TABLE IF NOT EXISTS `Site` (
    `site_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `title` varchar(255) NOT NULL,
    `theme_id` int(10) unsigned NOT NULL DEFAULT 0,
    `defaultPage_id` int(10) unsigned NOT NULL DEFAULT 0,
    KEY (`updated_dts`),
    PRIMARY KEY(`site_id`)
);


 -- \Stationer\Pencil\models\Submission
DROP TABLE IF EXISTS `Submission`;
CREATE TABLE IF NOT EXISTS `Submission` (
    `submission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `form_id` int(10) unsigned NOT NULL DEFAULT 0,
    `ip` int(10) unsigned NOT NULL DEFAULT 0,
    `ua` varchar(255) NOT NULL,
    `data` text NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`submission_id`)
);


 -- \Stationer\Pencil\models\Tag
DROP TABLE IF EXISTS `Tag`;
CREATE TABLE IF NOT EXISTS `Tag` (
    `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `label` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`tag_id`)
);


 -- \Stationer\Pencil\models\Template
DROP TABLE IF EXISTS `Template`;
CREATE TABLE IF NOT EXISTS `Template` (
    `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `type` int(10) unsigned NOT NULL DEFAULT 0,
    `body` mediumtext NOT NULL,
    `css` mediumtext NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`template_id`)
);


 -- \Stationer\Pencil\models\Text
DROP TABLE IF EXISTS `Text`;
CREATE TABLE IF NOT EXISTS `Text` (
    `text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `mimeType` varchar(255) NOT NULL DEFAULT 'text/plain',
    `body` mediumtext NOT NULL DEFAULT '',
    KEY (`updated_dts`),
    PRIMARY KEY(`text_id`)
);


 -- \Stationer\Pencil\models\Theme
DROP TABLE IF EXISTS `Theme`;
CREATE TABLE IF NOT EXISTS `Theme` (
    `theme_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `document` mediumtext NOT NULL,
    `footer` mediumtext NOT NULL,
    `header` mediumtext NOT NULL,
    `css` mediumtext NOT NULL,
    KEY (`updated_dts`),
    PRIMARY KEY(`theme_id`)
);


DROP TABLE IF EXISTS `Node_Tag`;
CREATE TABLE IF NOT EXISTS `Node_Tag` (
    `tag_id` int(11) NOT NULL,
    `node_id` int(11) NOT NULL,
    `created_uts` int(10) unsigned NOT NULL DEFAULT 0,
    `updated_dts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`tag_id`,`node_id`)
);
