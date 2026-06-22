#
# Table structure for 'tx_sitepackage_domain_model_minimal'
#
CREATE TABLE tx_sitepackage_domain_model_minimal (
   title varchar(255) NOT NULL DEFAULT '',
   text text NOT NULL DEFAULT '',
);

#
# Table structure for 'tx_sitepackage_domain_model_manual'
#
CREATE TABLE tx_sitepackage_domain_model_manual (
   title varchar(255) NOT NULL DEFAULT '',
   text text NOT NULL DEFAULT '',
   seo_title varchar(255) NOT NULL DEFAULT '',
   seo_description varchar(255) NOT NULL DEFAULT '',
   tx_yoastseo_focuskeyword varchar(100) DEFAULT '' NOT NULL,
);
