<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use YoastSeoForTypo3\YoastSeo\Controller\AjaxController;

return [
    'yoast_save_scores' => [
        'path' => 'yoast/savescores',
        'target' => AjaxController::class . '::saveScoresAction',
    ],
    'yoast_prominent_words' => [
        'path' => 'yoast/prominentwords',
        'target' => AjaxController::class . '::prominentWordsAction',
    ],
    'yoast_internal_linking_suggestions' => [
        'path' => 'yoast/internallinkingsuggestions',
        'target' => AjaxController::class . '::internalLinkingSuggestionsAction',
    ],
    'yoast_crawler_determine_pages' => [
        'path' => 'yoast/crawlerdeterminepages',
        'target' => AjaxController::class . '::crawlerDeterminePages',
    ],
    'yoast_crawler_index_pages' => [
        'path' => 'yoast/crawlerindexpages',
        'target' => AjaxController::class . '::crawlerIndexPages',
    ],
];
