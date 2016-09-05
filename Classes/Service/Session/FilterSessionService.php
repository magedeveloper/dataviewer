<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class FilterSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-filter";

	/**
	 * Session Keys for Sorting
	 * @var string
	 */
	const SESSION_KEY_FILTERS 		= "tx-dataviewer-filter-filters";
	const SESSION_KEY_SELECTED		= "tx-dataviewer-filter-selected";

	/**
	 * Sets the selected options to the session
	 * 
	 * @param array $selectedOptions
	 * @return FilterSessionService
	 */
	public function setSelectedOptions(array $selectedOptions)
	{
		return $this->writeToSession($selectedOptions, self::SESSION_KEY_FILTERS);
	}

	/**
	 * Gets the selected options from the session
	 * 
	 * @return array
	 */
	public function getSelectedOptions()
	{
		$selectedOptions = $this->restoreFromSession(self::SESSION_KEY_FILTERS);
		if (is_array($selectedOptions))
			return $selectedOptions;
			
		return array();	
	}

	/**
	 * Gets the selected options as an cleaned array
	 * with only needed keys
	 * 
	 * @return array
	 */
	public function getCleanSelectedOptions()
	{
		$selectedOptions = $this->getSelectedOptions();
		foreach($selectedOptions as $_i=>$_option)
		{
			unset($selectedOptions[$_i]["option_name"]);
			unset($selectedOptions[$_i]["id"]);
			unset($selectedOptions[$_i]["selected"]);
		}
		
		return $selectedOptions;
	}

	/**
	 * Removes an option from the
	 * selected options
	 * 
	 * @param string $id
	 * @return FilterSessionService
	 */
	public function removeOption($id)
	{
		$selectedOptions = $this->getSelectedOptions();
		
		foreach($selectedOptions as $_i=>$_selectedOption)
			if($_selectedOption["id"] == $id)
				unset($selectedOptions[$_i]);
				
		return $this->setSelectedOptions($selectedOptions);		
	}

	/**
	 * Checks if an option is selected
	 * 
	 * @param int $fieldId
	 * @param string $optionId
	 * @return bool
	 */
	public function checkIsSelected($fieldId, $optionId)
	{
		$selectedOptions = $this->getSelectedOptions();
	
		foreach($selectedOptions as $_option)
			if(($_option["field_id"] == $fieldId) && ($_option["id"] == $optionId))
				return true;
				
		return false;		
	}
}
