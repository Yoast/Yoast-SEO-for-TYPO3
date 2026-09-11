<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

class CornerstoneOverviewDataProvider extends AbstractOverviewDataProvider
{
    public function getKey(): string
    {
        return 'cornerstone';
    }

    public function getLabel(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:cornerstoneContent';
    }

    public function getDescription(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:cornerstoneContent.description';
    }

    public function getLink(): ?string
    {
        return 'https://yoa.st/typo3-cornerstone-content';
    }

    protected function createQueryBuilder(array $pageIds): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(TableNames::PAGES);

        $constraints = [
            $queryBuilder->expr()->eq('sys_language_uid', $this->dataProviderRequest->getLanguage()),
            $queryBuilder->expr()->eq('tx_yoastseo_cornerstone', 1),
        ];

        if (count($pageIds) > 0) {
            $constraints[] = $queryBuilder->expr()->in($this->getRestrictionField(), $pageIds);
        }

        return $queryBuilder->select(...self::PAGES_FIELDS)
            ->from(TableNames::PAGES)
            ->where(...$constraints);
    }
}
