<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Unit\Record\Builder;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use YoastSeoForTypo3\YoastSeo\Record\Builder\SchemaBuilder;
use YoastSeoForTypo3\YoastSeo\Record\Record;

#[CoversClass(SchemaBuilder::class)]
class SchemaBuilderTest extends UnitTestCase
{
    #[Test]
    public function testBuildWithAllFieldsEnabled(): void
    {
        $record = (new Record())
            ->setTableName('tx_news_domain_model_news')
            ->setDefaultSeoFields(true)
            ->setYoastSeoFields(true)
            ->setSitemapFields(true)
            ->setAddDescriptionField(false);

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        self::assertStringContainsString('CREATE TABLE tx_news_domain_model_news', $result[0]);
        self::assertStringContainsString('seo_title', implode("\n", $result));
        self::assertStringContainsString('no_index', implode("\n", $result));
        self::assertStringContainsString('no_follow', implode("\n", $result));
        self::assertStringContainsString('og_title', implode("\n", $result));
        self::assertStringContainsString('og_description', implode("\n", $result));
        self::assertStringContainsString('og_image', implode("\n", $result));
        self::assertStringContainsString('twitter_title', implode("\n", $result));
        self::assertStringContainsString('twitter_description', implode("\n", $result));
        self::assertStringContainsString('twitter_image', implode("\n", $result));
        self::assertStringContainsString('twitter_card', implode("\n", $result));
        self::assertStringContainsString('canonical_link', implode("\n", $result));
        self::assertStringContainsString('tx_yoastseo_focuskeyword', implode("\n", $result));
        self::assertStringContainsString('tx_yoastseo_score_readability', implode("\n", $result));
        self::assertStringContainsString('tx_yoastseo_score_seo', implode("\n", $result));
        self::assertStringContainsString('tx_yoastseo_cornerstone', implode("\n", $result));
        self::assertStringContainsString('sitemap_priority', implode("\n", $result));
        self::assertStringContainsString('sitemap_changefreq', implode("\n", $result));
        // 1 CREATE TABLE + 11 seo + 10 yoast + 2 sitemap + 1 closing = 25
        self::assertCount(25, $result);
    }

    #[Test]
    public function testBuildWithOnlyDefaultSeoFields(): void
    {
        $record = (new Record())
            ->setTableName('tx_test')
            ->setDefaultSeoFields(true)
            ->setYoastSeoFields(false)
            ->setSitemapFields(false)
            ->setAddDescriptionField(false);

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        // 1 CREATE TABLE + 11 seo fields + 1 closing = 13
        self::assertCount(13, $result);
        self::assertStringContainsString('seo_title', implode("\n", $result));
        self::assertStringNotContainsString('tx_yoastseo_focuskeyword', implode("\n", $result));
        self::assertStringNotContainsString('sitemap_priority', implode("\n", $result));
    }

    #[Test]
    public function testBuildWithOnlyYoastSeoFields(): void
    {
        $record = (new Record())
            ->setTableName('tx_test')
            ->setDefaultSeoFields(false)
            ->setYoastSeoFields(true)
            ->setSitemapFields(false)
            ->setAddDescriptionField(false);

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        // 1 CREATE TABLE + 10 yoast fields + 1 closing = 12
        self::assertCount(12, $result);
        self::assertStringNotContainsString('seo_title', implode("\n", $result));
        self::assertStringContainsString('tx_yoastseo_focuskeyword', implode("\n", $result));
        self::assertStringNotContainsString('sitemap_priority', implode("\n", $result));
    }

    #[Test]
    public function testBuildWithOnlySitemapFields(): void
    {
        $record = (new Record())
            ->setTableName('tx_test')
            ->setDefaultSeoFields(false)
            ->setYoastSeoFields(false)
            ->setSitemapFields(true)
            ->setAddDescriptionField(false);

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        // 1 CREATE TABLE + 2 sitemap fields + 1 closing = 4
        self::assertCount(4, $result);
        self::assertStringNotContainsString('seo_title', implode("\n", $result));
        self::assertStringNotContainsString('tx_yoastseo_focuskeyword', implode("\n", $result));
        self::assertStringContainsString('sitemap_priority', implode("\n", $result));
        self::assertStringContainsString('sitemap_changefreq', implode("\n", $result));
    }

    #[Test]
    public function testBuildWithNoFieldsEnabled(): void
    {
        $record = (new Record())
            ->setTableName('tx_test')
            ->setDefaultSeoFields(false)
            ->setYoastSeoFields(false)
            ->setSitemapFields(false)
            ->setAddDescriptionField(false);

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        // 1 CREATE TABLE + 1 closing = 2
        self::assertCount(2, $result);
        self::assertStringContainsString('CREATE TABLE tx_test', $result[0]);
        self::assertSame(');', $result[1]);
    }

    #[Test]
    public function testBuildWithDescriptionField(): void
    {
        $record = (new Record())
            ->setTableName('tx_test')
            ->setDefaultSeoFields(false)
            ->setYoastSeoFields(false)
            ->setSitemapFields(false)
            ->setAddDescriptionField(true)
            ->setDescriptionField('my_description');

        $builder = new SchemaBuilder();
        $builder->setRecord($record);
        $builder->build();

        $result = $builder->getResult();

        // 1 CREATE TABLE + 1 description field + 1 closing = 3
        self::assertCount(3, $result);
        self::assertStringContainsString('my_description text,', implode("\n", $result));
    }
}
