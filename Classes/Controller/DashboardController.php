<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Domain\ConsumableString;

class DashboardController extends AbstractBackendController
{
    public function indexAction(): ResponseInterface
    {
        return $this->returnResponse(
            ['nonce' => $this->getNonce()]
        );
    }

    protected function getNonce(): string
    {
        /** @var ConsumableString|null $nonceAttribute */
        $nonceAttribute = $this->request->getAttribute('nonce');
        if ($nonceAttribute instanceof ConsumableString) {
            return $nonceAttribute->consume();
        }
        return '';
    }
}
