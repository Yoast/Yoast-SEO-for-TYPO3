<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\EventListener;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\Event\ModifyUrlForCanonicalTagEvent;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\EventListener\RecordCanonicalListener;
use YoastSeoForTypo3\YoastSeo\Record\Record;
use YoastSeoForTypo3\YoastSeo\Record\RecordService;

#[CoversClass(RecordCanonicalListener::class)]
class RecordCanonicalListenerTest extends UnitTestCase
{
    protected RecordService $recordService;
    protected RecordCanonicalListener $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->recordService = $this->createMock(RecordService::class);
        $this->subject = new RecordCanonicalListener($this->recordService);
    }

    #[Test]
    public function testSetCanonicalWithNoActiveRecord(): void
    {
        $event = $this->createMock(ModifyUrlForCanonicalTagEvent::class);
        $this->recordService->method('getActiveRecord')->willReturn(null);

        $event->expects($this->never())->method('setUrl');

        $this->subject->setCanonical($event);
    }

    #[Test]
    public function testSetCanonicalWithNoCanonicalLink(): void
    {
        $event = $this->createMock(ModifyUrlForCanonicalTagEvent::class);
        $record = $this->createMock(Record::class);
        $record->method('getRecordData')->willReturn([]);

        $this->recordService->method('getActiveRecord')->willReturn($record);

        $event->expects($this->never())->method('setUrl');

        $this->subject->setCanonical($event);
    }

    #[Test]
    public function testSetCanonicalWithCanonicalLink(): void
    {
        $event = $this->createMock(ModifyUrlForCanonicalTagEvent::class);
        $record = $this->createMock(Record::class);
        $record->method('getRecordData')->willReturn(['canonical_link' => 'https://example.com']);

        $this->recordService->method('getActiveRecord')->willReturn($record);

        $GLOBALS['TSFE'] = $this->createMock(\stdClass::class);
        $GLOBALS['TSFE']->cObj = $this->createMock(ContentObjectRenderer::class);
        $GLOBALS['TSFE']->cObj->method('typoLink_URL')->willReturn('https://example.com');

        $event->expects($this->once())->method('setUrl')->with('https://example.com');

        $this->subject->setCanonical($event);
    }
}
