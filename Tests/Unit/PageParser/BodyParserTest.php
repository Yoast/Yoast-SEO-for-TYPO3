<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\PageParser;

use MaxServ\FrontendRequest\Dto\RequestContext;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\PageParser\BodyParser;

#[CoversClass(BodyParser::class)]
class BodyParserTest extends UnitTestCase
{
    protected BodyParser $subject;
    protected RequestContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new BodyParser();
        $this->context = new RequestContext();
    }

    #[Test]
    public function parseReturnsEmptyStringWhenBodyTagIsMissing(): void
    {
        self::assertSame('', $this->subject->parse('<html><head></head></html>', $this->context));
    }

    #[Test]
    public function parseReturnsEmptyStringForEmptyBody(): void
    {
        self::assertSame('', $this->subject->parse('<html><body></body></html>', $this->context));
    }

    #[Test]
    public function parseStripsDisallowedTagsWhenNoMarkersPresent(): void
    {
        $html = '<html><body><div><p>Hello <span>World</span></p></div></body></html>';
        self::assertSame('<p>Hello World</p>', $this->subject->parse($html, $this->context));
    }

    #[Test]
    public function parsePreservesAnchorAndImageTags(): void
    {
        $html = '<html><body><p>Visit <a href="/x">here</a> <img src="/y.png" alt="logo"></p></body></html>';
        self::assertSame('<p>Visit <a href="/x">here</a> <img src="/y.png" alt="logo"></p>', $this->subject->parse($html, $this->context));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function allowedHeadingsDataProvider(): array
    {
        return [
            'h1 is preserved' => [
                '<html><body><h1>Title</h1></body></html>',
                '<h1>Title</h1>',
            ],
            'h2 is preserved' => [
                '<html><body><h2>Title</h2></body></html>',
                '<h2>Title</h2>',
            ],
            'h3 is preserved' => [
                '<html><body><h3>Title</h3></body></html>',
                '<h3>Title</h3>',
            ],
            'h4 is preserved' => [
                '<html><body><h4>Title</h4></body></html>',
                '<h4>Title</h4>',
            ],
            'h5 is preserved' => [
                '<html><body><h5>Title</h5></body></html>',
                '<h5>Title</h5>',
            ],
            'h6 is preserved' => [
                '<html><body><h6>Title</h6></body></html>',
                '<h6>Title</h6>',
            ],
        ];
    }

    #[Test]
    #[DataProvider('allowedHeadingsDataProvider')]
    public function parsePreservesHeadingTagsIncludingH6(string $html, string $expected): void
    {
        self::assertSame($expected, $this->subject->parse($html, $this->context));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function typo3SearchMarkerDataProvider(): array
    {
        return [
            'extracts content between a single marker pair' => [
                '<html><body>before<!--TYPO3SEARCH_begin--><p>indexed</p><!--TYPO3SEARCH_end-->after</body></html>',
                '<p>indexed</p>',
            ],
            'concatenates multiple non-nested sections with a space' => [
                '<html><body><!--TYPO3SEARCH_begin--><p>one</p><!--TYPO3SEARCH_end-->ignore<!--TYPO3SEARCH_begin--><p>two</p><!--TYPO3SEARCH_end--></body></html>',
                '<p>one</p> <p>two</p>',
            ],
            'preserves outer section when markers are nested' => [
                '<html><body><!--TYPO3SEARCH_begin--><p>outer</p><!--TYPO3SEARCH_begin--><p>inner</p><!--TYPO3SEARCH_end--><p>tail</p><!--TYPO3SEARCH_end--></body></html>',
                '<p>outer</p><p>inner</p><p>tail</p>',
            ],
            'handles deeply nested markers' => [
                '<html><body><!--TYPO3SEARCH_begin-->A<!--TYPO3SEARCH_begin-->B<!--TYPO3SEARCH_begin-->C<!--TYPO3SEARCH_end-->D<!--TYPO3SEARCH_end-->E<!--TYPO3SEARCH_end--></body></html>',
                'ABCDE',
            ],
            'ignores end marker without matching begin' => [
                '<html><body><!--TYPO3SEARCH_end--><!--TYPO3SEARCH_begin--><p>kept</p><!--TYPO3SEARCH_end--></body></html>',
                '<p>kept</p>',
            ],
            'falls back to full body when begin marker has no matching end' => [
                '<html><body><p>foo</p><!--TYPO3SEARCH_begin--><p>bar</p></body></html>',
                '<p>foo</p><p>bar</p>',
            ],
            'matches markers case-insensitively' => [
                '<html><body><!--TYPO3SEARCH_BEGIN--><p>case</p><!--typo3search_end--></body></html>',
                '<p>case</p>',
            ],
            'tolerates whitespace inside marker comments' => [
                "<html><body><!--   TYPO3SEARCH_begin   --><p>spaced</p><!--\tTYPO3SEARCH_end\t--></body></html>",
                '<p>spaced</p>',
            ],
            'excludes content outside marker pair' => [
                '<html><body><p>excluded</p><!--TYPO3SEARCH_begin--><p>included</p><!--TYPO3SEARCH_end--><p>excluded too</p></body></html>',
                '<p>included</p>',
            ],
            'mixes nested and adjacent sibling sections' => [
                '<html><body><!--TYPO3SEARCH_begin-->A<!--TYPO3SEARCH_begin-->B<!--TYPO3SEARCH_end-->C<!--TYPO3SEARCH_end--> drop <!--TYPO3SEARCH_begin-->D<!--TYPO3SEARCH_end--></body></html>',
                'ABC D',
            ],
        ];
    }

    #[Test]
    #[DataProvider('typo3SearchMarkerDataProvider')]
    public function parseHandlesTypo3SearchMarkers(string $html, string $expected): void
    {
        self::assertSame($expected, $this->subject->parse($html, $this->context));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function scriptAndNoscriptStrippingDataProvider(): array
    {
        return [
            'strips script tag and its content' => [
                '<html><body><p>before</p><script>alert("nope");</script><p>after</p></body></html>',
                '<p>before</p><p>after</p>',
            ],
            'strips noscript tag and its content' => [
                '<html><body><p>before</p><noscript><p>fallback</p></noscript><p>after</p></body></html>',
                '<p>before</p><p>after</p>',
            ],
            'strips script with attributes' => [
                '<html><body><p>x</p><script type="application/ld+json" id="seo">{"@type":"Article"}</script><p>y</p></body></html>',
                '<p>x</p><p>y</p>',
            ],
            'strips closing script tag with trailing whitespace' => [
                '<html><body><p>x</p><script>alert(1);</script ><p>y</p></body></html>',
                '<p>x</p><p>y</p>',
            ],
            'strips closing noscript tag with trailing whitespace' => [
                '<html><body><p>x</p><noscript>fallback</noscript ><p>y</p></body></html>',
                '<p>x</p><p>y</p>',
            ],
            'strips multiple separate script blocks' => [
                '<html><body><script>a</script><p>middle</p><script>b</script></body></html>',
                '<p>middle</p>',
            ],
            'strips script that contains tag-like text in attributes' => [
                '<html><body><script data-x="<p>not a tag</p>">code</script><p>kept</p></body></html>',
                '<p>kept</p>',
            ],
            'strips scripts inside TYPO3SEARCH sections' => [
                '<html><body><!--TYPO3SEARCH_begin--><p>kept</p><script>removed</script><!--TYPO3SEARCH_end--></body></html>',
                '<p>kept</p>',
            ],
            'strips scripts that span multiple lines' => [
                "<html><body><script>\nvar a = 1;\nvar b = 2;\n</script><p>kept</p></body></html>",
                '<p>kept</p>',
            ],
        ];
    }

    #[Test]
    #[DataProvider('scriptAndNoscriptStrippingDataProvider')]
    public function parseStripsScriptAndNoscriptWithContent(string $html, string $expected): void
    {
        self::assertSame($expected, $this->subject->parse($html, $this->context));
    }

    #[Test]
    public function parseCollapsesArbitraryWhitespaceToSingleSpace(): void
    {
        $html = "<html><body><p>foo</p>\n\n\t   <p>bar</p>  \n  <p>baz</p></body></html>";
        self::assertSame('<p>foo</p> <p>bar</p> <p>baz</p>', $this->subject->parse($html, $this->context));
    }

    #[Test]
    public function parseTrimsResultingOutput(): void
    {
        $html = "<html><body>   \n  <p>content</p>   \n   </body></html>";
        self::assertSame('<p>content</p>', $this->subject->parse($html, $this->context));
    }

    #[Test]
    public function parseTrimsExtractedSectionsBeforeJoining(): void
    {
        $html = '<html><body><!--TYPO3SEARCH_begin-->   <p>a</p>   <!--TYPO3SEARCH_end-->   junk   <!--TYPO3SEARCH_begin-->   <p>b</p>   <!--TYPO3SEARCH_end--></body></html>';
        self::assertSame('<p>a</p> <p>b</p>', $this->subject->parse($html, $this->context));
    }

    #[Test]
    public function parseCombinesNestedMarkersHeadingsAndScriptStripping(): void
    {
        $html = '<html><body>'
            . '<script>tracking()</script>'
            . '<!--TYPO3SEARCH_begin-->'
            . '<h6>Sub</h6>'
            . '<script>x</script>'
            . '<!--TYPO3SEARCH_begin-->'
            . '<p>nested</p>'
            . '<!--TYPO3SEARCH_end-->'
            . '<!--TYPO3SEARCH_end-->'
            . '<script>more()</script>'
            . '</body></html>';

        self::assertSame('<h6>Sub</h6><p>nested</p>', $this->subject->parse($html, $this->context));
    }
}
