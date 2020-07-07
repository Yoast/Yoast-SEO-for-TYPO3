<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] = array(
    '_DEFAULT' =>
        array(
            'init' =>
                array(
                    'appendMissingSlash' => 'ifNotFile,redirect',
                    'emptyUrlReturnValue' => '/',
                ),
            'pagePath' =>
                array(
                    'rootpage_id' => '1',
                ),
            'preVars' =>
                array(
                    0 =>
                        array(
                            'GETvar' => 'L',
                            'valueMap' => array(
                                'nl' => 1,
                            ),
                            'noMatch' => 'bypass',
                        ),
                ),
            'fileName' =>
                array(
                    'defaultToHTMLsuffixOnPrev' => 0,
                    'acceptHTMLsuffix' => 1,
                    'index' =>
                        array(
                            'print' =>
                                array(
                                    'keyValues' =>
                                        array(
                                            'type' => 98,
                                        ),
                                ),
                        ),
                ),
        ),
);
