DROP TABLE IF EXISTS `#__pricelistbase`;DROP TABLE IF EXISTS `#__pricelistcity`;CREATE TABLE `#__pricelistbase` (  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',  `oper` VARCHAR(255) NOT NULL COMMENT 'Оператор',  `title` VARCHAR(255) NOT NULL COMMENT 'Наименование',  `price1` INT(11) NOT NULL COMMENT 'С установкой',  `price2` INT(11) NOT NULL COMMENT 'Без установки',  `price3` INT(11) NOT NULL COMMENT 'Без антенны',   PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;CREATE TABLE `#__pricelistcity` (  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',  `title` VARCHAR(255) NOT NULL COMMENT 'Описание или подсказка',  `city` VARCHAR(255) NOT NULL COMMENT 'Город',  `price1` INT(11) NOT NULL COMMENT '?',  `price2` INT(11) NOT NULL COMMENT '??',  `price3` INT(11) NOT NULL COMMENT '???',   PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;