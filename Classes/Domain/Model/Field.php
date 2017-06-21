<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class Field extends AbstractModel
{
	/**
	 * Display Field in Backend
	 * 
	 * @var boolean
	 */
	protected $isActive = true;

	/**
	 * Field is excluded
	 * (see TCA exclude)
	 *
	 * @var boolean
	 */
	protected $exclude = false;
	
	/**
	 * Id of the field
	 * 
	 * @var string
	 * @validate NotEmpty
	 */
	protected $id = '';

	/**
	 * Field Name
	 * 
	 * @var string
	 * @validate NotEmpty
	 */
	protected $type;

	/**
	 * Frontend Label
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $frontendLabel = '';

	/**
	 * Variable Name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $variableName = '';

	/**
	 * Field Configuration
	 *
	 * @var string
	 */
	protected $fieldConf = '';

	/**
	 * Field Description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Custom Backend CSS Class
	 *
	 * @var string
	 */
	protected $cssClass = '';

	/**
	 * Column Width
	 *
	 * @var string
	 */
	protected $columnWidth = 'col-sm-12';

	/**
	 * Field Unit
	 *
	 * @var string
	 */
	protected $unit = '';

	/**
	 * Tab Name
	 *
	 * @var string
	 */
	protected $tabName = '';

	/**
	 * Field is required
	 *
	 * @var boolean
	 */
	protected $isRequired = false;

	/**
	 * This field is used as the main record title
	 * 
	 * @var bool
	 */
	protected $isRecordTitle = false;

	/**
	 * Show the field title in the backend
	 * 
	 * @var bool
	 */
	protected $showTitle = false;

	/**
	 * Show empty values in frontend
	 *
	 * @var boolean
	 */
	protected $showEmpty = false;

	/**
	 * Validation Information
	 *
	 * @var string
	 */
	protected $validation = '';

	/**
	 * Request Update onChange
	 *
	 * @var boolean
	 */
	protected $requestUpdate = false;

	/**
	 * Display Conditions XML
	 *
	 * @var string
	 */
	protected $displayCond = '';

	/**
	 * Field Values
	 * 
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\FieldValue>
	 * @cascade remove
	 */
	protected $fieldValues = null;

	/**
	 * Default Template File for this Datatype
	 *
	 * @var string
	 */
	protected $templatefile = '';

	/**
	 * __construct
	 */
	public function __construct()
	{
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects()
	{
		$this->fieldValues = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Gets the setting to show this
	 * field in the backend record form
	 *
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

	/**
	 * Sets the setting to show this field
	 * in the backend record form
	 * 
	 * @param boolean $isActive
	 * @return void
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
	}

	/**
	 * Checks if the field is excluded
	 * 
	 * @return bool
	 */
	public function getExclude()
	{
		return $this->exclude;
	}

	/**
	 * Sets the field as exclude
	 * 
	 * @param bool $exclude
	 * @return void
	 */
	public function setExclude($exclude)
	{
		$this->exclude = $exclude;
	}

	/**
	 * Checks if the field is excluded
	 * 
	 * @return bool
	 */
	public function isExclude()
	{
		return $this->exclude;
	}
	

	/**
	 * Gets the setting to show this
	 * field in the backend record form
	 *
	 * @return boolean
	 */
	public function getShowInBackend()
	{
		return $this->isActive;
	}

	/**
	 * Returns the id
	 * 
	 * @return string $id
	 */
	public function getId() 
	{
		return $this->id;
	}

	/**
	 * Sets the id
	 * 
	 * @param string $id
	 * @return void
	 */
	public function setId($id) 
	{
		$this->id = $id;
	}

	/**
	 * Returns the type
	 * 
	 * @return string $type
	 */
	public function getType() 
	{
		return $this->type;
	}

	/**
	 * Sets the type
	 * 
	 * @param string $type
	 * @return void
	 */
	public function setType($type) 
	{
		$this->type = $type;
	}

	/**
	 * Returns the frontendLabel
	 * 
	 * @return string $frontendLabel
	 */
	public function getFrontendLabel() 
	{
		return $this->frontendLabel;
	}

	/**
	 * Sets the frontendLabel
	 * 
	 * @param string $frontendLabel
	 * @return void
	 */
	public function setFrontendLabel($frontendLabel) 
	{
		$this->frontendLabel = $frontendLabel;
	}

	/**
	 * Gets the variable name
	 * 
	 * @return string
	 */
	public function getVariableName()
	{
		return $this->variableName;
	}

	/**
	 * Sets the variable name
	 * 
	 * @param string $variableName
	 * @return void
	 */
	public function setVariableName($variableName)
	{
		$this->variableName = $variableName;
	}

	/**
	 * Gets the field code
	 *
	 * @return string
	 */
	public function getCode()
	{
		$text = ($this->variableName)?$this->variableName:$this->frontendLabel;
		$code = \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($text);
		return $code;
	}

	/**
	 * Gets a unique fieldname
	 * 
	 * @return string
	 */
	public function getFieldName()
	{
		$code = $this->getCode();
		$id = $this->getUid();
		return "{$id}";
	}

	/**
	 * Gets the field identification
	 *
	 * @return string
	 */
	public function getIdentification()
	{
		$code = $this->getCode();
		return "{record.{$code}}";
	}
	
	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Returns the cssClass
	 *
	 * @return string $cssClass
	 */
	public function getCssClass()
	{
		if ($this->isRequired())
			$this->cssClass .= " dataviewer-field-required";

		return trim( ($this->cssClass)?" {$this->cssClass}":"" );
	}

	/**
	 * Sets the cssClass
	 *
	 * @param string $cssClass
	 * @return void
	 */
	public function setCssClass($cssClass)
	{
		$this->cssClass = $cssClass;
	}

	/**
	 * Returns the unit
	 * 
	 * @return string $unit
	 */
	public function getUnit() 
	{
		return $this->unit;
	}

	/**
	 * Returns the unit including a trailing space
	 *
	 * @return string $unit
	 */
	public function getUnitSpace()
	{
		return ($this->unit)?" {$this->unit}":"";
	}

	/**
	 * Sets the unit
	 * 
	 * @param string $unit
	 * @return void
	 */
	public function setUnit($unit) 
	{
		$this->unit = $unit;
	}

	/**
	 * Returns the boolean state of required
	 * 
	 * @return boolean
	 */
	public function isRequired() 
	{
		return $this->isRequired;
	}

	/**
	 * Returns the isRequired
	 *
	 * @return boolean isRequired
	 */
	public function getIsRequired()
	{
		return $this->isRequired;
	}

	/**
	 * Sets the isRequired
	 *
	 * @param boolean $isRequired
	 * @return boolean isRequired
	 */
	public function setIsRequired($isRequired)
	{
		$this->isRequired = $isRequired;
	}

	/**
	 * Gets the setting to show title
	 * 
	 * @return boolean
	 */
	public function getShowTitle()
	{
		return $this->showTitle;
	}

	/**
	 * Sets title to display or not
	 * 
	 * @param boolean $showTitle
	 * @return void
	 */
	public function setShowTitle($showTitle)
	{
		$this->showTitle = $showTitle;
	}	

	/**
	 * Gets the setting if the field is
	 * used as record title
	 * 
	 * @return boolean
	 */
	public function getIsRecordTitle()
	{
		return $this->isRecordTitle;
	}

	/**
	 * Sets the field as record title
	 * 
	 * @param boolean $isRecordTitle
	 * @return void
	 */
	public function setIsRecordTitle($isRecordTitle)
	{
		$this->isRecordTitle = $isRecordTitle;
	}

	/**
	 * Adds a FieldValue
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValue
	 * @return void
	 */
	public function addFieldValue(\MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValue) 
	{
		$this->fieldValues->attach($fieldValue);
	}

	/**
	 * Removes a FieldValue
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValueToRemove The FieldValue to be removed
	 * @return void
	 */
	public function removeFieldValue(\MageDeveloper\Dataviewer\Domain\Model\FieldValue $fieldValueToRemove) 
	{
		$this->fieldValues->detach($fieldValueToRemove);
	}

	/**
	 * Returns the fieldValues
	 * 
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\FieldValue> $fieldValues
	 */
	public function getFieldValues() 
	{
		return $this->fieldValues;
	}

	/**
	 * Gets an according field value by id
	 * 
	 * @param int $fieldValueId
	 * @return bool|\MageDeveloper\Dataviewer\Domain\Model\FieldValue
	 */
	public function getFieldValueById($fieldValueId)
	{
		foreach($this->fieldValues as $_fieldValue)
		{
			if ($_fieldValue->getUid() == $fieldValueId)
				return $_fieldValue;
		}
		
		return false;
	}

	/**
	 * Sets the fieldValues
	 * 
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\FieldValue> $fieldValues
	 * @return void
	 */
	public function setFieldValues(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $fieldValues) 
	{
		$this->fieldValues = $fieldValues;
	}

	/**
	 * Checks if the field has a default field value
	 * 
	 * @return int
	 */
	public function hasDefaultValue()
	{
		return count($this->getDefaultValues());
	}

	/**
	 * Gets the default value of the field
	 * 
	 * @return array
	 */
	public function getDefaultValues()
	{
		$defaultValues = [];
		$fieldValues = clone $this->fieldValues;
		
		foreach($fieldValues as $_fieldValue)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\FieldValue $_fieldValue */
			if ($_fieldValue->isDefault())
				$defaultValues[] = $_fieldValue;
				
		}
		
		return $defaultValues;
	}

	/**
	 * Checks if the field has any field values
	 * 
	 * @return bool
	 */
	public function hasFieldValues()
	{
		return (count($this->getFieldValues()));
	}

	/**
	 * Checks if the field has an dynamic database
	 * value
	 *
	 * @return bool
	 */
	public function hasDynamicDatabaseValues()
	{
		$fieldValues = clone $this->fieldValues;

		foreach($fieldValues as $_fieldValue)
			if ($_fieldValue->getType() == \MageDeveloper\Dataviewer\Domain\Model\FieldValue::TYPE_DYNAMIC_DATABASE)
				return true;

		return false;
	}

	/**
	 * Checks if the field has an database
	 * value
	 *
	 * @return bool
	 */
	public function hasDatabaseValues()
	{
		$fieldValues = clone $this->fieldValues;

		foreach($fieldValues as $_fieldValue)
			if ($_fieldValue->getType() == \MageDeveloper\Dataviewer\Domain\Model\FieldValue::TYPE_DATABASE)
				return true;

		return false;
	}

	/**
	 * Gets the format configuration
	 * 
	 * @return string
	 */
	public function getFieldConf()
	{
		return $this->fieldConf;
	}

	/**
	 * Sets the field configuration
	 * 
	 * @param string $fieldConf
	 * @return void
	 */
	public function setFieldConf($fieldConf)
	{
		$this->fieldConf = $fieldConf;
	}

	/**
	 * Gets the setting to show empty
	 * values in frontend
	 * 
	 * @return boolean
	 */
	public function getShowEmpty()
	{
		return $this->showEmpty;
	}

	/**
	 * Sets to show empty values in
	 * frontend
	 * 
	 * @param boolean $showEmpty
	 * @return void
	 */
	public function setShowEmpty($showEmpty)
	{
		$this->showEmpty = $showEmpty;
	}

	/**
	 * Gets a specific format configuration value
	 * 
	 * @param string $confValueName
	 * @return mixed
	 */
	public function getConfig($confValueName)
	{
		$fieldConf = $this->getFieldConf();
		$flexformConfig = $this->flexFormService->convertFlexFormContentToArray($fieldConf);
		
		if(isset($flexformConfig[$confValueName]))
			return $flexformConfig[$confValueName];
			
		return;	
	}

	/**
	 * Checks if the field is assigned
	 * to any tab
	 * 
	 * @return string
	 */
	public function isTabAssigned()
	{
		return ($this->tabName);
	}

	/**
	 * Gets the assigned tab name
	 * 
	 * @return string
	 */
	public function getTabName()
	{
		if (!$this->tabName || $this->tabName == "" || strlen($this->tabName) <= 0)
			return \MageDeveloper\Dataviewer\Utility\LocalizationUtility::translate("tab.general");
		
		return $this->tabName;
	}

	/**
	 * Sets the tab name to assign
	 * the field to
	 * 
	 * @param string $tabName
	 * @return void
	 */
	public function setTabName($tabName)
	{
		$this->tabName = $tabName;
	}

	/**
	 * Gets the column width
	 * 
	 * @return string
	 */
	public function getColumnWidth()
	{
		return $this->columnWidth;
	}

	/**
	 * Sets the column width
	 * 
	 * @param string $columnWidth
	 * @return void
	 */
	public function setColumnWidth($columnWidth)
	{
		$this->columnWidth = $columnWidth;
	}

	/**
	 * Gets the tca eval string from the configuration
	 * 
	 * @return string
	 */
	public function getEval()
	{
		return $this->getConfig("eval");
	}

	/**
	 * Checks if the field has an specific eval
	 * 
	 * @param string $eval
	 * @return bool
	 */
	public function hasEval($eval)
	{
		return GeneralUtility::inList($this->getEval(), $eval);
	}

	/**
	 * Gets the validation information
	 * 
	 * @return string
	 */
	public function getValidation()
	{
		return $this->validation;
	}

	/**
	 * Sets the validation information
	 * 
	 * @param string $validation
	 * @return void
	 */
	public function setValidation($validation)
	{
		$this->validation = $validation;
	}

	/**
	 * Gets the validation configuration
	 * as an array
	 * 
	 * @return array
	 */
	public function getValidationConfiguration()
	{
		if ($this->validation)
			return GeneralUtility::xml2array($this->validation);
			
		return [];	
	}

	/**
	 * Gets the request update setting
	 * 
	 * @return bool
	 */
	public function getRequestUpdate()
	{
		return $this->requestUpdate;
	}

	/**
	 * Sets the request update
	 * 
	 * @param bool $requestUpdate
	 * @return void
	 */
	public function setRequestUpdate($requestUpdate)
	{
		$this->requestUpdate = $requestUpdate;
	}

	/**
	 * Gets the display conditions as array
	 * 
	 * @return string
	 */
	public function getDisplayCond()
	{
		$displayCondition = "<displayCond>{$this->displayCond}</displayCond>";
		$arr = GeneralUtility::xml2array($displayCondition);
		
		if(is_array($arr))
			return $arr;
		
		return $this->displayCond;
	}

	/**
	 * Sets the display condition xml
	 * 
	 * @param string $displayCond
	 * @return void
	 */
	public function setDisplayCond($displayCond)
	{
		$this->displayCond = $displayCond;
	}

	/**
	 * Gets the configuration of 'allowedFileExtensions' from the flexform format
	 * configuration
	 *
	 * @return array
	 */
	public function getForeignRecordDefaults()
	{
		$configuration = $this->getConfig("foreign_record_defaults");
		$extracted = $this->flexFormService->extractConfiguration($configuration, "defaults", "field", "default");
		if (is_array($extracted) && count($extracted))
		{
			$recordDefaults = [];
			foreach($extracted as $_def=>$_defVal)
			{
				$recordDefaults["columns"][$_def] = [
					"config" => [
						"default" => $_defVal,
					],
				];
			}
			
			return $recordDefaults;
		}

		return [];
	}

	/**
	 * Returns the templatefile
	 *
	 * @return string $templatefile
	 */
	public function getTemplatefile()
	{
		return $this->templatefile;
	}

	/**
	 * Sets the templatefile
	 *
	 * @param string $templatefile
	 * @return void
	 */
	public function setTemplatefile($templatefile)
	{
		$this->templatefile = $templatefile;
	}
	
}
