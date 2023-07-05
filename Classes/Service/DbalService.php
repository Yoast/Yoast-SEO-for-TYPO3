<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use Doctrine\DBAL\Driver\Statement;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Needed service to support doctrine/dbal 2.10 which is shipped with TYPO3 10 non-composer
 */
class DbalService
{
    /**
     * @param Statement|\Doctrine\DBAL\ForwardCompatibility\Result|\Doctrine\DBAL\Driver\ResultStatement $statement
     */
    public function getSingleResult($statement)
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() === 10) {
            return $statement->fetch();
        }
        return $statement->fetchAssociative();
    }

    /**
     * @param Statement|\Doctrine\DBAL\ForwardCompatibility\Result|\Doctrine\DBAL\Driver\ResultStatement $statement
     */
    public function getAllResults($statement)
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() === 10) {
            return $statement->fetchAll();
        }
        return $statement->fetchAllAssociative();
    }
}
