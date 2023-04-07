#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_yoastseo_focuskeyword tinytext,
	tx_yoastseo_focuskeyword_synonyms tinytext,
    tx_yoastseo_focuskeyword_related int(11) DEFAULT '0' NOT NULL,
	tx_yoastseo_hide_snippet_preview tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_cornerstone tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_score_readability varchar(50) DEFAULT '' NOT NULL,
	tx_yoastseo_score_seo varchar(50) DEFAULT '' NOT NULL,
    tx_yoastseo_robots_noimageindex tinyint(4) DEFAULT '0' NOT NULL,
    tx_yoastseo_robots_noarchive tinyint(4) DEFAULT '0' NOT NULL,
    tx_yoastseo_robots_nosnippet tinyint(4) DEFAULT '0' NOT NULL,

	KEY tx_yoastseo_cornerstone (tx_yoastseo_cornerstone),
);

#
# Table structure for 'tx_yoastseo_related_focuskeyword'
#
CREATE TABLE tx_yoastseo_related_focuskeyword (
    keyword varchar(255) DEFAULT '' NOT NULL,
    synonyms varchar(255) DEFAULT '' NOT NULL,

    uid_foreign int(11) DEFAULT '0' NOT NULL,
    tablenames varchar(64) DEFAULT '' NOT NULL,
);

#
# Table structure for 'tx_yoastseo_prominent_word'
#
CREATE TABLE tx_yoastseo_prominent_word (
    site int(11) DEFAULT '0' NOT NULL,
    stem varchar(255) DEFAULT '' NOT NULL,
    weight int(11) DEFAULT '0' NOT NULL,

    uid_foreign int(11) DEFAULT '0' NOT NULL,
    tablenames varchar(64) DEFAULT '' NOT NULL,
);
