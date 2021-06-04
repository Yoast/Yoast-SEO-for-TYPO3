#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_yoastseo_focuskeyword tinytext,
	tx_yoastseo_focuskeyword_synonyms tinytext,
	tx_yoastseo_hide_snippet_preview tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_cornerstone tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_score_readability varchar(50) DEFAULT '' NOT NULL,
	tx_yoastseo_score_seo varchar(50) DEFAULT '' NOT NULL,
	tx_yoastseo_snippetpreview tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_focuskeyword_analysis tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_readability_analysis tinyint(3) DEFAULT '0' NOT NULL,
	tx_yoastseo_focuskeyword_premium int(11) DEFAULT '0' NOT NULL,
	KEY tx_yoastseo_cornerstone (tx_yoastseo_cornerstone),
);

CREATE TABLE tt_content (
    tx_yoastseo_linking_suggestions int(11) DEFAULT '0' NOT NULL
);
