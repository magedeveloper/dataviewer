<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

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
class DatatypeRepository extends AbstractRepository
{
	/**
	 * FindAll Override
	 * 
	 * @param bool $respectStoragePage
	 * @param array $orderings
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAll($respectStoragePage = true, array $orderings = [])
	{
		$query = $this->createQueryWithSettings(true, false, $respectStoragePage);
		$querySettings = $query->getQuerySettings();
		
		if(!empty($orderings))
			$query->setOrderings($orderings);

		$this->setDefaultQuerySettings($querySettings);
		return $query->execute();
	}

	/**
	 * Finds all records on a given storage page id
	 *
	 * @param array $storagePids
	 * @param array $orderings
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllOnPids(array $storagePids, array $orderings = [])
	{
		$query 			= $this->createQuery();
		$querySettings 	= $query->getQuerySettings();

		$querySettings->setStoragePageIds($storagePids);
		$querySettings->setRespectStoragePage(true);
		
		if(!empty($orderings))
			$query->setOrderings($orderings);

		$this->setDefaultQuerySettings($querySettings);

		return $query->execute();
	}
	
	/**
	 * Finds all records on a given storage page id
	 * 
	 * @param int $storagePid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllOnPid($storagePid, array $orderings = [])
	{
		return $this->findAllOnPids([$storagePid], $orderings);
	}

	/**
	 * Gets the ids of all datatypes, where records of these
	 * types must be hidden in listings
	 * 
	 * @return array
	 */
	public function getRecordHiddenIds()
	{
		$query = $this->createQueryWithSettings(true,true,false);
		$datatypes =  $query->matching(	$query->equals("hide_records", 1) )->execute();
		
		$ids = [];
		if($datatypes && $datatypes->count() > 0)
			foreach($datatypes as $_datatype)
				$ids[] = $_datatype->getUid();
		
		return $ids;
	}

	/**
	 * Finds datatype by the hidden setting
	 * 
	 * @param bool $hiddenInLists
	 * @param bool $hiddenAdd
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByHiddenSetting($hiddenInLists = true, $hiddenAdd = true)
	{
		$query = $this->createQueryWithSettings(true,true,false);
		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("hide_records", (int)$hiddenInLists),
				$query->lessThanOrEqual("hide_add", (int)$hiddenAdd)
			)
		)->execute();
	}

}
