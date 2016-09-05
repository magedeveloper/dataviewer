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
class SearchSettingsService extends PluginSettingsService
{
	/**
	 * Search Types
	 * @var string
	 */
	const SEARCH_RECORD_TITLE			= "SEARCH_RECORD_TITLE";
	const SEARCH_FIELDS 				= "SEARCH_FIELDS";
	const SEARCH_RECORD_TITLE_FIELDS	= "SEARCH_RECORD_TITLE_FIELDS";

	/**
	 * Gets the search type from the plugin
	 * settings
	 * 
	 * @return null|string
	 */
	public function getSearchType()
	{
		return $this->getSettingByCode("search_type");
	}

	/**
	 * Gets the search fields configuration
	 * 
	 * @return array
	 */
	public function getSearchFields()
	{
		$settingId = "search_fields";
		$setting = $this->getSettingByCode($settingId);
		$searchFields = array();

		if (is_array($setting))
		{
			foreach($setting as $_searchField)
				$searchFields[] = $_searchField["searches"];
		}
		
		return $searchFields;
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
	 * Gets the minimum allowed chars count
	 * to start the search
	 * 
	 * @return int
	 */
	public function getMinimumChars()
	{
		return (int)$this->getSettingByCode("minimum_chars");
	}

	/**
	 * Get the setting to clear the search string
	 * on new page load
	 *
	 * @return bool
	 */
	public function getClearOnPageLoad()
	{
		return (bool)$this->getSettingByCode("clear_on_page_load");
	}
}
