<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use YoastSeoForTypo3\YoastSeo\Record\Builder\AbstractBuilder;

abstract class AbstractListener
{
    public function __construct(
        protected AbstractBuilder $builder
    ) {}
}
