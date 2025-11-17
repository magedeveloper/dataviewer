<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Settings\Plugin;

use Aix\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use Aix\Dataviewer\Service\Settings\AbstractSettingsService;
use Aix\Dataviewer\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PluginSettingsService extends AbstractSettingsService
{
    /**
     * Template Selection
     * @var string
     */
    const TEMPLATE_SELECTION_DEBUG          = 'DEBUG';
    const TEMPLATE_SELECTION_CUSTOM			= 'CUSTOM';
    const TEMPLATE_SELECTION_FLUID			= 'FLUID';

    /**
     * Plugin Name
     * @var string
     */
    protected $extensionName = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setExtensionName( 'tx_' . Configuration::EXTENSION_KEY );
    }

    /**
     * Sets the extension name
     *
     * @param string $extensionName
     * @return void
     */
    public function setExtensionName(string $extensionName): void
    {
        $this->extensionName = $extensionName;
    }

    /**
     * Gets the extension name
     *
     * @return string
     */
    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    /**
     * Gets the configured variable name
     * for records
     *
     * @return string
     */
    public function getRecordsVarName(): string
    {
        $name = (string)$this->getConfiguration('plugin.tx_dataviewer.settings.recordsVariableName');
        if(!is_string($name)) {
            return 'records';
        }
        return StringUtility::createCodeFromString($name);
    }

    /**
     * Gets the configured variable name for
     * a single record
     *
     * @return string
     */
    public function getRecordVarName(): string
    {
        $name = (string)$this->getConfiguration('plugin.tx_dataviewer.settings.singleRecordVariableName');
        if(!is_string($name)) {
            return 'record';
        }
        return StringUtility::createCodeFromString($name);
    }

    /**
     * Gets the configuration for the predefines
     * templates
     *
     * @return array
     */
    public function getPredefinedTemplates(): array
    {
        $configuration = $this->getConfiguration('plugin.tx_dataviewer.templates');
        if (is_array($configuration) && !empty($configuration)) {
            $configuration = GeneralUtility::removeDotsFromTS($configuration);
        }

        return $configuration;
    }

    /**
     * Gets the predefined template by a given
     * id
     *
     * @param string $templateId
     * @return string|null
     */
    public function getPredefinedTemplateById(string $templateId): ?string
    {
        $templates = $this->getPredefinedTemplates();
        $info = (isset($templates[$templateId]))?$templates[$templateId]:null;

        if (isset($info['file'])) {
            return $info['file'];
        }

        return $info;
    }

    /**
     * Gets the cache lifetime configuration
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return (int)$this->getConfiguration('plugin.tx_dataviewer.developer.cache_lifetime');
    }
}
