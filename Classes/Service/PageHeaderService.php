<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\SingletonInterface;

class PageHeaderService implements SingletonInterface
{
    protected bool $snippetPreviewEnabled = false;
    protected int $languageId = 0;
    protected int $pageId = 0;

    public function isSnippetPreviewEnabled(): bool
    {
        return $this->snippetPreviewEnabled;
    }

    public function setSnippetPreviewEnabled(bool $snippetPreviewEnabled): self
    {
        $this->snippetPreviewEnabled = $snippetPreviewEnabled;
        return $this;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function setLanguageId(int $languageId): self
    {
        $this->languageId = $languageId;
        return $this;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }

    public function setPageId(int $pageId): self
    {
        $this->pageId = $pageId;
        return $this;
    }
}
