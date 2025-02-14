<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractAjaxHandler implements AjaxHandlerInterface
{
    /**
     * @return array<string, mixed>
     */
    protected function getJsonData(ServerRequestInterface $request): array
    {
        $body = $request->getBody()->getContents();
        return json_decode($body, true);
    }
}
