Yoast SEO for TYPO3
======================

[![Build Status](https://travis-ci.org/Yoast/Yoast-SEO-for-TYPO3.svg?branch=master)](https://travis-ci.org/Yoast/Yoast-SEO-for-TYPO3)
[![Stable Version](https://poser.pugx.org/yoast-seo-for-typo3/yoast_seo/v/stable.svg)](https://packagist.org/packages/yoast-seo-for-typo3/yoast_seo)
[![License](https://poser.pugx.org/yoast-seo-for-typo3/yoast_seo/license.svg)](https://packagist.org/packages/yoast-seo-for-typo3/yoast_seo)

This plugin integrates text analysis and assessment from [YoastSEO.js](https://github.com/Yoast/YoastSEO.js). Content analysis can generate interesting metrics about a text and give you an assessment which can be used to improve the text.

## Installation
You can easily install the plugin with [Composer](https://getcomposer.org/). Just use the following command in the root of your project:  

```bash
composer require yoast-seo-for-typo3/yoast_seo
```

## Configuration
There is no need for configuration although it is recommended to remove all other SEO related plugins creating metatags in frontend. There are however some configuration options available. More information about configuring Yoast SEO for TYPO3 can be found in the manual on [https://docs.typo3.org](https://docs.typo3.org/typo3cms/extensions/yoast_seo/). 

### Changing Frontend behaviour
As it has always been, you can change frontend behaviour of `yoast_seo` via TypoScript. Check the [current file](Configuration/TypoScript/setup.txt) for reference.

## Reporting bugs / Contributions
Anyone is welcome to contribute to Yoast SEO for TYPO3. Please
[read the guidelines](.github/CONTRIBUTING.md) for contributing to this
repository and how to report bugs.

There are various ways you can contribute:

* [Raise an issue](https://github.com/Yoast/t3ext-yoast-seo/issues) on GitHub.
* Send us a Pull Request with your bug fixes and/or new features.


