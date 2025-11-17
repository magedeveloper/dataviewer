<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Repository;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Model\FieldValue;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class FieldRepository extends AbstractRepository
{
    /**
     * Finds entries for an field value
     * Executes an simple select query
     *
     * @param FieldValue $fieldValue
     * @return array
     */
    public function findEntriesForFieldValue(FieldValue $fieldValue): array
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
    public function rawQuery(array $fields, string $table, string $where = ''): array
    {
		$query = $this->createQuery();
		$fields = implode(",", $fields);
		$statement = "SELECT {$fields} FROM {$table} {$where}";

		$query->statement($statement);
		return $query->execute(true);
	}

    /**
     * FindAll Override
     *
     * @param bool $respectStoragePage
     * @return QueryResultInterface
     */
    public function findAll(bool $respectStoragePage = true): QueryResultInterface
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
     * @return QueryResultInterface
     */
    public function findAllOnPid(int $storagePid, bool $includeHidden = false): QueryResultInterface
    {
        return $this->findAllOnPids([$storagePid], !$includeHidden);
    }

    /**
     * Finds all fields by certain types
     *
     * @param array $types
     * @return QueryResultInterface|array
     * @throws InvalidQueryException
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
     * @return Field|null
     */
    public function findOneByVariableName(string $variableName): ?Field
    {
		$query = $this->createQueryWithSettings(true, true, false);
		return $query->matching(
			$query->equals("variable_name", $variableName)
		)->execute()->getFirst();
	}

    /**
     * Finds fields by a given datatype
     *
     * @param Datatype $datatype
     * @return QueryResultInterface
     */
    public function findByDatatype(Datatype $datatype): QueryResultInterface
    {
		$fields = $datatype->getFields();

		$ids = [];
		foreach ($fields as $_field)
			$ids[] = $_field->getUid();

		$query = $this->createQueryWithSettings(false, true, false);
		return $query->matching(
			$query->in("uid", $ids)
		)->execute();
	}

}
