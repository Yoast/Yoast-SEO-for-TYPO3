<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Event;

use TYPO3\CMS\Core\Site\Entity\Site;

final class ModifyPreviewUrlEvent
{
    public function __construct(
        private string $url,
        private Site $site,
        private int $pageId,
        private int $languageId,
    ) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
