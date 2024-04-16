<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Widgets\Provider;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Service\DbalService;

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
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('description', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->isNull('description')
            ),
        ];

        $items = [];
        $counter = 0;
        $iterator = 0;

        while ($counter < $this->limit) {
            $statement = $queryBuilder
                ->select('*')
                ->from('pages')
                ->where(...$constraints)
                ->orderBy('tstamp', 'DESC')
                ->setFirstResult($iterator)
                ->setMaxResults(1);
            $row = GeneralUtility::makeInstance(DbalService::class)->getSingleResult($statement);

            if ($row === false) {
                // Likely less pages than the limit, prevent infinite loop
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
