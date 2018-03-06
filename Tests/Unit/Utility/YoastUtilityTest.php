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
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

/**
 * Class YoastUtilityTest
 * @package YoastSeoForTypo3\YoastSeo\Tests\Unit\Utility
 */
class YoastUtilityTest extends UnitTestCase
{

    /**
     * ###############################
     *
     * TESTS
     *
     * ###############################
     */

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
    public function areTheRightDoktypesExtractedFromConfiguration($inputArray, $expected)
    {
        $actual = YoastUtility::getAllowedDoktypes($inputArray);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfiguration($pageId, $config, $expected)
    {
        $actual = YoastUtility::snippetPreviewEnabled($pageId, ['tx_yoastseo_dont_use' => false], $config);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider
     * @test
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecord($pageId, $pageRecord, $expected)
    {
        $actual = YoastUtility::snippetPreviewEnabled($pageId, $pageRecord, []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * ###############################
     *
     * DATAPROVIDERS
     *
     * ###############################
     */

    /**
     * Dataprovider for areTheRightDoktypesExtractedFromConfiguration test method
     *
     * @return array
     */
    public function areTheRightDoktypesExtractedFromConfigurationDataProvider()
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
     * @return array
     */
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageTsConfigurationDataProvider()
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
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageRecordDataProvider()
    {
        return [
            [
                1,
                [],
                true
            ],
            [
                1,
                ['tx_yoastseo_dont_use' => '0'],
                true
            ],
            [
                1,
                ['tx_yoastseo_dont_use' => false],
                true
            ],
            [
                1,
                ['tx_yoastseo_dont_use' => '1'],
                false
            ],
            [
                1,
                ['tx_yoastseo_dont_use' => true],
                false
            ],
        ];
    }
}
