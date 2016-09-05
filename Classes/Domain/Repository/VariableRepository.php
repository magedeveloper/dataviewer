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
class VariableRepository extends AbstractRepository
{
	/**
	 * Find variables by type
	 * 
	 * @param string $type
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function findByType($type)
	{
		$query = $this->createQueryWithSettings();

		return $query->matching(
			$query->equals("type", $type)
		)->execute();
	}

	/**
	 * Find Variables by given storage pids
	 * 
	 * @param array $storagePids
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByStoragePids(array $storagePids)
	{
		$query = $this->createQueryWithSettings(false, false, true, $storagePids);
		return $query->execute();
	}

}
