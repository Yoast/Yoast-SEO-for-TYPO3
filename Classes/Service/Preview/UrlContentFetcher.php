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
        $parsedUri = parse_url($uriToCheck);

        $scheme = $parsedUri['scheme'] ?? 'http';
        $host   = $parsedUri['host'];
        $port   = $parsedUri['port'] ?? ($scheme === 'https' ? '443' : '80');
        $path   = $parsedUri['path'] ?? '';
        $query  = isset($parsedUri['query'])    ? '?' . $parsedUri['query'] : '';
        $frag   = isset($parsedUri['fragment']) ? '#' . $parsedUri['fragment'] : '';

        $requestUrl = sprintf(
            '%s://%s%s%s%s',
            'http', '127.0.0.1', $path, $query, $frag
        );

        try {
            $response = $this->requestFactory->request(
                $requestUrl,
                'GET',
                [
                    'headers' => [
                        'Host' => $host,
                        'X-Forwarded-For'      => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'localhost',
                        'X-Forwarded-Proto'    => $scheme,
                        'X-Forwarded-Port'     => $port,
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
