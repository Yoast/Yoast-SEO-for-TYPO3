<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

    /**
     * @param int[] $pageIds
     */
    public function getResults(array $pageIds = []): ?Result
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::PAGES_TABLE);

        $constraints = [
            $queryBuilder->expr()->eq('sys_language_uid', $this->dataProviderRequest->getLanguage()),
            $queryBuilder->expr()->eq('tx_yoastseo_cornerstone', 1),
        ];

        if (count($pageIds) > 0) {
            $constraints[] = $queryBuilder->expr()->in(
                $this->dataProviderRequest->getLanguage() > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $queryBuilder->select('*')
            ->from(self::PAGES_TABLE)
            ->where(...$constraints)
            ->executeQuery();
    }
}
