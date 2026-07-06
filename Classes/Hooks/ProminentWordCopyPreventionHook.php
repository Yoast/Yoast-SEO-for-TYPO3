<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Hooks;

use TYPO3\CMS\Core\DataHandling\DataHandler;
use YoastSeoForTypo3\YoastSeo\Constants\TableNames;

class ProminentWordCopyPreventionHook
{
    /**
     * Prominent words are analysis results that are only ever written by the
     * ProminentWordsService through direct database queries. The only DataHandler
     * datamap writes for this table originate from copy operations (a page copy
     * copies all records located on the page through a nested datamap), which
     * corrupt the rows: all columns without TCA configuration (uid_foreign,
     * tablenames and site) are dropped and end up with their database defaults.
     * The copied page is re-analyzed on first use, so instead of carrying the
     * rows over, DataHandler writes to this table are prevented entirely.
     */
    public function processDatamap_beforeStart(DataHandler $dataHandler): void
    {
        unset($dataHandler->datamap[TableNames::PROMINENT_WORD]);
    }
}
