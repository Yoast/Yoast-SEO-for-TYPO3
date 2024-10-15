<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Platform\PlatformInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Backend\Overview\DataProviderRequest;
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
        'tx_yoastseo_score_seo',
    ];

    protected DataProviderRequest $dataProviderRequest;

    public function initialize(DataProviderRequest $dataProviderRequest): void
    {
        $this->dataProviderRequest = $dataProviderRequest;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function process(): array
    {
        $pages = [];
        foreach (array_chunk($this->getPageIds(), $this->getMaxBindParameters() - 10) as $chunk) {
            $query = $this->getResults($chunk);
            if ($query === null) {
                continue;
            }
            foreach ($query->fetchAllAssociative() as $page) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    public function getNumberOfItems(): int
    {
        $pageCount = 0;
        foreach (array_chunk($this->getPageIds(), $this->getMaxBindParameters() - 10) as $chunk) {
            $query = $this->getResults($chunk);
            if ($query === null) {
                continue;
            }
            $pageCount += $query->rowCount();
        }
        return (int)$pageCount;
    }

    /**
     * @return int[]
     */
    protected function getPageIds(): array
    {
        return PageAccessUtility::getPageIds($this->dataProviderRequest->getId());
    }

    /**
     * @return int<999, max>
     */
    protected function getMaxBindParameters(): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::PAGES_TABLE);
        return max(999, PlatformInformation::getMaxBindParameters($connection->getDatabasePlatform()));
    }
}
