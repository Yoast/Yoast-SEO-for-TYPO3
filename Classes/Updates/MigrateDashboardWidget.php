<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Updates;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('yoastDashboardWidgetMigrate')]
class MigrateDashboardWidget implements UpgradeWizardInterface
{
    protected const DASHBOARD_TABLE = 'be_dashboards';

    protected ConnectionPool $connectionPool;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function getIdentifier(): string
    {
        return 'yoastDashboardWidgetMigrate';
    }

    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3 - Migrate dashboard widget';
    }

    public function getDescription(): string
    {
        return 'Migrate the Yoast SEO "Pages without meta description" widget to the widget from core';
    }

    public function executeUpdate(): bool
    {
        $this->connectionPool->getConnectionForTable(self::DASHBOARD_TABLE)->executeQuery(
            'UPDATE ' . self::DASHBOARD_TABLE . ' SET widgets = REPLACE(widgets, "yoastseo-pagesWithoutMetaDescription", "seo-pagesWithoutMetaDescription")'
        );
        return true;
    }

    protected function dashboardTableExists(): bool
    {
        try {
            $connection = $this->connectionPool->getConnectionForTable(self::DASHBOARD_TABLE);
            if (method_exists($connection, 'getSchemaManager')) {
                $schemaManager = $connection->getSchemaManager();
            } else {
                $schemaManager = $connection->createSchemaManager();
            }
            return $schemaManager->tablesExist([self::DASHBOARD_TABLE]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateNecessary(): bool
    {
        return $this->dashboardTableExists();
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}
