<?php
namespace MageDeveloper\Dataviewer\Service\Settings\Plugin;

use \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ListSettingsService extends PluginSettingsService
{
	/**
	 * Selection Types
	 * @var string
	 */
	const SELECTION_TYPE_DATATYPES 			= "SELECTION_DATATYPE";
	const SELECTION_TYPE_RECORDS			= "SELECTION_RECORDS";
	const SELECTION_TYPE_CREATION_DATE		= "SELECTION_CREATION_DATE";
	const SELECTION_TYPE_CHANGE_DATE		= "SELECTION_CHANGE_DATE";
	const SELECTION_TYPE_FIELD_VALUE_FILTER	= "SELECTION_FIELD_VALUE_FILTER";
	const SELECTION_TYPE_ALL_RECORDS		= "SELECTION_ALL";

	/**
	 * Template Selection
	 * @var string
	 */
	const TEMPLATE_SELECTION_CUSTOM			= "CUSTOM";
	const TEMPLATE_SELECTION_FLUID			= "FLUID";

	/**
	 * Gets the record selection type
	 *
	 * @return null|string
	 */
	public function getRecordSelectionType()
	{
		return $this->getSettingByCode("record_selection_type");
	}

	/**
	 * Gets the selected datatype ids
	 *
	 * @return string
	 */
	public function getSelectedDatatypeIds()
	{
		return $this->getSettingByCode("datatype_selection");
	}

	/**
	 * Gets the selected record ids
	 *
	 * @return string
	 */
	public function getSelectedRecordIds()
	{
		return $this->getSettingByCode("record_selection");
	}

	/**
	 * Gets the selected record id from the
	 * single record selection field
	 *
	 * @return int
	 */
	public function getSelectedRecordId()
	{
		return (int)$this->getSettingByCode("single_record_selection");
	}

	/**
	 * Gets the selected field id from the
	 * settings
	 *
	 * @return int
	 */
	public function getSelectedFieldId()
	{
		return (int)$this->getSettingByCode("field_selection");
	}

	/**
	 * Gets the template override setting
	 *
	 * @return null|string
	 */
	public function getTemplateOverride()
	{
		return $this->getSettingByCode("template_override");
	}

	/**
	 * Gets the value from the template
	 * selection
	 *
	 * @return null|string
	 */
	public function getTemplateSelection()
	{
		return $this->getSettingByCode("template_selection");
	}

	/**
	 * Retrieves the template path from the template selection
	 * either from the override or the selector box
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		$templateSelection = $this->getTemplateSelection();
		$templateOverride  = $this->getTemplateOverride();
		
		if($templateSelection == self::TEMPLATE_SELECTION_CUSTOM && $templateOverride)
			return $templateOverride;
		
		return $this->getPredefinedTemplateById($templateSelection);
	}

	/**
	 * Checks if the plugin setting has a template
	 * override
	 *
	 * @return bool
	 */
	public function hasTemplate()
	{
		$templateSelection = $this->getTemplateSelection();
		$templateOverride  = $this->getTemplateOverride();
		
		if($templateSelection == self::TEMPLATE_SELECTION_CUSTOM)
			if($templateOverride)
				return true;
			else
				return false;
			
		if($templateSelection)	
			return true;
			
		return false;	
	}

	/**
	 * Checks if the plugin wants to render custom
	 * fluid code
	 * 
	 * @return bool
	 */
	public function isCustomFluidCode()
	{
		return ($this->getTemplateSelection() == self::TEMPLATE_SELECTION_FLUID);
	}

	/**
	 * Gets the setting for the divide type
	 *
	 * @return null|string
	 */
	public function getDivideType()
	{
		return $this->getSettingByCode("divide_records");
	}

	/**
	 * Gets the default sort field
	 *
	 * @return string
	 */
	public function getSortField()
	{
		return ($this->getSettingByCode("sort_field"))?$this->getSettingByCode("sort_field"):"title";
	}

	/**
	 * Get the sort order
	 *
	 * @return string
	 */
	public function getSortOrder()
	{
		return ($this->getSettingByCode("sort_order"))?$this->getSettingByCode("sort_order"):\TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
	}

	/**
	 * Gets the default value for the
	 * 'Items per Page'-Selection
	 * 
	 * @return int
	 */
	public function getPerPage()
	{
		return (int)$this->getSettingByCode("per_page");
	}

	/**
	 * Gets the date from setting
	 *
	 * @return \Datetime
	 */
	public function getDateFrom()
	{
		$setting = $this->getSettingByCode("date_from_selection");

		// Now
		if (!$setting) $setting = time();

		$datetimeObj = new \Datetime();
		$datetimeObj->setTimestamp($setting);

		return $datetimeObj;
	}

	/**
	 * Gets the date to setting
	 *
	 * @return \Datetime
	 */
	public function getDateTo()
	{
		$setting = $this->getSettingByCode("date_to_selection");

		// Tomorrow
		if (!$setting) $setting = time()+86400;

		$datetimeObj = new \Datetime();
		$datetimeObj->setTimestamp($setting);

		return $datetimeObj;
	}

	/**
	 * Gets the limitation value for records to show
	 *
	 * @return int|null
	 */
	public function getLimitation()
	{
		$limit = (int)$this->getSettingByCode("number_of_records");
		
		if($limit > 0)
			return $limit;
			
		return null;	
	}

	/**
	 * Gets the attribute filters setting
	 *
	 * @return array
	 */
	public function getFieldValueFilters()
	{
		$settingId = "field_value_filter";
		$setting = $this->getSettingByCode($settingId);
		$filters = [];
		
		if (is_array($setting))
		{
			foreach($setting as $_filterSetting)
				$filters[] = $_filterSetting["filters"];
		}

		return $filters;
	}

	/**
	 * Gets the default orderings from the plugin
	 * settings
	 *
	 * @return array
	 */
	public function getDefaultOrderings()
	{
		$sortField = $this->getSortField();
		return [
			$sortField => $this->getSortOrder(),
		];
	}

	/**
	 * Gets selected variable ids
	 *
	 * @return array
	 */
	public function getSelectedVariableIds()
	{
		$variables = $this->getSettingByCode("variable_injection");
		return GeneralUtility::trimExplode(",", $variables, true);
	}

	/**
	 * Debug Mode Enabled
	 *
	 * @return bool
	 */
	public function isDebug()
	{
		return (bool)$this->getSettingByCode("debug");
	}

	/**
	 * Sorting settings of this plugin
	 * are forced and not disturbed by 
	 * an other sorting plugin
	 * 
	 * @return bool
	 */
	public function isForcedSorting()
	{
		return (bool)$this->getSettingByCode("force_sorting");
	}

	/**
	 * Gets the template switch conditions
	 * from the plugin configuration
	 * 
	 * @return array
	 */
	public function getTemplateSwitchConditions()
	{
		$conditions = $this->getSettingByCode("template_switch");
		
		if(!is_array($conditions))
			$conditions = [];
			
		return $conditions;
	}

	/**
	 * Gets the custom header configuration from
	 * the plugin configuration
	 * 
	 * @return array
	 */
	public function getCustomHeaders()
	{
		$headers = $this->getSettingByCode("custom_headers");

		if(!is_array($headers))
			$headers = [];

		return $headers;
	}

	/**
	 * Gets the setting for rendering only the selected
	 * template without complete site template
	 * 
	 * @return bool
	 */
	public function renderOnlyTemplate()
	{
		return (bool)$this->getSettingByCode("render_only_template");
	}

	/**
	 * Gets the entered fluid code
	 * 
	 * @return null|string
	 */
	public function getFluidCode()
	{
		return $this->getSettingByCode("fluid_code");
	}
}
