<?php
namespace YoastSeoForTypo3\YoastSeo\UserFunctions;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $uriToCheck = urldecode($GLOBALS['TYPO3_REQUEST']->getQueryParams()['uriToCheck']);

        try {
            $content = $this->getContentFromUrl($uriToCheck);
            $data = $this->getDataFromContent($content, $uriToCheck);
        } catch (Exception $e) {
            $data = ['error' => 'Could not read the url ' . $uriToCheck . ': ' . $e->getMessage()];
        }

        return json_encode($data);
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

        /** @var SiteLanguage $siteLanguage */
        $siteLanguage = $GLOBALS['TYPO3_REQUEST']->getAttribute('language');

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

        $url = preg_replace('/\/$/', '', $uriToCheck);

        $titlePrependAppend = $this->getPageTitlePrependAppend();
        if ($content !== null) {
            return [
                'id' => $GLOBALS['TSFE']->id,
                'url' => $url,
                'baseUrl' => preg_replace('/' . preg_quote($GLOBALS['TSFE']->page['slug'], '/') . '$/', '', $url),
                'slug' => $GLOBALS['TSFE']->page['slug'],
                'title' => $title,
                'description' => $metaDescription,
                'locale' => $siteLanguage->getLocale(),
                'body' => $body,
                'pageTitlePrepend' => $titlePrependAppend['prepend'],
                'pageTitleAppend' => $titlePrependAppend['append'],
            ];
        }
        return ['error' => 'Could not read the url ' . $uriToCheck];
    }

    /**
     * @param $uriToCheck
     * @return null|string
     * @throws \TYPO3\CMS\Core\Exception
     */
    protected function getContentFromUrl($uriToCheck): ?string
    {
        $GLOBALS['TYPO3_CONF_VARS']['HTTP']['verify'] = false;
        $report = [];
        $content = GeneralUtility::getUrl($uriToCheck, 1, [], $report);
        if ($report['http_code'] === 200) {
            return $content;
        }
        throw new Exception($report['message']);
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
