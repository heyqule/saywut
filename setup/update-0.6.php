<?php
/**
 * Created by JetBrains PhpStorm.
 * User: heyqule
 * Date: 2/7/14
 * Time: 12:42 AM
 * To change this template use File | Settings | File Templates.
 */
echo '<br />Adding Keyword to Posts<br />';
$mysql->exec('alter table `posts` add column `keywords` varchar(255) DEFAULT NULL AFTER `contents`');
print_r($mysql->errorInfo());

echo '<br />Adding Fulltext table<br />';
$mysql->exec('
CREATE TABLE IF NOT EXISTS `posts_fulltext` (
  `id` bigint(20) unsigned,
  `title` varchar(255) DEFAULT NULL,
  `contents` longtext NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  UNIQUE (`id`),
  FULLTEXT (`title`,`keywords`,`contents`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
');

$mysql->exec('INSERT INTO posts_fulltext (`id`,`title`,`contents`,`keywords`)
SELECT `id`,`title`,`contents`,`keywords`
FROM posts');


echo '<br />Upgrade 0.6 Done... <br />';
