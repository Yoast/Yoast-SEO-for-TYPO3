<?php

namespace YoastSeoForTypo3\YoastSeo\Backend\Controller;


use Psr\Http;
use TYPO3\CMS;
use YoastSeoForTypo3\YoastSeo;

class PageContentController
{

    /**
     * @param Http\Message\ServerRequestInterface $request
     * @param Http\Message\ResponseInterface $response
     *
     * @return Http\Message\ResponseInterface
     */
    public function renderPagePreview(Http\Message\ServerRequestInterface $request, Http\Message\ResponseInterface $response)
    {
        $currentPage = null;
        $data = array('meta' => array());

        $queryParameters = $request->getQueryParams();

        if (array_key_exists('yoast', $queryParameters) && is_array($queryParameters['yoast'])
            && array_key_exists('preview', $queryParameters['yoast'])
            && is_array($queryParameters['yoast']['preview'])
            && array_key_exists('page', $queryParameters['yoast']['preview'])
            && !empty($queryParameters['yoast']['preview']['page'])
            && CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($queryParameters['yoast']['preview']['page'])
        ) {
            $currentPage = CMS\Backend\Utility\BackendUtility::getRecord(
                'pages',
                (int) $queryParameters['yoast']['preview']['page']
            );
        }

        if (is_array($currentPage)) {
            array_walk($currentPage, function ($columnValue, $columnName) use (&$data) {
                if (in_array($columnName, array(
                    'title',
                    'subtitle',
                    'nav_title',
                    'description',
                    'author',
                    'author_email',
                    YoastSeo\Backend\PageLayoutHeader::COLUMN_NAME
                ))) {
                    $data['meta'][$columnName] = $columnValue;
                }
            });
        }

        $response->getBody()->write(json_encode($data));

        return $response;
    }

}