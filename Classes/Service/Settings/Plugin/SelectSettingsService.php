<?php
namespace MageDeveloper\Dataviewer\Service\Settings\Plugin;

use \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class SelectSettingsService extends PluginSettingsService
{
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
	 * Gets the selection limit
	 *
	 * @return int
	 */
	public function getSelectionLimit()
	{
		return (int)$this->getSettingByCode("selection_limit");
	}

	/**
	 * Gets the label field
	 *
	 * @return string|int
	 */
	public function getLabelField()
	{
		return $this->getSettingByCode("label_field");
	}

	/**
	 * Gets the preselected record ids from
	 * the plugin configuration
	 * 
	 * @return array
	 */
	public function getPreselectedRecords()
	{
		$preselectedRecords = $this->getSettingByCode("preselected_records");
		return GeneralUtility::trimExplode(",", $preselectedRecords, true);
	}

	/**
	 * Determines if the form shall be auto-
	 * submitted in clicking a checkbox
	 * 
	 * @return bool
	 */
	public function isAutoSubmit()
	{
		return (bool)$this->getSettingByCode("auto_submit");
	}
}
