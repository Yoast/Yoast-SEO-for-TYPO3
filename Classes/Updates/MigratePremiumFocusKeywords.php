<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Updates;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('yoastPremiumFocusKeywordsMigrate')]
class MigratePremiumFocusKeywords implements UpgradeWizardInterface
{
    protected const PREMIUM_TABLE = 'tx_yoast_seo_premium_focus_keywords';
    protected const NEW_TABLE = 'tx_yoastseo_related_focuskeyword';

    protected ConnectionPool $connectionPool;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function getIdentifier(): string
    {
        return 'yoastPremiumFocusKeywordsMigrate';
    }

    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3 - Migrate premium focus keywords';
    }

    public function getDescription(): string
    {
        return 'Migrate focus keywords from the premium extension';
    }

    public function executeUpdate(): bool
    {
        $premiumFocusKeywords = $this->getPremiumFocusKeywords();
        $relatedTableColumns = $this->getRelatedTableColumns();
        foreach ($premiumFocusKeywords as $premiumFocusKeyword) {
            $premiumFocusKeyword['uid_foreign'] = $premiumFocusKeyword['parentid'];
            $premiumFocusKeyword['tablenames'] = $premiumFocusKeyword['parenttable'];
            unset(
                $premiumFocusKeyword['uid'],
                $premiumFocusKeyword['parentid'],
                $premiumFocusKeyword['parenttable']
            );
            $this->connectionPool->getConnectionForTable(self::NEW_TABLE)
                ->insert(
                    self::NEW_TABLE,
                    array_intersect_key($premiumFocusKeyword, $relatedTableColumns)
                );
        }

        $this->connectionPool->getConnectionForTable('pages')
            ->executeQuery(
                'UPDATE pages SET tx_yoastseo_focuskeyword_related = tx_yoastseo_focuskeyword_premium'
            );

        return true;
    }

    protected function getPremiumFocusKeywords(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::PREMIUM_TABLE);
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        return $queryBuilder
            ->select('*')
            ->from(self::PREMIUM_TABLE)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function getRelatedTableColumns(): array
    {
        $connection = $this->connectionPool->getConnectionForTable(self::NEW_TABLE);
        if (method_exists($connection, 'getSchemaManager')) {
            $schemaManager = $connection->getSchemaManager();
        } else {
            $schemaManager = $connection->createSchemaManager();
        }
        $columns = $schemaManager->listTableColumns(self::NEW_TABLE);
        return array_flip(array_keys($columns));
    }

    protected function premiumTableExists(): bool
    {
        try {
            $connection = $this->connectionPool->getConnectionForTable(self::PREMIUM_TABLE);
            if (method_exists($connection, 'getSchemaManager')) {
                $schemaManager = $connection->getSchemaManager();
            } else {
                $schemaManager = $connection->createSchemaManager();
            }
            return $schemaManager->tablesExist([self::PREMIUM_TABLE]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateNecessary(): bool
    {
        return $this->premiumTableExists();
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
