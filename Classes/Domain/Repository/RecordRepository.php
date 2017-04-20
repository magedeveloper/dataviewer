<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

use MageDeveloper\Dataviewer\Domain\Model\Datatype;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\ListSettingsService;
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
class RecordRepository extends AbstractRepository
{
	/**
	 * Default Select Fields
	 *
	 * @var array
	 */
	protected $defaultSelectFields = [
		"RECORD.uid",
		"RECORD.pid",
		"RECORD.title",
	];

	/**
	 * Default Orderings
	 *
	 * @var array
	 */
	protected $defaultOrderings = [
		'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
	];

	/**
	 * Find records by given uids
	 *
	 * @param array $uids
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByUids(array $uids, array $storagePids = [])
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();
		$querySettings->setRespectSysLanguage(true);

		if(!empty($storagePids))
		{
			$querySettings->setStoragePageIds($storagePids);
			$querySettings->setRespectStoragePage(true);
		}

		return $query->matching(
			$query->in("uid", $uids)
		)->execute();
	}

	/**
	 * Find latest records
	 *
	 * @param int $limit
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findLatest($limit = 10)
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();

		$querySettings->setRespectStoragePage(false);
		$querySettings->setIgnoreEnableFields(true);
		$querySettings->setRespectSysLanguage(true);

		$query->setOrderings(["crdate" => QueryInterface::ORDER_DESCENDING]);
		$query->setLimit($limit);

		return $query->matching(
			$query->logicalAnd(
				$query->greaterThan("pid", 0),
				$query->equals("datatype.hide_records", "0")
			)
		)->execute();
	}


	/**
	 * Find records by datatype
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function findByDatatype(Datatype $datatype)
	{
		$query = $this->createQueryWithSettings();

		return $query->matching(
			$query->equals("datatype", $datatype->getUid())
		)->execute();
	}

	/**
	 * Find record by a list of datatype ids
	 *
	 * @param array $datatypeIds
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByDatatypeIds(array $datatypeIds, array $storagePids = [])
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();

		$querySettings->setRespectStoragePage(false);
		$querySettings->setRespectSysLanguage(true);
		if(!empty($storagePids))
		{
			$querySettings->setStoragePageIds($storagePids);
			$querySettings->setRespectStoragePage(true);
		}

		return $query->matching(
			$query->in("datatype", $datatypeIds)
		)->execute();
	}

	/**
	 * Find records by a list of record ids
	 *
	 * @param array $recordIds
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByRecordIds(array $recordIds, array $storagePids)
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);
		$querySettings->setRespectSysLanguage(true);

		//$orderings = [];
		//foreach ($recordIds as $_id)
		//	$orderings["title={$_id}"] = QueryInterface::ORDER_DESCENDING;

		//$query->setOrderings($orderings);

		$records = $query->matching(
			$query->in("uid", $recordIds)
		)->execute();


		// THIS IS A DIRTY FIX FOR MANUAL SORTING THE RECORDS BY THE INPUT RECORD IDS
		// I HOPE THIS IS CHANGED SOON BECAUSE IT COSTS A LOT OF SPEED HERE
		// THANKS TO DOCTRINE THE OLD FIX ABOVE BECAME INVALID :(
		$result = [];
		foreach($recordIds as $_recordId)
		{
		    foreach($records as $_record)
		        if($_record->getUid() == $_recordId)
		          $result[] = $_record;
		}

		return $records;
	}

	/**
	 * Find records by a creation date range
	 *+
	 * @param \Datetime $dateFrom
	 * @param \Datetime $dateTo
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByCreationDateRange(\Datetime $dateFrom, \Datetime $dateTo, array $storagePids)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);
		$querySettings->setRespectSysLanguage(true);

		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("crdate", $dateFrom->getTimestamp()),
				$query->lessThanOrEqual("crdate", $dateTo->getTimestamp())
			)
		)->execute();
	}

	/**
	 * Find records by a record change time range
	 *
	 * @param \Datetime $dateFrom
	 * @param \Datetime $dateTo
	 * @param array $storagePids
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByChangeDateRange(\Datetime $dateFrom, \Datetime $dateTo, array $storagePids)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);
		$querySettings->setRespectSysLanguage(true);

		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("tstamp", $dateFrom->getTimestamp()),
				$query->lessThanOrEqual("tstamp", $dateTo->getTimestamp())
			)
		)->execute();
	}

	/**
	 * Find all records on a set of storage pids
	 *
	 * @param array $storagePids
	 * @param bool $includeHidden
	 * @param bool $respectHideRecordsSetting
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAll(array $storagePids = [], $includeHidden = false, $respectHideRecordsSetting = false, array $orderings = [])
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(!empty($storagePids));
		$querySettings->setIgnoreEnableFields($includeHidden);
		$querySettings->setRespectSysLanguage(true);
		$query->setQuerySettings($querySettings);

		if(!empty($orderings))
			$query->setOrderings($orderings);

		$sub = $query;

		if (!empty($storagePids))
		{
			$querySettings->setStoragePageIds($storagePids);
			$query->setQuerySettings($querySettings);

			if($respectHideRecordsSetting)
			{
				return $query->matching(
					$sub->equals("datatype.hide_records", "0")
				)->execute();
			}

		}

		return $query->execute();
	}

	/**
	 * Find records by advanced conditions like
	 * filters, limit
	 *
	 * @param array $filters
	 * @param int|string $sortField
	 * @param string $sortOrder
	 * @param null|int $limit
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByAdvancedConditions(array $filters = [], $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING, $limit = null, array $storagePids = [])
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectSysLanguage(true);

		if(!empty($storagePids))
			$querySettings->setStoragePageIds($storagePids);
		else
			$querySettings->setRespectStoragePage(false);

		$query->setQuerySettings($querySettings);

		$statement = $this->getStatementByAdvancedConditions($filters, $sortField, $sortOrder, $limit, $storagePids);

		$query->statement($statement);
		$result = $query->execute(true);

		// Apply Sorting
		if(is_numeric($sortField))
		{
			usort($result, function ($a, $b) {
				return $a['sort'] - $b['sort'];
			});
		}

		if(is_numeric($sortField) && $sortOrder == QueryInterface::ORDER_DESCENDING)
			$result=array_reverse($result, true);

		return $result;
	}

	/**
	 * Counts records by conditions like
	 * filters, limit
	 *
	 * @param array $filters
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function countByConditions(array $filters = [], array $storagePids = [])
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectSysLanguage(true);

		if(!empty($storagePids))
			$querySettings->setStoragePageIds($storagePids);
		else
			$querySettings->setRespectStoragePage(false);

		$query->setQuerySettings($querySettings);

		$defaultSelectFields = $this->defaultSelectFields;
		$this->defaultSelectFields = ["RECORD.uid"];
		$statement = $this->getStatementByAdvancedConditions($filters, "title", "ASC", null, $storagePids);
		$this->defaultSelectFields = $defaultSelectFields;
		$query->statement($statement);
		$result = $query->execute(true);
		return (is_array($result))?count($result):0;
	}

	/**
	 * Find records by advanced conditions like
	 * filters, limit
	 *
	 * @param array $filters
	 * @param int|string $sortField
	 * @param string $sortOrder
	 * @param null|int $limit
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function getStatementByAdvancedConditions(array $filters = [], $sortField = "title", $sortOrder = QueryInterface::ORDER_ASCENDING, $limit = null, array $storagePids = [])
	{
		$subSelectOrdering = "";
		if(is_numeric($sortField))
		{
			$fieldId = (int)$sortField;
			$subSelectOrdering = ",";
			$subSelectOrdering .= "(SELECT search FROM tx_dataviewer_domain_model_recordvalue AS SSRV WHERE SSRV.field = {$fieldId} AND SSRV.record = RECORD.uid AND SSRV.hidden = 0 AND SSRV.deleted = 0 LIMIT 1) AS sort";
			$sortField = "sort";
		}

		$defaultSelectFields = implode(",", $this->defaultSelectFields);

		$statement = "";
		$statement .= "SELECT            {$defaultSelectFields}{$subSelectOrdering}"."\r\n";
		$statement .= "FROM              tx_dataviewer_domain_model_record AS RECORD"."\r\n";
		$statement .= "LEFT JOIN         tx_dataviewer_domain_model_recordvalue AS RECORDVALUE"."\r\n";
		$statement .= "ON                RECORDVALUE.record = RECORD.uid"."\r\n";
		$statement .= "WHERE             RECORD.deleted = '0'"."\r\n";
		$statement .= "AND               RECORD.hidden = '0'"."\r\n";
		$statement .= "AND               RECORDVALUE.deleted = '0'"."\r\n";
		$statement .= "AND               RECORDVALUE.hidden = '0'"."\r\n";

		if(!empty($storagePids))
		{
			$storagePids = implode(",", $storagePids);
			$statement .= "AND               RECORD.pid IN ({$storagePids})"."\r\n";
			$statement .= "AND               RECORDVALUE.pid IN ({$storagePids})"."\r\n";
		}


		if(!empty($filters))
			$statement .= $this->_createAdditionalWhereClauseByFilters($filters)."";

		$statement .= "GROUP BY          RECORD.uid"."\r\n";
		$statement .= "ORDER BY          {$sortField} {$sortOrder}"."\r\n";

		if (!is_null($limit))
			$statement .= "LIMIT             {$limit}"."\r\n";

		return $statement;
	}

	/**
	 * Creates an additional where clause string by
	 * given filters
	 *
	 * @param array $filters
	 * @return string
	 */
	protected function _createAdditionalWhereClauseByFilters(array $filters)
	{
		$additionalWhereClause = "";

		foreach($filters as $_filter)
		{
			$fieldId 			= $_filter["field_id"];
			$filterCondition 	= $_filter["filter_condition"];
			$filterValue 		= $_filter["field_value"];
			$filterCombination  = $_filter["filter_combination"];
			$filterField		= (isset($_filter["filter_field"]))?$_filter["filter_field"]:"search";

			$additionalWhereClause .= $this->_getSqlCondition($fieldId, $filterCondition, $filterValue, $filterCombination, $filterField)."\r\n";
		}

		return $additionalWhereClause;
	}

	/**
	 * @param int $fieldId
	 * @param string $filterCondition
	 * @param mixed $filterValue
	 * @return string
	 */
	protected function _getSqlCondition($fieldId, $filterCondition, $filterValue, $filterCombination = "AND", $filterField = "search")
	{
		$sql = "";
		$cond = "";

		if($filterCombination == "AND" || $filterCombination == "OR")
			$filterCombination .= " ...";
			
		$filterCondition = strtolower($filterCondition);

		if(is_numeric($fieldId))
		{
			$sub = $this->_getSqlCondition($filterField, $filterCondition, $filterValue, "AND");
			$sub = preg_replace("/\s+/", " ",$sub); // Beauty
			$fv = "SELECT record FROM tx_dataviewer_domain_model_recordvalue WHERE field = {$fieldId} {$sub}";

			$searchField = "RECORD.uid";
			$filterCondition = "in";
			//$filterCombination = "AND ...";
			$filterValue = $fv;
		}
		else
		{
			$searchField = $fieldId;
		}



		/*
		eq			=				'{$var}'					->equals
		neq			!=				'{$var}'					->logicalNot->equals
		like		LIKE			'%{$var}%'					->like
		nlike		NOT LIKE		'%{$var}%'					->logicalNot->like
		in			IN				([trimExplode]{$var})		->in
		nin			NOT IN			([trimExplode]{$var})		->logicalNot->in
		gt			>				{(int)$var}					->greaterThan
		lt			<				{(int)$var}					->lessThan
		gte			>=				{(int)$var}					->greaterThanOrEqual
		lte			<=				{(int)$var}					->lessThanOrEqual
		fis			FIND_IN_SET		'{$var}'					->FIND_IN_SET
		*/

		//$filterValue =  $GLOBALS['TYPO3_DB']->quoteStr($filterValue, 'tx_dataviewer_domain_model_recordvalue');

		switch($filterCondition)
		{
			case "eq":
				$cond = "{$searchField} = '{$filterValue}'"."";
				break;
			case "neq":
				$cond = "{$searchField} != '{$filterValue}'"."";
				break;
			case "like":
				if(strpos($filterValue, "%") === false) $filterValue = "%{$filterValue}%";
				$cond = "{$searchField} LIKE '{$filterValue}'"."";
				break;
			case "nlike":
				if(strpos($filterValue, "%") === false) $filterValue = "%{$filterValue}%";
				$cond = "{$searchField} NOT LIKE '{$filterValue}'"."";
				break;
			case "in":
				$cond = "{$searchField} IN ({$filterValue})"."";
				break;
			case "nin":
				$cond = "{$searchField} NOT IN ({$filterValue})"."";
				break;
			case "gt":
				$filterValue = (int)$filterValue;
				$cond = "{$searchField} > {$filterValue}"."";
				break;
			case "lt":
				$filterValue = (int)$filterValue;
				$cond = "{$searchField} < {$filterValue}"."";
				break;
			case "gte":
				$filterValue = (int)$filterValue;
				$cond = "{$searchField} >= {$filterValue}"."";
				break;
			case "lte":
				$filterValue = (int)$filterValue;
				$cond = "{$searchField} <= {$filterValue}"."";
				break;
			case "fis":
				$cond = "FIND_IN_SET('{$filterValue}', {$searchField}) > 0 "."";
				break;
            case "between":
                if(is_array($filterValue))
                    $filterValue = implode(" AND ", $filterValue);

                $cond = "{$searchField} BETWEEN {$filterValue}"."";
                break;
            case "nbetween":
                if(is_array($filterValue))
                    $filterValue = implode(" AND ", $filterValue);

                $cond = "{$searchField} NOT BETWEEN {$filterValue}"."";
                break;
		}

		$posOfDots = strpos($filterCombination, "...");
		$simpleCond = substr($filterCombination, 0, $posOfDots);
		$filterCombination = substr($filterCombination, $posOfDots);
		$filterCombination = str_pad($simpleCond, 18, " ").$filterCombination;
		$cond = str_replace("...", $cond, $filterCombination);
		
		return $sql . $cond;
	}

}
