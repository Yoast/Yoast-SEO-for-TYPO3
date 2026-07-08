<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Hooks;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;
use YoastSeoForTypo3\YoastSeo\Hooks\ProminentWordCopyPreventionHook;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(ProminentWordCopyPreventionHook::class)]
class ProminentWordCopyPreventionHookTest extends AbstractFunctionalTestCase
{
    protected ConnectionPool $connectionPool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages_with_seo.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/prominent_words.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/tt_content.csv');
        $this->setUpBackendUser(1);
        $this->setUpBackendRequest();

        $this->connectionPool = $this->getContainer()->get(ConnectionPool::class);
    }

    #[Test]
    public function testCopyingPageDoesNotCopyProminentWords(): void
    {
        $this->copyPage(1);

        $words = $this->connectionPool->getQueryBuilderForTable(TableNames::PROMINENT_WORD)
            ->select('uid_foreign', 'tablenames')
            ->from(TableNames::PROMINENT_WORD)
            ->executeQuery()
            ->fetchAllAssociative();

        self::assertCount(2, $words);
        foreach ($words as $word) {
            self::assertSame(TableNames::PAGES, $word['tablenames']);
            self::assertSame(1, (int)$word['uid_foreign']);
        }
    }

    #[Test]
    public function testCopyingPageStillCopiesOtherRecords(): void
    {
        $newPageId = $this->copyPage(1);

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $contentElements = $queryBuilder->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('pid', $newPageId)
            )
            ->executeQuery()
            ->fetchOne();

        self::assertSame(1, (int)$contentElements);
    }

    #[Test]
    public function testDeletingPageStillDeletesProminentWords(): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], [
            TableNames::PAGES => [
                1 => ['delete' => 1],
            ],
        ]);
        $dataHandler->process_cmdmap();

        $words = $this->connectionPool->getQueryBuilderForTable(TableNames::PROMINENT_WORD)
            ->count('uid')
            ->from(TableNames::PROMINENT_WORD)
            ->executeQuery()
            ->fetchOne();

        self::assertSame(0, (int)$words);
    }

    protected function copyPage(int $pageId): int
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], [
            TableNames::PAGES => [
                $pageId => ['copy' => 0],
            ],
        ]);
        $dataHandler->process_cmdmap();

        return (int)$dataHandler->copyMappingArray[TableNames::PAGES][$pageId];
    }
}
