# Change Log

This changelog is according to [Keep a Changelog](http://keepachangelog.com).

All notable changes to this project will be documented in this file.
We will follow [Semantic Versioning](http://semver.org/).

## Yoast SEO Premium for TYPO3
Besides the free version of our plugin, we also have a premium version. The free version enables you to do all necessary optimizations. With the premium version, we make it even easier to do! More information can be found on https://www.maxserv.com/yoast.

## Unreleased
### Changed
* You can set the doktypes that Yoast should analyse now by `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']` in stead of TypoScript. It will still have a fallback to the old TypoScript configuration. ([#283](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/283))
* Altered the [documentation](https://docs.typo3.org/p/yoast-seo-for-typo3/yoast_seo/master/en-us/) a little bit.

### Fixed
* Restored the compatibility with PHP 7.0 ([#266](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/266))
* Prevent caching of page when analysing a page that is disabled. ([#272](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/272))
* When the base in site configuration is only `/`, the absolute URL (which is needed by the analysis) will now be based on the current domain. [#279](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/279)
* Made sure that if you set the allowed doktypes via `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']`, the doktypes will always be available when defining which fields in the page properties.  ([#283](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/283))  

## 5.0.1 May 2, 2019
### Changed
* Updated Yoast libraries
* Added configuration to exclude certain doktypes in the structured data output for breadcrumbs

### Fixed
* Fixed problems with overview module in CMS8
* Made sure the extension also works with PHP 7.0
* Resolved problems with the upgrade wizard in CMS8
* Removed message in console about translations
* Fixed styling issues with modal in page module
* Preview URL generation in page module is now only done on allowed doktypes making sure you don't get an error on the not allowed doktypes.

## 5.0.0 April 12, 2019
### Added
* Basic schema.org integration helping you to give structured data to search engines. In this basic version, we will help you with your breadcrumbs and your general site information.
* Possibility for extension developers to create their own schema.org data providers
* Focus keyword is now renamed to Focus keyphrase. You can find more information about the difference on https://yoast.com/difference-between-keyword-and-keyphrase/
* All latest features now available for TYPO3 CMS v8LTS and v9LTS 
* New single H1 assessment to check if only one H1 is used within the text

### Changed
* For various languages, we’ll now filter out function words that precede the keyphrase in the title when analysing your title. This means that if you use words like [the], [on] or [what] before your keyphrase in the title, it won’t affect your score.
* The keyword density assessment now takes the length of the focus keyphrase into account, because it can be much harder to use a longer keyphrase in your text. In this new version, you’ll need to use your longer keyphrase less often in the text than a shorter keyphrase to get a green bullet.
* When you have no outbound links on your page, you will get a red bullet instead of an orange one.
* Check if your keyphrase is used in the alt attributes of your images.
* All the technology behind the integration is rewritten so we are future proof and can handle new updates of the Yoast SEO libraries.

### Fixed
* Fixed problem with analysing pages when using a quote in your focus keyword
* When you have larger pages to analyse it could happen in previous versions that your browser crashed. Because we have rewritten the technology to analyse your content, your browser should not crash on large amounts of content anymore.

## 4.1.0 January 16, 2019
### Added
* Added possibility to view snippet preview on disabled pages.

### Changed
* Add check if route-enhancers are configured.
 
### Fixed
* We fixed some multi-domain and SSL related bugs.
* Fixed HTML markup in modals in the page view. 

## 3.0.5 January 16, 2019
### Fixed
* The sitemap will now also show mountpoints and other expected pages and will not show pages that are not available. 
* Fixed problem with RealURL auto configuration that might override earlier set configuration.

## 4.0.0 November 23, 2018
### Changed
* Added support for TYPO3 CMS 9LTS
* In the v4.x branch, we dropped support for 7LTS and 8LTS. The v3.x will still support 7LTS and 8LTS   

## 3.0.4 August 24, 2018
### Fixed
* The update of YoastSEO.js caused some errors that needs to be investigated more. For now we reverted the update of YoastSEO.js
* Fixed error about wrong value hidden when saving a page 

## 3.0.3 August 23, 2018
### Fixed
* Error when creating a page via the context menu is fixed now
* Fixed the last_mod date for page records of the sitemap.xml 

### Changed
* Updated YoastSEO.js to version 1.38.1

## 3.0.2 July 29, 2018
### Fixed
* Fixed missing links in documentation to Sitemap XML documentation

## 3.0.1 July 27, 2018
### Added
* Added documentation for Sitemap XML feature

### Changed
* Updated YoastSEO.js to version 1.36.0

## 3.0.0 July 25, 2018
### Breaking changes
* We removed the configuration for EXT:news and EXT:cal. It worked for people with these extension installed, but for
several other people it was giving errors and warnings. If you are using EXT:news or EXT:cal, please see our manual to
get info on how to configure EXT:news or EXT:cal to use Yoast SEO for TYPO3.

### Added
* A feature a lot of people been waiting for: a sitemap.xml feature. Please check the manual how to configure it for your situation.
* For older TYPO3 versions, we added a PNG icon for the extension.
* We filter out script and style tags in the content for the snippet preview so you won't see any inline JavaScript or CSS anymore in your snippet preview.

### Changed
* Updated YoastSEO.js to version 1.35.5
* We did some cleanups in the TypoScript configuration
* Added some template for issue and pull request creation on GitHub
* Updated license with additional terms

### Fixed
* It is now possible to have quotes in your title or description without getting warnings.
* When you have a multi language site, the preview URL of your translation will now be determined correctly.
* When you configure Yoast SEO for TYPO3 on your own records, it is now possible (but not recommended) to have the title field on another tab as the snippet preview.
* A stupid mistake: the overview of pages without description was actually checking if a SEO title was filled. We fixed that!
* We added some database fields to prevent errors in the database migration tool.
* Also for translated pages the og:image and twitter:image is working correctly now.
* The module icons are now also working correctly in Internet Explorer

## 2.1.0 March 9, 2018
### Added
* You can now mark a page as cornerstone content. Cornerstone content have more strict analysis because it are the most important pages on your website.

### Changed
* Added documentation how to set fallback social images based in multisite setups
* Updated backend modules and added Premium information

### Fixed
* Added some missing backend labels

## 2.0.2 February 26, 2018
### Fixed
* Before showing the snippet preview in the Page module, we will now check if the user has the permissions for the right backend module 
* Fixed some linting issues causing some CI processes to fail

## 2.0.1 February 16, 2018
### Fixed
* You can now set fallback images for social sharing again
* Fixed TypoScript config to make it easier to override width and height of images for social sharing

## 2.0.0 February 13, 2018
### Added
* Possibility to integrate Yoast SEO for TYPO3 in records of third party plugins
* Added integration with EXT:news
* Render more social metatags

### Changed
* Backendmodule is split up in multiple modules to have a more clear menu and the possibility to add new modules quit easily.
* Changed way of defining metatags from template based to TypoScript to make it possible to use the core features to render metatags.
* Show score of analysis in icon and text for accessibility reasons
* Prepared several fields to be named the same as it will be in CMS9.
* Updated YoastSEO.js to version 1.29.0 

### Fixed
* The title prepend and append will not be saved in the databased anymore when saving the SEO title of the field.

## 1.4.1 January 8th, 2018
### Fixed
* Fixed syntax of Twitter cards
* Remove title prepend and append when saving the title in the snippet preview.

## 1.4.0 December 20th, 2017
### Added
* Added translations for backend fields

### Changed
* Updated YoastSEO.js to version 1.28 which contains several enhancements. The most important is "The upper boundary of the meta description length has been changed from 156 to 320 characters"
* We now make sure you can't insert a too long title and description for your Twitter cards.

### Fixed
* TypoScript is now always loaded at the right time

## 1.3.0 December 13th, 2017
### Added
* Added possibility to hide the analysis in Page module in your user settings
* It is now possible to disable the redering of SEO tags by the Yoast plugin in frontend on a specific page
* Add language selector in backend module so you can switch languages in the analysis

### Changed
* We improved the notifications when something went wrong analysing the content. It will close automatically after
5 seconds now and will give you more detailed information.
* Don't use default rendering of title tag from TYPO3 when the Yoast plugin is enabled
* Cleanup class PageMetaRenderer

### Fixed
* Added missing SEO-Title field in Page Language Overlay
* Disable snippet preview in backend, if plugin is disabled at page

## 1.2.3 October 11th, 2017
### Fixed
* Fixed bug with merging other page fields when uploading social image
* Disable snippet preview does now also work in localized pages

For older changelogs, check the releases on [GitHub](https://github.com/Yoast/Yoast-SEO-for-TYPO3/releases)
