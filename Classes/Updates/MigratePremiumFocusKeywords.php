<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Updates;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use YoastSeoForTypo3\YoastSeo\Service\DbalService;

class MigratePremiumFocusKeywords implements UpgradeWizardInterface
{
    protected const PREMIUM_TABLE = 'tx_yoast_seo_premium_focus_keywords';
    protected const NEW_TABLE = 'tx_yoastseo_related_focuskeyword';

    protected ?ConnectionPool $connectionPool = null;

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
        foreach ($premiumFocusKeywords as $premiumFocusKeyword) {
            $premiumFocusKeyword['uid_foreign'] = $premiumFocusKeyword['parentid'];
            $premiumFocusKeyword['tablenames'] = $premiumFocusKeyword['parenttable'];
            unset(
                $premiumFocusKeyword['uid'],
                $premiumFocusKeyword['parentid'],
                $premiumFocusKeyword['parenttable']
            );
            $this->connectionPool->getConnectionForTable(self::NEW_TABLE)
                ->insert(self::NEW_TABLE, $premiumFocusKeyword);
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
        $statement = $queryBuilder
            ->select('*')
            ->from(self::PREMIUM_TABLE)
            ->execute();
        return GeneralUtility::makeInstance(DbalService::class)->getAllResults($statement);
    }

    protected function premiumTableExists(): bool
    {
        try {
            return GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(self::PREMIUM_TABLE)
                ->getSchemaManager()
                ->tablesExist([self::PREMIUM_TABLE]);
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
            DatabaseUpdatedPrerequisite::class
        ];
    }
}
