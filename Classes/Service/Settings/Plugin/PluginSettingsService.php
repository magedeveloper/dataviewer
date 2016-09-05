<?php
namespace MageDeveloper\Dataviewer\Service\Settings\Plugin;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use MageDeveloper\Dataviewer\Service\Settings\AbstractSettingsService;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PluginSettingsService extends AbstractSettingsService
{
	/**
	 * Plugin Name
	 * @var string
	 */
	protected $extensionName;

	/**
	 * Constructor
	 * 
	 * @return PluginSettingsService
	 */
	public function __construct()
	{
		$this->setExtensionName( "tx_" . Configuration::EXTENSION_KEY );
	}

	/**
	 * Sets the extension name
	 * 
	 * @param string $extensionName
	 * @return void
	 */
	public function setExtensionName($extensionName)
	{
		$this->extensionName = $extensionName;
	}

	/**
	 * Gets the extension name
	 * 
	 * @return string
	 */
	public function getExtensionName()
	{
		return $this->extensionName;
	}

	/**
	 * Gets the extension configuration
	 * by a given configuration pathj
	 * 
	 * @param string $path Path to the configuration
	 * @return string|null
	 */
	public function getConfiguration($path)
	{
		$extensionName = $this->getExtensionName();
		return $this->getExtensionConfiguration($extensionName, $path);
	}

	/**
	 * Gets the target content uid
	 *
	 * @return int
	 */
	public function getTargetContentUid()
	{
		return (int)$this->getSettingByCode("target_plugin");
	}

	/**
	 * Gets the configured variable name
	 * for records
	 * 
	 * @return string
	 */
	public function getRecordsVarName()
	{
		$name = $this->getConfiguration("settings.recordsVariableName");
		return \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($name);
	}

	/**
	 * Gets the configured variable name for
	 * a single record
	 * 
	 * @return string
	 */
	public function getRecordVarName()
	{
		$name = $this->getConfiguration("settings.singleRecordVariableName");
		return \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($name);
	}

	/**
	 * Gets the configured variable name for
	 * a part of a record
	 * 
	 * @return string
	 */
	public function getPartVarName()
	{
		$name = $this->getConfiguration("settings.partVariableName");
		return \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($name);
	}
}
