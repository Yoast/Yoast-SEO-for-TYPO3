<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class PagesWithoutDescriptionOverviewDataProvider extends AbstractOverviewDataProvider
{
    public function getKey(): string
    {
        return 'withoutDescription';
    }

    public function getLabel(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:withoutDescription';
    }

    public function getDescription(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:withoutDescription.description';
    }

    public function getLink(): ?string
    {
        return 'https://yoa.st/typo3-meta-description';
    }

    protected const PAGES_TABLE = 'pages';

    public function getResults(array $pageIds = []): ?Result
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::PAGES_TABLE);

        $constraints = [
            $queryBuilder->expr()->or(
                $queryBuilder->expr()->eq('description', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->isNull('description')
            ),
            $queryBuilder->expr()->in('doktype', YoastUtility::getAllowedDoktypes()),
            $queryBuilder->expr()->eq('tx_yoastseo_hide_snippet_preview', 0),
            $queryBuilder->expr()->eq('sys_language_uid', $this->dataProviderRequest->getLanguage()),
        ];

        if (count($pageIds) > 0) {
            $constraints[] = $queryBuilder->expr()->in(
                $this->dataProviderRequest->getLanguage() > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $queryBuilder->select(...self::PAGES_FIELDS)
            ->from(self::PAGES_TABLE)
            ->where(...$constraints)
            ->executeQuery();
    }
}
