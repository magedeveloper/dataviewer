<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

use TYPO3\CMS\Core\Database\DatabaseConnection;

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
class FieldRepository extends AbstractRepository
{
	/**
	 * Finds entries for an field value
	 * Executes an simple select query
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValue
	 * @return array
	 */
	public function findEntriesForFieldValue(\MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValue)
	{
		$tablename 		= $fieldValue->getTableContent();
		$columnname 	= $fieldValue->getColumnName();
		$whereClause	= $fieldValue->getWhereClause();

		$query = $this->createQuery();
		$statement = "SELECT {$columnname} FROM {$tablename} {$whereClause}";
		$query->statement($statement);
		$result = $query->execute(true);
		return $result;
	}

	/**
	 * Executes an raw query
	 *
	 * @param array $fields
	 * @param string $table
	 * @param string $where
	 * @return array
	 */
	public function rawQuery($fields, $table, $where)
	{
		$query = $this->createQuery();
		$fields = implode(",", $fields);
		$statement = "SELECT {$fields} FROM {$table} {$where}";

		$query->statement($statement);
		$result = $query->execute(true);

		return $result;
	}

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
	 * @param bool $includeHidden
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllOnPid($storagePid, $includeHidden = false)
	{
		return $this->findAllOnPids([$storagePid]);
	}

	/**
	 * Finds all records on given storage page ids
	 *
	 * @param array $storagePids
	 * @param bool $includeHidden
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findAllOnPids(array $storagePids, $includeHidden = false)
	{
		$query = $this->createQueryWithSettings(true, $includeHidden, true, $storagePids);
		$querySettings = $query->getQuerySettings();

		$this->setDefaultQuerySettings($querySettings);

		return parent::findAll();
	}

	/**
	 * Finds all fields by certain types
	 *
	 * @param array $types
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByTypes(array $types)
	{
		$query = $this->createQueryWithSettings(true, false, false);
		return $query->matching(
			$query->in("type", $types)
		)->execute();
	}

	/**
	 * Finds a field by given variable name
	 *
	 * @param string $variableName
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field|null
	 */
	public function findOneByVariableName($variableName)
	{
		$query = $this->createQueryWithSettings(true, true, false);
		return $query->matching(
			$query->equals("variable_name", $variableName)
		)->execute()->getFirst();
	}
}
