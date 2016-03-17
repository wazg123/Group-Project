USE mammalweb;

CREATE TABLE `status`
(
	`photo_id` int(11) NOT NULL,
	`classified` TINYINT(1) NOT NULL DEFAULT 0,
	`toExperts` TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `classification`
(
	`classification_id` int(11) NOT NULL AUTO_INCREMENT,
	`photo_id` int(11) NOT NULL,
	`species` int(11) NOT NULL,
	`number` int(4) NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`classification_id`),
	KEY `classification_id` (`photo_id`),
	KEY `species` (`species`),
	KEY `person_id` (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;