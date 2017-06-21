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
class Datatype extends AbstractModel
{
	/**
	 * Datatype Name
	 * 
	 * @var string
	 * @validate NotEmpty
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
	 * @var boolean
	 */
	protected $hideRecords = false;

	/**
	 * Hide Add Button in the backend
	 *
	 * @var boolean
	 */
	protected $hideAdd = false;

	/**
	 * Datatype is hidden
	 *
	 * @var boolean
	 */
	protected $hidden = FALSE;

	/**
	 * Datatype - Field Relations
	 * 
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\Field>
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
		$this->fields = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the name
	 * 
	 * @return string $name
	 */
	public function getName() 
	{
		return $this->name;
	}

	/**
	 * Sets the name
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setName($name) 
	{
		$this->name = $name;
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
	 * Returns the icon
	 * 
	 * @return string $icon
	 */
	public function getIcon() 
	{
		return $this->icon;
	}

	/**
	 * Sets the icon
	 * 
	 * @param string $icon
	 * @return void
	 */
	public function setIcon($icon) 
	{
		$this->icon = $icon;
	}

	/**
	 * Gets an information string about this datatype
	 * 
	 * @return string
	 */
	public function getInfo()
	{
		$info = "";
		if ($this->getUid())
			$info .= "[{$this->getUid()}] ";
			
		$info .= $this->getName();
		
		return $info;
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

	/**
	 * Gets the color
	 * 
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * Sets the color for the datatype
	 * 
	 * @param string $color
	 * @return void
	 */
	public function setColor($color)
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
	public function getHideRecords()
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
	public function setHideRecords($hideRecords = true)
	{
		$this->hideRecords = $hideRecords;
	}

	/**
	 * Gets the configuration to hide the
	 * add button in the backend
	 * 
	 * @return boot
	 */
	public function getHideAdd()
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
	public function setHideAdd($hideAdd = true)
	{
		$this->hideAdd = $hideAdd;
	}
	
	

	/**
	 * Checks if the fieldvalue is hidden
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return $this->hidden;
	}

	/**
	 * Sets the hidden status
	 *
	 * @param bool $hidden
	 */
	public function setHidden($hidden)
	{
		$this->hidden = $hidden;
	}

	/**
	 * Gets the hidden status
	 *
	 * @return bool
	 */
	public function getHidden()
	{
		return $this->hidden;
	}
	
	/**
	 * Adds a Field
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return void
	 */
	public function addField(\MageDeveloper\Dataviewer\Domain\Model\Field $field) 
	{
		$this->fields->attach($field);
	}

	/**
	 * Removes a Field
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $fieldToRemove The Field to be removed
	 * @return void
	 */
	public function removeField(\MageDeveloper\Dataviewer\Domain\Model\Field $fieldToRemove) 
	{
		$this->fields->detach($fieldToRemove);
	}

	/**
	 * Returns the fields
	 * 
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\Field> $fields
	 */
	public function getFields() 
	{
		return $this->fields;
	}

	/**
	 * Gets an array with all fields, sorted
	 * by type
	 * 
	 * @return array
	 */
	public function getSortedFields()
	{
		$sortArr = [];
		$fields = $this->fields;
		
		foreach($fields as $_field)
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
	public function getApproachableFields()
	{
		$approachable = [];

		$fields = $this->fields;
		foreach($fields as $_field)
			$approachable[$_field->getCode()] = $_field;

		return $approachable;
	}

	/**
	 * Sets the fields
	 * 
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MageDeveloper\Dataviewer\Domain\Model\Field> $fields
	 * @return void
	 */
	public function setFields(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $fields) 
	{
		$this->fields = $fields;
	}

	/**
	 * Checks if the datatype has fields
	 * 
	 * @return int
	 */
	public function hasFields()
	{
		return (count($this->fields));	
	}

	/**
	 * Gets the title divider
	 * 
	 * @return string
	 */
	public function getTitleDivider()
	{
		return $this->titleDivider;
	}

	/**
	 * Sets the title divider
	 * 
	 * @param string $titleDivider
	 * @return void
	 */
	public function setTitleDivider($titleDivider)
	{
		$this->titleDivider = $titleDivider;
	}
	
	/**
	 * Checks if the datatype has a field
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return bool
	 */
	public function hasField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$foundField = $this->getFieldEquivalent($field);
		return ($foundField instanceof \MageDeveloper\Dataviewer\Domain\Model\Field);
	}

	/**
	 * Gets a field by id
	 * 
	 * @param int $fieldId
	 * @return bool|\MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function getFieldById($fieldId)
	{
		foreach($this->fields as $_field)
			if ($_field->getUid() == $fieldId)
				return $_field;

		return false;
	}

	/**
	 * Gets the field equivalent
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return bool|\MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function getFieldEquivalent(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		return $this->getFieldById($field->getUid());
	}

	/**
	 * Determines if the record has an title field
	 * or needs to use its own title
	 *
	 * @return bool
	 */
	public function getHasTitleField()
	{
		$fields = $this->getFields();

		foreach($fields as $_field)
		{
			/* @var Field $_field */
			if ($_field->getIsRecordTitle())
				return true;
		}
	
		return false;
	}

	/**
	 * Sets the tab configuration
	 * 
	 * @param string $tabConfig
	 * @return void
	 */
	public function setTabConfig($tabConfig)
	{
		$this->tabConfig = $tabConfig;
	}

	/**
	 * Gets the tab configuration
	 * 
	 * @return string
	 */
	public function getTabConfig()
	{
		return $this->tabConfig;
	}

	/**
	 * Gets the complete tab configuration array
	 * 
	 * @return array
	 */
	public function getTabConfigurationArray()
	{
		$tabConfig = $this->getTabConfig();
		$flexformConfig = $this->flexFormService->convertFlexFormContentToArray($tabConfig);
		
		$configArray = [];
		if(isset($flexformConfig["field"]) && is_array($flexformConfig["field"]))
			foreach($flexformConfig["field"] as $_id=>$_tab)
			{
				$tab = $_tab["tab"];
				
				$label = $tab["tab_name"];
				
				if(GeneralUtility::isFirstPartOfStr($label, "LLL:"))
					$label = \MageDeveloper\Dataviewer\Utility\LocalizationUtility::translate($label);
				
				$icon = $tab["tab_icon"];
				$colorCss = (isset($tab["tab_icon_color"]))?" style=\"color:{$tab["tab_icon_color"]}\"":"";

				if($icon)
					$icon = "<span class=\"icon-unify\"{$colorCss}><i class=\"fa {$icon}\"></i></span>";
				
				$fields = GeneralUtility::trimExplode(",", $tab["tab_fields"]);
				
				if(count($fields))
				{
					$configArray[$_id] = [
						"label" => $label,
						"icon" => $icon,
						"content" => array_flip($fields),
					];
				}
			}

		return $configArray;
	}
}
