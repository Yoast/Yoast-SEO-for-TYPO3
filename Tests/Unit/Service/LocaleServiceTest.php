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
use TYPO3\CMS\Core\Localization\Locales;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\LocaleService;
use YoastSeoForTypo3\YoastSeo\Service\SiteService;

#[CoversClass(LocaleService::class)]
class LocaleServiceTest extends UnitTestCase
{
    protected Locales $locales;
    protected SiteService $siteService;
    protected LocaleService $subject;

    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();

        $this->locales = $this->createMock(Locales::class);
        $this->siteService = $this->createMock(SiteService::class);
        $this->subject = new LocaleService($this->locales, $this->siteService);
    }

    #[Test]
    public function testGetLanguageIdFromDataWithArray(): void
    {
        $data = ['databaseRow' => ['sys_language_uid' => [1]]];
        self::assertSame(1, $this->subject->getLanguageIdFromData($data));
    }

    #[Test]
    public function testGetLanguageIdFromDataWithInt(): void
    {
        $data = ['databaseRow' => ['sys_language_uid' => 2]];
        self::assertSame(2, $this->subject->getLanguageIdFromData($data));
    }

    #[Test]
    public function testGetLanguageIdFromDataWithMissingKey(): void
    {
        $data = ['databaseRow' => []];
        self::assertSame(0, $this->subject->getLanguageIdFromData($data));
    }

    #[Test]
    public function testGetLanguageIdFromDataWithMissingDatabaseRow(): void
    {
        $data = [];
        self::assertSame(0, $this->subject->getLanguageIdFromData($data));
    }

    #[Test]
    public function testGetSupportedLanguagesFromExtconf(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['supportedLanguages'] = [0 => 'en', 1 => 'de'];

        $result = $this->subject->getSupportedLanguages();

        self::assertSame([0 => 'en', 1 => 'de'], $result);

        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['supportedLanguages']);
    }

    #[Test]
    public function testGetSupportedLanguagesEmpty(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['supportedLanguages']);

        $result = $this->subject->getSupportedLanguages();

        self::assertSame([], $result);
    }
}
