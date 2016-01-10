<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_error',1); 

require_once '../config.php';

echo 'Initializing DB... <br />';
$mysql = new PDO('mysql:host='.MYSQL_DB_HOST.';port='.MYSQL_DB_PORT.';dbname='.MYSQL_DB_NAME.';',MYSQL_DB_USER,MYSQL_DB_PASS);

try
{

    $mysql->beginTransaction();

    $mysql->exec('SET FOREIGN_KEY_CHECKS=0;');

    echo 'Configuraing Database <br />';
    $mysql->exec('
CREATE TABLE IF NOT EXISTS `posts_bots` (
  `id` int(11) unsigned AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `POST_CLASS_COMBO` (`class`,`name`),
  KEY `POST_CLASS` (`class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');

    echo '<br />posts_bots <br />';
    print_r($mysql->errorInfo());

$mysql->exec('
CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(20) unsigned AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `provider_id` int(11) unsigned DEFAULT NULL,
  `provider_cid` varchar(255) DEFAULT NULL,
  `contents` longtext NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `POST_EXTERNAL_PROVIDER_INDEX` (`provider_id`,`provider_cid`),
  KEY `POST_UPDATE_TIME` (`update_time`),
  KEY `POST_CREATE_TIME` (`create_time`),
  KEY `POST_EXTERNAL_PROVIDER_ID` (`provider_id`),
  CONSTRAINT `POST_BOT` FOREIGN KEY (`provider_id`) REFERENCES `posts_bots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');

echo '<br />posts <br />';
print_r($mysql->errorInfo());

$mysql->exec('
CREATE TABLE IF NOT EXISTS `posts_meta` (
  `id` bigint(20) unsigned AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `meta_name` varchar(255) DEFAULT NULL,
  `meta_value` varchar(10240) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `POST_META_VARIABLE` (`post_id`,`meta_name`),
  KEY `POST_META_VARIABLE_NAME` (`meta_name`),
  CONSTRAINT `POST_META_POST_ID` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');
echo '<br />posts_meta <br />';
print_r($mysql->errorInfo());

$mysql->exec('
CREATE TABLE IF NOT EXISTS`posts_logs` (
  `id` bigint(20) unsigned AUTO_INCREMENT,
  `bot_id` int(11) unsigned NOT NULL,
  `event_type` smallint(6) unsigned NOT NULL,
  `message` text NOT NULL,
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `LOG_EVENT_TYPE` (`event_type`),
  KEY `LOG_CREATE_TIME` (`create_time`),
  CONSTRAINT `LOG_POST_BOT` FOREIGN KEY (`bot_id`) REFERENCES `posts_bots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');
echo '<br />posts_logs <br />';
print_r($mysql->errorInfo());

include 'update-0.6.php';

$mysql->exec('
SET FOREIGN_KEY_CHECKS=1;
');

echo '<br /><br />FINAL Done...';

    $mysql->commit();

} catch(Exception $e) {
    $mysql->rollBack();
    echo '<br />ERROR:<br />';
    echo nl2br(print_r($e,true));
}