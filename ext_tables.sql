#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_yoastseo_focuskeyword varchar(32) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_image varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_image varchar(255) DEFAULT '' NOT NULL,
);