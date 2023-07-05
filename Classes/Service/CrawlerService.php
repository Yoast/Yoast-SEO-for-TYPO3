<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\PageAccessUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

class CrawlerService
{
    protected const REGISTRY_NAMESPACE = 'tx_yoastseo';
    protected const REGISTRY_KEY = 'crawler-%d-%d';
    protected const INDEX_CHUNK = 50;

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cache;

    /**
     * @var \TYPO3\CMS\Core\Registry
     */
    protected $registry;

    public function __construct()
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('pages');
        $this->registry = GeneralUtility::makeInstance(Registry::class);
    }

    public function getAmountOfPages(int $site, int $languageId): int
    {
        return count($this->getPagesToIndex($site, $languageId));
    }

    public function getIndexInformation(int $site, int $languageId, $offset = 0): array
    {
        $pagesToIndex = $this->getPagesToIndex($site, $languageId);
        $total = count($pagesToIndex);

        $progressInformation = $this->getProgressInformation($site, $languageId);
        $currentOffset = $offset > 0 ? $offset : ($progressInformation['offset'] ?? 0);
        $this->setProgressInformation($site, $languageId, $currentOffset, $total);

        return [
            'pages' => array_splice($pagesToIndex, $currentOffset, self::INDEX_CHUNK),
            'current' => $currentOffset,
            'nextOffset' => $currentOffset + self::INDEX_CHUNK,
            'total' => $total
        ];
    }

    public function getProgressInformation(int $site, int $languageId): array
    {
        return (array)$this->registry->get(
            self::REGISTRY_NAMESPACE,
            sprintf(self::REGISTRY_KEY, $site, $languageId),
            []
        );
    }

    protected function setProgressInformation(int $site, int $languageId, int $offset, int $total): void
    {
        $this->registry->set(
            self::REGISTRY_NAMESPACE,
            sprintf(self::REGISTRY_KEY, $site, $languageId),
            [
                'offset' => $offset,
                'total' => $total
            ]
        );
    }

    public function resetProgressInformation(int $site, int $languageId): void
    {
        $this->registry->remove(self::REGISTRY_NAMESPACE, sprintf(self::REGISTRY_KEY, $site, $languageId));
    }

    protected function getPagesToIndex(int $site, int $languageId): array
    {
        $cacheIdentifier = 'YoastSeoCrawler' . $site . '-' . $languageId;
        if (($pagesToIndex = $this->cache->get($cacheIdentifier)) === false) {
            $treeList = PageAccessUtility::getPageIds($site);
            $pagesToIndex = [];
            foreach (array_chunk($treeList, 1000) as $treeChunk) {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('pages');

                if ($languageId > 0) {
                    $select = 'l10n_parent';
                    $constraints = [
                        $queryBuilder->expr()->eq('sys_language_uid', $languageId),
                        $queryBuilder->expr()->in(
                            'l10n_parent',
                            $treeChunk
                        ),
                    ];
                } else {
                    $select = 'uid';
                    $constraints = [
                        $queryBuilder->expr()->in(
                            'uid',
                            $treeChunk
                        )
                    ];
                }

                $statement = $queryBuilder->select($select)
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->in(
                            'doktype',
                            YoastUtility::getAllowedDoktypes()
                        ),
                        ...$constraints
                    )->execute();
                $pages = GeneralUtility::makeInstance(DbalService::class)->getAllResults($statement);
                $pagesToIndex = array_merge($pagesToIndex, array_column($pages, $select));
            }
        }
        return $pagesToIndex;
    }
}
