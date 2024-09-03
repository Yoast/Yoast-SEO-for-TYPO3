<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @deprecated Will be removed once TYPO3 v11 support is dropped
 */
class PagesWithoutDescriptionDataProvider implements PageProviderInterface
{
    private array $excludedDoktypes;
    private int $limit;

    public function __construct(array $excludedDoktypes, int $limit)
    {
        $this->excludedDoktypes = $excludedDoktypes;
        $this->limit = $limit ?: 5;
    }

    public function getPages(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');

        $constraints = [
            $queryBuilder->expr()->notIn('doktype', $this->excludedDoktypes),
            $queryBuilder->expr()->eq('no_index', 0),
            $queryBuilder->expr()->or(
                $queryBuilder->expr()->eq('description', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->isNull('description')
            ),
        ];

        $items = [];
        $counter = 0;
        $iterator = 0;

        while ($counter < $this->limit) {
            $row = $queryBuilder
                ->select('*')
                ->from('pages')
                ->where(...$constraints)
                ->orderBy('tstamp', 'DESC')
                ->setFirstResult($iterator)
                ->setMaxResults(1)
                ->executeQuery()
                ->fetchAssociative();

            if ($row === false) {
                // Likely fewer pages than the limit, prevent infinite loop
                break;
            }

            $iterator++;

            if (!$this->getBackendUser()->doesUserHaveAccess($row, Permission::PAGE_SHOW)) {
                continue;
            }

            $items[] = $row;
            $counter++;
        }
        return $items;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
