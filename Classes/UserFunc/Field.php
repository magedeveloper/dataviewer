<?php
namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Domain\Repository\FieldRepository;
use Aix\Dataviewer\Factory\TableFactory;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Form\Element\UserElement;
use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\Service\Settings\FieldSettingsService;
use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;
use Aix\Dataviewer\Utility\LocalizationUtility;
use Aix\Dataviewer\Utility\StringUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapFactory;

class Field
{
	/**
	 * Field Repository
	 *
	 * @var FieldRepository
	 */
	protected $fieldRepository;

    /**
     * Record Repository
     *
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

	/**
	 * FlexForm Service
	 *
	 * @var FlexFormService
	 */
	protected $flexFormService;

    /**
     * Plugin Settings Service
     *
     * @var PluginSettingsService
     */
    protected $pluginSettingsService;

    /**
     * Field Settings Service
     *
     * @var FieldSettingsService
     */
    protected $fieldSettingsService;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->fieldRepository = GeneralUtility::makeInstance(FieldRepository::class);
        $this->datatypeRepository = GeneralUtility::makeInstance(DatatypeRepository::class);
		$this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
		$this->pluginSettingsService = GeneralUtility::makeInstance(PluginSettingsService::class);
        $this->fieldSettingsService = GeneralUtility::makeInstance(FieldSettingsService::class);
	}

	/**
	 * Displays the generated field identifier for frontend identification
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayGeneratedFieldIdentifier(array &$config, &$parentObject)
	{
		$row = $config['row'];
		$text = ($row['variable_name'] != '')?$row['variable_name']:$row['frontend_label'];
		$code = StringUtility::createCodeFromString($text);
		$title = LocalizationUtility::translate('formvalue_access_to_hidden_field');
		$recordName = $this->pluginSettingsService->getRecordVarName();

		if (!$code) {
            $code = '<em>generated on save</em>';
        }

        $table = $config['table'];
        $uid = $row['uid'];

        /* @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->assignMultiple([
            'row' => $row,
            'text' => $text,
            'table' => $table,
            'uid' => $uid,
            'frontendLabelFieldName' => "input[data-formengine-input-name=\"data[{$table}][{$uid}][frontend_label]\"]",
            'variableNameFieldName' => "input[data-formengine-input-name=\"data[{$table}][{$uid}][variable_name]\"]",
            'frontendLabelValueFieldName' => "input[name=\"data[{$table}][{$uid}][frontend_label]\"]",
            'variableValueFieldName' => "input[name=\"data[{$table}][{$uid}][variable_name]\"]",
            'title' => $title,
            'recordName' => $recordName,
            'code' => $code,
        ]);

        $template = 'EXT:dataviewer/Resources/Private/Templates/UserFunc/Field/GeneratedFieldIdentifier.html';
        $template = GeneralUtility::getFileAbsFileName($template);
        $view->setTemplatePathAndFilename($template);

		return $view->render();
	}

	/**
	 * Displays all available field ids for helping while
	 * using display conditions
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayAvailableFieldIds(array &$config, &$parentObject)
	{
	    /* @var Template $templateUserFunc */
        $templateUserFunc = GeneralUtility::makeInstance(Template::class);
        $row = $config['row'];
        $pid = $row['pid'];
        $fields = $this->fieldRepository->findAllOnPid($pid);
        if(count($fields) > 0) {
            $config['parameters']['fields'] = $fields;
        } else {
            return '<br />'.LocalizationUtility::translate('flexform.available_field_ids.no_fields_found');
        }

        return $templateUserFunc->displayTemplate($config, $parentObject);
	}

	/**
	 * Populate fields
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFields(array &$config, &$parentObject)
	{
		$options = [];

		$fields = $this->fieldRepository->findAll(false);

		$sorted = [];
		foreach ($fields as $_field)
		{
			$pid = $_field->getPid();
			$sorted[$pid][] = $_field;
		}

		ksort($sorted);

		foreach ($sorted as $pid=>$fields) {
		    $page = BackendUtility::getRecord('pages',$pid);
		    $title = "[PID:{$pid}]"." ".$page['title'];
		    $options[] = [
                'label' => $title,
                'value' => '--div--'
            ];
			foreach ($fields as $_field) {
			    /* @var \Aix\Dataviewer\Domain\Model\Field $_field */
				$label = $this->_getFieldLabel($_field);
				$options[] = [
                    'label' => $label,
                    'value' => $_field->getVariableName()
                ];
			}
		}

		$config['items'] = array_merge($config['items'], $options);
	}

    /**
     * Populate fields
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function populateFieldIds(array &$config, &$parentObject)
    {
        $options = [];

        $fields = $this->fieldRepository->findAll(false);

        $sorted = [];
        foreach ($fields as $_field)
        {
            $pid = $_field->getPid();
            $sorted[$pid][] = $_field;
        }

        ksort($sorted);

        foreach ($sorted as $pid=>$fields) {
            $page = BackendUtility::getRecord('pages',$pid);
            $title = "[PID:{$pid}]"." ".$page['title'];
            $options[] = [$title, '--div--'];
            foreach ($fields as $_field) {
                /* @var \Aix\Dataviewer\Domain\Model\Field $_field */
                $label = $this->_getFieldLabel($_field);
                $options[] = [$label, $_field->getUid()];
            }
        }

        $config['items'] = array_merge($config['items'], $options);
    }

    /**
     * Populate fields
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function populateFieldsAndPalettesOnCurrentPid(array &$config, &$parentObject)
    {
        $this->populateFieldsOnCurrentPid($config, $parentObject);

        // Retrieve possible palettes
        $datatypeId = $config['row']['uid'];
        /* @var \Aix\Dataviewer\Domain\Model\Datatype $datatype */
        $datatype = $this->datatypeRepository->findByUid($datatypeId,false);

        if ($datatype instanceof \Aix\Dataviewer\Domain\Model\Datatype) {
            $palettes = $datatype->getPalettes();

            $options = [];
            foreach ($palettes as $_paletteName=>$_fields) {
                $paletteString = implode(', ', $_fields);
                $options[] = ["PALETTE: {$paletteString}", $_paletteName];
            }
            $config['items'] = array_merge($config['items'], $options);
        }
    }

	/**
	 * Populate fields
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFieldsOnCurrentPid(array &$config, &$parentObject)
	{
		$pid = $config['flexParentDatabaseRow']['pid'];

		$options = [];
		$fields = $this->fieldRepository->findAllOnPids([$pid], true);

		$sortedFields = [];

		foreach ($fields as $_field)
		{
			$type = $_field->getType();
			$sortedFields[$type][] = $_field;
		}

		ksort($sortedFields);

		foreach ($sortedFields as $_type=>$_fields)
		{
			foreach ($_fields as $_field)
			{
				$label = $this->_getFieldLabel($_field);
				$options[] = [$label, $_field->getUid()];
			}
		}

        $config['items'] = array_merge($config['items'], $options);
	}

	/**
	 * Populate fields
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFieldsOnStoragePages(array &$config, &$parentObject)
	{
		$pages = $config['flexParentDatabaseRow']['pages'];

		if (!is_array($pages))
			$pages = GeneralUtility::intExplode(',', $pages);

		$pids = [];
		foreach ($pages as $_page)
		{
			if (is_array($_page)) {
				$pids[] = $_page['uid'];
			}
			else {
				preg_match('/(?<table>.*)_(?<uid>[0-9]{0,11})|.*/', $_page, $match);

				if (is_array($match))
				{
					if (isset($match['uid']))
						$pids[] = $match['uid'];
					else
						$pids[] = $match[0];
				}
			}

		}

		$options = [];
		$fields = $this->fieldRepository->findAllOnPids($pids);

		foreach ($fields as $_field)
		{
			$label = $this->_getFieldLabel($_field);
			$options[] = [$label, $_field->getUid()];
		}

		$config['items'] = array_merge($config['items'], $options);
	}

    /**
     * Populate fields by a table name setting
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return void
     */
    public function populateFieldsFromDatatype(array &$config, &$parentObject)
    {
        $datatypeId = null;
        if (array_key_exists('datatype', $config['row'])) {
            if(is_array($config['row']['datatype'])) {
                $datatypeId = reset($config['row']['datatype']);
            } else {
                $datatypeId = $config['row']['datatype'];
            }
        }
        if(!null === $datatypeId && $datatypeId > 0) {
            $datatype = $this->datatypeRepository->findByUid($datatypeId);

            if ($datatype instanceof \Aix\Dataviewer\Domain\Model\Datatype) {
                $config['row']['foreign_table'] = $datatype->getTablename();
            }

            $this->populateFieldsFromTable($config, $parentObject);
        }

    }

    /**
     * Populate fields by a table name setting
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return void
     */
    public function populateFieldsFromTtContent(array &$config, &$parentObject)
    {
        $config['row']['foreign_table'] = 'tt_content';
        $this->populateFieldsFromTable($config, $parentObject);
    }

    /**
     * Populate fields by a table name setting
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return void
     */
    public function populateFieldsFromTable(array &$config, &$parentObject)
    {
        $row = $config['row'];
        $table = (is_array($row['foreign_table'])) ? reset($row['foreign_table']) : $row['foreign_table'];
        $options = [];

        if ($table == '' || !$table) return;

        // Retrieve all fields of the selected table
        /* @var Connection $query */
        $query = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($table);

        $columns = $query->fetchAllAssociative("SHOW COLUMNS FROM {$table}");

        if (is_array($columns) && count($columns) > 0) {
            foreach ($columns as $_column) {
                $field = $_column['Field'];
                $options[] = [
                    'label' => $field,
                    'value' => $field
                ];
            }
        }

        $config['items'] = array_merge($config['items'], $options);
    }

    /**
     * Populate fieldtypes
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     * @throws \Exception
     */
    public function populateFieldtypes(array &$config, &$parentObject)
    {
        $fieldConfiguration = $this->fieldSettingsService->getFieldConfiguration();

        $options = [];
        foreach ($fieldConfiguration as $_id=>$_config) {
            $options[] = [
                'label' => $_config['label'],
                'value' => $_id,
                'icon' => $_config['icon'],
            ];
        }

        if (count($options) <= 0) {
            $exceptionMessage = LocalizationUtility::translate('message.no_fieldtypes_found_please_check_ts');
            throw new \Exception($exceptionMessage);
        }

        if (is_array($config['items']))
            $config['items'] = array_merge($config['items'], $options);
        else
            $config['items'] = $options;
    }

    /**
     * Populates a list of subfields when
     * selected field is a model
     *
     * @param array $config
     * @param UserElement $parentObject
     * @return string
     */
    public function displayFlexFormSubFields(array &$config, UserElement &$parentObject)
    {
        $html = '';
        $flexFormRowData = $parentObject->getData('flexFormRowData');
        $fieldUid = (int)$flexFormRowData['field']['vDEF'][0];
        /** @var \Aix\Dataviewer\Domain\Model\Field $field */
        $field = $this->fieldRepository->findOneByUid($fieldUid);

        if($field instanceof \Aix\Dataviewer\Domain\Model\Field) {

            $frontendType = $field->getFrontendType();

            if(class_exists($frontendType)) {
                // Selected field is a domain/model or object storage field
                // We get the related information
                if($datatypeUid = $field->getConfig('datatype')) {
                    $datatype = $this->datatypeRepository->findOneByUid($datatypeUid);
                    if($datatype instanceof \Aix\Dataviewer\Domain\Model\Datatype) {
                        $tableName = $datatype->getTablename();
                    }
                } else {
                    /** @var DataMapFactory $dataMapFactory */
                    $dataMapFactory = GeneralUtility::makeInstance(DataMapFactory::class);
                    $dataMap = $dataMapFactory->buildDataMap($frontendType);
                    $tableName = $dataMap->getTableName();
                }

                if($tableName != '') {
                    /** @var TableFactory $tableFactory */
                    $tableFactory = GeneralUtility::makeInstance(TableFactory::class);
                    if($tableFactory->tableExists($tableName)) {
                        $configFt = $config;
                        $configFt['row']['foreign_table'] = $tableName;
                        $configFt['items'] = [
                            ['',LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.default_sort')],
                        ];
                        $this->populateFieldsFromTable($configFt, $parentObject);

                        if(!empty($configFt['items'])) {
                            $options = '';
                            foreach($configFt['items'] as $_item) {
                                $selected = ($_item[0] == $config['itemFormElValue'])?'selected':'';
                                $options.="<option value=\"{$_item[0]}\" {$selected}>{$_item[1]}</option>";
                            }

                            $label = LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.sub_field');
                            $description = LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.sub_field.description');
                            $html .= "
                                <label class=\"t3js-formengine-label\">{$label}</label>
                                <br />
                                <div class=\"alert alert-info\">{$description}</div>
                                <div class=\"formengine-field-item t3js-formengine-field-item\">
                                    <div class=\"form-control-wrap\">
                                        <select id=\"{$config['itemFormElID']}\" name=\"{$config['itemFormElName']}\" class=\"form-control form-control-adapt\">".$options."</select>
                                    </div>
                                </div>
                            ";
                        }
                    }
                }
            }
        }

        return $html;
    }

	/**
	 * Transforms a label for a field
	 *
	 * @param \Aix\Dataviewer\Domain\Model\Field $field
	 * @return string
	 */
	protected function _getFieldLabel(\Aix\Dataviewer\Domain\Model\Field $field)
	{
		return "[{$field->getPid()}] " . strtoupper($field->getType()) . ": " . $field->getFrontendLabel() . " {".$field->getCode()."}";
	}
}
