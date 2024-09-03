<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Platform\PlatformInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PageAccessUtility;

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

    protected array $callerParams = [];

    public function process(array $params): array
    {
        $this->callerParams = $params;

        return $this->getRestrictedPagesResults();
    }

    public function numberOfItems(array $params): int
    {
        $this->callerParams = $params;

        return $this->getRestrictedPagesResults(true);
    }

    protected function getRestrictedPagesResults(bool $returnOnlyCount = false)
    {
        $pageIds = PageAccessUtility::getPageIds((int)$_GET['id']);

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

            foreach ($query->fetchAllAssociative() as $page) {
                $pages[] = $page;
            }
        }

        if ($returnOnlyCount === false) {
            return $pages;
        }

        return $pageCount;
    }
}
