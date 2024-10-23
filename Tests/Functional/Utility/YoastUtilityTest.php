<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Utility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use YoastSeoForTypo3\YoastSeo\Tests\Functional\AbstractFunctionalTestCase;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

#[CoversClass(YoastUtility::class)]
class YoastUtilityTest extends AbstractFunctionalTestCase
{
    protected const DEFAULT_DOKTYPES = [1, 6];
    protected const EXTCONF_DOKTYPES = [2, 3];

    protected array $configurationToUseInTestInstance = [
        'EXTCONF' => [
            'yoast_seo' => [
                'allowedDoktypes' => self::EXTCONF_DOKTYPES,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);
    }

    #[Test]
    public function getAllowedDoktypesReturnsAllowedDoktypes(): void
    {
        $allowedDoktypes = YoastUtility::getAllowedDoktypes();

        self::assertSame(array_merge(self::DEFAULT_DOKTYPES, self::EXTCONF_DOKTYPES), $allowedDoktypes);
    }

    #[DataProvider('areTheRightDoktypesExtractedFromConfigurationDataProvider')]
    #[Test]
    public function areTheRightDoktypesExtractedFromConfiguration(array $inputArray, array $expected): void
    {
        $actual = YoastUtility::getAllowedDoktypes($inputArray);

        self::assertEquals($expected, $actual);
    }

    public static function areTheRightDoktypesExtractedFromConfigurationDataProvider(): array
    {
        return [
            'empty configuration array should return default and test EXTCONF allowedDoktypes' => [
                [],
                array_merge(self::DEFAULT_DOKTYPES, self::EXTCONF_DOKTYPES),
            ],
            'configuration array with doktypes 1 and 10 should not return doktype 1 more than once' => [
                [
                    'allowedDoktypes' => [
                        'page' => 1,
                        'custom' => 10,
                    ],
                ],
                array_merge(self::DEFAULT_DOKTYPES, self::EXTCONF_DOKTYPES, [10]),
            ],
            'configuration array with doktype 10 should return default and test EXTCONF allowedDoktypes plus doktype 10' => [
                [
                    'allowedDoktypes' => [
                        'custom' => 10,
                    ],
                ],
                array_merge(self::DEFAULT_DOKTYPES, self::EXTCONF_DOKTYPES, [10]),
            ],
            'configuration array with doktypes 1 (different key) and 6 should not return doktype 1 more than once' => [
                [
                    'allowedDoktypes' => [
                        'duplicateDoktype' => 1,
                        'custom' => 10,
                    ],
                ],
                array_merge(self::DEFAULT_DOKTYPES, self::EXTCONF_DOKTYPES, [10]),
            ],
        ];
    }
}
