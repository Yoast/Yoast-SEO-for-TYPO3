<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview;

use GuzzleHttp\Exception\RequestException;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UrlContentFetcher
{
    public function __construct(
        protected RequestFactory $requestFactory
    ) {}

    public function fetch(string $uriToCheck): string
    {
        try {
            $response = $this->requestFactory->request(
                $uriToCheck,
                'GET',
                [
                    'headers' => [
                        'X-Yoast-Page-Request' => GeneralUtility::hmac(
                            $uriToCheck
                        ),
                    ],
                ]
            );
        } catch (RequestException $e) {
            throw new Exception((string)$e->getCode(), 0, $e);
        }

        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        }

        throw new Exception((string)$response->getStatusCode());
    }
}
