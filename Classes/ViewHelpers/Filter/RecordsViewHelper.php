<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Filter;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Service\Query\ExtbaseQueryService;
use Aix\Dataviewer\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\RepositoryInterface;

/**
 * This ViewHelper filters through a record result
 *
 * Examples
 * ========
 *
 * {dv:filter.records(records:records,filters:filters,variables:variables)}
 *
 * {dv:filter.records(records:records,filters:{condition:'AND'}
 *
 * Operators can be found in
 * EXT:dataviewer/Classes/Service/QueryBuilderParser/jQueryQueryBuilderFunctions.php:$operators
 *
 *
 *
 * Example
 * --------
 * {dv:filter.records(records:records,filters:{condition:'AND',rules:{0:{field:'title',operator:'contains',value:'sales'}}})}
 * or
 * {dv:filter.records(datatype:datatype,filters:{condition:'AND',rules:{0:{field:'title',operator:'contains',value:'sales'}}}
 *
 * Example array of a filter
 * -------------------------
 * $filters = [
 *     'condition'=>'AND',
 *     'rules' => [
 *        [
 *           'field' => 'zips',
 *           'operator' => 'contains',
 *           'value' => '30159',
 *        ]
 *     ],
 *  ];
 *
 *
 */
class RecordsViewHelper extends AbstractViewHelper
{
    /**
     * @var ExtbaseQueryService
     */
    protected $extbaseQueryService;

    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @param ExtbaseQueryService $extbaseQueryService
     */
    public function injectExtbaseQueryService(ExtbaseQueryService $extbaseQueryService)
    {
        $this->extbaseQueryService = $extbaseQueryService;
    }

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('records', '\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult', 'Records to filter', false);
        $this->registerArgument('datatype', 'mixed', 'Datatype to load repository', false);
        $this->registerArgument('filters', 'array','Array with filter conditions', false, []);
        $this->registerArgument('variables', 'array','Array with variables to inject', false, []);
        $this->registerArgument('respectStoragePage', 'bool', 'Ignore storage pids', false, true);
        $this->registerArgument('storagePageIds','array', 'Storage page ids', false, []);
        $this->registerArgument('ignoreEnableFields','bool', 'Ignore Enable fields', false, false);

        parent::initializeArguments();
    }

    /**
     * @return mixed
     */
    public function render()
    {
        /* @var QueryResult $records */
        $records = $this->arguments['records'];
        $datatype = $this->arguments['datatype'];

        $query = null;
        if ($records instanceof QueryResult) {
            $query = $records->getQuery();
        } else if ($datatype instanceof Datatype) {
            $repository = $datatype->getRepository();
            if($repository instanceof RepositoryInterface) {
                /* @var \Aix\Dataviewer\Domain\Repository\AbstractRepository $repository */
                $query = $repository->createQuery();
            }
        } else if (is_numeric($datatype)) {
            $datatype = $this->datatypeRepository->findByUid((int)$datatype, false);
            if ($datatype instanceof Datatype) {
                $repository = $datatype->getRepository();
                if ($repository instanceof RepositoryInterface) {
                    /* @var \Aix\Dataviewer\Domain\Repository\AbstractRepository $repository */
                    $query = $repository->createQuery();
                }
            }
        }


        if(is_null($query)) {
            throw new \Exception('Either argument \'records\' nor argument \'datatype\' did not match the requirements.');
        }

        /* @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $filters = $this->arguments['filters'];
        $query = $this->extbaseQueryService->getQueryResult($query, json_encode($filters), $this->arguments['variables']);

        $query->getQuerySettings()->setRespectStoragePage($this->arguments['respectStoragePage']);
        $query->getQuerySettings()->setIgnoreEnableFields($this->arguments['ignoreEnableFields']);

        if(!empty($this->arguments['storagePageIds'])) {
            $query->getQuerySettings()->setStoragePageIds($this->arguments['storagePageIds']);
        }

        return $query->execute();
    }
}