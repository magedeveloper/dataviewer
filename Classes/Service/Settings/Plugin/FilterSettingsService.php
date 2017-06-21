<?php
namespace MageDeveloper\Dataviewer\Service\Settings\Plugin;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
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
class FilterSettingsService extends PluginSettingsService
{
	/**
	 * Gets the array with the filters
	 * 
	 * @return array
	 */
	public function getFilters()
	{
		$filterRawArray 	= $this->getSettingByCode("field_filter");
		$optionsRawArray 	= $this->getOptions();
		$filterArray = [];
		
		if(is_array($filterRawArray))
		{
			foreach($filterRawArray as $_filter)
			{
				$filter = $_filter["fields"];
				$filter["options"] = $this->getOptionsForField($filter["field_id"]);
				$filterArray[] = $filter;

			}
		}

		return $filterArray;
	}

	/**
	 * Gets options for filters
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		$optionsRawArray = $this->getSettingByCode("filter_options");
		$optionsArray = [];

		if(is_array($optionsRawArray))
		{
			foreach($optionsRawArray as $_option)
			{
				$option = $_option["fields"];
				$option["id"] = GeneralUtility::shortMD5($option["option_name"], 10);
				$optionsArray[] = $option;
			}
		}
		
		return $optionsArray;
	}

	/**
	 * Gets all possible filter options for
	 * a given field id
	 * 
	 * @param int $fieldId
	 * @return array
	 */
	public function getOptionsForField($fieldId)
	{
		$allOptions = $this->getOptions();
		$options = [];
		
		foreach($allOptions as $_fOpt)
		{
			if($fieldId == $_fOpt["field_id"])
				$options[] = $_fOpt;
			
		}
		
		return $options;
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
	 * Gets the setting to show the active filters
	 * 
	 * @return bool
	 */
	public function getShowActiveFilters()
	{
		return (bool)$this->getSettingByCode("show_active_filters");
	}
}
