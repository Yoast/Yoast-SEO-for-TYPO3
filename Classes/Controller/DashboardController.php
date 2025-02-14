<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Domain\ConsumableString;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DashboardController extends AbstractBackendController
{
    public function indexAction(): ResponseInterface
    {
        return $this->returnResponse(
            'Dashboard/Index',
            ['nonce' => $this->getNonce()]
        );
    }

    protected function getNonce(): string
    {
        if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() < 12) {
            return '';
        }
        /** @var ConsumableString|null $nonceAttribute */
        $nonceAttribute = $this->request->getAttribute('nonce');
        if ($nonceAttribute instanceof ConsumableString) {
            return $nonceAttribute->consume();
        }
        return '';
    }
}
