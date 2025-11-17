<?php
namespace Aix\Dataviewer\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DefaultRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

abstract class AbstractRepository extends Repository
{
    /**
     * Creates a query with predefined settings
     *
     * @param bool $respectSysLanguage
     * @param bool $ignoreEnableFields
     * @param bool $respectStoragePage
     * @param array $storagePids
     * @param int|null $languageUid
     * @return Query
     */
    public function createQueryWithSettings(
        bool $respectSysLanguage = true,
        bool $ignoreEnableFields = false,
        bool $respectStoragePage = true,
        array $storagePids = [],
        ?int $languageUid = null
    ): Query {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectSysLanguage($respectSysLanguage);
        $query->getQuerySettings()->setIgnoreEnableFields($ignoreEnableFields);
        $query->getQuerySettings()->setRespectStoragePage($respectStoragePage);

        if (!is_null($languageUid))
            $query->getQuerySettings()->setLanguageUid($languageUid);

        if (!empty($storagePids))
            $query->getQuerySettings()->setStoragePageIds($storagePids);

        return $query;
    }

    /**
     * Returns all objects of this repository.
     *
     * @param bool $respectSysLanguage
     * @param bool $onlyEnabled
     * @param bool $respectStoragePage
     * @param array $storagePids
     * @param null|int $languageUid
     * @return QueryResultInterface|array
     */
    public function findAllBySettings(
        bool $respectSysLanguage = true,
        bool $onlyEnabled = true,
        bool $respectStoragePage = false,
        array $storagePids = [],
        ?int $languageUid = null
    ) {
        $query = $this->createQueryWithSettings($respectSysLanguage, $onlyEnabled, $respectStoragePage, $storagePids, $languageUid);

        return $query->execute();
    }

    /**
     * Find a category from the repository with a
     * specified uid
     *
     * @param int $uid Uid
     * @param bool $onlyEnabled Only Enabled category
     * @param bool $respectSysLanguage
     * @param int|null $languageUid
     * @return object
     */
    public function findByUid($uid, bool $onlyEnabled = true, bool $respectSysLanguage = false, ?int $languageUid = null)
    {
        $query = $this->createQueryWithSettings($respectSysLanguage, !$onlyEnabled, false,[],$languageUid);
        return $query->matching(
            $query->equals("uid", $uid)
        )->execute()->getFirst();
    }

    /**
     * Finds all records on a given storage page id
     *
     * @param array $storagePids
     * @param array $onlyEnabled
     * @param null|int $languageUid
     * @return QueryResultInterface
     */
    public function findAllOnPids(array $storagePids = [], $onlyEnabled = true, int $languageUid = null): QueryResultInterface
    {
        $query = $this->createQueryWithSettings(true, !$onlyEnabled, false, $storagePids, $languageUid);
        $querySettings 	= $query->getQuerySettings();
        $querySettings->setStoragePageIds($storagePids);
        $querySettings->setRespectStoragePage(true);
        $this->setDefaultQuerySettings($querySettings);
        return $query->execute();
    }

    /**
     * @param array $uids
     * @param array $storagePids
     * @param array $removeRestrictions Restrictions to remove from query
     * @return array Array with result objects
     */
    public function findByUids(array $uids, array $storagePids = [], array $removeRestrictions = []): array
    {
        /** @var DataMapper $dataMapper */
        $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
        $tableName = $dataMapper->convertClassNameToTableName($this->objectType);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($tableName);

        /** @var DefaultRestrictionContainer $defaultRestrictionContainer */
        $defaultRestrictionContainer = GeneralUtility::makeInstance(DefaultRestrictionContainer::class);
        if(!empty($removeRestrictions)) {
            foreach($removeRestrictions as $_restriction) {
                $defaultRestrictionContainer->removeByType($_restriction);
            }
        }

        /** @var QueryBuilder $query */
        $query = $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where($queryBuilder->expr()->in('uid', $uids))
            ->add('orderBy', "FIELD({$tableName}.uid," . implode(',', $uids) . ')')
        ;

        if(!empty($storagePids)) {
            $query->andWhere($queryBuilder->expr()->in('pid', $storagePids));
        }

        return $dataMapper->map($this->objectType, $query->execute()->fetchAll());
    }

}
