<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\MetaTag\AdvancedRobots;

use DG\BypassFinals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\MetaTag\AdvancedRobots\RobotsRulesGenerator;

#[CoversClass(RobotsRulesGenerator::class)]
class RobotsRulesGeneratorTest extends UnitTestCase
{
    protected function setUp(): void
    {
        BypassFinals::enable();
        parent::setUp();
    }

    #[DataProvider('flagsDataProvider')]
    #[Test]
    public function testGenerateRules(array $flags, string $expected): void
    {
        $generator = new RobotsRulesGenerator();
        self::assertSame($expected, $generator->generateRules($flags));
    }

    public static function flagsDataProvider(): array
    {
        return [
            'all false returns index,follow' => [
                [
                    'noImageIndex' => false,
                    'noArchive' => false,
                    'noSnippet' => false,
                    'noIndex' => false,
                    'noFollow' => false,
                ],
                'index,follow',
            ],
            'all true returns all robots directives' => [
                [
                    'noImageIndex' => true,
                    'noArchive' => true,
                    'noSnippet' => true,
                    'noIndex' => true,
                    'noFollow' => true,
                ],
                'noimageindex,noarchive,nosnippet,noindex,nofollow',
            ],
            'only noIndex' => [
                [
                    'noImageIndex' => false,
                    'noArchive' => false,
                    'noSnippet' => false,
                    'noIndex' => true,
                    'noFollow' => false,
                ],
                'noindex,follow',
            ],
            'only noFollow' => [
                [
                    'noImageIndex' => false,
                    'noArchive' => false,
                    'noSnippet' => false,
                    'noIndex' => false,
                    'noFollow' => true,
                ],
                'index,nofollow',
            ],
            'noImageIndex and noArchive' => [
                [
                    'noImageIndex' => true,
                    'noArchive' => true,
                    'noSnippet' => false,
                    'noIndex' => false,
                    'noFollow' => false,
                ],
                'noimageindex,noarchive,index,follow',
            ],
        ];
    }
}
