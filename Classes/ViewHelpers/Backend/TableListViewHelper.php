<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Backend;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

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
class TableListViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper
{
	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
	{
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Renders a record list as known from the TYPO3 list module
	 * Note: This feature is experimental!
	 *
	 * @param string $tableName name of the database table
	 * @param array $fieldList list of fields to be displayed. If empty, only the title column (configured in $TCA[$tableName]['ctrl']['title']) is shown
	 * @param int $storagePid by default, records are fetched from the storage PID configured in persistence.storagePid. With this argument, the storage PID can be overwritten
	 * @param int $levels corresponds to the level selector of the TYPO3 list module. By default only records from the current storagePid are fetched
	 * @param string $filter corresponds to the "Search String" textbox of the TYPO3 list module. If not empty, only records matching the string will be fetched
	 * @param int $recordsPerPage amount of records to be displayed at once. Defaults to $TCA[$tableName]['interface']['maxSingleDBListItems'] or (if that's not set) to 100
	 * @param string $sortField table field to sort the results by
	 * @param bool $sortDescending if TRUE records will be sorted in descending order
	 * @param bool $readOnly if TRUE, the edit icons won't be shown. Otherwise edit icons will be shown, if the current BE user has edit rights for the specified table!
	 * @param bool $enableClickMenu enables context menu
	 * @param string $clickTitleMode one of "edit", "show" (only pages, tt_content), "info
	 * @param bool $alternateBackgroundColors Deprecated since TYPO3 CMS 7, will be removed in TYPO3 CMS 8
	 * @param bool $noControlPanels No control panels
	 * @return string the rendered record list
	 * @see \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList
	 */
	public function render($tableName, array $fieldList = array(), $storagePid = null, $levels = 0, $filter = '', $recordsPerPage = 0, $sortField = '', $sortDescending = false, $readOnly = false, $enableClickMenu = true, $clickTitleMode = null, $alternateBackgroundColors = false, $noControlPanels = true)
	{
		if ($alternateBackgroundColors) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::deprecationLog(
				'The option alternateBackgroundColors has no effect anymore and can be removed without problems. The parameter will be removed in TYPO3 CMS 8.'
			);
		}

		$pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id'), $GLOBALS['BE_USER']->getPagePermsClause(1));
		/** @var $dblist \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList */
		$dblist = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList::class);

		$dblist->pageRow = $pageinfo;
		if ($readOnly === false) {
			$dblist->calcPerms = $GLOBALS['BE_USER']->calcPerms($pageinfo);
		}
		$dblist->showClipboard = false;
		$dblist->disableSingleTableView = true;
		$dblist->clickTitleMode = $clickTitleMode;
		$dblist->clickMenuEnabled = $enableClickMenu;
		if ($storagePid === null) {
			$frameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			$storagePid = $frameworkConfiguration['persistence']['storagePid'];
		}

		$dblist->start($storagePid, $tableName, (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pointer'), $filter, $levels, $recordsPerPage);
		$dblist->allFields = true;
		$dblist->dontShowClipControlPanels = true;
		$dblist->displayFields = false;
		$dblist->setFields = array($tableName => $fieldList);
		$dblist->noControlPanels = $noControlPanels;
		$dblist->sortField = $sortField;
		$dblist->sortRev = $sortDescending;
		$dblist->script = $_SERVER['REQUEST_URI'];

		$dblist->generateList();
		$html = "";
		
		
		$html .= $dblist->HTMLcode;
		
		
		
		return $html;
	}
}
