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
class SortSettingsService extends PluginSettingsService
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
	 * Gets the field ids and record column names 
	 * for the sorting field
	 * 
	 * @return array
	 */
	public function getSortFields()
	{
		$setting = $this->getSettingByCode("field_ids");
		return \MageDeveloper\Dataviewer\Utility\StringUtility::explodeSeparatedString($setting, [","]);
	}

	/**
	 * Gets the fields for the per page
	 * 
	 * @return array
	 */	
	public function getPerPageFields()
	{
		$setting = $this->getSettingByCode("per_page");
		$perPage = \MageDeveloper\Dataviewer\Utility\StringUtility::explodeSeparatedString($setting);
		
		return array_combine(array_values($perPage), array_values($perPage));
	}

}
