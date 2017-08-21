Yoast SEO for TYPO3
======================

[![Stable Version](https://poser.pugx.org/yoast-seo-for-typo3/yoast_seo/v/stable.svg)](https://packagist.org/packages/yoast-seo-for-typo3/yoast_seo)
[![License](https://poser.pugx.org/yoast-seo-for-typo3/yoast_seo/license.svg)](https://packagist.org/packages/yoast-seo-for-typo3/yoast_seo)

This plugin integrates text analysis and assessment from [YoastSEO.js](https://github.com/Yoast/YoastSEO.js). Content analysis can generate interesting metrics about a text and give you an assessment which can be used to improve the text.

## Installation
You can easily install the plugin with [Composer](https://getcomposer.org/). Just use the following command in the root of your project:  

```bash
composer require yoast-seo-for-typo3/yoast_seo
```

## Configuration
There is no need for configuration although it is recommended to remove all other SEO related plugins creating metatags in frontend.

However, a few lowlevel things can still be configured using an extension that overwrites the `EXTCONF` of `yoast_seo`.

### Make your extension overwrite yoast_seo
Set your configuration extension's `ext_emconf.php` accordingly:
```php
    'constraints' =>
        [
            'depends' =>
                [
                    'typo3' => '7.6.0-8.7.99',
                    'yoast_seo' => '*'
                ],
            'conflicts' => [],
            'suggests' => [],
        ],
```

Now, you can simply add the following snipptes to the `ext_localconf.php` of your configuration extension and change them according to your needs.

#### Show / Hide tabs
You can completely hide certain tabs from the backend module.

**WARNING:** *This is only a usability change and won't properly protect the access to the functionalities of the respective tabs in a secure way!*


```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['viewSettings'] = array (
	 'showAnalysisTab' => true
	,'showSocialTab'   => true
	//HIDE: ,'showAdvancedTab' => true,
);
```

#### Show / Hide menu entries
You can completely hide certain entries from the top menu.

**WARNING:** *This is only a usability change and won't properly protect the access to the functionalities of the respective tabs in a secure way!*


```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['menuActions'] = array (
	 ['action' => 'edit', 'label' => 'edit']
	// HIDE: ,['action' => 'dashboard', 'label' => 'dashboard']
	// HIDE: ,['action' => 'settings', 'label' => 'settings']
);
```

#### Overwrite the PreviewDomain to a custom value
You can change the `previewDomain` setting. The default behaviour is, that `yoast_seo` will get the domain from `sys_domain`, just like the normal preview functionality of TYPO3.
```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewDomain'] = 'demo.typo3.local';
```

#### Change the PreviewURL Template
You can change the script-path of the preview, e.g. usable for SPAs.
```php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['previewUrlTemplate'] = '/#%d&type=%d&L=%d';
```

### Changing Frontend behaviour
As it has always been, you can change frontend behaviour of `yoast_seo` via TypoScript. Check the [current file](Configuration/TypoScript/setup.txt) for reference.

## Reporting bugs / Contributions
Anyone is welcome to contribute to Yoast SEO for TYPO3. Please
[read the guidelines](.github/CONTRIBUTING.md) for contributing to this
repository and how to report bugs.

There are various ways you can contribute:

* [Raise an issue](https://github.com/Yoast/t3ext-yoast-seo/issues) on GitHub.
* Send us a Pull Request with your bug fixes and/or new features.


