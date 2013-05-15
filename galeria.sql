CREATE  TABLE IF NOT EXISTS `galeria`.`tokens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `token` VARCHAR(255) NULL DEFAULT NULL ,
  `data` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
DEFAULT CHARACTER SET = latin1;

CREATE  TABLE IF NOT EXISTS `galeria`.`users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `password` VARCHAR(255) NULL DEFAULT NULL ,
  `is_admin` TINYINT(1) NULL DEFAULT '0' ,
  `is_active` TINYINT(1) NULL DEFAULT '0' ,
  `created` DATETIME NULL DEFAULT NULL ,
  `modified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
DEFAULT CHARACTER SET = latin1;