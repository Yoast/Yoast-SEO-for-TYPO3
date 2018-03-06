<?php
namespace YoastSeoForTypo3\YoastSeo\DataProviders;

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

use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * Class CornerstoneOverviewDataProvider
 * @package YoastSeoForTypo3\YoastSeo\DataProviders
 */
abstract class AbstractOverviewDataProvider implements OverviewDataProviderInterface
{

    /**
     * @var array
     */
    protected $callerParams;

    /**
     * @param array $params
     *
     * @return array
     */
    public function process($params)
    {
        $this->callerParams = $params;

        return $this->getData();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function numberOfItems($params)
    {
        $this->callerParams = $params;

        return $this->getData(true);
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
