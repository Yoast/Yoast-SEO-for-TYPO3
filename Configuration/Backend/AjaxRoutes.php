<?php

return [
    'yoast_preview' => [
        'path' => 'yoast/preview',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::previewAction'
    ],
    'yoast_save_scores' => [
        'path' => 'yoast/savescores',
        'target' => \YoastSeoForTypo3\YoastSeo\Controller\AjaxController::class . '::saveScoresAction'
    ]
];
