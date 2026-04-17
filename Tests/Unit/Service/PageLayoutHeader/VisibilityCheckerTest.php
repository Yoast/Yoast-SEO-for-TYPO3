<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service\PageLayoutHeader;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\VisibilityChecker;

#[CoversClass(VisibilityChecker::class)]
class VisibilityCheckerTest extends UnitTestCase
{
    /**
     * @var VisibilityChecker&MockObject&AccessibleObjectInterface
     */
    protected VisibilityChecker $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $backendUser = $this->getAccessibleMock(BackendUserAuthentication::class, ['check']);
        $backendUser->method('check')->willReturn(true);

        $this->subject = $this->getAccessibleMock(
            VisibilityChecker::class,
            ['getBackendUser', 'getPageTsConfig'],
        );
        $this->subject->method('getBackendUser')
            ->willReturn($backendUser);
    }

    #[DataProvider('isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider')]
    #[Test]
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfiguration(int $pageId, array $config, bool $expected): void
    {
        $this->subject->method('getPageTsConfig')->willReturn($config);
        $actual = $this->subject->_call('isSnippetPreviewEnabled', $pageId, []);

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider')]
    #[Test]
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecord(int $pageId, array $pageRecord, bool $expected): void
    {
        $this->subject->method('getPageTsConfig')->willReturn([]);
        $actual = $this->subject->_call('isSnippetPreviewEnabled', $pageId, $pageRecord);

        self::assertEquals($expected, $actual);
    }

    public static function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider(): array
    {
        return [
            'empty tsconfig should show snippet preview' => [
                1,
                [],
                true,
            ],
            'disableSnippetPreview set to 0 should show snippet preview' => [
                1,
                [
                    'mod.' => [
                        'web_SeoPlugin.' => [
                            'disableSnippetPreview' => 0,
                        ],
                    ],
                ],
                true,
            ],
            'disableSnippetPreview set to 1 should not show snippet preview' => [
                1,
                [
                    'mod.' => [
                        'web_SeoPlugin.' => [
                            'disableSnippetPreview' => 1,
                        ],
                    ],
                ],
                false,
            ],
        ];
    }

    public static function isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider(): array
    {
        return [
            'page record without tx_yoastseo_hide_snippet_preview should show snippet preview' => [
                1,
                [],
                true,
            ],
            'tx_yoastseo_hide_snippet_preview set to 0 should show snippet preview' => [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '0'],
                true,
            ],
            'tx_yoastseo_hide_snippet_preview set to false should show snippet preview' => [
                1,
                ['tx_yoastseo_hide_snippet_preview' => false],
                true,
            ],
            'tx_yoastseo_hide_snippet_preview set to 1 should not show snippet preview' => [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '1'],
                false,
            ],
            'tx_yoastseo_hide_snippet_preview set to true should not show snippet preview' => [
                1,
                ['tx_yoastseo_hide_snippet_preview' => true],
                false,
            ],
        ];
    }

    #[DataProvider('isAnalysisEnabledCorrectlyBasedOnPageRecordDataProvider')]
    #[Test]
    public function isAnalysisEnabledCorrectlyBasedOnPageRecord(array $pageRecord, bool $expected): void
    {
        $actual = $this->subject->isAnalysisEnabled($pageRecord);

        self::assertEquals($expected, $actual);
    }

    public static function isAnalysisEnabledCorrectlyBasedOnPageRecordDataProvider(): array
    {
        return [
            'page record without tx_yoastseo_disable_analysis should enable analysis' => [
                [],
                true,
            ],
            'tx_yoastseo_disable_analysis set to 0 should enable analysis' => [
                ['tx_yoastseo_disable_analysis' => '0'],
                true,
            ],
            'tx_yoastseo_disable_analysis set to false should enable analysis' => [
                ['tx_yoastseo_disable_analysis' => false],
                true,
            ],
            'tx_yoastseo_disable_analysis set to 1 should disable analysis' => [
                ['tx_yoastseo_disable_analysis' => '1'],
                false,
            ],
            'tx_yoastseo_disable_analysis set to true should disable analysis' => [
                ['tx_yoastseo_disable_analysis' => true],
                false,
            ],
        ];
    }
}
