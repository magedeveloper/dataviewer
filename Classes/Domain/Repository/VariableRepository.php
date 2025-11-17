<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class VariableRepository extends AbstractRepository
{
    /**
     * Find variables by type
     *
     * @param string $type
     * @return QueryResultInterface
     */
    public function findByType(string $type): QueryResultInterface
    {
		$query = $this->createQueryWithSettings(true,false,false);

		return $query->matching(
			$query->equals('type', $type)
		)->execute();
	}

    /**
     * Find Variables by given storage pids
     *
     * @param array $storagePids
     * @return QueryResultInterface
     */
    public function findByStoragePids(array $storagePids): QueryResultInterface
    {
		$query = $this->createQueryWithSettings(false, false, true, $storagePids);
		return $query->execute();
	}

    /**
     * Find Variables by types
     * @param array $types
     * @return QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByTypes(array $types): QueryResultInterface
    {
        $query = $this->createQueryWithSettings(true,false,false);

        return $query->matching(
            $query->in('type', $types)
        )->execute();
    }
}
