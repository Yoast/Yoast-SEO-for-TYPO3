<?php

declare(strict_types=1);

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

use Doctrine\DBAL\Driver\ResultStatement;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Platform\PlatformInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PageAccessUtility;

/**
 * Class CornerstoneOverviewDataProvider
 */
abstract class AbstractOverviewDataProvider implements OverviewDataProviderInterface
{
    protected const PAGES_TABLE = 'pages';

    protected const PAGES_FIELDS = [
        'uid',
        'doktype',
        'hidden',
        'starttime',
        'endtime',
        'fe_group',
        't3ver_state',
        'nav_hide',
        'is_siteroot',
        'module',
        'content_from_pid',
        'extendToSubpages',
        'sys_language_uid',
        'title',
        'seo_title',
        'tx_yoastseo_score_readability',
        'tx_yoastseo_score_seo'
    ];

    /**
     * @var array
     */
    protected $callerParams;

    /**
     * @param array $params
     *
     * @return array
     */
    public function process(array $params): array
    {
        $this->callerParams = $params;

        return $this->getData();
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function numberOfItems(array $params): int
    {
        $this->callerParams = $params;

        return $this->getData(true);
    }

    protected function getRestrictedPagesResults(bool $returnOnlyCount = false)
    {
        $pageIds = PageAccessUtility::getPageIds();
        if ($pageIds === null) {
            return $this->allResults($returnOnlyCount);
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::PAGES_TABLE);
        $maxBindParameters = PlatformInformation::getMaxBindParameters($connection->getDatabasePlatform());

        $pages = [];
        $pageCount = 0;
        foreach (array_chunk($pageIds, $maxBindParameters - 10) as $chunk) {
            $query = $this->getResults($chunk);
            if ($query === null) {
                continue;
            }

            if ($returnOnlyCount) {
                $pageCount += $query->rowCount();
                continue;
            }

            foreach ($query->fetchAll() as $page) {
                $pages[] = $page;
            }
        }

        if ($returnOnlyCount === false) {
            return $pages;
        }

        return $pageCount;
    }

    protected function getResults(array $pageIds = []): ?ResultStatement
    {
        // Needs to be overwritten by provider, not possible to set an abstract function due to API change
        return null;
    }

    protected function allResults(bool $returnOnlyCount = false)
    {
        $query = $this->getResults();
        if ($query === null) {
            return $returnOnlyCount ? 0 : [];
        }
        if ($returnOnlyCount) {
            return $query->rowCount();
        }
        return $query->fetchAll();
    }
}
