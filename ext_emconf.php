<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Yoast SEO for TYPO3',
    'description' => 'Optimise your website for search engines with Yoast SEO for TYPO3. With this extension you get all the technical SEO stuff you need and will help editors to write high quality content.',
    'category' => 'misc',
    'author' => 'MaxServ / Yoast',
    'author_company' => 'MaxServ B.V., Yoast',
    'author_email' => '',
    'clearCacheOnLoad' => 0,
    'dependencies' => '',
    'state' => 'stable',
    'uploadfolder' => 0,
    'version' => '4.1.0',
    'constraints' => array(
        'depends' => array(
            'typo3' => '9.5.0-9.5.99',
            'seo' => ''
        ),
        'conflicts' => array(),
        'suggests' => array(
        ),
    ),
    'autoload' => array(
        'psr-4' => array('YoastSeoForTypo3\\YoastSeo\\' => 'Classes')
    ),
    'conflicts' => '',
    'suggests' => array(),
);
