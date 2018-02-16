# Change Log

This changelog is according to [Keep a Changelog](http://keepachangelog.com).

All notable changes to this project will be documented in this file.
We will follow [Semantic Versioning](http://semver.org/).

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
