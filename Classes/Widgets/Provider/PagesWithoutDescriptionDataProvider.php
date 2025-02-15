<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Traits\BackendUserTrait;

/**
 * @deprecated Will be removed once TYPO3 v11 support is dropped
 */
class PagesWithoutDescriptionDataProvider implements PageProviderInterface
{
    use BackendUserTrait;

    public function __construct(
        /** @var int[] */
        private array $excludedDoktypes,
        private int $limit
    ) {
        $this->limit = $limit ?: 5;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
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
}
