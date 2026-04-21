<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\MetaTag\AdvancedRobots;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots\AdvancedRobotsGenerator;
use YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots\RobotsMetaTagManager;
use YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots\RobotsRulesGenerator;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

#[CoversClass(AdvancedRobotsGenerator::class)]
class AdvancedRobotsGeneratorTest extends UnitTestCase
{
    protected RecordService $recordService;
    protected RobotsMetaTagManager $metaTagManager;
    protected RobotsRulesGenerator $robotsRulesGenerator;
    protected AdvancedRobotsGenerator $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->recordService = $this->createMock(RecordService::class);
        $this->metaTagManager = $this->createMock(RobotsMetaTagManager::class);
        $this->robotsRulesGenerator = $this->createMock(RobotsRulesGenerator::class);
        $this->subject = new AdvancedRobotsGenerator(
            $this->recordService,
            $this->metaTagManager,
            $this->robotsRulesGenerator
        );
    }

    #[Test]
    public function testGenerateWithActiveRecordAndFlags(): void
    {
        $record = (new Record())
            ->setGenerateRobotsTag(true)
            ->setRecordData([
                'tx_yoastseo_robots_noimageindex' => 1,
                'tx_yoastseo_robots_noarchive' => 0,
                'tx_yoastseo_robots_nosnippet' => 0,
                'no_index' => 1,
                'no_follow' => 0,
            ]);
        $this->recordService->method('getActiveRecord')->willReturn($record);

        $this->robotsRulesGenerator->expects(self::once())
            ->method('generateRules')
            ->willReturn('noimageindex,noindex,follow');

        $this->metaTagManager->expects(self::once())->method('removeRobotsTag');
        $this->metaTagManager->expects(self::once())->method('addRobotsTag')->with('noimageindex,noindex,follow');

        $this->subject->generate([]);
    }

    #[Test]
    public function testGenerateSkipsWhenNoRecord(): void
    {
        $this->recordService->method('getActiveRecord')->willReturn(null);

        $this->metaTagManager->expects(self::never())->method('removeRobotsTag');
        $this->metaTagManager->expects(self::never())->method('addRobotsTag');

        $this->subject->generate([]);
    }

    #[Test]
    public function testGenerateSkipsWhenAllFlagsFalse(): void
    {
        $record = (new Record())
            ->setGenerateRobotsTag(true)
            ->setRecordData([
                'tx_yoastseo_robots_noimageindex' => 0,
                'tx_yoastseo_robots_noarchive' => 0,
                'tx_yoastseo_robots_nosnippet' => 0,
                'no_index' => 0,
                'no_follow' => 0,
            ]);
        $this->recordService->method('getActiveRecord')->willReturn($record);

        $this->metaTagManager->expects(self::never())->method('removeRobotsTag');
        $this->metaTagManager->expects(self::never())->method('addRobotsTag');

        $this->subject->generate([]);
    }

    #[Test]
    public function testGenerateFallsBackToParamsPage(): void
    {
        $this->recordService->method('getActiveRecord')->willReturn(null);

        $pageData = [
            'tx_yoastseo_robots_noimageindex' => 0,
            'tx_yoastseo_robots_noarchive' => 0,
            'tx_yoastseo_robots_nosnippet' => 0,
            'no_index' => 1,
            'no_follow' => 0,
        ];

        $this->robotsRulesGenerator->expects(self::once())
            ->method('generateRules')
            ->willReturn('noindex,follow');

        $this->metaTagManager->expects(self::once())->method('removeRobotsTag');
        $this->metaTagManager->expects(self::once())->method('addRobotsTag')->with('noindex,follow');

        $this->subject->generate(['page' => $pageData]);
    }
}
