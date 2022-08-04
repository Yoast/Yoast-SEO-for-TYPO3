<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Install;

use Doctrine\DBAL\Exception\InvalidFieldNameException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class SeoTitleUpdate
 */
class SeoTitleUpdate implements UpgradeWizardInterface
{
    public function getIdentifier(): string
    {
        return 'seoTitleUpdate';
    }

    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3: SEO title';
    }

    public function getDescription(): string
    {
        return 'Migrate data from tx_yoastseo_title to seo_title in pages table';
    }

    public function executeUpdate(): bool
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $qb->getRestrictions()->removeAll();

        $rows = $qb->select('*')
            ->from('pages')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->isNotNull('tx_yoastseo_title'),
                    $qb->expr()->neq('tx_yoastseo_title', $qb->createNamedParameter(''))
                ),
                $qb->expr()->eq('seo_title', $qb->createNamedParameter(''))
            )
            ->execute()
            ->fetchAll();

        foreach ($rows as $row) {
            $qb->update('pages')
                ->set('seo_title', $qb->createNamedParameter($row['tx_yoastseo_title']), false)
                ->where(
                    $qb->expr()->eq('uid', $qb->createNamedParameter($row['uid'], Connection::PARAM_INT))
                )
                ->execute();
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $qb->getRestrictions()->removeAll();

        try {
            $qb->select('*')
                ->from('pages')
                ->where(
                    $qb->expr()->orX(
                        $qb->expr()->isNotNull('tx_yoastseo_title'),
                        $qb->expr()->neq('tx_yoastseo_title', $qb->createNamedParameter(''))
                    ),
                    $qb->expr()->eq('seo_title', $qb->createNamedParameter(''))
                );
            return (bool)$qb->execute()->rowCount();
        } catch (InvalidFieldNameException $e) {
            // Not needed to update when the old column doesn't exist
            return false;
        }
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
