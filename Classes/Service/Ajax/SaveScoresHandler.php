<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use YoastSeoForTypo3\YoastSeo\Service\SaveScoresService;

class SaveScoresHandler extends AbstractAjaxHandler
{
    public function __construct(
        protected SaveScoresService $saveScoresService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getJsonData($request);
        if (!empty($data['table']) && !empty($data['uid'])) {
            $this->saveScoresService->save($data);
        }
        return new JsonResponse(['OK']);
    }
}
