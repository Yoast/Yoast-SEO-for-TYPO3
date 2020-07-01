<?php
namespace YoastSeoForTypo3\YoastSeo\Install\CMS8;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Doctrine\DBAL\Exception\InvalidFieldNameException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Class CanonicalFieldUpdate
 */
class CanonicalFieldUpdate extends AbstractUpdate
{
    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'canonicalFieldUpdate';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3: Canonical field';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Migrate data from canonical_url to canonical_link in pages (and overlay) table';
    }

    /**
     * @param array $databaseQueries
     * @param string $customMessage
     * @return bool
     */
    public function performUpdate(array &$databaseQueries, &$customMessage): bool
    {
        foreach (['pages', 'pages_language_overlay'] as $tableName) {
            if ($this->checkForMigration($tableName)) {
                $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
                $qb->getRestrictions()->removeAll();

                $rows = $qb->select('*')
                    ->from($tableName)
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->orX(
                                $qb->expr()->isNotNull('canonical_url'),
                                $qb->expr()->neq('canonical_url', $qb->createNamedParameter(''))
                            ),
                            $qb->expr()->eq('canonical_link', $qb->createNamedParameter(''))
                        )
                    )
                    ->execute()
                    ->fetchAll();

                foreach ($rows as $row) {
                    $qb = GeneralUtility::makeInstance(ConnectionPool::class)
                        ->getQueryBuilderForTable($tableName);
                    $qb->update($tableName)
                        ->set('canonical_link', $qb->createNamedParameter($row['canonical_url']), false)
                        ->where(
                            $qb->expr()->eq('uid', $qb->createNamedParameter($row['uid'], Connection::PARAM_INT))
                        );
                    $databaseQueries[] = $qb->getSQL();
                    $qb->execute();
                }
            }
        }
        $this->markWizardAsDone();

        return true;
    }

    /**
     * @param string $description
     * @return bool
     */
    public function checkForUpdate(&$description): bool
    {
        if ($this->isWizardDone()) {
            return false;
        }

        if ($this->checkForMigration('pages') === false
            && $this->checkForMigration('pages_language_overlay') === false) {
            return false;
        }

        return true;
    }

    /**
     * Check for migration
     *
     * @param $tableName
     * @return bool
     */
    protected function checkForMigration($tableName): bool
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($tableName);
        $qb->getRestrictions()->removeAll();

        try {
            $qb->count('uid')
                ->from($tableName)
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->orX(
                            $qb->expr()->isNotNull('canonical_url'),
                            $qb->expr()->neq('canonical_url', $qb->createNamedParameter(''))
                        ),
                        $qb->expr()->eq('canonical_link', $qb->createNamedParameter(''))
                    )
                );
            return (bool)$qb->execute()->fetchColumn();
        } catch (InvalidFieldNameException $e) {
            // Not needed to update when the old column doesn't exist
            return false;
        }
    }
}
