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
use YoastSeoForTypo3\YoastSeo\Service\SaveScoresService;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;

#[CoversClass(SaveScoresService::class)]
class SaveScoresServiceTest extends AbstractFunctionalTestCase
{
    protected SaveScoresService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages_with_seo.csv');
        $this->setUpBackendUser(1);
        $this->setUpBackendRequest();

        $this->subject = new SaveScoresService(
            $this->getContainer()->get(ConnectionPool::class)
        );
    }

    #[Test]
    public function testSaveUpdatesScoresForDefaultLanguagePage(): void
    {
        $this->subject->save([
            'table' => 'pages',
            'uid' => '1',
            'languageId' => '0',
            'readabilityScore' => '7',
            'seoScore' => '8',
        ]);

        $row = $this->getPageById(1);
        self::assertSame('7', $row['tx_yoastseo_score_readability']);
        self::assertSame('8', $row['tx_yoastseo_score_seo']);
    }

    #[Test]
    public function testSaveUpdatesScoresForTranslatedPage(): void
    {
        $this->subject->save([
            'table' => 'pages',
            'uid' => '1',
            'languageId' => '1',
            'readabilityScore' => '5',
            'seoScore' => '6',
        ]);

        $row = $this->getPageById(2);
        self::assertSame('5', $row['tx_yoastseo_score_readability']);
        self::assertSame('6', $row['tx_yoastseo_score_seo']);
    }

    #[Test]
    public function testSaveDoesNothingWhenRecordNotFound(): void
    {
        $this->subject->save([
            'table' => 'pages',
            'uid' => '999',
            'languageId' => '0',
            'readabilityScore' => '5',
            'seoScore' => '6',
        ]);

        $row = $this->getPageById(1);
        self::assertSame('', $row['tx_yoastseo_score_readability']);
        self::assertSame('', $row['tx_yoastseo_score_seo']);
    }

    #[Test]
    public function testSaveDoesNothingWhenScoresMissing(): void
    {
        $this->subject->save([
            'table' => 'pages',
            'uid' => '1',
            'languageId' => '0',
        ]);

        $row = $this->getPageById(1);
        self::assertSame('', $row['tx_yoastseo_score_readability']);
        self::assertSame('', $row['tx_yoastseo_score_seo']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPageById(int $uid): array
    {
        $connection = $this->getContainer()->get(ConnectionPool::class)->getConnectionForTable('pages');
        $row = $connection->select(
            ['uid', 'tx_yoastseo_score_readability', 'tx_yoastseo_score_seo'],
            'pages',
            ['uid' => $uid]
        )->fetchAssociative();
        self::assertIsArray($row);
        return $row;
    }
}
