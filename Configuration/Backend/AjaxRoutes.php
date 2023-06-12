<?php

return [
    'yoast_preview' => [
        'path' => 'yoast/preview',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::previewAction'
    ],
    'yoast_save_scores' => [
        'path' => 'yoast/savescores',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::saveScoresAction'
    ],
    'yoast_prominent_words' => [
        'path' => 'yoast/prominentwords',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::promimentWordsAction'
    ],
    'yoast_internal_linking_suggestions' => [
        'path' => 'yoast/internallinkingsuggestions',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::internalLinkingSuggestionsAction'
    ],
    'yoast_crawler_determine_pages' => [
        'path' => 'yoast/crawlerdeterminepages',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::crawlerDeterminePages',
    ],
    'yoast_crawler_index_pages' => [
        'path' => 'yoast/crawlerindexpages',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::crawlerIndexPages'
    ]
];
