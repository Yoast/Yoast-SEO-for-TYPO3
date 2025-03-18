<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\CrawlerHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\InternalLinkingSuggestionsHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\ProminentWordsHandler;
use YoastSeoForTypo3\YoastSeo\Service\Ajax\SaveScoresHandler;

final readonly class AjaxController
{
    public function __construct(
        protected SaveScoresHandler $saveScoresHandler,
        protected ProminentWordsHandler $prominentWordsHandler,
        protected InternalLinkingSuggestionsHandler $internalLinkingSuggestionsHandler,
        protected CrawlerHandler $crawlerHandler,
    ) {}

    public function saveScoresAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        return $this->saveScoresHandler->handle($request);
    }

    public function prominentWordsAction(
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
