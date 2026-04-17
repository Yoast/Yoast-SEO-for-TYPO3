<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Updates;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

#[UpgradeWizard('yoastSeoMigrateHideSnippetPreviewToDisableAnalysis')]
class MigrateHideSnippetPreviewToDisableAnalysis implements UpgradeWizardInterface
{
    protected ConnectionPool $connectionPool;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    public function getIdentifier(): string
    {
        return 'yoastSeoMigrateHideSnippetPreviewToDisableAnalysis';
    }

    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3 - Migrate hide snippet preview to disable analysis';
    }

    public function getDescription(): string
    {
        return 'Copies the hide_snippet_preview flag to the new disable_analysis field so existing pages keep their current behavior after the visibility split.';
    }

    public function executeUpdate(): bool
    {
        $this->connectionPool
            ->getConnectionForTable(TableNames::PAGES)
            ->executeStatement(
                'UPDATE pages SET tx_yoastseo_disable_analysis = 1 WHERE tx_yoastseo_hide_snippet_preview = 1 AND tx_yoastseo_disable_analysis = 0'
            );

        return true;
    }

    public function updateNecessary(): bool
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(TableNames::PAGES);

        $count = $queryBuilder
            ->count('uid')
            ->from(TableNames::PAGES)
            ->where(
                $queryBuilder->expr()->eq('tx_yoastseo_hide_snippet_preview', 1),
                $queryBuilder->expr()->eq('tx_yoastseo_disable_analysis', 0)
            )
            ->executeQuery()
            ->fetchOne();

        return (int)$count > 0;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}
