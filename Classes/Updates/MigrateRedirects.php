<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Updates;

use Doctrine\DBAL\Exception\TableNotFoundException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MigrateRedirects implements UpgradeWizardInterface
{
    protected const PAGE_LINK_PROTOCOL = 't3://page?uid=';

    public function getIdentifier(): string
    {
        return 'yoastRedirectsMigrate';
    }

    public function getTitle(): string
    {
        return 'Yoast SEO for TYPO3 - Migrate premium redirects';
    }

    public function getDescription(): string
    {
        return 'Migrate redirects from the old Yoast Premium redirects module to sys_redirect';
    }

    public function executeUpdate(): bool
    {
        $domains = $this->getDomains();
        $redirects = $this->getRedirects();
        foreach ($redirects as $redirect) {
            if (empty($redirect['old_host']) || $redirect['old_host'] === '*') {
                $sourceHost = '*';
            } else {
                $sourceHost = $domains[$redirect['old_host']] ?? '*';
            }

            if (MathUtility::canBeInterpretedAsInteger(
                $redirectPage = str_replace(self::PAGE_LINK_PROTOCOL, '', $redirect['new_url'])
            )) {
                $redirect['new_url'] = self::PAGE_LINK_PROTOCOL . $redirectPage;
                if ((int)$redirect['sys_language_uid'] > 0) {
                    $redirect['new_url'] .= '&L=' . (int)$redirect['sys_language_uid'];
                }
            }

            $sysRedirect = [
                'createdby' => $redirect['createdby'],
                'createdon' => $redirect['createdon'],
                'updatedon' => $redirect['updatedon'],
                'deleted' => $redirect['deleted'],
                'disabled' => $redirect['disabled'],
                'starttime' => $redirect['starttime'],
                'endtime' => $redirect['endtime'],

                'source_host' => $sourceHost,
                'source_path' => $redirect['old_url'],
                'is_regexp' => $redirect['is_regexp'],

                'force_https' => $redirect['force_https'],
                'keep_query_parameters' => $redirect['keep_query_parameters'],
                'target' => $redirect['new_url'],
                'target_statuscode' => $redirect['new_url_statuscode'],
            ];
            GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('sys_redirect')
                ->insert('sys_redirect', $sysRedirect);
        }
        return true;
    }

    public function updateNecessary(): bool
    {
        return count($this->getRedirects()) > 0;
    }

    protected function getDomains(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_domain');
        $queryBuilder->getRestrictions()->removeAll();

        try {
            $domains = $queryBuilder->select('*')
                ->from('sys_domain')
                ->execute()->fetchAllAssociative();
        } catch (TableNotFoundException $e) {
            return [];
        }

        $domainArray = [];
        foreach ($domains as $domain) {
            $domainArray[$domain['uid']] = $domain['domainName'];
        }
        return $domainArray;
    }

    protected function getRedirects(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_yoast_seo_premium_redirect');
        $queryBuilder->getRestrictions()->removeAll();

        try {
            return $queryBuilder->select('*')
                ->from('tx_yoast_seo_premium_redirect')
                ->where(
                    $queryBuilder->expr()->eq('deleted', 0)
                )
                ->execute()
                ->fetchAllAssociative();
        } catch (TableNotFoundException $e) {
            return [];
        }
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }
}
