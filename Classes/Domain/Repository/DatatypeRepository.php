<?php
namespace Aix\Dataviewer\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class DatatypeRepository extends AbstractRepository
{
    /**
     * FindAll Override
     *
     * @param bool $respectStoragePage
     * @param array $orderings
     * @return array|QueryResultInterface
     */
    public function findAll(bool $respectStoragePage = true, array $orderings = [])
    {
		$query = $this->createQueryWithSettings(true, false, $respectStoragePage);
		$querySettings = $query->getQuerySettings();

		if (!empty($orderings))
			$query->setOrderings($orderings);

		$this->setDefaultQuerySettings($querySettings);
		return $query->execute();
	}

    /**
     * Finds all records on a given storage page id
     *
     * @param int $storagePid
     * @param array $orderings
     * @return QueryResultInterface
     */
    public function findAllOnPid(int $storagePid, array $orderings = []): QueryResultInterface
    {
		return $this->findAllOnPids([$storagePid], $orderings);
	}

    /**
     * Gets the ids of all datatypes, where records of these
     * types must be hidden in listings
     *
     * @return array
     */
    public function getRecordHiddenIds(): array
    {
		$query = $this->createQueryWithSettings(true,true,false);
		$datatypes =  $query->matching(	$query->equals("hide_records", 1) )->execute();

		$ids = [];
		if ($datatypes && $datatypes->count() > 0)
			foreach ($datatypes as $_datatype)
				$ids[] = $_datatype->getUid();

		return $ids;
	}

    /**
     * Finds datatype by the hidden setting
     *
     * @param bool $hiddenInLists
     * @param bool $hiddenAdd
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByHiddenSetting(bool $hiddenInLists = true, bool $hiddenAdd = true): QueryResultInterface
    {
		$query = $this->createQueryWithSettings(true,true,false);
		return $query->matching(
			$query->logicalAnd(
				$query->greaterThanOrEqual("hide_records", (int)$hiddenInLists),
				$query->lessThanOrEqual("hide_add", (int)$hiddenAdd)
			)
		)->execute();
	}

    /**
     * Find all datatypes of records that exists on
     * given pids
     *
     * @param array $storagePids
     * @return array
     */
    public function findAllOfRecordsOnPid(array $storagePids): array
    {
		$pids = implode(",", $storagePids);
		$statement = "SELECT datatype FROM tx_dataviewer_domain_model_record WHERE pid IN ({$pids}) GROUP BY datatype";
		$query = $this->createQuery();

		$query->statement($statement);
		$datatypes = $query->execute(true);

		$datatypeIds = [];
		if (is_array($datatypes))
		{
			foreach ($datatypes as $_datatype)
				$datatypeIds[] = $_datatype["datatype"];
		}

		return $this->findByUids($datatypeIds);
	}

}
