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
use YoastSeoForTypo3\YoastSeo\Hooks\DisableAnalysisCleanupHook;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(DisableAnalysisCleanupHook::class)]
class DisableAnalysisCleanupHookTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages_with_seo.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/prominent_words_translated.csv');
        $this->setUpBackendUser(1);
        $this->setUpBackendRequest();
    }

    #[Test]
    public function testDisablingDefaultLanguageKeepsTranslationWords(): void
    {
        $this->disableAnalysis(1);

        self::assertSame([1], $this->getRemainingLanguageIds());
    }

    #[Test]
    public function testDisablingTranslationDeletesOnlyTranslationWords(): void
    {
        $this->disableAnalysis(2);

        self::assertSame([0], $this->getRemainingLanguageIds());
    }

    protected function disableAnalysis(int $pageId): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([
            TableNames::PAGES => [
                $pageId => ['tx_yoastseo_disable_analysis' => 1],
            ],
        ], []);
        $dataHandler->process_datamap();
    }

    /**
     * @return int[]
     */
    protected function getRemainingLanguageIds(): array
    {
        $languageIds = $this->getContainer()->get(ConnectionPool::class)
            ->getQueryBuilderForTable(TableNames::PROMINENT_WORD)
            ->select('sys_language_uid')
            ->from(TableNames::PROMINENT_WORD)
            ->groupBy('sys_language_uid')
            ->executeQuery()
            ->fetchFirstColumn();

        return array_map('intval', $languageIds);
    }
}
