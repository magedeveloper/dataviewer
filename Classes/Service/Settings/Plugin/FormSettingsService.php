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

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FormSettingsService extends PluginSettingsService
{
	/**
	 * Gets the selected datatype ids
	 *
	 * @return string
	 */
	public function getSelectedDatatypeIds()
	{
		return $this->getSettingByCode("datatype_selection");
	}

	/**
	 * Gets the template override setting
	 *
	 * @return null|string
	 */
	public function getTemplateOverride()
	{
		return $this->getSettingByCode("template_override");
	}

	/**
	 * Checks if the plugin setting has a template
	 * override
	 *
	 * @return bool
	 */
	public function hasTemplateOverride()
	{
		return ($this->getTemplateOverride() != "");
	}

	/**
	 * Gets selected variable ids
	 *
	 * @return array
	 */
	public function getSelectedVariableIds()
	{
		$variables = $this->getSettingByCode("variable_injection");
		return GeneralUtility::trimExplode(",", $variables, true);
	}

	/**
	 * Debug Mode Enabled
	 *
	 * @return bool
	 */
	public function isDebug()
	{
		return (bool)$this->getSettingByCode("debug");
	}

}
