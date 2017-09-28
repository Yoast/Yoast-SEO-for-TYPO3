<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit;

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
 * @package YoastSeoForTypo3\YoastSeo\Tests\Unit
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
     * @dataProvider doktypeConfigurationDataProvider
     * @test
     */
    public function areTheRightDoktypesExtractedFromConfiguration($inputArray, $expected)
    {
        $result = YoastUtility::getAllowedDoktypes($inputArray);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function doktypeConfigurationDataProvider()
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
                [1, 6]
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
}
