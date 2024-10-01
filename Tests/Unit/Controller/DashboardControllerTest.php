<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Controller\DashboardController;

/**
 * @covers \YoastSeoForTypo3\YoastSeo\Controller\DashboardController
 */
final class DashboardControllerTest extends UnitTestCase
{
    private DashboardController $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            DashboardController::class,
            ['returnResponse'],
            [],
            '',
            false
        );

        $responseStub = $this->createStub(HtmlResponse::class);
        $this->subject->method('returnResponse')->willReturn($responseStub);
    }

    /**
     * @test
     */
    public function isActionController(): void
    {
        self::assertInstanceOf(ActionController::class, $this->subject);
    }

    /**
     * @test
     */
    public function indexActionReturnsHtmlResponse(): void
    {
        $result = $this->subject->indexAction();

        self::assertInstanceOf(HtmlResponse::class, $result);
    }
}
