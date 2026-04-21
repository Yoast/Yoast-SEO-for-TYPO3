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
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\TwitterGenerator;
use YoastSeoForTypo3\YoastSeo\Record\Record;

#[CoversClass(TwitterGenerator::class)]
class TwitterGeneratorTest extends UnitTestCase
{
    protected MetaTagManagerRegistry $managerRegistry;
    protected MetaTagManagerInterface $cardManager;
    protected MetaTagManagerInterface $titleManager;
    protected MetaTagManagerInterface $descriptionManager;
    protected TwitterGenerator $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->cardManager = $this->createMock(MetaTagManagerInterface::class);
        $this->titleManager = $this->createMock(MetaTagManagerInterface::class);
        $this->descriptionManager = $this->createMock(MetaTagManagerInterface::class);

        $this->managerRegistry = $this->createMock(MetaTagManagerRegistry::class);
        $this->managerRegistry->method('getManagerForProperty')->willReturnMap([
            ['twitter:card', $this->cardManager],
            ['twitter:title', $this->titleManager],
            ['twitter:description', $this->descriptionManager],
        ]);

        $this->subject = new TwitterGenerator($this->managerRegistry);
    }

    #[Test]
    public function testSetsTwitterCardWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['twitter_card' => 'summary_large_image']);

        $this->cardManager->expects(self::once())->method('removeProperty')->with('twitter:card');
        $this->cardManager->expects(self::once())->method('addProperty')->with('twitter:card', 'summary_large_image');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSkipsTwitterCardWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['twitter_card' => '']);

        $this->cardManager->expects(self::never())->method('removeProperty');
        $this->cardManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSetsTwitterTitleWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['twitter_title' => 'Twitter Title']);

        $this->titleManager->expects(self::once())->method('removeProperty')->with('twitter:title');
        $this->titleManager->expects(self::once())->method('addProperty')->with('twitter:title', 'Twitter Title');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSkipsTwitterTitleWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['twitter_title' => '']);

        $this->titleManager->expects(self::never())->method('removeProperty');
        $this->titleManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSetsTwitterDescriptionWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['twitter_description' => 'Twitter Desc']);

        $this->descriptionManager->expects(self::once())->method('removeProperty')->with('twitter:description');
        $this->descriptionManager->expects(self::once())->method('addProperty')->with('twitter:description', 'Twitter Desc');

        $this->subject->generate($record);
    }

    #[Test]
    public function testSkipsTwitterDescriptionWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['twitter_description' => '']);

        $this->descriptionManager->expects(self::never())->method('removeProperty');
        $this->descriptionManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }
}
