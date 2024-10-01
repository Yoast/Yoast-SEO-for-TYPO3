<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\CrawlerHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\InternalLinkingSuggestionsHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\PreviewHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\ProminentWordsHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\SaveScoresHandler;

class AjaxController
{
    public function __construct(
        protected PreviewHandler $previewHandler,
        protected SaveScoresHandler $saveScoresHandler,
        protected ProminentWordsHandler $prominentWordsHandler,
        protected InternalLinkingSuggestionsHandler $internalLinkingSuggestionsHandler,
        protected CrawlerHandler $crawlerHandler,
    ) {}

    public function previewAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->previewHandler->handle($request);
    }

    public function saveScoresAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->saveScoresHandler->handle($request);
    }

    public function promimentWordsAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->prominentWordsHandler->handle($request);
    }

    public function internalLinkingSuggestionsAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->internalLinkingSuggestionsHandler->handle($request);
    }

    public function crawlerDeterminePages(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->crawlerHandler->handle($request->withAttribute('handlerRequest', 'determine'));
    }

    public function crawlerIndexPages(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->crawlerHandler->handle($request->withAttribute('handlerRequest', 'index'));
    }
}
