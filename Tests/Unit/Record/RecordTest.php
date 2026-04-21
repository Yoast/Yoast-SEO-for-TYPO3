<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Record;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Record\Record;

#[CoversClass(Record::class)]
class RecordTest extends UnitTestCase
{
    protected Record $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Record();
    }

    #[Test]
    public function testDefaultValues(): void
    {
        self::assertSame('', $this->subject->getTableName());
        self::assertTrue($this->subject->hasDefaultSeoFields());
        self::assertTrue($this->subject->hasYoastSeoFields());
        self::assertTrue($this->subject->hasSitemapFields());
        self::assertSame('', $this->subject->getTypes());
        self::assertSame('title', $this->subject->getTitleField());
        self::assertSame('description', $this->subject->getDescriptionField());
        self::assertFalse($this->subject->shouldAddDescriptionField());
        self::assertSame('after:title', $this->subject->getFieldsPosition());
        self::assertSame([], $this->subject->getOverrideTca());
        self::assertSame([], $this->subject->getGetParameters());
        self::assertNull($this->subject->getRecordUid());
        self::assertSame([], $this->subject->getRecordData());
        self::assertTrue($this->subject->shouldGeneratePageTitle());
        self::assertTrue($this->subject->shouldGenerateMetaTags());
        self::assertTrue($this->subject->shouldGenerateRobotsTag());
    }

    #[Test]
    public function testFluentInterface(): void
    {
        self::assertSame($this->subject, $this->subject->setTableName('pages'));
        self::assertSame($this->subject, $this->subject->setDefaultSeoFields(false));
        self::assertSame($this->subject, $this->subject->setYoastSeoFields(false));
        self::assertSame($this->subject, $this->subject->setSitemapFields(false));
        self::assertSame($this->subject, $this->subject->setTypes('0,1'));
        self::assertSame($this->subject, $this->subject->setTitleField('name'));
        self::assertSame($this->subject, $this->subject->setDescriptionField('desc'));
        self::assertSame($this->subject, $this->subject->setAddDescriptionField(true));
        self::assertSame($this->subject, $this->subject->setFieldsPosition('before:title'));
        self::assertSame($this->subject, $this->subject->setOverrideTca(['foo' => 'bar']));
        self::assertSame($this->subject, $this->subject->setGetParameters(['tx_news' => ['news']]));
        self::assertSame($this->subject, $this->subject->setRecordUid(42));
        self::assertSame($this->subject, $this->subject->setRecordData(['uid' => 42]));
        self::assertSame($this->subject, $this->subject->setGeneratePageTitle(false));
        self::assertSame($this->subject, $this->subject->setGenerateMetaTags(false));
        self::assertSame($this->subject, $this->subject->setGenerateRobotsTag(false));
    }

    #[Test]
    public function testGetterSetterPairs(): void
    {
        $this->subject->setTableName('tt_content');
        self::assertSame('tt_content', $this->subject->getTableName());

        $this->subject->setDefaultSeoFields(false);
        self::assertFalse($this->subject->hasDefaultSeoFields());

        $this->subject->setYoastSeoFields(false);
        self::assertFalse($this->subject->hasYoastSeoFields());

        $this->subject->setSitemapFields(false);
        self::assertFalse($this->subject->hasSitemapFields());

        $this->subject->setTypes('0,1,2');
        self::assertSame('0,1,2', $this->subject->getTypes());

        $this->subject->setTitleField('name');
        self::assertSame('name', $this->subject->getTitleField());

        $this->subject->setDescriptionField('summary');
        self::assertSame('summary', $this->subject->getDescriptionField());

        $this->subject->setAddDescriptionField(true);
        self::assertTrue($this->subject->shouldAddDescriptionField());

        $this->subject->setFieldsPosition('before:title');
        self::assertSame('before:title', $this->subject->getFieldsPosition());

        $overrideTca = ['columns' => ['title' => ['config' => ['type' => 'input']]]];
        $this->subject->setOverrideTca($overrideTca);
        self::assertSame($overrideTca, $this->subject->getOverrideTca());

        $getParameters = [['tx_news_pi1', 'news']];
        $this->subject->setGetParameters($getParameters);
        self::assertSame($getParameters, $this->subject->getGetParameters());

        $this->subject->setRecordUid(99);
        self::assertSame(99, $this->subject->getRecordUid());

        $recordData = ['uid' => 99, 'title' => 'Test'];
        $this->subject->setRecordData($recordData);
        self::assertSame($recordData, $this->subject->getRecordData());

        $this->subject->setGeneratePageTitle(false);
        self::assertFalse($this->subject->shouldGeneratePageTitle());

        $this->subject->setGenerateMetaTags(false);
        self::assertFalse($this->subject->shouldGenerateMetaTags());

        $this->subject->setGenerateRobotsTag(false);
        self::assertFalse($this->subject->shouldGenerateRobotsTag());
    }
}
