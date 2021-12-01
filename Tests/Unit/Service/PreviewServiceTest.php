<?php
namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Service;

/*
 * This file is part of the "Yoast SEO for TYPO3" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Service\PreviewService;

class PreviewServiceTest extends UnitTestCase
{
    /**
     * Check if the PreviewService strips the body contents correctly
     *
     * @param string $input
     * @param string $expected
     *
     * @dataProvider isBodyStrippedCorrectlyDataProvider
     * @test
     */
    public function isBodyStrippedCorrectly(string $input, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->callInaccessibleMethod(new PreviewService(), 'prepareBody', $input)
        );
    }

    /**
     * @return array
     */
    public function isBodyStrippedCorrectlyDataProvider(): array
    {
        return [
            [
                '<script>strippedcontent</script>content',
                'content',
            ],
            [
                '<div>content</div>',
                'content',
            ],
            [
                '<section>content</section><p>content</p>',
                'content<p>content</p>'
            ],
            [
                '<h1>heading</h1><h2>heading2</h2>',
                '<h1>heading</h1><h2>heading2</h2>'
            ]
        ];
    }
}
