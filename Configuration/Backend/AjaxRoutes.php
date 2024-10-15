<?php

use YoastSeoForTypo3\YoastSeo\Controller\AjaxController;

return [
    'yoast_preview' => [
        'path' => 'yoast/preview',
        'target' => AjaxController::class . '::previewAction'
    ],
    'yoast_save_scores' => [
        'path' => 'yoast/savescores',
        'target' => AjaxController::class . '::saveScoresAction'
    ],
    'yoast_prominent_words' => [
        'path' => 'yoast/prominentwords',
        'target' => AjaxController::class . '::promimentWordsAction'
    ],
    'yoast_internal_linking_suggestions' => [
        'path' => 'yoast/internallinkingsuggestions',
        'target' => AjaxController::class . '::internalLinkingSuggestionsAction'
    ],
    'yoast_crawler_determine_pages' => [
        'path' => 'yoast/crawlerdeterminepages',
        'target' => AjaxController::class . '::crawlerDeterminePages',
    ],
    'yoast_crawler_index_pages' => [
        'path' => 'yoast/crawlerindexpages',
        'target' => AjaxController::class . '::crawlerIndexPages'
    ]
];
