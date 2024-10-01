<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Utility;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * @covers \YoastSeoForTypo3\YoastSeo\Utility\YoastUtility
 */
class YoastUtilityTest extends FunctionalTestCase
{
    protected const DOKTYPES_FROM_CONFIGURATION = [1, 2, 3];

    protected array $configurationToUseInTestInstance = [
        'EXTCONF' => [
            'yoast_seo' => [
                'allowedDoktypes' => self::DOKTYPES_FROM_CONFIGURATION,
            ],
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);
        Bootstrap::initializeLanguageObject();
    }

    #[Test]
    public function getAllowedDoktypesReturnsAllowedDoktypes(): void
    {
        $allowedDoktypes = YoastUtility::getAllowedDoktypes();

        self::assertSame(self::DOKTYPES_FROM_CONFIGURATION, $allowedDoktypes);
    }

    #[DataProvider('areTheRightDoktypesExtractedFromConfigurationDataProvider')]
    #[Test]
    public function areTheRightDoktypesExtractedFromConfiguration(array $inputArray, array $expected): void
    {
        $actual = YoastUtility::getAllowedDoktypes($inputArray);

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider')]
    #[Test]
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfiguration(int $pageId, array $config, bool $expected): void
    {
        $actual = YoastUtility::snippetPreviewEnabled($pageId, ['tx_yoastseo_hide_snippet_preview' => false], $config);

        self::assertEquals($expected, $actual);
    }

    #[DataProvider('isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider')]
    #[Test]
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecord(int $pageId, array $pageRecord, bool $expected): void
    {
        $actual = YoastUtility::snippetPreviewEnabled($pageId, $pageRecord, []);

        self::assertEquals($expected, $actual);
    }

    public static function areTheRightDoktypesExtractedFromConfigurationDataProvider(): array
    {
        return [
            [
                [],
                self::DOKTYPES_FROM_CONFIGURATION,
            ],
            [
                [
                    'allowedDoktypes' => [
                        'page' => 1,
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::DOKTYPES_FROM_CONFIGURATION, [6]),
            ],
            [
                [
                    'allowedDoktypes' => [
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::DOKTYPES_FROM_CONFIGURATION, [6]),
            ],
            [
                [
                    'allowedDoktypes' => [
                        'duplicateDoktype' => 1,
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::DOKTYPES_FROM_CONFIGURATION, [6]),
            ],
        ];
    }

    public static function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider(): array
    {
        return [
            [
                1,
                [],
                true,
            ],
            [
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
            [
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
            [
                1,
                [],
                true,
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '0'],
                true,
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => false],
                true,
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '1'],
                false,
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => true],
                false,
            ],
        ];
    }
}
