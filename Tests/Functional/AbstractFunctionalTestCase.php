<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Tests\Functional;

use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractFunctionalTestCase extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/yoast_seo',
    ];

    protected array $coreExtensionsToLoad = [
        'seo',
    ];

    protected function setUpBackendRequest(): void
    {
        $GLOBALS['LANG'] = $this->getContainer()->get(LanguageServiceFactory::class)->create('default');

        $request = new ServerRequest();
        $request = $request->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $GLOBALS['TYPO3_REQUEST'] = $request->withUri(new Uri('http://localhost/'));
    }
}
