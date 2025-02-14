<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\DataProviders;

use Doctrine\DBAL\Result;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class OrphanedContentDataProvider extends AbstractOverviewDataProvider
{
    public function getKey(): string
    {
        return 'orphaned';
    }

    public function getLabel(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:orphanedContent';
    }

    public function getDescription(): string
    {
        return 'LLL:EXT:yoast_seo/Resources/Private/Language/BackendModuleOverview.xlf:orphanedContent.description';
    }

    public function getLink(): ?string
    {
        return 'https://yoa.st/1ja';
    }

    /** @var int[] */
    protected array $referencedPages = [];

    /**
     * @param int[] $pageIds
     */
    public function getResults(array $pageIds = []): ?Result
    {
        if ($this->referencedPages === []) {
            $this->referencedPages = $this->getReferencedPages();
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');

        $constraints = [
            $qb->expr()->in('doktype', YoastUtility::getAllowedDoktypes()),
            $qb->expr()->eq('sys_language_uid', $this->dataProviderRequest->getLanguage()),
        ];
        if (count($this->referencedPages) > 0) {
            $constraints[] = $qb->expr()->notIn('uid', $this->referencedPages);
        }
        if (count($pageIds) > 0) {
            $constraints[] = $qb->expr()->in(
                $this->dataProviderRequest->getLanguage() > 0 ? $GLOBALS['TCA']['pages']['ctrl']['transOrigPointerField'] : 'uid',
                $pageIds
            );
        }

        return $qb->select('*')
            ->from('pages')
            ->where(...$constraints)
            ->executeQuery();
    }

    /**
     * @return int[]
     */
    protected function getReferencedPages(): array
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_refindex');

        $constraints = [
            $qb->expr()->eq('ref_table', $qb->createNamedParameter('pages')),
            $qb->expr()->notIn(
                'field',
                $qb->createNamedParameter([
                    'l10n_parent',
                    'db_mountpoints',
                ], Connection::PARAM_STR_ARRAY)
            ),
        ];

        $references = $qb->select('ref_uid')
            ->from('sys_refindex')
            ->where(...$constraints)
            ->groupBy('ref_uid')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_column($references, 'ref_uid');
    }
}
