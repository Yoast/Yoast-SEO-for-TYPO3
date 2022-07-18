<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record\Builder;

use YoastSeoForTypo3\YoastSeo\Record\Record;

abstract class AbstractBuilder
{
    /**
     * @var \YoastSeoForTypo3\YoastSeo\Record\Record
     */
    protected $record;

    public function setRecord(Record $record): self
    {
        $this->record = $record;
        return $this;
    }

    abstract public function build(): void;
    abstract public function getResult(): array;
}
