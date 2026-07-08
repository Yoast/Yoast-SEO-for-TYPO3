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
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use YoastSeoForTypo3\YoastSeo\Service\LinkingSuggestionsService;
use YoastSeoForTypo3\YoastSeo\Service\SiteService;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(LinkingSuggestionsService::class)]
class LinkingSuggestionsServiceTest extends AbstractFunctionalTestCase
{
    protected ConnectionPool $connectionPool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/linking_suggestions_paging.csv');

        $this->connectionPool = $this->getContainer()->get(ConnectionPool::class);
    }

    /**
     * Three (pid, tablenames) record groups share the request stem. With a batch size of two,
     * the first page holds two groups (pids 1 and 2), but pid 1 contains two analyzed records,
     * so the first page expands to three records. The loop must keep paging until a page returns
     * fewer record groups than the batch size, otherwise the record on the second page (pid 3)
     * is silently dropped.
     */
    #[Test]
    public function collectScoresContinuesPagingWhenAPidHoldsMultipleRecords(): void
    {
        $subject = $this->createSubject(2);

        $scores = $subject->collectScoresForTest(1, 0, ['seo' => 10]);

        self::assertArrayHasKey('3-pages', $scores, 'Record on the second page was never scored.');
        self::assertArrayHasKey('1-pages', $scores);
        self::assertArrayHasKey('100-pages', $scores);
        self::assertArrayHasKey('2-pages', $scores);
    }

    /**
     * Pins the scoring math and the site / language filtering:
     * - record 11 matches the request vector proportionally and must score exactly 1.0
     * - record 13 shares one stem, record 12 is diluted by a heavy non-matching stem
     * - rows on another site (record 21), another language (record 31) and corrupt
     *   rows (site 0, uid_foreign 0) must neither appear nor skew the document
     *   frequencies used in the scores
     */
    #[Test]
    public function collectScoresComputesNormalizedScoresFilteredBySiteAndLanguage(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/linking_suggestions_scoring.csv');
        $subject = $this->createSubject(100);

        $scores = $subject->collectScoresForTest(1, 0, ['alpha' => 10, 'beta' => 5]);

        self::assertSame(['11-pages', '13-pages', '12-pages'], array_keys($scores));
        self::assertEqualsWithDelta(1.0, $scores['11-pages'], 0.0001);
        self::assertEqualsWithDelta(0.447214, $scores['13-pages'], 0.0001);
        self::assertEqualsWithDelta(0.035748, $scores['12-pages'], 0.0001);
    }

    #[Test]
    public function collectScoresReturnsSameScoresRegardlessOfBatchSize(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/linking_suggestions_scoring.csv');

        $allAtOnce = $this->createSubject(100)->collectScoresForTest(1, 0, ['alpha' => 10, 'beta' => 5]);
        $oneByOne = $this->createSubject(1)->collectScoresForTest(1, 0, ['alpha' => 10, 'beta' => 5]);

        self::assertSame($allAtOnce, $oneByOne);
    }

    private function createSubject(int $batchSize): LinkingSuggestionsService
    {
        $siteService = $this->createMock(SiteService::class);
        $pageRepository = $this->createMock(PageRepository::class);

        return new class ($this->connectionPool, $pageRepository, $siteService, $batchSize) extends LinkingSuggestionsService {
            /**
             * @param array<string, int|string> $words
             * @return array<string, float|int>
             */
            public function collectScoresForTest(int $site, int $languageId, array $words): array
            {
                $this->site = $site;
                $this->languageId = $languageId;
                $this->documentFrequencyCache = [];

                return $this->collectScores($words);
            }
        };
    }
}
