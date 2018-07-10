<?php
namespace YoastSeoForTypo3\YoastSeo\DataHandling;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class DatabaseSchemaService
 * @package YoastSeoForTypo3\YoastSeo\Controller
 */
class DatabaseSchemaService
{

    /**
     * @var array
     */
    protected $databaseSchema = [
        'cal' => '
CREATE TABLE tx_cal_event (
	seo_title varchar(255) DEFAULT \'\' NOT NULL,
	seo_description text,
	tx_yoastseo_focuskeyword varchar(32) DEFAULT \'\' NOT NULL,
);
        ',
        'news' => '
CREATE TABLE tx_news_domain_model_news (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT \'0\' NOT NULL,
	tx_yoastseo_focuskeyword varchar(32) DEFAULT \'\' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);
        ',
    ];

    /**
     * Add needed database schema for yoast_seo and extensions, if extension is installed.
     *
     * @param array $sqlString Current SQL statements to be executed
     * @return array Modified arguments of SqlExpectedSchemaService::tablesDefinitionIsBeingBuilt signal
     */
    public function addDatabaseSchemaForExtensions(array $sqlString)
    {
        foreach ($this->databaseSchema as $extension => $databaseSchema) {
            if (ExtensionManagementUtility::isLoaded($extension)) {
                $sqlString[] = $databaseSchema;
            }
        }
        return ['sqlString' => $sqlString];
    }
}
