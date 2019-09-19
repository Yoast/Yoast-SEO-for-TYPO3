<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class SnippetPreview
 * @package YoastSeoForTypo3\YoastSeo\UserFunctions
 */
class SnippetPreview
{
    /**
     * Render function
     *
     * @return string
     */
    public function render(): string
    {
        if (version_compare(TYPO3_branch, '9.5', '>=')) {
            $uriToCheck = urldecode($GLOBALS['TYPO3_REQUEST']->getQueryParams()['uriToCheck']);
        } else {
            $additionalGetVars = urldecode($_GET['additionalGetVars']);
            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $uriToCheck = $cObj->typolink_URL([
                'parameter' => (int)$_GET['pageIdToCheck'],
                'forceAbsoluteUrl' => 1,
                'additionalParams' => $additionalGetVars . '&L=' . (int)$_GET['languageIdToCheck'],
                'linkAccessRestrictedPages' => 1,
            ]);
        }

        try {
            $content = $this->getContentFromUrl($uriToCheck);
            $data = $this->getDataFromContent($content, $uriToCheck, (int)$_GET['languageIdToCheck']);
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
     * Get data from content
     *
     * @param string $content
     * @param string $uriToCheck
     * @param int $languageId
     * @return array
     */
    protected function getDataFromContent($content, $uriToCheck, $languageId): array
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
                $body = implode($indexableContents[0], '');
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
        $baseUrl = preg_replace('/' . preg_quote($GLOBALS['TSFE']->page['slug'], '/') . '$/', '', $url);

        $faviconSrc = $baseUrl . '/favicon.ico';
        $favIconFound = preg_match('/<link rel=\"shortcut icon\" href=\"(.*)\"/i', $content, $matchesFavIcon);
        if ($favIconFound) {
            $faviconSrc = $matchesFavIcon[1];
        }
        $favIconHeader = @get_headers($faviconSrc);
        if ($favIconHeader[0] === 'HTTP/1.1 404 Not Found') {
            $faviconSrc = '';
        }

        $titlePrependAppend = $this->getPageTitlePrependAppend();
        if ($content !== null) {
            return [
                'id' => $GLOBALS['TSFE']->id,
                'url' => $url,
                'baseUrl' => $baseUrl,
                'slug' => $GLOBALS['TSFE']->page['slug'],
                'title' => $title,
                'description' => $metaDescription,
                'locale' => $locale,
                'body' => $body,
                'faviconSrc' => $faviconSrc,
                'pageTitlePrepend' => $titlePrependAppend['prepend'],
                'pageTitleAppend' => $titlePrependAppend['append'],
            ];
        }
        return [];
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
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $uriToCheck);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Yoast-Page-Request: ' . GeneralUtility::hmac($uriToCheck)]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] === 200) {
            return $content;
        }
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception($info['http_code']);
    }

    /**
     * Get page title prepend append
     *
     * @return array
     */
    protected function getPageTitlePrependAppend(): array
    {
        $prependAppend = ['prepend' => '', 'append' => ''];
        $siteTitle = trim($GLOBALS['TSFE']->tmpl->setup['sitetitle'] ?? '');
        $pageTitleFirst = (bool)($GLOBALS['TSFE']->config['config']['pageTitleFirst'] ?? false);
        $pageTitleSeparator = $this->getPageTitleSeparator();
        // only show a separator if there are both site title and page title
        if ($siteTitle === '') {
            $pageTitleSeparator = '';
        } elseif (empty($pageTitleSeparator)) {
            // use the default separator if non given
            $pageTitleSeparator = ': ';
        }

        if ($pageTitleFirst) {
            $prependAppend['append'] = $pageTitleSeparator . $siteTitle;
        } else {
            $prependAppend['prepend'] = $siteTitle . $pageTitleSeparator;
        }

        return $prependAppend;
    }

    /**
     * Get page title separator
     *
     * @return string
     */
    protected function getPageTitleSeparator(): string
    {
        $pageTitleSeparator = '';
        // Check for a custom pageTitleSeparator, and perform stdWrap on it
        if (isset($GLOBALS['TSFE']->config['config']['pageTitleSeparator'])
            && $GLOBALS['TSFE']->config['config']['pageTitleSeparator'] !== '') {
            $pageTitleSeparator = $GLOBALS['TSFE']->config['config']['pageTitleSeparator'];

            if (isset($GLOBALS['TSFE']->config['config']['pageTitleSeparator.'])
                && is_array($GLOBALS['TSFE']->config['config']['pageTitleSeparator.'])) {
                $pageTitleSeparator = $GLOBALS['TSFE']->cObj->stdWrap(
                    $pageTitleSeparator,
                    $GLOBALS['TSFE']->config['config']['pageTitleSeparator.']
                );
            } else {
                $pageTitleSeparator .= ' ';
            }
        }

        return $pageTitleSeparator;
    }
}
