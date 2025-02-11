<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface AjaxHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
