# Change Log

This changelog is according to [Keep a Changelog](http://keepachangelog.com).

All notable changes to this project will be documented in this file.
We will follow [Semantic Versioning](http://semver.org/).

## UNRELEASED

### Breaking
- Replaced the `urlToCheck` with a new `ModifyPreviewUrl` event

### Added
- Added "Flesh reading score" to the Insights
- Added "Reading time" to the Insights
- Added "Word count" to the Insights
- New testing setup for unit, functional and acceptance tests 
- Translations script to download translations from GlotPress (wordpress)

### Changed
- Updated all the underlying Yoast libraries to the latest version
  - Added custom combine-reducers package to replace the existing one with CSP issues
- Moved all build related files to the `Build` directory
- Lots of splitting up code and moving it to separate classes
- Updates scss files to work with the new dark mode in TYPO3 13
- Labels for Readability and SEO score are now taken from the Yoast translations instead of xlf
- Moved `tx_yoastseo_hide_snippet_preview` to the SEO tab
- PHPStan level set to 8

### Fixed
- CSP issues within the `Cornerstone` form element and the Dashboard backend module
- Dark mode styling for the Dashboard and Overview backend module
- Ajax Calls for `saveScores` and `prominentWords` have the correct JSON header now
- (TYPO3 12+) Language id is now taken from `moduleData` within the request object for displaying the snippet preview
  - All languages mode now shows the default language instead of nothing
- Optimized images that are used in the Dashboard module and load them directly instead of through `f:image`
  - Removed old unused images
- Translations are now correctly loaded based on the backend locale 
- `og_image` and `twitter_image` now have the correct `foreign_match_fields` -> `tablenames` when using the Record functionality

## 10.1.0 January 27, 2025
### Added
- `TypoScriptStructuredDataProvider` to add structured data to the page, configured with TypoScript (premium functionality)

### Fixed
- Renamed `JavascriptModules.php` to `JavaScriptModules.php`

### Removed
- Premium support mention within the documentation

## 10.0.0 October 15, 2024
### Breaking
- Dropped support for TYPO3 10
- Dropped support for PHP 7
- Removed `DbalService` which was used to support older `doctrine/dbal` versions for TYPO3 10
- Removed `ArrayPaginator` which was used to support TYPO3 10
- Changed the structure of the overview filters for the `Overview` backend module
  - Instead of registering it in the `EXTCONF` the filters are now registered through the `Services.yaml`
  - Extra methods have been added to the `OverviewDataProviderInterface` as a replacement of the array configuration
  - DataProviders are now initialized with a new `DataProviderRequest` DTO object

### Added
- Support for TYPO3 13
- Upgrade wizard to migrate the dashboard widget "Pages without meta description" to the core widget

#### Changed
- Update ddev configuration with latest changes from https://github.com/a-r-m-i-n/ddev-for-typo3-extensions
  - Added `ddev composer-install-dashboard $VERSION` and `ddev composer-remove-dashboard $VERSION` commands to simplify testing dashboard widgets
  - Added `ddev composer-update-all $VERSION` to update all composer dependencies to the latest version

## 9.0.3 December 18, 2023
### Fixed
- Prevent undefined array key warning within `LinkingSuggestionsService`
- Missing label of the tx_yoastseo_prominent_word table
- Removed exclude=true from tx_yoastseo_prominent_word fields, table already has hideTable
- Use `websiteTitle` of site configuration within snippet preview, previously this was only taken from site languages instead of the site itself

### Changed
- Updated mentions of the premium extension within the documentation

## 9.0.2 September 18, 2023
### Fixed
- Show warning on linking suggestions when the language cannot be retrieved from the content element, use the default language in case "All Languages" is selected
- Added extra checks within the metatag generators in case fields do not exist (opengraph, twitter, robots)
- `hasSitemapFields` now correctly returns the `sitemapFields` instead of `yoastSeoFields`
- Filter the columns to import from the old premium database table before inserting in the new table within `MigratePremiumFocusKeywords`
- `access` from `user,group` to `user` within the configuration for backend modules (`Configuration/Backend/Modules.php`)

## 9.0.1 July 6, 2023
### Fixed
- Content Security Policy issues in CMS12 by adding extra parameter within BackendYoastConfig and removing unnecessary inline code within FocusKeywordAnalysis.html
- Removed loading of `extended-list-widget.min.css` within `PageOverviewWidget`
- `doctrine/dbal` 2.10 support by adding `DbalService` which checks for the TYPO3 major version, if 10 then use the old `fetch` methods
- Loading state of internal linking suggestions when the bodytext is empty (e.g. new content element)
- Avoid exceptions because of broken showitem tt_content types
- PHP warnings within several Form classes due to non-existing doktype index within databaseRow array
- Favicons with relative urls or urls with a port
- Caught InvalidArgumentException within BreadcrumbStructuredDataProvider when the current site language does not exist

## 9.0.0 June 12, 2023
### Breaking
- Dropped support for CMS9
- API for `overview_filters` has changed, instead of `getData` providers should have a `getResults(array $pageIds)` method (see `OverviewDataProviderInterface`)
- Removed `get_focus_keyword` hook, was not used anymore

### Added
- Support for CMS12
- New feature to automatically activate Yoast SEO functionality on custom records
- Upgrade wizard for migrating premium redirects to `sys_redirect`
- Features from the premium version:
  - Related keyphrases with their own synonyms
  - Insights to show prominent words on a page
  - Internal linking suggestions within content elements based on the prominent words
  - Crawler backend module to update Yoast data for complete sites
  - Orphaned content view within the Overview module
  - Advanced robots: nosnippet, noimageindex, noarchive
  - Typoscript functionality for Structured Data
  - Dashboard widgets for Orphaned content and pages without meta description
- Upgrade wizards for migration premium focus keywords and redirects
- Introduced custom ModeSwitcher to create a different Mobile/Desktop switcher within the preview

### Changed
- Restructured backend modules to work with CMS10, CMS11 and CMS12. Introduced `legacy` actions for CMS10
- Simplified `ext_localconf` and `ext_tables.php`
  - Removed `TYPO3_MODE` checks
  - Introduced `ConfigurationUtility` which provides the default configuration and nodes for the formEngine and
  - Merged adding the `YoastConfig` javascript and backend CSS into one `BackendYoastConfig` hook
- Introduced `SnippetPreviewService` which both `PageLayoutHeader` and `SnippetPreview` use to render the snippet preview
- Optimized `SnippetPreview` by removing unnecesary code and raw html
- Simplified `PageLayoutHeader`, moved all html to separate fluid template, removed `AbstractPageLayoutHeader` and `PageLayoutHeaderService`
- Simplified all the FormEngine node classes
- The backend module `Overview` now needs a valid page selected from the tree, this is to retrieve the languages from the currently selected site
- Updated `AdditionalPreviewData`, removed old methods to check for `getWebsiteTitle` (always available now)
- Updated dev-dependencies to latest (usable) versions and fixed CI build
- Removed old `linkingSuggestions.js` from `dist`
- Removed upgrade wizards for old canonical and seo title fields
- Simplified `.gitattributes`
- Updated and removed old grunt configuration, fixed sass build

### Removed
- Premium backend module
- All references for premium including all related javascript modals

## 9.0.0-alpha-1 August 16, 2022
### Breaking
- Dropped support for PHP <7.4

### Added
- New feature to automatically activate Yoast SEO functionality on custom records

### Changed
- All javascript related build configuration (grunt, webpack) has been moved to the `grunt` directory
- All build related files (phpstan, phplint, php-cs-fixer) has been moved to the `.Build` directory
- Removed ext_icon.png, Extension.svg already present in `Resources/Public/Icons`
- Updated all code with typed properties and arguments, minor cleanups

## 8.3.1 August 3, 2022
### Fixed
- Github CI pipelines by adding the correct "allow-plugins" to composer.json

## 8.3.0 August 3, 2022
### Fixed
- Lists within the Overview module now take the "DB Mounts" of a user into account
- Optimized pagination of Overview module
- Fixed PHP8 error when the metatag `x-yoast-title-config` (for some reason) cannot be read from the frontend
- Added extra try-catch block to catch guzzle exceptions within `PreviewService`
- Path to the Yoast Backend CSS for the new composer mode

## 8.2.0 June 23, 2022
### Added
- Support for new TYPO3 composer mode

### Changed
- Replaced javascript React classes with functional components
- Updated a lot of npm packages
- Updated documentation structure

### Fixed
- Adjusted analysis calls when there is more than 1 related keyphrase due to bug in "yoastseo" npm package
- PHP8 fixes:
    - Prevent 'undefined array key' warnings with PHP8 when `EXTCONF` allowedDoktypes missing
    - Warning within `FocusKeywordAnalysis.php`
    - Notice exceptions within `SnippetPreview` and `PreviewService`
- Ignore hidden pages within `BreadcrumbStructuredDataProvider`
- CGL issue within `YoastUtility`
- Wrong PHP array Syntax within StructuredDataProvider documentation
- Deprecated extension name usage within ext_tables.php
- Loading issues with the title and description progress bar
- Display issue with the title progress bar within CMS11
- Usage of deprecated cache name within `StructuredDataProviderManager`

## 8.1.0 November 24, 2021
### Added
- PHP8 Support (for CMS11)
- Fully updated the documentation

### Fixed
- `getPreviewPageId` now always returns an integer (used for custom records analysis)
- Removed old include of `constants.typoscript`
- Fatal error from `PageLayoutHeader` when you select "All Languages" in CMS11 is now gone
- Composer `branch-alias` is updated
- `.gitattributes` is updated to exclude more files/directories
- Added `min-height` to the preview container to prevent (most of the) layout shifting while preview is loading
- `additionalGetVars` are now url-decoded before generating the preview uri (used for custom records analysis)

## 8.0.0 October 4, 2021
### Breaking changes
- Dropped CMS8 support
  - All AJAX requests are now done through the Backend AjaxRoutes
  - Canonical and XmlSitemap functionality has been removed, provided by `EXT:seo`
  - Support for `pages_language_overlay` is removed
- Dropped the `urlToCheck` hook for `PageLayoutHeader` which was introduced by moving the `getUriToCheck`, this is now only available for `UrlService` (as it was before)
- Without a meta description, only a placeholder is being shown in the snippet preview. Previously content from the page was being shown, but this hurts the analysis.

### Added
- CMS11 support
- Introduced strict typing and return-types to all classes
- `YoastConfigInlineJs` hook to make it possible to add to the YoastConfig JSON from multiple classes
- Replaced the `widget.paginate` viewhelper call with within the Overview functionality with the Pagination API, added backport classes for CMS9
- Placeholders for the Premium functionalities "Insights" and "Advanced robots"

### Changed
- Updated underlying yoastseo javascript libraries
- Restructured `PageLayoutHeader` hook by introducing `AbstractPageLayoutHeader` and `PageHeaderService` to save generated information
- Removed RouteEnhancerError check within `PageLayoutHeader` because all AJAX requests are now done through Backend AjaxRoutes
- Moved `getUriToCheck` method to `UrlService`
- Updated Premium information within backend module and all modals
- Removed `linkingSuggestions` javascript from extension, unused and caused an extra 2.5MB on the extension size
- Huge cleanup of whole extension

### Fixed
- Check if the content returned by the HTTP request for the snippet preview is not `false`
- Removed unused `$invert` functionality within `stripTagsContent` in `PreviewService`
- Removed semicolon from generated LD+JSON
- Changed `break` to `continue` within `StructuredDataProviderManager` to prevent only first provider rendering from cache
- Set TCA fields without a database column to `type=none`
- `TYPO3_MODE=FE` checks added to the pageRenderer hooks to prevent errors in other extensions
- Re-added `worker.js` to Javascript build
- php-cs-fixer configuration

## 7.2.4 August 12, 2021
### Fixed
- additionalGetVars are working again for CMS8 (used for record analysis)
- Removed accidental `git` keyword within content reducer to avoid javascript console errors
- `locale` extraction, which is used to determine the language for analysis, now works for locales with a hyphen (e.g. `en-US`) and for html tags with extra attributes

## 7.2.3 August 10, 2021
### Fixed
- Removed XSS possibilities within snippet preview and structured data

## 7.2.2 June 25, 2021
### Fixed
- `twitter:card` is now rendered for CMS8
- Records with language setting "All" (`-1`) have a fallback to language id `0` now
- `urltoCheck` hook works again, regression fix as the url-generation had been moved to `AjaxController`
- Minor updates in various javascript-dependencies

## 7.2.1 April 26, 2021
### Fixed
- Passing through the completely generated uri as an `uriToCheck` parameter has been removed, url generating now happens within AjaxController based on page and language parameter

## 7.2.0 Februari 19, 2021
### Added
- Possibility to use stdWrap within sitemap typoscript
### Fixed
- Regular expression to parse body content is optimized
- The default favicon.ico is now loaded on subpages
- The preview page id for translated records (like EXT:news) is now correctly set

## 7.1.3 December 23, 2020
### Fixed
* Excluded some more unnecessary files and folders from the TER release
* Changed comment for TER release and mention this changelog

## 7.1.2 December 23, 2020
### Fixed
* Excluded unnecessary files and folders from the TER release so it is not to big to publish

## 7.1.1 December 23, 2020
### Fixed
* Added missing extension key for TER release script

## 7.1.0 December 23, 2020
### Added
* If your site is secured with basic auth, we have you covered now. You can set the username and password in the settings so you are able to analyse pages that are secured by basic auth. Please be careful with adding credentials to your repository!

### Fixed
* Special characters in the title of a page are now rendered correctly in the snippet preview

### Changed
* Added automatic deployments to the TYPO3 Extension Repository
* A warning is added to the documentation that the Sitemap of EXT:yoast_seo should only be used in TYPO3 v8.

## 7.0.7 December 9, 2020
### Fixed
* It should not matter if a backend user has backend access to the page which is used to preview. This is mainly when using Yoast SEO for records other than pages and the detail page itself is not accessible for the backend user.

## 7.0.6 November 20, 2020
### Fixed
* The script adding additional information for the preview now checks if the getWebsiteTitle method exists before calling it. This prevents errors in TYPO3 v9 as this method is not availalbe there.

## 7.0.5 November 17, 2020
### Fixed
* Cast value of cookie to a string to prevent errors in log

## 7.0.4 November 16, 2020
### Fixed
* Make sure both strings and integers can be used when setting allowed doktypes
* Use the site title of the site configuration when this property is set.
* Removed usage of $_EXTKEY in ext_tables.php to prevent other extensions to crash.

### Changed
* Changed the link to TypoScript file in documentation
* The JSON-LD tag will only be rendered when structured data is available

## 7.0.3 September 21, 2020
### Fixed
* Make sure the path to the backend CSS is always available to show a modal. This should fix the problem of a not being able to edit a content element after updating to 7.0.2

## 7.0.2 September 19, 2020
### Fixed
* Removed hardcoded links to typo3conf/ext folder
* Fixed fontsize of text width measurement to ensure we get same calculations as in the WordPress plugin of Yoast SEO

## 7.0.1 September 7, 2020
### Changed
* Added a placeholder to seo_title field to show the user what the value will be if no specific SEO title is set
* Removed negative margin to avoid issues with other plugins
* Updated dependencies

### Fixed
* When changing tabs in the page properties, tje preview keeps working

## 7.0.0 August 10, 2020
### Breaking changes
* We removed the possibility to set the doktypes that needs to be analysed from within TypoScript. The doktypes are needed during the TCA setup and by using TypoScript some installations did crash when no database connection was already in place. The feature was never documented but it was possible so this is marked as a breaking change. Please set your allowed doktypes in your ext_localconf.php as described in the [documentation](https://docs.typo3.org/p/yoast-seo-for-typo3/yoast_seo/6.0/en-us/Configuration/SnippetPreview/Index.html#enable-snippet-preview-on-specific-page-types).

### Added
* A [DDEV](https://ddev.readthedocs.io/en/stable/) setup is now included in the repository to make it easy to contribute to this project and check the results in TYPO3 v8, 9 and 10. We added some information in the documentation as well.
* To be able to alter the URL that should be analysed, we introduced a hook after the link is generated. With this hook, you have the possibility to add your own logic to determine which URL to analyse. More information can be found in the [documentation](https://docs.typo3.org/p/yoast-seo-for-typo3/yoast_seo/6.0/en-us/).

### Changed
* There was a library included which was not used anymore so we removed the dependency to the nimut/testing-framework package.
* Moved from Travis to GitHub actions for the CI process

### Fixed
* Removed include of empty TypoScript file which caused errors in TypoScript Object Manager when installed in non-composer-mode.
* Add missing return true to executeUpdate function in CanonicalFieldUpdate.php.
* The canonical and title fields are now rendered correctly in frontend again in TYPO3 v8
* Removed wrong information about usage of EXT:realurl in the documentation
* Fixed issue in output of structured data that caused warnings when more inline javascript was added to the frontend.
* The canonical link in TYPO3 v8 is now checking the right GET parameters while building the canonical

## 6.0.1 April 22, 2020
### Fixed
* We fixed the error in the Upgrade Wizard in CMS 9 and 10.

## 6.0.0 April 21, 2020
### Added
* You can use Yoast SEO now with TYPO3 v10 LTS. We did the update without losing support for TYPO3 v8. So this update is available for TYPO3 v8, v9 and v10.
* Added word form support for Dutch (Yoast library)
* Added the transition word assessment for Hungarian (Yoast library)

### Changed
* Updated Snippet Preview layout (Yoast library)

## 5.1.0 September 20, 2019
### Added
* Visually updated snippet preview to match latest changes on Google.
* Favicons visible in mobile snippet preview
* Added proper error message when server is not able to access the page it wants to analyse.

### Changed
* You can set the doktypes that Yoast should analyse now by `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']` in stead of TypoScript. It will still have a fallback to the old TypoScript configuration. ([#283](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/283))
* Altered the [documentation](https://docs.typo3.org/p/yoast-seo-for-typo3/yoast_seo/master/en-us/) a little bit.

### Fixed
* Restored the compatibility with PHP 7.0 ([#266](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/266))
* Prevent caching of page when analysing a page that is disabled. ([#272](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/272))
* When the base in site configuration is only `/`, the absolute URL (which is needed by the analysis) will now be based on the current domain. [#279](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/279)
* Made sure that if you set the allowed doktypes via `$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['yoast_seo']['allowedDoktypes']`, the doktypes will always be available when defining which fields in the page properties.  ([#283](https://github.com/Yoast/Yoast-SEO-for-TYPO3/issues/283))
* The right URL to analyse the content is now generated when you have a multi-site setup

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
