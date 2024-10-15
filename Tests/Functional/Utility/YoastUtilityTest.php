<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Utility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use YoastSeoForTypo3\YoastSeo\Utility\YoastUtility;

#[CoversClass(YoastUtility::class)]
class YoastUtilityTest extends FunctionalTestCase
{
    protected const EXTCONF_DOKTYPES = [1, 2, 3];

    protected array $configurationToUseInTestInstance = [
        'EXTCONF' => [
            'yoast_seo' => [
                'allowedDoktypes' => self::EXTCONF_DOKTYPES,
            ],
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);
    }

    #[Test]
    public function getAllowedDoktypesReturnsAllowedDoktypes(): void
    {
        $allowedDoktypes = YoastUtility::getAllowedDoktypes();

        self::assertSame(self::EXTCONF_DOKTYPES, $allowedDoktypes);
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
            'empty configuration array should return EXTCONF allowedDoktypes' => [
                [],
                self::EXTCONF_DOKTYPES,
            ],
            'configuration array with doktypes 1 and 6 should not return doktype 1 more than once' => [
                [
                    'allowedDoktypes' => [
                        'page' => 1,
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::EXTCONF_DOKTYPES, [6]),
            ],
            'configuration array with doktype 6 should return EXTCONF allowedDoktypes plus doktype 6' => [
                [
                    'allowedDoktypes' => [
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::EXTCONF_DOKTYPES, [6]),
            ],
            'configuration array with doktypes 1 (different key) and 6 should not return doktype 1 more than once' => [
                [
                    'allowedDoktypes' => [
                        'duplicateDoktype' => 1,
                        'backend_user_section' => 6,
                    ],
                ],
                array_merge(self::EXTCONF_DOKTYPES, [6]),
            ],
        ];
    }
}
