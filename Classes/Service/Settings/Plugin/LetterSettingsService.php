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
class LetterSettingsService extends PluginSettingsService
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
	 * Gets all letters for the letter selection
	 * 
	 * @return array
	 */
	public function getLetters()
	{
		$letterArray = range("A", "Z");
		array_unshift($letterArray, "#");
		
		return $letterArray;
	}

	/**
	 * Gets the preselected letter from the plugin
	 * settings
	 * 
	 * @return null|string
	 */
	public function getPreselectedLetter()
	{
		return $this->getSettingByCode("preselected_letter");
	}

	/**
	 * Gets the letter selection field
	 * 
	 * @return int|string
	 */
	public function getLetterSelectionField()
	{
		$field = $this->getSettingByCode("field_id");
		$field = ($field == 0)?"title":$field;
		return $field;
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
