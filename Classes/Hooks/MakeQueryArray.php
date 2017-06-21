<?php
namespace MageDeveloper\Dataviewer\Hooks;

use TYPO3\CMS\Backend\RecordList\RecordListGetTableHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
class MakeQueryArray
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Backend Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\BackendSessionService
	 * @inject
	 */
	protected $backendSessionService;

	/**
	 * Constructor
	 *
	 * @return MakeQueryArray
	 */
	public function __construct()
	{
		$this->objectManager    		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->recordRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->backendSessionService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\BackendSessionService::class);
	}

	/**
	 * @param array $queryParts
	 * @param \TYPO3\CMS\Recordlist\RecordList\AbstractDatabaseRecordList $parentObj
	 * @param string $table
	 * @param int $id
	 * @param string $addWhere
	 * @param string $fieldList
	 * @param array $_params
	 */
	public function makeQueryArray_post(&$queryParts, &$parentObj, &$table, &$id, &$addWhere, &$fieldList, &$_params)
	{
		if($table != "tx_dataviewer_domain_model_record") return;
		
		$this->backendSessionService->setAccordingPid($id);
		
		// Check the selected sort field
		// evaluate if the field is numeric
		// so if numeric, create the advanced sort query
		// and if not, just change the sort field to the selected one
		$sortBy = $this->backendSessionService->getSortBy();
		$sortOrder = $this->backendSessionService->getSortOrder();
		$sortOrder = ($sortOrder == "desc")?"desc":"asc";
		$sortOrder = strtoupper($sortOrder);
		$search = $this->backendSessionService->getSearch();

		if(is_numeric($sortBy))
		{
			// The sorting is by a field, so we need to add a sorting subselect to
			// the normal query
			$sortingSelect = "(SELECT search FROM `tx_dataviewer_domain_model_recordvalue` AS SSRV WHERE SSRV.field = {$sortBy} AND SSRV.record = `tx_dataviewer_domain_model_record`.`uid` AND SSRV.hidden = 0 AND SSRV.deleted = 0 LIMIT 1) AS sort";
			$queryParts["SELECT"] = '`tx_dataviewer_domain_model_record`.*'.','.$sortingSelect;
			$sortBy = "sort";
		}
		
		$originalQueryWhereParts = $queryParts["WHERE"];
		if($search)
			$queryParts["WHERE"] .= " AND title LIKE '%{$search}%' ";
		
		if($sortBy)
			$queryParts["ORDERBY"] = "{$sortBy} {$sortOrder}";
		
		// Original Count
		$result = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$dbCount = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
		
		// Panic reset, to show all records as it would do before
		if($dbCount <= 0)
		{
			// Not sure if we should also reset the search string here...
			//$this->backendSessionService->setSearch(null);
			$queryParts["WHERE"] = $originalQueryWhereParts;
		}
		
	}

}
