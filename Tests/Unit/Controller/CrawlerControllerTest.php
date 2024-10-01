<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Controller\CrawlerController;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerJavascriptConfigService;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;

/**
 * @covers \YoastSeoForTypo3\YoastSeo\Controller\CrawlerController
 */
final class CrawlerControllerTest extends UnitTestCase
{
    private CrawlerController $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            CrawlerController::class,
            ['returnResponse'],
            [],
            '',
            false
        );

        $crawlerService = $this->createMock(CrawlerService::class);
        $this->subject->_set('crawlerService', $crawlerService);

        $crawlerJavascriptConfigService = $this->createMock(CrawlerJavascriptConfigService::class);
        $this->subject->_set('crawlerJavascriptConfigService', $crawlerJavascriptConfigService);

        $siteFinder = $this->createMock(SiteFinder::class);
        $this->subject->_set('siteFinder', $siteFinder);

        $request = $this->createMock(Request::class);
        $this->subject->_set('request', $request);

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

    /**
     * @test
     */
    public function resetProgressActionReturnsRedirectResponse(): void
    {
        $result = $this->subject->resetProgressAction(1, 1);

        self::assertInstanceOf(RedirectResponse::class, $result);
    }
}
