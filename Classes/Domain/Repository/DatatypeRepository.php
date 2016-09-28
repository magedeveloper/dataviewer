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
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAll($respectStoragePage = true)
	{
		$query = $this->createQueryWithSettings(true, false, $respectStoragePage);
		$querySettings = $query->getQuerySettings();

		$this->setDefaultQuerySettings($querySettings);
		
		return parent::findAll(); 
	}

	/**
	 * Finds all records on a given storage page id
	 * 
	 * @param int $storagePid
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllOnPid($storagePid)
	{
		$query = $this->createQueryWithSettings(true, true, true, [$storagePid]);
		$querySettings = $query->getQuerySettings();
		
		$this->setDefaultQuerySettings($querySettings);
		
		return parent::findAll();
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

}
