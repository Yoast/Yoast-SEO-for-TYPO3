<?php
declare(strict_types=1);
namespace YoastSeoForTypo3\YoastSeo\Utility;


use Composer\InstalledVersions;
use Composer\Semver\Comparator;

class ComposerUtility
{
    const PACKAGE_NAME_OF_CMS_COMPOSER_INSTALLERS = 'typo3/cms-composer-installers';

    public static function isNewComposerMode(): bool
    {
        if(\class_exists(InstalledVersions::class) === false) {
            return false;
        }
        $isInstalled = InstalledVersions::isInstalled(self::PACKAGE_NAME_OF_CMS_COMPOSER_INSTALLERS);
        if($isInstalled === false) {
            return false;
        }
        $versionOfCmsComposerInstallers = InstalledVersions::getPrettyVersion('typo3/cms-composer-installers');
        $lessThan = Comparator::lessThan($versionOfCmsComposerInstallers, 'v4');
        if($lessThan === true) {
            return false;
        }
        return true;
    }
}
