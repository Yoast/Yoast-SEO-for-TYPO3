<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Install;

use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class CanonicalFieldUpdate
 */
class CanonicalFieldUpdate implements UpgradeWizardInterface
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
     * Check for migration
     *
     * @param string $tableName
     * @return bool
     */
    protected function checkForMigration(string $tableName): bool
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
        } catch (TableNotFoundException $e) {
            // Not needed to update when the table doesn't exist
            return false;
        } catch (InvalidFieldNameException $e) {
            // Not needed to update when the old column doesn't exist
            return false;
        }
    }

    public function executeUpdate(): bool
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
                    $qb->execute();
                }
            }
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        return !($this->checkForMigration('pages') === false
            && $this->checkForMigration('pages_language_overlay') === false);
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
