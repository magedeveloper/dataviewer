<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend\Render;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class TemplateFileViewHelper extends AbstractRenderViewHelper
{
    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Arguments initialization
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('id', 'string', 'Template File Identifier', true);
    }

    /***
     * @return array|null
     * @throws InvalidConfigurationTypeException
     */
    protected function _getTemplateConfiguration(): ?array
    {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $templateConfiguration = $configuration['plugin.']['tx_dataviewer.']['templates.'];

        if(is_array($templateConfiguration)) {
            return GeneralUtility::removeDotsFromTS($templateConfiguration);
        }

        return null;
    }

    /**
     * @return string
     * @throws InvalidConfigurationTypeException
     */
    public function render(): string
    {
        $templateConfiguration = $this->_getTemplateConfiguration();

        if (is_array($templateConfiguration) && array_key_exists($this->arguments['id'], $templateConfiguration)) {
            $singleTemplateConfiguration = $templateConfiguration[$this->arguments['id']];
            $string = '';
            if(isset($singleTemplateConfiguration['icon'])) {
                $filePath = GeneralUtility::getFileAbsFileName($singleTemplateConfiguration['icon']);
                $filePath = PathUtility::getAbsoluteWebPath($filePath);
                $string .= '<img src="'.$filePath.'" border="0">'. ' ';
            }

            if(isset($singleTemplateConfiguration['name'])) {
                $string .= $singleTemplateConfiguration['name'];
            }

            if(isset($singleTemplateConfiguration['file'])) {
                return $string . ' ' . '(' . $singleTemplateConfiguration['file'] . ')';
            }

        }

        return '<div class="alert alert-info" style="margin:0;"><strong>'.'DEBUG'.'</strong></div>';
    }
}
