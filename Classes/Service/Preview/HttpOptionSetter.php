<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Service\Preview;

class HttpOptionSetter
{
    public function setHttpOptions(): void
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
                $basicAuth['password'],
            ];
        }
    }
}
