#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	seo_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_focuskeyword varchar(32) DEFAULT '' NOT NULL,
	canonical_url varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_robot_instructions int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_facebook_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_image int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_twitter_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_image int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_dont_use tinyint(3) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (
	seo_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_focuskeyword varchar(32) DEFAULT '' NOT NULL,
	canonical_url varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_robot_instructions int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_facebook_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_facebook_image int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_twitter_title varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_description varchar(255) DEFAULT '' NOT NULL,
	tx_yoastseo_twitter_image int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_news_domain_model_news'

CREATE TABLE tx_news_domain_model_news (
	tx_yoastseo_focuskeyword varchar(32) DEFAULT '' NOT NULL,
);