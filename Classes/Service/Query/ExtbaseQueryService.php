<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Query;

use Aix\Dataviewer\Service\QueryBuilderParser\ExtbaseQueryParser;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

class ExtbaseQueryService implements SingletonInterface
{
    /**
     * @var ExtbaseQueryParser
     */
    protected $extbaseQueryParser;

    /**
     * @var QueryFilterService
     */
    protected $queryFilterService;

    /**
     * @param ExtbaseQueryParser $extbaseQueryParser
     */
    public function injectExtbaseQueryParser(ExtbaseQueryParser $extbaseQueryParser)
    {
        $this->extbaseQueryParser = $extbaseQueryParser;
    }

    /**
     * @param QueryFilterService $queryFilterService
     */
    public function injectQueryFilterService(QueryFilterService $queryFilterService)
    {
        $this->queryFilterService = $queryFilterService;
    }

    /**
     * Gets a query by given filters
     * @param Query $query
     * @param mixed $queryBuilderFilters
     * @return Query
     */
    public function getQueryResult(Query $query, $queryBuilderFilters): Query
    {
        return $this->extbaseQueryParser->jQueryToExtbase($queryBuilderFilters, $query);
    }

    /**
     * Applies certain settings to query
     * @param Query $query
     * @param array $settings
     * @return Query
     */
    public function applySettingsToQuery(Query $query, array $settings, array $storagePids = []): Query
    {
        $includeHidden = (bool)$settings['include_hidden'];
        if($includeHidden === true) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        $includeRecursive = (bool)$settings['include_recursive'];
        if($includeRecursive === true && !empty($storagePids)) {
            $pids = [];
            foreach($storagePids as $_sPid) {
                $recursivePids = $this->getRecursivePids($_sPid,true);
                $pids = array_merge($pids, $recursivePids);
            }
            $pids = array_unique($pids);
            $querySettings = $query->getQuerySettings();
            $querySettings->setStoragePageIds($pids);
            $query->setQuerySettings($querySettings);
        }

        return $query;
    }

    /**
     * Gets a query by plugin settings.
     * Respects all possible settings
     * @param Query $query
     * @param array $settings
     * @param array $variables
     * @return Query
     */
    public function applyFiltersToQuery(Query $query, array $settings, array $variables = []): Query
    {
        $filters = $this->queryFilterService->parseFilters($settings, $variables);
        // Obtaining result from filters
        return $this->getQueryResult($query, $filters);
    }

    /**
     * @param Query $query
     * @param array $filters
     * @param array $settings
     * @param array $variables
     * @return Query
     */
    public function applyPostProcessFiltersToQuery(Query $query, array $settings, array $variables = []): Query
    {
        $postProcessFilters = $this->queryFilterService->parsePostProcessFilters($settings, $variables);

        $ids = [];
        if(count($postProcessFilters) > 0) {
            foreach($postProcessFilters as $_filter) {
                $postProcessType = $_filter->post_process;
                $singleResult = $this->getQueryResult($query, $_filter);
                if(!array_key_exists($postProcessType, $ids)) {
                    $ids[$postProcessType] = [];
                }

                $ids[$postProcessType][] = array_column($singleResult->execute(true),'uid');
            }
        }

        if(array_key_exists('diff', $ids)) {
            if(count($ids['diff']) >= 2) {
                $result = call_user_func_array('array_intersect', $ids['diff']);
                if(!empty($result)) {
                    $query->matching($query->in('uid', $result));
                } else {
                    // We enforce no found values, because no values match
                    $query->matching($query->in('uid', [0]));
                }

                // Preventing problems because using orig uids here
                $query->getQuerySettings()->setRespectSysLanguage(false);
            }
        }

        return $query;
    }

    /**
     * Applies limit and offset to query
     * @param Query $query
     * @param array $settings
     * @param array $variables
     * @return Query
     */
    public function applyLimitOffsetToQuery(Query $query, array $settings, array $variables = []): Query
    {
        // Order and Order Direction
        if($orderings = $this->queryFilterService->parseOrderings($settings, $variables)) {
            // Setting configured orderings
            $query->setOrderings($orderings);
        }

        // Limit
        if($limit = $this->queryFilterService->parseLimit($settings, $variables)) {
            $query->setLimit($limit);

            // Offset is only available, when limit is set
            if($offset = $this->queryFilterService->parseOffset($settings, $variables)) {
                $query->setOffset($offset);
            }
        }

        return $query;
    }

    /**
     * @param int $parent
     * @param bool $asArray
     * @param int $depth
     * @return int|int[]|string
     */
    public function getRecursivePids(int $parent = 0, bool $asArray = true, int $depth = 99999)
    {
        $childPids = $this->getTreeList($parent, $depth, 0, 1);
        if ($asArray) {
            $childPids = GeneralUtility::intExplode(',', $childPids);
        }

        return $childPids;
    }

    /**
     * Recursively fetch all descendants of a given page
     *
     * @param int $id uid of the page
     * @param int $depth
     * @param int $begin
     * @param string $permClause
     * @return string comma separated list of descendant pages
     */
    public function getTreeList($id, $depth, $begin = 0, $permClause = '')
    {
        $depth = (int)$depth;
        $begin = (int)$begin;
        $id    = (int)$id;
        if ($id < 0) {
            $id = abs($id);
        }
        if ($begin === 0) {
            $theList = $id;
        } else {
            $theList = '';
        }
        if ($id && $depth > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
            $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
            $queryBuilder->select('uid')
                ->from('pages')
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', 0)
                )
                ->orderBy('uid');
            if ($permClause !== '') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($permClause));
            }
            $statement = $queryBuilder->execute();
            while ($row = $statement->fetchAssociative()) {
                if ($begin <= 0) {
                    $theList .= ',' . $row['uid'];
                }
                if ($depth > 1) {
                    $theSubList = $this->getTreeList($row['uid'], $depth - 1, $begin - 1, $permClause);
                    if (!empty($theList) && !empty($theSubList) && ($theSubList[0] !== ',')) {
                        $theList .= ',';
                    }
                    $theList .= $theSubList;
                }
            }
        }

        return $theList;
    }
}
