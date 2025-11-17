<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Hooks;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Repository\AbstractRepository;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Factory\ClassFactory;
use Aix\Dataviewer\Factory\TableFactory;
use Aix\Dataviewer\Form\Value\AbstractValue;
use Aix\Dataviewer\Service\Cache\TcaCacheService;
use Aix\Dataviewer\Service\Settings\FieldSettingsService;
use Aix\Dataviewer\Utility\UrlUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Aix\Dataviewer\Service\Backend\FlashMessageService;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class DataHandling
{
    /**
     * Persistence Managaer
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Cache Manager
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * Table Factory
     * @var TableFactory
     */
    protected $tableFactory;

    /**
     * Class Factory
     * @var ClassFactory
     */
    protected $classFactory;

    /**
     * TCA Cache Service
     * @var TcaCacheService
     */
    protected $tcaCacheService;

    /**
     * @var FieldSettingsService
     */
    protected $fieldSettingsService;

    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var FlashMessageService
     */
    protected $backendFlashMessageService;

    /**
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param CacheManager $cacheManager
     */
    public function injectCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param TableFactory $tableFactory
     */
    public function injectTableFactory(TableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
    }

    /**
     * @param ClassFactory $classFactory
     */
    public function injectClassFactory(ClassFactory $classFactory)
    {
        $this->classFactory = $classFactory;
    }

    /**
     * @param TcaCacheService $tcaCacheService
     */
    public function injectTcaCacheService(TcaCacheService $tcaCacheService)
    {
        $this->tcaCacheService = $tcaCacheService;
    }

    /**
     * @param FieldSettingsService $fieldSettingsService
     */
    public function injectFieldSettingsService(FieldSettingsService $fieldSettingsService)
    {
        $this->fieldSettingsService = $fieldSettingsService;
    }

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * @param FlashMessageService $backendFlashMessageService
     * @return void
     */
    public function injectBackendFlashMessageService(FlashMessageService $backendFlashMessageService)
    {
        $this->backendFlashMessageService = $backendFlashMessageService;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->persistenceManager   = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->cacheManager         = GeneralUtility::makeInstance(CacheManager::class);
        $this->tableFactory         = GeneralUtility::makeInstance(TableFactory::class);
        $this->classFactory         = GeneralUtility::makeInstance(ClassFactory::class);
        $this->tcaCacheService      = GeneralUtility::makeInstance(TcaCacheService::class);
        $this->fieldSettingsService = GeneralUtility::makeInstance(FieldSettingsService::class);
        $this->datatypeRepository   = GeneralUtility::makeInstance(DatatypeRepository::class);
        $this->backendFlashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
    }

    /**
     * @param string $table
     * @return Connection
     */
    protected function _getConnectionForTable(string $table): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $recordToDelete
     * @param bool $recordWasDeleted
     * @param mixed $parentObj
     */
    public function processCmdmap_deleteAction(string $table, int $id, array $recordToDelete, bool &$recordWasDeleted, &$parentObj)
    {
    }

    /**
     * @param mixed $parentObj
     */
    public function processDatamap_beforeStart(&$parentObj)
    {
    }

    /**
     * @param string $status
     * @param string $table
     * @param mixed $id
     * @param array $fieldArray
     * @param mixed $parentObj
     * @return void
     * @throws \Doctrine\DBAL\Exception
     * @throws NoSuchCacheException
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, $id, array $fieldArray, &$parentObj)
    {
        /*********************************************************************************************
         * Changing datatype icons overall records
         *********************************************************************************************/
        if ($table == 'tx_dataviewer_domain_model_datatype') {

            $datatype = BackendUtility::getRecord($table, $id);

            $tableExists = false;
            if(is_array($datatype) && array_key_exists('tablename', $datatype) && !is_null($datatype['tablename']) && $datatype['tablename'] != '') {
                $tableExists = $this->tableFactory->tableExists($datatype['tablename']);
            }

            if ($tableExists) {

                if (isset($fieldArray['icon'])) {
                    // Update icons
                    $tableName = $datatype['tablename'];
                    if ($tableName) {
                        $connection = $this->_getConnectionForTable($tableName);
                        $connection->update($tableName,['icon' => $fieldArray['icon']],['deleted' => 0]);
                    }
                }

                // Clear datatype tca cache
                $this->tcaCacheService->remove("Tca_Datatype_{$datatype["uid"]}");
            }

        }

        /*********************************************************************************************
         * Check if table is a dataviewer record table
         *********************************************************************************************/
        if($this->tableFactory->isRecordTable($table)) {

            // Check if id is like NEW<hash>
            if(!MathUtility::canBeInterpretedAsInteger($id)) {
                if(array_key_exists($id, $parentObj->substNEWwithIDs)) {
                    $id = $parentObj->substNEWwithIDs[$id];
                }
            }

            if(MathUtility::canBeInterpretedAsInteger($id)) {
                // Generate values when a record was saved and the id is int
                // This regenerates values for post-processing purposes,
                // configured in dataviewers field configuration 'value'
                $this->generateValues($id, $table);
            }
        }

        // Save processed data
        $this->persistenceManager->persistAll();
    }

    /**
     * Generate possible values that the record
     * has, to post-process data
     * @param int $id
     * @param string $table
     */
    public function generateValues(int $id, string $table): void
    {
        // Generate all values from value generator settings
        $recordRow = BackendUtility::getRecordWSOL($table, $id, '*');
        $datatypeUid = $recordRow['datatype'];
        if($datatypeUid > 0) {
            /* @var Datatype $datatype */
            $datatype = $this->datatypeRepository->findByUid($datatypeUid, false);
            if($datatype instanceof Datatype) {
                $repository = $datatype->getRepository();
                if($repository instanceof AbstractRepository) {
                    $recordsWSOL = $repository->findbyUids([$id],[],[HiddenRestriction::class,StartTimeRestriction::class,EndTimeRestriction::class]);
                    $record = reset($recordsWSOL);
                    if($record instanceof AbstractRecordModel) {
                        $valueGeneratorFields = $this->fieldSettingsService->getFieldTypesWithValueGenerator();
                        $update = [];
                        $pathSegments = [];
                        foreach($datatype->getFields() as $_field) {
                            /* @var Field $_field */
                            // Check if vield is a value generator field
                            if(in_array($_field->getType(), $valueGeneratorFields)) {
                                // The value for this field needs to be re-generated
                                $valueClass = $this->fieldSettingsService->getValueGeneratorClass($_field);
                                if(class_exists($valueClass)) {
                                    /* @var AbstractValue $value */
                                    $value = GeneralUtility::makeInstance($valueClass);
                                    $value->setRecord($record);
                                    $value->setDatatype($datatype);
                                    $value->setField($_field);

                                    try {
                                        $resultVal = $value->getValue();
                                        // Update the record model for later usage
                                        $update[$_field->getVariableName()] = $resultVal;
                                        $recordRow[$_field->getVariableName()] = $resultVal;
                                        $record->_setProperty($_field->getVariableName(), $resultVal);

                                        // Check if value is for path_segment
                                        if($_field->getUseAsPathSegment() === true) {
                                            if(is_string($resultVal)) {
                                                $pathSegments[] = $resultVal;
                                            }
                                        }

                                    } catch (\Exception $e) {
                                        // We continue the process, maybe the field does not want to write the result
                                        $this->backendFlashMessageService->addFlashMessage($e->getMessage(), '',ContextualFeedbackSeverity::ERROR);
                                    }

                                }
                            } else {
                                $resultVal = (string)$recordRow[$_field->getVariableName()];
                            }

                            // Check if value is for path_segment
                            if ($_field->getUseAsPathSegment() === true) {
                                if (is_string($resultVal)) {
                                    $pathSegments[] = $resultVal;
                                }
                            }

                        }

                        // Generation of the 'title'
                        if($datatype->getHasTitleField()) {
                            $newTitle = '';
                            $divider = $datatype->getTitleDivider();
                            $divider = str_replace("X", " ", $divider);
                            foreach($datatype->getFields() as $_field) {
                                if($_field->getIsRecordTitle()) {
                                    $newTitle .= $recordRow[$_field->getVariableName()].$divider;
                                }
                            }

                            $newTitle = trim($newTitle, $divider);
                            $recordRow['title'] = $newTitle;
                            $record->setTitle($newTitle);
                            $update['title'] = $newTitle;
                        }

                        // Generation of path_segment
                        $pathSegmentFromPost = '';
                        if(array_key_exists('data', $_POST) && array_key_exists($table, $_POST['data']) && array_key_exists($id, $_POST['data'][$table])) {
                            if(array_key_exists('path_segment', $_POST['data'][$table][$id])) {
                                $pathSegmentFromPost = $_POST['data'][$table][$id]['path_segment'];
                            }
                        }
                        $pathSegment = implode('_', $pathSegments);
                        $pathSegment = str_replace(' ', '-', $pathSegment);
                        $pathSegment = UrlUtility::generatePathSegment($pathSegment);

                        if($pathSegment != '' && !empty($pathSegments)) {
                            if(($pathSegmentFromPost != $pathSegment) && ($pathSegmentFromPost != '')) {
                                $pathSegment = $pathSegmentFromPost;
                            }
                            $record->setPathSegment($pathSegment);
                            $update['path_segment'] = $pathSegment;
                        }

                        if($pathSegment == '' && empty($pathSegments)) {
                            $pathSegment = UrlUtility::generatePathSegment($record->getTitle());
                            if(($pathSegmentFromPost != $pathSegment) && ($pathSegmentFromPost != '')) {
                                $pathSegment = $pathSegmentFromPost;
                            }
                            $update['path_segment'] = $pathSegment;
                        }

                        $record->setPathSegment($pathSegment);

                        if(!empty($update)) {
                            // RAW UPDATE TABLE
                            $this->_getConnectionForTable($table)->update($table, $update, ['uid'=>$id], []);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param mixed $parentObj
     */
    public function processDatamap_afterAllOperations(&$parentObj)
    {
    }

    /**
     * Prevent saving of a news record if the editor doesn't have access to all categories of the news record
     *
     * @param array $incomingFieldArray
     * @param string $table
     * @param mixed $id
     * @param mixed $parentObj
     */
    public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, string $table, $id, &$parentObj)
    {
        switch ($table) {
            case 'sys_template':
                /*********************************************************************************************
                 * Clearing the cache, when EXT:dataviewer Static was added or exists in the template static
                 *********************************************************************************************/
                if (isset($incomingFieldArray['include_static_file'])) {
                    $staticFileInclude = GeneralUtility::trimExplode(',',$incomingFieldArray['include_static_file'],true);

                    if (in_array('EXT:dataviewer/Configuration/TypoScript', $staticFileInclude)) {
                        $this->cacheManager->flushCaches();
                    }
                    return;
                }
                break;
            case 'tx_dataviewer_domain_model_field':
                // We need to check, if the field type has changed
                // When is changed, we need to reset the configuration here
                if (isset($incomingFieldArray['type'])) {
                    if (is_numeric($id)) {
                        $incomingType = $incomingFieldArray['type'];
                        $parentRecord = BackendUtility::getRecord($table, $id);

                        if (is_array($parentRecord) && array_key_exists('type', $parentRecord)) {
                            if ($parentRecord['type'] != $incomingType) {
                                // If field type has changed, we need to reset the configuration
                                $incomingFieldArray['field_conf'] = '';
                            }
                        }
                    }
                }
                break;
            case 'tx_dataviewer_domain_model_datatype':
                // We need to check if the tablename is set
                // If no tablename is defined, we need to create one out of the datatype name
                $tableName = ($incomingFieldArray['tablename'])??'';
                if($tableName == '' || !strpos($tableName,'tx_dataviewer_domain_model_record_') === false) {
                    if(array_key_exists('name', $incomingFieldArray)) {
                        $incomingFieldArray['tablename'] = $this->tableFactory->suggestTableNameByDatatypeName($incomingFieldArray['name']);
                    }
                }
                break;
            default:
                break;
        }
    }

    /**
     * @param string $status
     * @param string $table
     * @param mixed $id
     * @param array $fieldArray
     * @param mixed $parentObj
     */
    public function processDatamap_postProcessFieldArray(string $status, string $table, $id, array $fieldArray, &$parentObj)
    {
    }

    /**
     * processCmdmap
     *
     * @param string $command
     * @param string $table
     * @param mixed $id
     * @param mixed $value
     * @param bool $commandIsProcessed
     * @param mixed $parentObj
     * @param bool $pasteUpdate
     * @return void
     */
    public function processCmdmap(string $command, string $table, $id, $value, bool &$commandIsProcessed, $parentObj, $pasteUpdate)
    {
    }

    /**
     * processCmdmap_afterFinish
     *
     * @param mixed $dataHandler
     * @return void
     */
    public function processCmdmap_afterFinish($dataHandler)
    {
    }

}
