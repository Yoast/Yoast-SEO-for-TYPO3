<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Service;

use GuzzleHttp\Exception\RequestException;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PreviewService
 */
class PreviewService
{
    /**
     * Page id
     *
     * @var int
     */
    protected $pageId;

    /**
     * Typoscript config
     *
     * @var array
     */
    protected $config;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $cObj;

    /**
     * Get preview data
     *
     * @param string $uriToCheck
     * @param int $pageId
     * @return false|string
     */
    public function getPreviewData(string $uriToCheck, int $pageId)
    {
        $this->pageId = $pageId;

        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        try {
            $content = $this->getContentFromUrl($uriToCheck);
            $data = $this->getDataFromContent($content, $uriToCheck);
        } catch (Exception $e) {
            $data = [
                'error' => [
                    'uriToCheck' => $uriToCheck,
                    'statusCode' => $e->getMessage(),
                ]
            ];
        }
        return json_encode($data);
    }

    /**
     * Get content from url
     *
     * @param string $uriToCheck
     * @return null|string
     * @throws \TYPO3\CMS\Core\Exception
     */
    protected function getContentFromUrl(string $uriToCheck): ?string
    {
        $backupSettings = $GLOBALS['TYPO3_CONF_VARS']['HTTP'];
        $this->setHttpOptions();

        try {
            $response = GeneralUtility::makeInstance(RequestFactory::class)
                ->request(
                    $uriToCheck,
                    'GET',
                    [
                        'headers' => [
                            'X-Yoast-Page-Request' => GeneralUtility::hmac(
                                $uriToCheck
                            )
                        ]
                    ]
                );
        } catch (RequestException $e) {
            throw new Exception((string)$e->getCode());
        }

        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = $backupSettings;
        if ($response->getStatusCode() === 200) {
            return $response->getBody()->getContents();
        }
        throw new Exception($response->getStatusCode());
    }

    /**
     * Get data from content
     *
     * @param string|null $content
     * @param string $uriToCheck
     * @return array
     */
    protected function getDataFromContent(?string $content, string $uriToCheck): array
    {
        $title = $body = $metaDescription = '';
        $locale = 'en';

        $localeFound = preg_match('/<html[^>]*lang="([a-z\-A-Z]*)"/is', $content, $matchesLocale);
        $titleFound = preg_match("/<title[^>]*>(.*?)<\/title>/is", $content, $matchesTitle);
        $descriptionFound = preg_match(
            "/<meta[^>]*name=[\" | \']description[\"|\'][^>]*content=[\"]([^\"]*)[\"][^>]*>/i",
            $content,
            $matchesDescription
        );
        $bodyFound = preg_match("/<body[^>]*>(.*)<\/body>/is", $content, $matchesBody);

        if ($bodyFound) {
            $body = $matchesBody[1];

            preg_match_all(
                '/<!--\s*?TYPO3SEARCH_begin\s*?-->.*?<!--\s*?TYPO3SEARCH_end\s*?-->/mis',
                $body,
                $indexableContents
            );

            if (is_array($indexableContents[0]) && !empty($indexableContents[0])) {
                $body = implode('', $indexableContents[0]);
            }
        }

        if ($titleFound) {
            $title = $matchesTitle[1];
        }

        if ($descriptionFound) {
            $metaDescription = $matchesDescription[1];
        }

        if ($localeFound) {
            [$locale] = explode('-', trim($matchesLocale[1]));
        }
        $urlParts = parse_url(preg_replace('/\/$/', '', $uriToCheck));
        $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];
        $url = $baseUrl . ($urlParts['path'] ?? '');

        $faviconSrc = $baseUrl . '/favicon.ico';
        $favIconFound = preg_match('/<link rel=\"shortcut icon\" href=\"([^"]*)\"/i', $content, $matchesFavIcon);
        if ($favIconFound) {
            $faviconSrc = $matchesFavIcon[1];
        }
        $favIconHeader = @get_headers($faviconSrc);
        if (($favIconHeader[0] ?? '') === 'HTTP/1.1 404 Not Found') {
            $faviconSrc = '';
        }

        $titlePrepend = $titleAppend = '';
        preg_match('/<meta name=\"x-yoast-title-config\" value=\"([^"]*)\"/i', $content, $matchesTitleConfig);
        if (count($matchesTitleConfig) > 1) {
            [$titlePrepend, $titleAppend] = explode('|||', (string)$matchesTitleConfig[1]);
        }

        $body = $this->prepareBody($body);

        if ($content !== null) {
            return [
                'id' => $this->pageId,
                'url' => $url,
                'baseUrl' => $baseUrl,
                'slug' => '/',
                'title' => strip_tags(html_entity_decode($title)),
                'description' => strip_tags(html_entity_decode($metaDescription)),
                'locale' => $locale,
                'body' => $body,
                'faviconSrc' => $faviconSrc,
                'pageTitlePrepend' => $titlePrepend,
                'pageTitleAppend' => $titleAppend,
            ];
        }
        return [];
    }

    protected function prepareBody(string $body): string
    {
        $body = $this->stripTagsContent($body, '<script><noscript>');
        $body = preg_replace(['/\s?\n\s?/', '/\s{2,}/'], [' ', ' '], $body);
        $body = strip_tags($body, '<h1><h2><h3><h4><h5><p><a><img>');

        return trim($body);
    }

    /**
     * @param string $text
     * @param string $tags
     * @return string
     */
    protected function stripTagsContent(string $text, string $tags = ''): string
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $foundTags);
        $tagsArray = array_unique($foundTags[1]);

        if (is_array($tagsArray) && count($tagsArray) > 0) {
            return preg_replace('@<(' . implode('|', $tagsArray) . ')\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    /**
     * Set http options for the preview request
     */
    protected function setHttpOptions(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify'] = false;
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewSettings']['basicAuth'])) {
            return;
        }

        $basicAuth = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewSettings']['basicAuth'];
        if (!is_array($basicAuth) || !isset($basicAuth['username'], $basicAuth['password'])) {
            return;
        }

        if (!empty($basicAuth['username']) && !empty($basicAuth['password'])) {
            $GLOBALS['TYPO3_CONF_VARS']['HTTP']['auth'] = [
                $basicAuth['username'],
                $basicAuth['password']
            ];
        }
    }
}
