/*global define, top, TYPO3, YoastConfig */

define(['jquery', 'ckeditor'], function ($, CKEDITOR) {
    'use strict';

    CKEDITOR.on('instanceReady', function(){
        let content = '';

        for (var instance in CKEDITOR.instances) {
            content += CKEDITOR.instances[instance].getData();
        }

        let worker = createAnalysisWorker(false);
        console.log(worker);
    });
});


