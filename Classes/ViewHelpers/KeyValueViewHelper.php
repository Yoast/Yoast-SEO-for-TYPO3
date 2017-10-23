<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class KeyValueViewHelper
 * @package YoastSeoForTypo3\YoastSeo\ViewHelpers
 */
class KeyValueViewHelper extends AbstractViewHelper
{

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * Inject configuration manager
     *
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function initializeArguments()
    {
        $this->registerArgument('obj', 'mixed', '');
        $this->registerArgument('prop', 'mixed', '');
    }

    public function render()
    {
        $obj = $this->arguments['obj'];
        $prop = $this->arguments['prop'];

        $data = [];
        if (is_object($obj)) {
            $data = get_object_vars($obj);
        } elseif (is_array($obj)) {
            $data = $obj;
        }

        return $this->getContentObject($data)->getData($prop, $data);
    }

    /**
     * @param array $data
     * @return ContentObjectRenderer
     */
    protected function getContentObject(array $data)
    {
        $contentObjectRenderer = $this->configurationManager->getContentObject();
        if ($contentObjectRenderer === null) {
            $contentObjectRenderer = $this->objectManager->get(ContentObjectRenderer::class);
            $contentObjectRenderer->start($data);
        }
        return $contentObjectRenderer;
    }
}
