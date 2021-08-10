<?php
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Exception;
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
    public function getPreviewData($uriToCheck, $pageId)
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
     * @param $uriToCheck
     * @return null|string
     * @throws \TYPO3\CMS\Core\Exception
     */
    protected function getContentFromUrl($uriToCheck): string
    {
        $backupSettings = $GLOBALS['TYPO3_CONF_VARS']['HTTP'];
        $this->setHttpOptions();
        $report = [];
        $content = GeneralUtility::getUrl(
            $uriToCheck,
            1,
            [
                'X-Yoast-Page-Request' => GeneralUtility::hmac(
                    $uriToCheck
                )
            ],
            $report
        );

        $GLOBALS['TYPO3_CONF_VARS']['HTTP'] = $backupSettings;
        if ((int)$report['error'] === 0) {
            return $content;
        }
        throw new Exception($report['error']);
    }

    /**
     * Get data from content
     *
     * @param string $content
     * @param string $uriToCheck
     * @return array
     */
    protected function getDataFromContent($content, $uriToCheck): array
    {
        $title = $body = $metaDescription = '';
        $locale = 'en';

        $localeFound = preg_match('/<html lang="([a-z]*)"/is', $content, $matchesLocale);
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
            $locale = trim($matchesLocale[1]);
        }
        $urlParts = parse_url(preg_replace('/\/$/', '', $uriToCheck));
        $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];
        $url = $baseUrl . $urlParts['path'];

        $faviconSrc = $baseUrl . '/favicon.ico';
        $favIconFound = preg_match('/<link rel=\"shortcut icon\" href=\"([^"]*)\"/i', $content, $matchesFavIcon);
        if ($favIconFound) {
            $faviconSrc = $matchesFavIcon[1];
        }
        $favIconHeader = @get_headers($faviconSrc);
        if ($favIconHeader[0] === 'HTTP/1.1 404 Not Found') {
            $faviconSrc = '';
        }

        preg_match('/<meta name=\"x-yoast-title-config\" value=\"([^"]*)\"/i', $content, $matchesTitleConfig);
        list($titlePrepend, $titleAppend) = explode('|||', (string)$matchesTitleConfig[1]);

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
        $body = $this->stripTagsContent($body, '<script><noscript>', true);
        $body = preg_replace(['/\s?\n\s?/', '/\s{2,}/'], [' ', ' '], $body);
        $body = strip_tags($body, '<h1><h2><h3><h4><h5><p><a><img>');

        return trim($body);
    }

    protected function stripTagsContent($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tagsArray = array_unique($tags[1]);

        if (is_array($tagsArray) && count($tagsArray) > 0) {
            if ($invert === false) {
                return preg_replace('@<(?!(?:' . implode('|', $tagsArray) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            }

            return preg_replace('@<(' . implode('|', $tagsArray) . ')\b.*?>.*?</\1>@si', '', $text);
        } elseif ($invert === false) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }

        return $text;
    }

    /**
     * Set http options for the preview request
     */
    protected function setHttpOptions()
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
