<?php
namespace MageDeveloper\Dataviewer\Hooks;

use TYPO3\CMS\Backend\RecordList\RecordListGetTableHookInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
class BuildQueryParameters
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
	 * @return BuildQueryParameters
	 */
	public function __construct()
	{
		$this->objectManager    		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->recordRepository 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->backendSessionService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\BackendSessionService::class);
	}

	/**
	 * @param array $parameters
	 * @param string $table
	 * @param int $pageId
	 * @param array $additionalConstraints
	 * @param array $fieldList
	 * @param \TYPO3\CMS\Recordlist\RecordList\DatabaseRecordList $dbRecordList
	 */
	public function buildQueryParametersPostProcess(&$parameters, &$table, &$pageId, &$additionalConstraints, &$fieldList, &$dbRecordList)
	{
		if($table != "tx_dataviewer_domain_model_record") return;
		
		$this->backendSessionService->setAccordingPid($pageId);

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
			// We can no more do sorting by a field, because of the bad doctrine implementation
			// with panic quoting after the hook
			// ISSUE #80874
			
			// The sorting is by a field, so we need to add a sorting subselect to
			// the normal query
			//$sortBy = "sort";
			//$sortingSelect = "(SELECT search FROM tx_dataviewer_domain_model_recordvalue WHERE tx_dataviewer_domain_model_recordvalue.field = {$sortBy} AND tx_dataviewer_domain_model_recordvalue.record = tx_dataviewer_domain_model_record.uid AND tx_dataviewer_domain_model_recordvalue.hidden = 0 AND tx_dataviewer_domain_model_recordvalue.deleted = 0 LIMIT 1) AS {$sortBy}";
			//$parameters["fields"][] = $sortingSelect;
			//$parameters["orderBy"][0][0] = $sortBy;
		}

		$originalQueryWhereParts = $parameters["where"];

		$parameters["orderBy"][0][1] = $sortOrder;
		sort($parameters["where"]);

		if($search)
			$parameters["where"][] = "title LIKE '%{$search}%' ";

		/* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($parameters['table']);

		$queryBuilder
			->select(...$parameters['fields'])
			->from($parameters['table'])
			->where(...$parameters['where']);

		$dbCount = (int)$queryBuilder->count("*")->execute()->fetchColumn();

		// Panic reset, to show all records as it would do before
		if($dbCount <= 0)
		{
			// Not sure if we should also reset the search string here...
			//$this->backendSessionService->setSearch(null);
			$parameters["where"] = $originalQueryWhereParts;
		}
		
		return;
	}

}
