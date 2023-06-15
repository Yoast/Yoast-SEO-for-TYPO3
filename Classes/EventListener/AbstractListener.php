<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\EventListener;

use YoastSeoForTypo3\YoastSeo\Record\Builder\AbstractBuilder;

abstract class AbstractListener
{
    /**
     * @var \YoastSeoForTypo3\YoastSeo\Record\Builder\AbstractBuilder
     */
    protected AbstractBuilder $builder;

    public function __construct(AbstractBuilder $builder)
    {
        $this->builder = $builder;
    }
}
