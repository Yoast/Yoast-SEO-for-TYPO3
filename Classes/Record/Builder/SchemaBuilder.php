<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record\Builder;

class SchemaBuilder extends AbstractBuilder
{
    protected array $sqlData = [];

    public function build(): void
    {
        $this->sqlData[] = 'CREATE TABLE ' . $this->record->getTableName() . ' (';

        if ($this->record->hasDefaultSeoFields()) {
            $this->addDefaultSeoFields();
        }

        if ($this->record->hasYoastSeoFields()) {
            $this->addYoastSeoFields();
        }

        if ($this->record->shouldAddDescriptionField()) {
            $this->addDescriptionField();
        }

        $this->sqlData[] = ');';
    }

    protected function addDefaultSeoFields(): void
    {
        $this->sqlData[] = "seo_title varchar(255) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "no_index tinyint(4) DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "no_follow tinyint(4) DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "og_title varchar(255) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "og_description text,";
        $this->sqlData[] = "og_image int(11) unsigned DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "twitter_title varchar(255) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "twitter_description text,";
        $this->sqlData[] = "twitter_image int(11) unsigned DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "twitter_card varchar(255) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "canonical_link varchar(2048) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "sitemap_priority decimal(2,1) DEFAULT '0.5' NOT NULL,";
        $this->sqlData[] = "sitemap_changefreq varchar(10) DEFAULT '' NOT NULL,";
    }

    protected function addYoastSeoFields(): void
    {
        $this->sqlData[] = "tx_yoastseo_focuskeyword tinytext,";
        $this->sqlData[] = "tx_yoastseo_focuskeyword_synonyms tinytext,";
        $this->sqlData[] = "tx_yoastseo_cornerstone tinyint(3) DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "tx_yoastseo_score_readability varchar(50) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "tx_yoastseo_score_seo varchar(50) DEFAULT '' NOT NULL,";
        $this->sqlData[] = "tx_yoastseo_focuskeyword_premium int(11) DEFAULT '0' NOT NULL,";
        $this->sqlData[] = "KEY tx_yoastseo_cornerstone (tx_yoastseo_cornerstone),";
    }

    protected function addDescriptionField(): void
    {
        $this->sqlData[] = $this->record->getDescriptionField() . " text,";
    }

    public function getResult(): array
    {
        return $this->sqlData;
    }
}
