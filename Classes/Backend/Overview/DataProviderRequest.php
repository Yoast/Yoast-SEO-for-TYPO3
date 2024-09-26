<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Backend\Overview;

class DataProviderRequest
{
    public function __construct(
        protected int $id = 0,
        protected int $language = 0,
        protected string $table = '',
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
