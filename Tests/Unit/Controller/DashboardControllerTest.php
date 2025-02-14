<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Controller\DashboardController;

#[CoversClass(DashboardController::class)]
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

    #[Test]
    public function isActionController(): void
    {
        self::assertInstanceOf(ActionController::class, $this->subject);
    }

    #[Test]
    public function indexActionReturnsHtmlResponse(): void
    {
        $serverRequest = (new ServerRequest())->withAttribute('extbase', new ExtbaseRequestParameters());
        $this->subject->_set('request', new Request($serverRequest));
        $result = $this->subject->indexAction();

        self::assertInstanceOf(HtmlResponse::class, $result);
    }
}
