<?php
namespace YoastSeoForTypo3\YoastSeo\Service;

use TYPO3\CMS\Core\Context\Context;
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
            $uriToCheck = $this->appendSimulateUser($uriToCheck);
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
        $GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify'] = false;
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
        $bodyFound = preg_match("/<body[^>]*>(.*?)<\/body>/is", $content, $matchesBody);

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
        $url = preg_replace('/\/$/', '', $uriToCheck);
        $baseUrl = preg_replace('/' . preg_quote('/', '/') . '$/', '', $url);

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
                'title' => $title,
                'description' => $metaDescription,
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
     * Append TYPO3 ADMCMD_simUser parameter to access all pages
     *
     * @param string $uriToCheck
     * @return string
     */
    protected function appendSimulateUser(string $uriToCheck) : string {
        $context = GeneralUtility::makeInstance(Context::class);
        $backendUserId = $context->getPropertyFromAspect('backend.user', 'id') ?? 0;
        if ($backendUserId > 0) {
            $uriToCheck .= '?ADMCMD_simUser=' . $backendUserId;
        }

        return $uriToCheck;
    }
}
