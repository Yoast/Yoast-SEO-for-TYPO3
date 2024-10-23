<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Controller;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Controller\CrawlerController;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerJavascriptConfigService;
use YoastSeoForTypo3\YoastSeo\Service\Crawler\CrawlerService;

#[CoversClass(CrawlerController::class)]
final class CrawlerControllerTest extends UnitTestCase
{
    private CrawlerController $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
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

        $uriBuilder = $this->createMock(UriBuilder::class);
        $this->subject->_set('uriBuilder', $uriBuilder);

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
        $result = $this->subject->indexAction();

        self::assertInstanceOf(HtmlResponse::class, $result);
    }

    #[Test]
    public function resetProgressActionReturnsRedirectResponse(): void
    {
        $result = $this->subject->resetProgressAction(1, 1);

        self::assertInstanceOf(RedirectResponse::class, $result);
    }
}
