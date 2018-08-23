# Change Log

This changelog is according to [Keep a Changelog](http://keepachangelog.com).

All notable changes to this project will be documented in this file.
We will follow [Semantic Versioning](http://semver.org/).

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
