<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\MetaTag\Generator;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerInterface;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\OpenGraphGenerator;
use YoastSeoForTypo3\YoastSeo\Record\Record;

#[CoversClass(OpenGraphGenerator::class)]
class OpenGraphGeneratorTest extends UnitTestCase
{
    protected MetaTagManagerRegistry $managerRegistry;
    protected MetaTagManagerInterface $ogTitleManager;
    protected MetaTagManagerInterface $ogDescriptionManager;
    protected OpenGraphGenerator $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->ogTitleManager = $this->createMock(MetaTagManagerInterface::class);
        $this->ogDescriptionManager = $this->createMock(MetaTagManagerInterface::class);

        $this->managerRegistry = $this->createMock(MetaTagManagerRegistry::class);
        $this->managerRegistry->method('getManagerForProperty')->willReturnMap([
            ['og:title', $this->ogTitleManager],
            ['og:description', $this->ogDescriptionManager],
        ]);

        $this->subject = new OpenGraphGenerator($this->managerRegistry);
    }

    #[Test]
    public function testSetsOgTitleWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['og_title' => 'OG Title']);

        $this->ogTitleManager->expects(self::once())->method('removeProperty')->with('og:title');
        $this->ogTitleManager->expects(self::once())->method('addProperty')->with('og:title', 'OG Title');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSkipsOgTitleWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['og_title' => '']);

        $this->ogTitleManager->expects(self::never())->method('removeProperty');
        $this->ogTitleManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSetsOgDescriptionWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['og_description' => 'OG Description']);

        $this->ogDescriptionManager->expects(self::once())->method('removeProperty')->with('og:description');
        $this->ogDescriptionManager->expects(self::once())->method('addProperty')->with('og:description', 'OG Description');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSkipsOgDescriptionWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['og_description' => '']);

        $this->ogDescriptionManager->expects(self::never())->method('removeProperty');
        $this->ogDescriptionManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }
}
