<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\SiteService;

#[CoversClass(SiteService::class)]
class SiteServiceTest extends UnitTestCase
{
    protected SiteFinder $siteFinder;
    protected SiteService $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->siteFinder = $this->createMock(SiteFinder::class);
        $this->subject = new SiteService($this->siteFinder);
    }

    #[Test]
    public function testGetSiteByPageIdReturnsSite(): void
    {
        $site = $this->createMock(Site::class);
        $this->siteFinder->method('getSiteByPageId')->with(1)->willReturn($site);

        self::assertSame($site, $this->subject->getSiteByPageId(1));
    }

    #[Test]
    public function testGetSiteByPageIdCachesSite(): void
    {
        $site = $this->createMock(Site::class);
        $this->siteFinder->expects(self::once())->method('getSiteByPageId')->with(1)->willReturn($site);

        $this->subject->getSiteByPageId(1);
        $result = $this->subject->getSiteByPageId(1);

        self::assertSame($site, $result);
    }

    #[Test]
    public function testGetSiteByPageIdReturnsNullOnException(): void
    {
        $this->siteFinder->method('getSiteByPageId')->willThrowException(new SiteNotFoundException('not found'));

        self::assertNull($this->subject->getSiteByPageId(999));
    }

    #[Test]
    public function testGetSiteRootPageIdReturnsRootPageId(): void
    {
        $site = $this->createMock(Site::class);
        $site->method('getRootPageId')->willReturn(1);
        $this->siteFinder->method('getSiteByPageId')->willReturn($site);

        self::assertSame(1, $this->subject->getSiteRootPageId(5));
    }

    #[Test]
    public function testGetSiteRootPageIdReturnsZeroWhenNoSite(): void
    {
        $this->siteFinder->method('getSiteByPageId')->willThrowException(new SiteNotFoundException('not found'));

        self::assertSame(0, $this->subject->getSiteRootPageId(999));
    }

    #[Test]
    public function testGetWebsiteTitleReturnsLanguageTitle(): void
    {
        $siteLanguage = $this->createMock(SiteLanguage::class);
        $siteLanguage->method('getWebsiteTitle')->willReturn('My Site DE');

        $site = $this->createMock(Site::class);
        $site->method('getLanguageById')->with(1)->willReturn($siteLanguage);

        $this->siteFinder->method('getSiteByPageId')->willReturn($site);

        self::assertSame('My Site DE', $this->subject->getWebsiteTitle(1, 1));
    }

    #[Test]
    public function testGetWebsiteTitleFallsBackToSiteConfig(): void
    {
        $siteLanguage = $this->createMock(SiteLanguage::class);
        $siteLanguage->method('getWebsiteTitle')->willReturn('');

        $site = $this->createMock(Site::class);
        $site->method('getLanguageById')->with(0)->willReturn($siteLanguage);
        $site->method('getConfiguration')->willReturn(['websiteTitle' => 'Fallback Title']);

        $this->siteFinder->method('getSiteByPageId')->willReturn($site);

        self::assertSame('Fallback Title', $this->subject->getWebsiteTitle(1, 0));
    }

    #[Test]
    public function testGetWebsiteTitleReturnsEmptyWhenNoSite(): void
    {
        $this->siteFinder->method('getSiteByPageId')->willThrowException(new SiteNotFoundException('not found'));

        self::assertSame('', $this->subject->getWebsiteTitle(999, 0));
    }
}
