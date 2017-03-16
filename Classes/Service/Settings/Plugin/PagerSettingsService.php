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
class PagerSettingsService extends PluginSettingsService
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

	/**
	 * Setting to show the 'View All' Selection
	 * in the per Page Field
	 * 
	 * @return bool
	 */
	public function showViewAll()
	{
		return (bool)$this->getSettingByCode("show_view_all");
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

	/**
	 * Get the setting for displaying a number of pages left
	 * and right of the current selected page
	 *
	 * @return bool
	 */
	public function getLeftRightPagesCount()
	{
		return (int)$this->getSettingByCode("left_right_pages");
	}

	/**
	 * Get the setting for the compact mode
	 *
	 * @return bool
	 */
	public function isCompactMode()
	{
		return (bool)$this->getSettingByCode("compact_pages");
	}


}
