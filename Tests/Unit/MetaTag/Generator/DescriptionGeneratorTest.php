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
use YoastSeoForTypo3\YoastSeo\MetaTag\Generator\DescriptionGenerator;
use YoastSeoForTypo3\YoastSeo\Record\Record;

#[CoversClass(DescriptionGenerator::class)]
class DescriptionGeneratorTest extends UnitTestCase
{
    protected MetaTagManagerRegistry $managerRegistry;
    protected MetaTagManagerInterface $metaTagManager;
    protected DescriptionGenerator $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->metaTagManager = $this->createMock(MetaTagManagerInterface::class);
        $this->managerRegistry = $this->createMock(MetaTagManagerRegistry::class);
        $this->managerRegistry->method('getManagerForProperty')->with('description')->willReturn($this->metaTagManager);
        $this->subject = new DescriptionGenerator($this->managerRegistry);
    }

    #[Test]
    public function testGenerateSetsDescriptionWhenPresent(): void
    {
        $record = (new Record())->setRecordData(['description' => 'My SEO description']);

        $this->metaTagManager->expects(self::once())->method('removeProperty')->with('description');
        $this->metaTagManager->expects(self::once())->method('addProperty')->with('description', 'My SEO description');

        $this->subject->generate($record);
    }

    #[Test]
    public function testGenerateSkipsWhenEmpty(): void
    {
        $record = (new Record())->setRecordData(['description' => '']);

        $this->metaTagManager->expects(self::never())->method('removeProperty');
        $this->metaTagManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }

    #[Test]
    public function testGenerateSkipsWhenFieldMissing(): void
    {
        $record = (new Record())->setRecordData([]);

        $this->metaTagManager->expects(self::never())->method('removeProperty');
        $this->metaTagManager->expects(self::never())->method('addProperty');

        $this->subject->generate($record);
    }

    #[Test]
    public function testGenerateUsesCustomDescriptionField(): void
    {
        $record = (new Record())
            ->setDescriptionField('my_desc')
            ->setRecordData(['my_desc' => 'Custom description']);

        $this->metaTagManager->expects(self::once())->method('removeProperty')->with('description');
        $this->metaTagManager->expects(self::once())->method('addProperty')->with('description', 'Custom description');

        $this->subject->generate($record);
    }
}
