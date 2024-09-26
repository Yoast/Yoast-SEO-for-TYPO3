<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\MetaTag\Generator;

use YoastSeoForTypo3\YoastSeo\Record\Record;

interface GeneratorInterface
{
    public function generate(Record $record): void;
}
