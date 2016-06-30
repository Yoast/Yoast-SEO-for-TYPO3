<?php
$EM_CONF[$_EXTKEY] = array(
	'title' => 'Yoast SEO',
	'description' => '',
	'category' => 'misc',
	'author' => 'Yoast SEO for TYPO3',
	'author_company' => 'MaxServ B.V., Yoast',
	'author_email' => '',
	'clearCacheOnLoad' => 0,
	'dependencies' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'version' => '0.0.1',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
	'autoload' => array(
		'psr-4' => array('YoastSeoForTypo3\\YoastSeo\\' => 'Classes')
	),
	'conflicts' => '',
	'suggests' => array(),
);
