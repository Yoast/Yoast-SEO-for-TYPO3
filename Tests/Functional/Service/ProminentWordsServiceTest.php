<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use YoastSeoForTypo3\YoastSeo\Service\ProminentWordsService;
use YoastSeoForTypo3\YoastSeo\Service\SiteService;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(ProminentWordsService::class)]
class ProminentWordsServiceTest extends AbstractFunctionalTestCase
{
    protected const TABLE = 'tx_yoastseo_prominent_word';

    protected ProminentWordsService $subject;
    protected ConnectionPool $connectionPool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages_with_seo.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/prominent_words.csv');
        $this->setUpBackendUser(1);
        $this->setUpBackendRequest();

        $this->connectionPool = $this->getContainer()->get(ConnectionPool::class);

        $siteService = $this->createMock(SiteService::class);
        $siteService->method('getSiteRootPageId')->willReturn(1);

        $this->subject = new ProminentWordsService($this->connectionPool, $siteService);
    }

    #[Test]
    public function testSaveCreatesNewWords(): void
    {
        $this->subject->saveProminentWords(1, 1, 'pages', 0, [
            'seo' => 10,
            'yoast' => 5,
            'typo3' => 8,
        ]);

        $words = $this->getAllWords();
        $stems = array_column($words, 'stem');
        self::assertContains('typo3', $stems);
    }

    #[Test]
    public function testSaveUpdatesChangedWeights(): void
    {
        $this->subject->saveProminentWords(1, 1, 'pages', 0, [
            'seo' => 20,
            'yoast' => 5,
        ]);

        $words = $this->getAllWords();
        $seoWord = $this->findWordByStem($words, 'seo');
        self::assertNotNull($seoWord);
        self::assertSame(20, (int)$seoWord['weight']);
    }

    #[Test]
    public function testSaveDeletesRemovedWords(): void
    {
        $this->subject->saveProminentWords(1, 1, 'pages', 0, [
            'seo' => 10,
        ]);

        $words = $this->getAllWords();
        $stems = array_column($words, 'stem');
        self::assertNotContains('yoast', $stems);
    }

    #[Test]
    public function testSaveNoOpForUnchangedWeights(): void
    {
        $this->subject->saveProminentWords(1, 1, 'pages', 0, [
            'seo' => 10,
            'yoast' => 5,
        ]);

        $words = $this->getAllWords();
        self::assertCount(2, $words);

        $seoWord = $this->findWordByStem($words, 'seo');
        self::assertSame(10, (int)$seoWord['weight']);

        $yoastWord = $this->findWordByStem($words, 'yoast');
        self::assertSame(5, (int)$yoastWord['weight']);
    }

    #[Test]
    public function testSaveMixedOperations(): void
    {
        $this->subject->saveProminentWords(1, 1, 'pages', 0, [
            'seo' => 15,
            'typo3' => 12,
        ]);

        $words = $this->getAllWords();
        $stems = array_column($words, 'stem');

        self::assertContains('seo', $stems);
        self::assertContains('typo3', $stems);
        self::assertNotContains('yoast', $stems);

        $seoWord = $this->findWordByStem($words, 'seo');
        self::assertSame(15, (int)$seoWord['weight']);

        $typo3Word = $this->findWordByStem($words, 'typo3');
        self::assertSame(12, (int)$typo3Word['weight']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getAllWords(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        return $queryBuilder->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid_foreign', 1),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter('pages')),
                $queryBuilder->expr()->eq('sys_language_uid', 0)
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param array<int, array<string, mixed>> $words
     * @return array<string, mixed>|null
     */
    protected function findWordByStem(array $words, string $stem): ?array
    {
        foreach ($words as $word) {
            if ($word['stem'] === $stem) {
                return $word;
            }
        }
        return null;
    }
}
