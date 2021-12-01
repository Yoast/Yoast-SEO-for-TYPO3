<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Utility;

/*
 * This file is part of the "Yoast SEO for TYPO3" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Utility\JsonConfigUtility;

class JsonConfigUtilityTest extends UnitTestCase
{
    /**
     * Check if the JsonUtility returns the correctly generated JSON
     *
     * @param array $configuration
     * @param array $configurationOverride
     * @param string $expected
     *
     * @dataProvider isConfigurationGeneratedCorrectlyDataProvider
     * @test
     */
    public function isConfigurationGeneratedCorrectly(array $configuration, array $configurationOverride, string $expected): void
    {
        $jsonConfigUtility = new JsonConfigUtility();
        $jsonConfigUtility->addConfig($configuration);
        $jsonConfigUtility->addConfig($configurationOverride);

        $this->assertEquals(
            $expected,
            $jsonConfigUtility->render()
        );
    }

    /**
     * @return array
     */
    public function isConfigurationGeneratedCorrectlyDataProvider(): array
    {
        return [
            [
                [
                    'data' => [
                        'table' => 'pages'
                    ],
                ],
                [],
                'const YoastConfig = {"data":{"table":"pages"}};'
            ],
            [
                [
                    'data' => [
                        'table' => 'pages'
                    ],
                ],
                [
                    'data' => [
                        'table' => 'tt_content'
                    ],
                ],
                'const YoastConfig = {"data":{"table":"tt_content"}};'
            ],
            [
                [
                    'data' => [
                        'table' => 'pages'
                    ],
                ],
                [
                    'data' => [
                        'table' => 'tt_content',
                        'uid' => ''
                    ],
                ],
                'const YoastConfig = {"data":{"table":"tt_content"}};'
            ]
        ];
    }
}
