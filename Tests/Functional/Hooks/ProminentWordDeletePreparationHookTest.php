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
use YoastSeoForTypo3\YoastSeo\Hooks\ProminentWordDeletePreparationHook;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(ProminentWordDeletePreparationHook::class)]
class ProminentWordDeletePreparationHookTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/editor_with_page_tree.csv');
        $this->setUpBackendUser(2);
        $this->setUpBackendRequest();
    }

    #[Test]
    public function testEditorWithoutProminentWordAccessCanDeletePageTree(): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], [
            TableNames::PAGES => [
                1 => ['delete' => 1],
            ],
        ]);
        $dataHandler->process_cmdmap();

        self::assertSame([], $dataHandler->errorLog);
        self::assertSame([4], $this->getRemainingIds(TableNames::PAGES));
        self::assertSame([3], $this->getRemainingIds(TableNames::PROMINENT_WORD));
    }

    /**
     * @return list<int>
     */
    protected function getRemainingIds(string $table): array
    {
        $queryBuilder = $this->getContainer()->get(ConnectionPool::class)->getQueryBuilderForTable($table);

        return array_map('intval', $queryBuilder->select('uid')
            ->from($table)
            ->orderBy('uid')
            ->executeQuery()
            ->fetchFirstColumn());
    }
}
