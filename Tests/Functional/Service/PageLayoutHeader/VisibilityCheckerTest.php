<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional\Service\PageLayoutHeader;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use YoastSeoForTypo3\YoastSeo\Service\PageLayoutHeader\VisibilityChecker;

#[CoversClass(VisibilityChecker::class)]
class VisibilityCheckerTest extends FunctionalTestCase
{
    /**
     * @var VisibilityChecker&MockObject&AccessibleObjectInterface
     */
    protected VisibilityChecker $subject;

    protected array $configurationToUseInTestInstance = [
        'EXTCONF' => [
            'yoast_seo' => [
                'allowedDoktypes' => [1, 6],
            ],
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);
    }

    #[Test]
    #[DataProvider('isSnippetPreviewEnabledCorrectlyBasedOnPageDoktypeDataProvider')]
    public function isSnippetPreviewEnabledCorrectlyBasedOnPageDoktype(int $doktype, bool $expected): void
    {
        $visibilityChecker = new VisibilityChecker();
        self::assertEquals($expected, $visibilityChecker->shouldShowPreview(1, ['doktype' => $doktype]));
    }

    public static function isSnippetPreviewEnabledCorrectlyBasedOnPageDoktypeDataProvider(): array
    {
        return [
            'doktype 1 should return true' => [
                1,
                true,
            ],
            'doktype 6 should return true' => [
                6,
                true,
            ],
            'doktype 2 should return false' => [
                2,
                false,
            ],
        ];
    }
}
