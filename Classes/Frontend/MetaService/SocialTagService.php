<?php
namespace YoastSeoForTypo3\YoastSeo\Frontend\MetaService;


use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class SocialTagService implements TagRendererServiceInterface
{

	/**
	 * @param TypoScriptFrontendController $typoScriptFrontendController
	 *
	 * @return string
	 */
	public function render(TypoScriptFrontendController $typoScriptFrontendController)
	{
		$socialTags = '';
		if ($typoScriptFrontendController->page['tx_yoastseo_facebook_title'])
		{
			$socialTags = '<meta property="og:title" content="' . $typoScriptFrontendController->page['tx_yoastseo_facebook_title'] . '">';
		}
		if ($typoScriptFrontendController->page['tx_yoastseo_facebook_description'])
		{
			$socialTags .= '<meta property="og:description" content="' . $typoScriptFrontendController->page['tx_yoastseo_facebook_description'] . '">';
		}
		if ($typoScriptFrontendController->page['tx_yoastseo_facebook_image'])
		{
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			if($fileRepository instanceof FileRepository)
			{
				$image = $fileRepository->findByRelation('pages', 'tx_yoastseo_facebook_image', $typoScriptFrontendController->id);
				$firstImage = $image[0];
				if ($firstImage instanceof FileReference)
				{
					$configuration['parameter'] = $firstImage->getPublicUrl();
					$configuration['forceAbsoluteUrl'] = true;
					$configuration['useCashHash'] = true;
					$imageUrl = $typoScriptFrontendController->cObj->typoLink_URL($configuration);
				}
			}
			if ($imageUrl)
			{
				$socialTags .= '<meta property="og:image" content="' . $imageUrl . '">';
			}

		}
		if ($typoScriptFrontendController->page['tx_yoastseo_twitter_title'])
		{
			$socialTags .= '<meta property="twitter:title" content="' . $typoScriptFrontendController->page['tx_yoastseo_twitter_title'] . '">';
		}
		if ($typoScriptFrontendController->page['tx_yoastseo_twitter_description'])
		{
			$socialTags .= '<meta property="twitter:description" content="' . $typoScriptFrontendController->page['tx_yoastseo_twitter_title']. '">';
		}
		if ($typoScriptFrontendController->page['tx_yoastseo_twitter_image'])
		{
			$fileRepository = GeneralUtility::makeInstance(FileRepository::class);
			if($fileRepository instanceof FileRepository)
			{
				$image = $fileRepository->findByRelation('pages', 'tx_yoastseo_twitter_image', $typoScriptFrontendController->id);
				$firstImage = $image[0];
				if ($firstImage instanceof FileReference)
				{
					$configuration['parameter'] = $firstImage->getPublicUrl();
					$configuration['forceAbsoluteUrl'] = true;
					$configuration['useCashHash'] = true;
					$imageUrl = $typoScriptFrontendController->cObj->typoLink_URL($configuration);
				}
			}
			if ($imageUrl)
			{
				$socialTags .= '<meta property="twitter:image" content="' . $imageUrl . '">';
			}
		}

		return $socialTags;
	}
}