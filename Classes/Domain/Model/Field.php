<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Model;

use Aix\Dataviewer\Exception\TcaGeneratorException;
use Aix\Dataviewer\Service\Settings\FieldSettingsService;
use Aix\Dataviewer\Tca\AbstractField;
use Aix\Dataviewer\Tca\FieldInterface;
use Aix\Dataviewer\Utility\FieldtypeConfigurationUtility;
use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Field extends AbstractModel
{
    /**
     * Display Field in Backend
     *
     * @var bool
     */
    protected $isActive = true;

    /**
     * Field is excluded
     * (see TCA exclude)
     *
     * @var bool
     */
    protected $exclude = false;

    /**
     * Field is excluded from translations
     * (see TCA l10n_mode -> exclude)
     *
     * @var bool
     */
    protected $l10nExclude = false;

    /**
     * Id of the field
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $id = '';

    /**
     * Field Name
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $type;

    /**
     * Frontend Label
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $frontendLabel = '';

    /**
     * Frontend Type
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
     */
    protected $frontendType = 'string';

    /**
     * Is Object Storage
     *
     * @var bool
     */
    protected $isObjectStorage = false;

    /**
     * Variable Name
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
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
     * Field is required
     *
     * @var bool
     */
    protected $isRequired = false;

    /**
     * This field is used as the main record title
     *
     * @var bool
     */
    protected $isRecordTitle = false;

    /**
     * Use the value for sorting
     *
     * @var bool
     */
    protected $useForSorting = false;

    /**
     * Use the value as path segement
     *
     * @var bool
     */
    protected $useAsPathSegment = false;

    /**
     * Validation Information
     *
     * @var string
     */
    protected $validation = '';

    /**
     * Request Update onChange
     *
     * @var bool
     */
    protected $requestUpdate = false;

    /**
     * Display Conditions XML
     *
     * @var string
     */
    protected $displayCond = '';

    /**
     * Name of the according palette
     *
     * @var string
     */
    protected $palette = '';

    /**
     * Field is indexed
     *
     * @var bool
     */
    protected $isIndex = false;

    /**
     * @var string
     */
    protected $databaseType = '';

    /**
     * Field Values
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\FieldValue>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $fieldValues = null;

    /**
     * Default Template File for this Datatype
     *
     * @var string
     */
    protected $templatefile = '';

    /**
     * Configuration
     *
     * @var array
     */
    protected $config = [];

    /**
     * _languageUid
     * @var int|null
     */
    protected ?int $_languageUid = 0;

    /**
     * @var int
     */
    protected $l10nParent;

    /**
     * Field is searchable in the backend
     *
     * @var bool
     */
    protected $backendSearchable = false;

    /**
     * Cache generated TCA
     *
     * @var boolean
     */
    protected $cacheTca = false;

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
        $this->fieldValues = new ObjectStorage();
    }

    /**
     * Gets the setting to show this
     * field in the backend record form
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Sets the setting to show this field
     * in the backend record form
     *
     * @param bool $isActive
     * @return void
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * Checks if the field is excluded
     *
     * @return bool
     */
    public function getExclude(): bool
    {
        return $this->exclude;
    }

    /**
     * Sets the field as exclude
     *
     * @param bool $exclude
     * @return void
     */
    public function setExclude(bool $exclude): void
    {
        $this->exclude = $exclude;
    }

    /**
     * Checks if the field is excluded
     *
     * @return bool
     */
    public function isExclude(): bool
    {
        return $this->exclude;
    }

    /**
     * Checks if the field is excluded
     * from translations
     *
     * @return bool
     */
    public function getL10nExclude(): bool
    {
        return $this->l10nExclude;
    }

    /**
     * Sets the field as excluded
     * from translations
     *
     * @param bool $l10nExclude
     * @return void
     */
    public function setL10nExclude(bool $l10nExclude): void
    {
        $this->l10nExclude = $l10nExclude;
    }

    /**
     * Checks if the field is excluded
     * from translations
     *
     * @return bool
     */
    public function isL10nExclude(): bool
    {
        return $this->l10nExclude;
    }

    /**
     * Gets the setting to show this
     * field in the backend record form
     *
     * @return bool
     */
    public function getShowInBackend(): bool
    {
        return $this->isActive;
    }

    /**
     * Returns the id
     *
     * @return string $id
     */
    public function getId(): sting
    {
        return $this->id;
    }

    /**
     * Sets the id
     *
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Returns the type
     *
     * @return string $type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Returns the frontendLabel
     *
     * @return string $frontendLabel
     */
    public function getFrontendLabel(): string
    {
        return $this->frontendLabel;
    }

    /**
     * Sets the frontendLabel
     *
     * @param string $frontendLabel
     * @return void
     */
    public function setFrontendLabel(string $frontendLabel): void
    {
        $this->frontendLabel = $frontendLabel;
    }

    /**
     * Gets the variable name
     *
     * @return string
     */
    public function getVariableName(): string
    {
        return $this->variableName;
    }

    /**
     * Sets the variable name
     *
     * @param string $variableName
     * @return void
     */
    public function setVariableName(string $variableName): void
    {
        $this->variableName = $variableName;
    }

    /**
     * Gets the field code
     *
     * @return string
     */
    public function getCode(): string
    {
        $text = ($this->variableName)?$this->variableName:$this->frontendLabel;
        $code = StringUtility::createCodeFromString($text);
        return $code;
    }

    /**
     * @return string
     */
    public function getUcCode(): string
    {
        return ucfirst($this->getCode());
    }

    /**
     * Gets a unique fieldname
     *
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->getCode();
    }

    /**
     * Gets the field identification
     *
     * @return string
     */
    public function getIdentification(): string
    {
        $code = $this->getCode();
        return "{record.{$code}}";
    }

    /**
     * @return string
     */
    public function getFrontendType(): string
    {
        $type = $this->frontendType;
        return $type;
    }

    /**
     * @param string $frontendType
     * @return void
     */
    public function setFrontendType(string $frontendType): void
    {
        $this->frontendType = $frontendType;
    }

    /**
     * @return bool
     */
    public function getIsObjectStorage(): bool
    {
        return $this->isObjectStorage;
    }

    /**
     * @param bool $isObjectStorage
     * @return void
     */
    public function setIsObjectStorage(bool $isObjectStorage): void
    {
        $this->isObjectStorage = $isObjectStorage;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the boolean state of required
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Returns the isRequired
     *
     * @return bool isRequired
     */
    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Sets the isRequired
     *
     * @param bool $isRequired
     * @return void
     */
    public function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }

    /**
     * Gets the setting if the field is
     * used as record title
     *
     * @return bool
     */
    public function getIsRecordTitle(): bool
    {
        return $this->isRecordTitle;
    }

    /**
     * Sets the field as record title
     *
     * @param bool $isRecordTitle
     * @return void
     */
    public function setIsRecordTitle(bool $isRecordTitle): void
    {
        $this->isRecordTitle = $isRecordTitle;
    }

    /**
     * Gets the field use for sorting
     *
     * @return bool
     */
    public function getUseForSorting(): bool
    {
        return $this->useForSorting;
    }

    /**
     * Sets the field use for sorting
     *
     * @param bool $useForSorting
     * @return void
     */
    public function setUseForSorting(bool $useForSorting): void
    {
        $this->useForSorting = $useForSorting;
    }

    /**
     * Use as path segment
     *
     * @return bool
     */
    public function getUseAsPathSegment(): bool
    {
        return $this->useAsPathSegment;
    }

    /**
     * Sets to use as path segement
     *
     * @param bool $useAsPathSegment
     */
    public function setUseAsPathSegment(bool $useAsPathSegment): void
    {
        $this->useAsPathSegment = $useAsPathSegment;
    }

    /**
     * Adds a FieldValue
     *
     * @param FieldValue $fieldValue
     * @return void
     */
    public function addFieldValue(FieldValue $fieldValue): void
    {
        $this->fieldValues->attach($fieldValue);
    }

    /**
     * Removes a FieldValue
     *
     * @param FieldValue $fieldValueToRemove The FieldValue to be removed
     * @return void
     */
    public function removeFieldValue(FieldValue $fieldValueToRemove): void
    {
        $this->fieldValues->detach($fieldValueToRemove);
    }

    /**
     * Returns the fieldValues
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\FieldValue> $fieldValues
     */
    public function getFieldValues()
    {
        return $this->fieldValues;
    }

    /**
     * Gets an according field value by id
     *
     * @param int $fieldValueId
     * @return bool|FieldValue
     */
    public function getFieldValueById(int $fieldValueId)
    {
        foreach ($this->fieldValues as $_fieldValue) {
            if ($_fieldValue->getUid() == $fieldValueId)
                return $_fieldValue;
        }

        return false;
    }

    /**
     * Sets the fieldValues
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\FieldValue> $fieldValues
     * @return void
     */
    public function setFieldValues(ObjectStorage $fieldValues): void
    {
        $this->fieldValues = $fieldValues;
    }

    /**
     * Checks if the field has a default field value
     *
     * @return int
     */
    public function hasDefaultValue(): int
    {
        return count($this->getDefaultValues());
    }

    /**
     * Gets the default value of the field
     *
     * @return array
     */
    public function getDefaultValues(): array
    {
        $defaultValues = [];
        $fieldValues = clone $this->fieldValues;

        foreach ($fieldValues as $_fieldValue)
        {
            /* @var \Aix\Dataviewer\Domain\Model\FieldValue $_fieldValue */
            if ($_fieldValue->isDefault())
                $defaultValues[] = $_fieldValue;

        }

        return $defaultValues;
    }

    /**
     * Checks if the field has any field values
     *
     * @return int
     */
    public function hasFieldValues(): int
    {
        return (count($this->getFieldValues()));
    }

    /**
     * Checks if the field has an database
     * value
     *
     * @return bool
     */
    public function hasDatabaseValues(): bool
    {
        $fieldValues = clone $this->fieldValues;

        foreach ($fieldValues as $_fieldValue) {
            if ($_fieldValue->getType() == FieldValue::TYPE_DATABASE) {
                return true;
            }
        }


        return false;
    }

    /**
     * Checks if the field has an database
     * value
     *
     * @return bool
     */
    public function hasTypoScriptValues(): bool
    {
        $fieldValues = clone $this->fieldValues;

        foreach ($fieldValues as $_fieldValue) {
            if ($_fieldValue->getType() == FieldValue::TYPE_TYPOSCRIPT) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the field has an database
     * value
     *
     * @return bool
     */
    public function hasFieldValuesValues(): bool
    {
        $fieldValues = clone $this->fieldValues;

        foreach ($fieldValues as $_fieldValue) {
            if ($_fieldValue->getType() == FieldValue::TYPE_FIELDVALUES) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a field is dynamic with its fieldvalues
     *
     * @return bool
     */
    public function isDynamic(): bool
    {
        return ($this->hasDatabaseValues() || $this->hasFieldValuesValues() || $this->hasTypoScriptValues());
    }

    /**
     * Gets the format configuration
     *
     * @return string
     */
    public function getFieldConf(): string
    {
        return $this->fieldConf;
    }

    /**
     * Sets the field configuration
     *
     * @param string $fieldConf
     * @return void
     */
    public function setFieldConf(string $fieldConf): void
    {
        $this->fieldConf = $fieldConf;
    }

    /**
     * Gets a complete configuration array
     *
     * @return array
     */
    public function getConfigArray(): array
    {
        $fieldConf = $this->getFieldConf();
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexformService->convertFlexFormContentToArray($fieldConf);
    }

    /**
     * Overrides or sets a configuration
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function setConfig(string $name, $value)
    {
        $this->config[$name] = $value;
    }

    /**
     * Gets a specific format configuration value
     *
     * @param string $confValueName
     * @return mixed
     */
    public function getConfig(string $confValueName)
    {
        if (empty($this->config)) {
            $fieldConf = $this->getFieldConf();
            $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
            $flexformConfig = $flexformService->convertFlexFormContentToArray($fieldConf);

            $this->config = array_merge($flexformConfig, $this->config);
        }

        $confValue = ($this->config[$confValueName]??null);
        return $confValue;
    }

    /**
     * Gets the tca eval string from the configuration
     *
     * @return string
     */
    public function getEval(): string
    {
        return (string)$this->getConfig('eval');
    }

    /**
     * Checks if the field has an specific eval
     *
     * @param string $eval
     * @return bool
     */
    public function hasEval(string $eval): bool
    {
        return GeneralUtility::inList($this->getEval(), $eval);
    }

    /**
     * Gets the validation information
     *
     * @return string
     */
    public function getValidation(): string
    {
        return $this->validation;
    }

    /**
     * Sets the validation information
     *
     * @param string $validation
     * @return void
     */
    public function setValidation(string $validation): void
    {
        $this->validation = $validation;
    }

    /**
     * Gets the validation configuration
     * as an array
     *
     * @return array
     */
    public function getValidationConfiguration(): array
    {
        if ($this->validation) {
            return GeneralUtility::xml2array($this->validation);
        }

        return [];
    }

    /**
     * Gets the request update setting
     *
     * @return bool
     */
    public function getRequestUpdate(): bool
    {
        return $this->requestUpdate;
    }

    /**
     * Sets the request update
     *
     * @param bool $requestUpdate
     * @return void
     */
    public function setRequestUpdate(bool $requestUpdate): void
    {
        $this->requestUpdate = $requestUpdate;
    }

    /**
     * Gets the display conditions as array
     *
     * @return mixed
     */
    public function getDisplayCond()
    {
        $displayCondition = "<displayCond>{$this->displayCond}</displayCond>";
        $arr = GeneralUtility::xml2array($displayCondition);

        if (is_array($arr)) {
            return $arr;
        }

        return $this->displayCond;
    }

    /**
     * Sets the display condition xml
     *
     * @param string $displayCond
     * @return void
     */
    public function setDisplayCond(string $displayCond): void
    {
        $this->displayCond = $displayCond;
    }

    /**
     * Gets the configuration of 'allowedFileExtensions' from the flexform format
     * configuration
     *
     * @return array
     */
    public function getForeignRecordDefaults(): array
    {
        $configuration = $this->getConfig('foreign_record_defaults');
        if(!is_array($configuration)) {
            $configuration = [];
        }

        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        $extracted = $flexformService->extractConfiguration($configuration, 'defaults', 'field', 'default');
        $recordDefaults = [];
        if (is_array($extracted) && count($extracted)) {
            $recordDefaults = [];
            foreach ($extracted as $_def=>$_defVal)
            {
                $recordDefaults['columns'][$_def] = [
                    'config' => [
                        'default' => $_defVal,
                    ],
                ];
            }
        }

        return $recordDefaults;
    }

    /**
     * Returns the templatefile
     *
     * @return string $templatefile
     */
    public function getTemplatefile(): string
    {
        return $this->templatefile;
    }

    /**
     * Sets the templatefile
     *
     * @param string $templatefile
     * @return void
     */
    public function setTemplatefile(string $templatefile): void
    {
        $this->templatefile = $templatefile;
    }

    /**
     * @param int $_languageUid
     * @return void
     */
    public function set_languageUid(int $_languageUid): void
    {
        $this->_languageUid = $_languageUid;
    }

    /**
     * @return int
     */
    public function get_languageUid(): int
    {
        return $this->_languageUid;
    }

    /**
     * Set l10n parent
     *
     * @param int $l10nParent
     */
    public function setL10nParent(int $l10nParent): void
    {
        $this->l10nParent = $l10nParent;
    }

    /**
     * Get l10n parent
     *
     * @return int
     */
    public function getL10nParent(): int
    {
        return $this->l10nParent;
    }

    /**
     * Gets the according palette name
     *
     * @return string
     */
    public function getPalette(): string
    {
        return $this->palette;
    }

    /**
     * Sets the according palette name
     *
     * @param string $palette
     * @return void
     */
    public function setPalette(string $palette): void
    {
        $this->palette = $palette;
    }

    /**
     * Checks if the field is indexed
     *
     * @return bool
     */
    public function getIsIndex(): bool
    {
        return $this->isIndex;
    }

    /**
     * Sets the field to be indexed
     *
     * @param bool $isIndex
     * @return void
     */
    public function setIsIndex(bool $isIndex): void
    {
        $this->isIndex = $isIndex;
    }

    /**
     * @return string
     */
    public function getDatabaseType(): string
    {
        return $this->databaseType;
    }

    /**
     * @param string $databaseType
     */
    public function setDatabaseType(string $databaseType): void
    {
        $this->databaseType = $databaseType;
    }

    /**
     * @return bool
     */
    public function getBackendSearchable(): bool
    {
        return $this->backendSearchable;
    }

    /**
     * @param bool $backendSearchable
     */
    public function setBackendSearchable(bool $backendSearchable): void
    {
        $this->backendSearchable = $backendSearchable;
    }

    /**
     * @return bool
     */
    public function getCacheTca(): bool
    {
        return $this->cacheTca;
    }

    /**
     * @param bool $cacheTca
     * @return void
     */
    public function setCacheTca(bool $cacheTca): void
    {
        $this->cacheTca = $cacheTca;
    }

    /**
     * Gets the according tca to the field
     *
     * @return AbstractField|null
     */
    public function getTca(): ?AbstractField
    {
        $type = $this->getType();
        $fieldSettingsService = GeneralUtility::makeInstance(FieldSettingsService::class);
        $config = $fieldSettingsService->getFieldTypeConfiguration($type);
        $class = ($config['class']) ?? '';
        if ($class != '' && class_exists($class)) {
            $tcaField = GeneralUtility::makeInstance($class);
            if ($tcaField instanceof FieldInterface) {
                /* @var AbstractField $tcaField */
                $tcaField->setField($this);
                return $tcaField;
            }
        } else {
            throw new TcaGeneratorException('Class \''.$class.'\' for field with uid \'' . $this->getUid() . '\' ('.$this->getVariableName().') on pid \'' . $this->getPid() . '\' cannot be found!');
        }

        return null;
    }

}
