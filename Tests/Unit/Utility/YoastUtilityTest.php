<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class YoastUtilityTest
 */
class YoastUtilityTest extends UnitTestCase
{
    /**
     * Check if the YoastUtility::getAllowedDoktypes method returns the right doktypes from the extension
     * configuration.
     *
     * @param array $inputArray
     * @param array $expected
     *
     * @dataProvider areTheRightDoktypesExtractedFromConfigurationDataProvider
     * @test
     */
    public function areTheRightDoktypesExtractedFromConfiguration(array $inputArray, array $expected): void
    {
        $actual = YoastUtility::getAllowedDoktypes($inputArray);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the YoastUtility::getAllowedDoktypes method returns the correct doktypes from the EXTCONF array
     * and configuration array
     *
     * @param array $extconfArray
     * @param array $configurationArray
     * @param array $expected
     *
     * @dataProvider areTheRightDoktypesExtractedFromExtConfDataProvider
     * @test
     */
    public function areTheRightDoktypesExtractedFromExtConf(array $extconfArray, array $configurationArray, array $expected): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes'] = $extconfArray;

        $actual = YoastUtility::getAllowedDoktypes($configurationArray);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the YoastUtility::snippetPreviewEnabled method returns the correct result based on Page TSConfig
     *
     * @param int $pageId
     * @param array $config
     * @param bool $expected
     *
     * @dataProvider isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfiguration(int $pageId, array $config, bool $expected): void
    {
        $this->setValidBackendUser();

        $actual = YoastUtility::snippetPreviewEnabled($pageId, ['tx_yoastseo_hide_snippet_preview' => false], $config);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the YoastUtility::snippetPreviewEnabled method returns the correct result based on page record
     *
     * @param int $pageId
     * @param array $pageRecord
     * @param bool $expected
     *
     * @dataProvider isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecord(int $pageId, array $pageRecord, bool $expected): void
    {
        $this->setValidBackendUser();

        $actual = YoastUtility::snippetPreviewEnabled($pageId, $pageRecord, []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Check if the YoastUtility::snippetPreviewEnabled returns false if pages:tx_yoastseo_snippetpreview is not within
     * the non_exclude_fields of the backend user
     *
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyWithExcludeFields(): void
    {
        $GLOBALS['BE_USER'] = new BackendUserAuthentication();
        $GLOBALS['BE_USER']->groupData['non_exclude_fields'] = '';

        $actual = YoastUtility::snippetPreviewEnabled(1, [], []);

        $this->assertEquals($actual, false);
    }

    /**
     * Check if the YoastUtility::snippetPreviewEnabled returns false if the hideYoastInPageModule is true within
     * the user settings of the backend user
     *
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyWithUserSettings(): void
    {
        $this->setValidBackendUser();
        $GLOBALS['BE_USER']->uc['hideYoastInPageModule'] = true;

        $actual = YoastUtility::snippetPreviewEnabled(1, [], []);

        $this->assertEquals($actual, false);
    }

    /**
     * Check if the YoastUtility::isPremiumInstalled returns false
     *
     * @test
     */
    public function isPremiumInstalledAlwaysFalse(): void
    {
        $this->assertEquals(YoastUtility::isPremiumInstalled(), false);
    }

    /**
     * Check if the YoastUtility::inProductionMode method returns the correct result based on the
     * environment variable and typoscript configuration
     *
     * @param int $environmentVariable
     * @param int $configurationMode
     * @param bool $expected
     *
     * @dataProvider isProductionModeCorrectlySetDataProvider
     * @test
     */
    public function isProductionModeCorrectlySet(int $environmentVariable, int $configurationMode, bool $expected): void
    {
        $_ENV['YOAST_DEVELOPMENT_MODE'] = $environmentVariable;
        $configuration = ['developmentMode' => $configurationMode];

        $this->assertEquals(YoastUtility::inProductionMode($configuration), $expected);
    }

    /**
     * Check if the YoastUtility::fixAbsoluteUrl method returns the correct result based on the
     * supplied url
     *
     * @param string $ssl
     * @param string $host
     * @param string $url
     * @param string $expected
     *
     * @dataProvider isRelativeUrlCorrectlyChangedToAbsoluteUrlDataProvider
     * @test
     */
    public function isRelativeUrlCorrectlyChangedToAbsoluteUrl(string $ssl, string $host, string $url, string $expected): void
    {
        GeneralUtility::flushInternalRuntimeCaches();
        $_SERVER['HTTPS'] = $ssl;
        $_SERVER['HTTP_HOST'] = $host;

        $this->assertEquals(YoastUtility::fixAbsoluteUrl($url), $expected);
    }

    /**
     * Dataprovider for areTheRightDoktypesExtractedFromConfiguration test method
     *
     * @return array
     */
    public function areTheRightDoktypesExtractedFromConfigurationDataProvider(): array
    {
        return [
            [
                [],
                [1]
            ],
            [
                [
                    'allowedDoktypes' => [
                        'page' => 1,
                        'backend_user_section' => 6
                    ]
                ],
                [1, 6]
            ],
            [
                [
                    'allowedDoktypes' => [
                        'backend_user_section' => 6
                    ]
                ],
                [6]
            ],
            [
                [
                    'allowedDoktypes' => [
                        'duplicateDoktype' => 1,
                        'backend_user_section' => 6
                    ]
                ],
                [1, 6]
            ],
        ];
    }

    /**
     * Dataprovider for areTheRightDoktypesExtractedFromExtConf test method
     *
     * @return array
     */
    public function areTheRightDoktypesExtractedFromExtConfDataProvider(): array
    {
        return [
            [
                [
                    'page' => 1,
                ],
                [
                    'allowedDoktypes' => [
                        'backend_user_section' => 6
                    ]
                ],
                [1, 6]
            ],
            [
                [
                    'page' => 1,
                    'backend_user_section' => 6
                ],
                [
                    'allowedDoktypes' => [
                        'page' => 1,
                        'backend_user_section' => 6
                    ]
                ],
                [1, 6]
            ],
        ];
    }

    /**
     * @return array
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider(): array
    {
        return [
            [
                1,
                [],
                true
            ],
            [
                1,
                [
                    'mod.' => [
                        'web_SeoPlugin.' => [
                            'disableSnippetPreview' => 0
                        ]
                    ]
                ],
                true
            ],
            [
                1,
                [
                    'mod.' => [
                        'web_SeoPlugin.' => [
                            'disableSnippetPreview' => 1
                        ]
                    ]
                ],
                false
            ]
        ];
    }

    /**
     * @return array
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider(): array
    {
        return [
            [
                1,
                [],
                true
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '0'],
                true
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => false],
                true
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => '1'],
                false
            ],
            [
                1,
                ['tx_yoastseo_hide_snippet_preview' => true],
                false
            ],
        ];
    }

    /**
     * @return array
     */
    public function isProductionModeCorrectlySetDataProvider(): array
    {
        return [
            [
                0,
                0,
                true
            ],
            [
                1,
                0,
                false
            ],
            [
                0,
                1,
                false
            ],
            [
                1,
                1,
                false
            ]
        ];
    }

    /**
     * @return array
     */
    public function isRelativeUrlCorrectlyChangedToAbsoluteUrlDataProvider(): array
    {
        return [
            [
                'on',
                'example.com',
                '/',
                'https://example.com/'
            ],
            [
                'off',
                'example.com',
                '/',
                'http://example.com/'
            ],
            [
                'on',
                'example.com',
                '/test',
                'https://example.com/test'
            ],
            [
                'on',
                'example.com',
                'http://example.com/',
                'http://example.com/'
            ],
            [
                'off',
                'example.com',
                'https://example.com/',
                'https://example.com/'
            ]
        ];
    }

    protected function setValidBackendUser(): void
    {
        if (!$GLOBALS['BE_USER'] instanceof BackendUserAuthentication) {
            $GLOBALS['BE_USER'] = new BackendUserAuthentication();
            $GLOBALS['BE_USER']->groupData = ['non_exclude_fields' => 'pages:tx_yoastseo_snippetpreview'];
        }
    }
}
