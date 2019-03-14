/*global define, top, TYPO3, YoastConfig */

define(['jquery', 'ckeditor'], function ($, CKEDITOR) {
    'use strict';

    function getData() {
        let content = '';

        for (let instance in CKEDITOR.instances) {
            content += CKEDITOR.instances[instance].getData();
        }

        console.log(content);
        return content;
    }

    $(function () {
        CKEDITOR.on('instanceReady', function(){
            getData();
            for (let instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].on('change', function (evt) {
                    getData();
                })
            }
        });
    });
});


