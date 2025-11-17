<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Domain\Model;

use Aix\Dataviewer\Domain\Repository\AbstractRepository;
use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\Utility\LocalizationUtility;
use Aix\Dataviewer\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Datatype extends AbstractModel
{
	/**
	 * Datatype Name
	 *
	 * @var string
	 * @TYPO3\CMS\Extbase\Annotation\Validate(validator="NotEmpty")
	 */
	protected $name = '';

	/**
	 * Datatype Description. Will be showed when creating a new record.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Datatype Icon
	 *
	 * @var string
	 */
	protected $icon = '';

	/**
	 * Default Template File for this Datatype
	 *
	 * @var string
	 */
	protected $templatefile = '';

	/**
	 * Background Color for the Datatype
	 *
	 * @var string
	 */
	protected $color = '';

	/**
	 * Hide Records of this type in the backend
	 *
	 * @var bool
	 */
	protected $hideRecords = false;

	/**
	 * Hide Add Button in the backend
	 *
	 * @var bool
	 */
	protected $hideAdd = false;

	/**
	 * Datatype - Field Relations
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\Field>
	 */
	protected $fields = NULL;

	/**
	 * Tab Configuration
	 *
	 * @var string
	 */
	protected $tabConfig = '';

	/**
	 * Title Divider
	 *
	 * @var string
	 */
	protected $titleDivider = ' ';

    /**
     * Table Name
     *
     * @var string
     */
	protected $tablename = '';

    /**
     * Disable 'General' Tab
     *
     * @var bool
     */
    protected $disableGeneralTab = false;

    /**
     * @var Field|null
     */
    protected $thumbnailField = null;

    /**
     * Enable SEO Fields
     *
     * @var boolean
     */
    protected $enableSeo = true;

    /**
     * Cache generated TCA
     *
     * @var boolean
     */
    protected $cacheTca = true;

	/**
	 * __construct
	 */
	public function __construct()
	{
	    parent::__construct();
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
	protected function initStorageObjects(): void
	{
		$this->fields = new ObjectStorage();
	}

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName(): string
	{
		return $this->name;
	}

    /**
     * Gets an instance of the according repository
     * @return AbstractRepository|null
     */
	public function getRepository(): ?AbstractRepository
    {
        $repositoryClassName = $this->getFullyQualifiedRepositoryClassName();
        if(class_exists($repositoryClassName) && $repositoryClassName != '') {
            try {
                $repository = GeneralUtility::makeInstance($repositoryClassName);
            } catch (\Exception $e) {
            }
            if($repository instanceof AbstractRepository) {
                return $repository;
            }
        }

        return null;
    }

    /**
     * Gets the datatype namespace
     *
     * @param string $domain
     * @return string
     */
	protected function _getNamespace(string $domain = 'Model'): string
    {
        $parts = $this->_getNamespaceParts();
        // Remove last element from parts
        unset($parts[count($parts)-1]);
        $namespace = 'Aix\\Dataviewer\\Domain\\'.$domain.'\\Record\\';
        $namespace.=implode('\\', $parts);

        if (count($parts)) {
            $namespace.='\\';
        }

        return $namespace;
    }

    /**
     * Generates folder parts from the according
     * tablename
     *
     * @return array
     */
    protected function _getNamespaceParts(): array
    {
        $partName = str_replace('tx_dataviewer_domain_model_record_','',$this->tablename);
        $parts = GeneralUtility::trimExplode("_", $partName);
        return array_map('ucfirst', $parts);
    }

    /**
     * Gets the single class name from
     * the datatype name
     *
     * @return string
     */
	public function getClassName(): string
    {
        $parts = $this->_getNamespaceParts();
        return end($parts);
    }

    /**
     * Gets the model namespace
     *
     * @return string
     */
    public function getModelNamespace(): string
    {
        return trim($this->_getNamespace('Model'), '\\');
    }

    /**
     * Gets the repository namespace
     *
     * @return string
     */
    public function getRepositoryNamespace(): string
    {
        return trim($this->_getNamespace('Repository'), '\\');
    }

    /**
     * Gets the fully qualified class name from the
     * datatype name
     *
     * @return string
     */
    public function getFullyQualifiedClassName(): string
    {
       return $this->getModelNamespace() . '\\' . $this->getClassName();
    }

    /**
     * Gets the fully qualified repository name
     * from the datatype name
     *
     * @return string
     */
    public function getFullyQualifiedRepositoryClassName(): string
    {
        return $this->getRepositoryNamespace() . '\\' .$this->getClassName() . 'Repository';
    }

    /**
     * Get the class file path generated from
     * the datatype name
     *
     * @return string
     */
    public function getClassFilePath(): string
    {
        return $this->_generateFilePath('EXT:dataviewer/Classes/Domain/Model/Record/');
    }

    /**
     * Get the repository file path generated
     * from the datatype name
     *
     * @return string
     */
    public function getRepositoryFilePath(): string
    {
        return $this->_generateFilePath('EXT:dataviewer/Classes/Domain/Repository/Record/','Repository');
    }

    /**
     * Generates a file path for the according
     * datatype
     *
     * @param string $prefix
     * @param string $suffix
     * @return string
     */
    protected function _generateFilePath(string $prefix, string $suffix = ''): string
    {
        $parts = $this->_getNamespaceParts();
        $filename = end($parts) . $suffix .'.php';
        unset($parts[count($parts)-1]);
        $path=$prefix.implode('/', $parts);

        if (count($parts)) {
            $path.='/';
        }

        return $path.$filename;
    }

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
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
	public function setDescription(string $description): string
	{
		$this->description = $description;
	}

	/**
	 * Returns the icon
	 *
	 * @return string $icon
	 */
	public function getIcon(): string
	{
		return $this->icon;
	}

	/**
	 * Sets the icon
	 *
	 * @param string $icon
	 * @return void
	 */
	public function setIcon(string $icon): void
	{
		$this->icon = $icon;
	}

	/**
	 * Gets an information string about this datatype
	 *
	 * @return string
	 */
	public function getInfo(): string
	{
		$info = "";
		if ($this->getUid())
			$info .= "[{$this->getUid()}] ";

		$info .= $this->getName();

		return $info;
	}

	/**
	 * Gets the color
	 *
	 * @return string
	 */
	public function getColor(): string
	{
		return $this->color;
	}

	/**
	 * Sets the color for the datatype
	 *
	 * @param string $color
	 * @return void
	 */
	public function setColor(string $color): void
	{
		$this->color = $color;
	}

	/**
	 * Gets the setting to hide
	 * records of this type in
	 * the backend
	 *
	 * @return bool
	 */
	public function getHideRecords(): bool
	{
		return $this->hideRecords;
	}

	/**
	 * Sets the configuration to hide backend
	 * records of this type
	 *
	 * @param bool $hideRecords
	 * @return void
	 */
	public function setHideRecords(bool $hideRecords = true): void
	{
		$this->hideRecords = $hideRecords;
	}

	/**
	 * Gets the configuration to hide the
	 * add button in the backend
	 *
	 * @return bool
	 */
	public function getHideAdd(): bool
	{
		return $this->hideAdd;
	}

	/**
	 * Sets the configuration to hide the add button
	 * in the backend
	 *
	 * @param bool $hideAdd
	 * @return void
	 */
	public function setHideAdd(bool $hideAdd = true): void
	{
		$this->hideAdd = $hideAdd;
	}

	/**
	 * Adds a Field
	 *
	 * @param Field $field
	 * @return void
	 */
	public function addField(Field $field): void
	{
		$this->fields->attach($field);
	}

	/**
	 * Removes a Field
	 *
	 * @param Field $fieldToRemove The Field to be removed
	 * @return void
	 */
	public function removeField(Field $fieldToRemove): void
	{
		$this->fields->detach($fieldToRemove);
	}

	/**
	 * Returns the fields
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\Field> $fields
	 */
	public function getFields(): ObjectStorage
	{
		return $this->fields;
	}

	/**
	 * Gets an array with all fields, sorted
	 * by type
	 *
	 * @return array
	 */
	public function getSortedFields(): array
	{
		$sortArr = [];
		$fields = $this->fields;

		foreach ($fields as $_field)
			$sortArr[$_field->getType()][] = $_field;

		asort($sortArr);

		return $sortArr;
	}

	/**
	 * Gets the fields as an approachable
	 * array
	 *
	 * @return array
	 */
	public function getApproachableFields(): array
	{
		$approachable = [];

		$fields = $this->fields;
		foreach ($fields as $_field)
			$approachable[$_field->getCode()] = $_field;

		return $approachable;
	}

	/**
	 * Sets the fields
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Aix\Dataviewer\Domain\Model\Field> $fields
	 * @return void
	 */
	public function setFields(ObjectStorage $fields): ObjectStorage
	{
		$this->fields = $fields;
	}

	/**
	 * Checks if the datatype has fields
	 *
	 * @return int
	 */
	public function hasFields(): int
	{
		return $this->fields != null ? (count($this->fields)) : 0;
	}

	/**
	 * Gets the title divider
	 *
	 * @return string
	 */
	public function getTitleDivider(): string
	{
		return str_replace("(SPACE)", " ", $this->titleDivider);
	}

	/**
	 * Sets the title divider
	 *
	 * @param string $titleDivider
	 * @return void
	 */
	public function setTitleDivider(string $titleDivider): void
	{
		$this->titleDivider = $titleDivider;
	}

	/**
	 * Checks if the datatype has a field
	 *
	 * @param Field $field
	 * @return bool
	 */
	public function hasField(Field $field): bool
	{
        foreach ($this->getFields() as $_field) {
            if ($_field->getUid() == $field->getUid()) {
                return true;
            }
        }

        return false;
	}

	/**
	 * Gets a field by id
	 *
	 * @param int $fieldId
	 * @return null|Field
	 */
	public function getFieldById(int $fieldId): ?Field
	{
		foreach ($this->fields as $_field)
			if ($_field->getUid() == $fieldId)
				return $_field;

		return null;
	}

    /**
     * Gets a field by variable name
     *
     * @param string $variableName
     * @return null|Field
     */
    public function getFieldByVariableName(string $variableName): ?Field
    {
        foreach ($this->fields as $_field)
            if ($_field->getVariableName() == $variableName)
                return $_field;

        return null;
    }

	/**
	 * Determines if the record has an title field
	 * or needs to use its own title
	 *
	 * @return bool
	 */
	public function getHasTitleField(): bool
	{
		$fields = $this->getFields();
		foreach ($fields as $_field) {
			/* @var Field $_field */
			if ($_field->getIsRecordTitle())
				return true;
		}

		return false;
	}

    /**
     * Gets the table name
     *
     * @return string
     */
    public function getTablename(): string
    {
        return $this->tablename;
    }

    /**
     * Sets the table name
     *
     * @param string $tablename
     * @return void
     */
    public function setTablename(string $tablename): void
    {
        $this->tablename = $tablename;
    }

    /**
     * Gets the setting to disable
     * the general tab
     *
     * @return bool
     */
    public function getDisableGeneralTab(): bool
    {
        return $this->disableGeneralTab;
    }

    /**
     * Sets to disable the general tab
     *
     * @param bool $disableGeneralTab
     * @return void
     */
    public function setDisableGeneralTab(bool $disableGeneralTab): void
    {
        $this->disableGeneralTab = $disableGeneralTab;
    }

    /**
     * @return Field|null
     */
    public function getThumbnailField(): ?Field
    {
        return $this->thumbnailField;
    }

    /**
     * @param Field|null $thumbnailField
     */
    public function setThumbnailField(?Field $thumbnailField): void
    {
        $this->thumbnailField = $thumbnailField;
    }

    /**
     * @return bool
     */
    public function getEnableSeo(): bool
    {
        return $this->enableSeo;
    }

    /**
     * @param bool $enableSeo
     */
    public function setEnableSeo(bool $enableSeo): void
    {
        $this->enableSeo = $enableSeo;
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
	 * Sets the tab configuration
	 *
	 * @param string $tabConfig
	 * @return void
	 */
	public function setTabConfig(string $tabConfig): void
	{
		$this->tabConfig = $tabConfig;
	}

	/**
	 * Gets the tab configuration
	 *
	 * @return string
	 */
	public function getTabConfig(): string
	{
		return $this->tabConfig;
	}

    /**
     * @return string
     */
	public function getTabConfiguration(): string
    {
        $generalConfig = "";
        $tabConfig = "";
        $tabConfigurationArray = $this->getTabConfigurationArray();

        if (!$this->getDisableGeneralTab()) {
            $generalConfig .= "--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,";
            if(!$this->getHasTitleField()) {
                $generalConfig.="title,";
            }
        }

        // Use tab configuration for the rest of the fields
        $usedFieldIds = [];
        foreach ($tabConfigurationArray as $_tabId=>$_tabConfig) {
            $tabLabel = $_tabConfig["label"];
            $tabFields = $_tabConfig["fields"];
            $usedFieldIds = array_merge($usedFieldIds,$_tabConfig["fieldIds"]);
                $tabConfig .= "--div--;{$tabLabel},{$tabFields},";
        }

        $usedFieldIds = array_unique($usedFieldIds);

        // Get all fields that are not assigned to tabs
        $fields = $this->getFields();
        foreach ($fields as $_field) {
            if ($_field->getPalette() == "") {
                if (!in_array($_field->getUid(), $usedFieldIds)) {
                    $generalConfig .= "{$_field->getCode()},";
                }
            }
        }

        return $generalConfig.$tabConfig;
    }

	/**
	 * Gets the complete tab configuration array
	 *
	 * @return array
	 */
	public function getTabConfigurationArray(): array
	{
		$tabConfig = $this->getTabConfig();
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
		$flexformConfig = $flexformService->convertFlexFormContentToArray($tabConfig);

		$configArray = [];
		if (isset($flexformConfig["field"]) && is_array($flexformConfig["field"])) {
            foreach ($flexformConfig["field"] as $_id => $_tab) {
                $tab = $_tab["tab"];
                $label = $tab["tab_name"];

                if (str_starts_with($label, "LLL:")) {
                    $label = LocalizationUtility::translate($label);
                }

                $icon = (array_key_exists('tab_icon', $tab))?$tab['tab_icon']:'';
                $colorCss = (isset($tab["tab_icon_color"])) ? " style=\"color:{$tab["tab_icon_color"]}\"" : "";

                if ($icon != '') {
                    $icon = "<span class=\"icon-unify\"{$colorCss}><i class=\"fa {$icon}\"></i></span>";
                }

                $fields = GeneralUtility::trimExplode(",", $tab["tab_fields"]);

                if (count($fields)) {
                    $assignedFields = [];
                    $containsPalette = false;
                    foreach ($fields as $_field) {
                        if (is_numeric($_field)) {
                            $field = $this->getFieldById((int)$_field);
                            if ($field instanceof Field) {
                                $assignedFields[] = $field->getCode();
                            }
                        } else {
                            // is palette, because no numeric field id was given
                            $paletteCode = StringUtility::createCodeFromString($_field);
                            $assignedFields[] = "--palette--;;{$paletteCode}";
                            $containsPalette = true;
                        }
                    }

                    $fieldsStr = implode(",", $assignedFields);

                    $configArray[$_id] = [
                        "label" => $label,
                        "fields" => $fieldsStr,
                        "fieldIds" => $fields,
                        "containsPalette" => $containsPalette,
                    ];

                }
            }
        }
		return $configArray;
	}

    /**
     * Gets palettes information
     * to create tca palettes
     *
     * @return array
     */
	public function getPalettes(): array
    {
        $palettes = [];
        $fields = $this->getFields();

        foreach ($fields as $_field) {
            /* @var Field $_field */
            $palette = $_field->getPalette();
            if (trim($palette) != "") {
                $palettes[$palette][] = $_field->getCode();
            }
        }

        return $palettes;
    }
}
