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
class FormSettingsService extends PluginSettingsService
{
	/**
	 * Action String Definitation
	 * 
	 * @var string
	 */
	const ACTION_NEW 	= "new";
	const ACTION_EDIT 	= "edit";
	const ACTION_DELETE = "delete"; 

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
	 * Gets the template override setting
	 *
	 * @return null|string
	 */
	public function getTemplateOverride()
	{
		return $this->getSettingByCode("template_override");
	}

	/**
	 * Checks if the plugin setting has a template
	 * override
	 *
	 * @return bool
	 */
	public function hasTemplateOverride()
	{
		return ($this->getTemplateOverride() != "");
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
	 * Gets the file upload folder from the plugin
	 * settings
	 * 
	 * @return null|string
	 */
	public function getFileUploadFolder()
	{
		$defaultFileUploadFolder = "dataviewer/upload/";
		$setting = $this->getSettingByCode("file_upload_folder");
		
		$fileUploadFolder = $defaultFileUploadFolder;
		if(strlen($setting))
		{
			list($id, $fileUploadFolder) = explode(":", $setting);
		}
		
		return $fileUploadFolder;
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
	 * Gets the allowed action from the plugin configuration
	 * 
	 * @return array
	 */
	public function getAllowedActions()
	{
		$setting = $this->getSettingByCode("allowed_actions");
		$allowedActions = GeneralUtility::trimExplode(",", $setting, true);
		
		if(!is_array($allowedActions))
			$allowedActions = [];
			
		return $allowedActions;	
	}

	/**
	 * Checks if a action is allowed by the plugin
	 * 
	 * @param string $action
	 * @return bool
	 */
	public function isAllowedAction($action)
	{
		return in_array($action, $this->getAllowedActions());
	}

	/**
	 * Gets the redirect target after the successful
	 * creation of a new record
	 *
	 * @return int
	 */
	public function getRedirectAfterSuccessfulCreation()
	{
		$setting = (int)$this->getSettingByCode("redirect_success_new");
		return ($setting > 0)?$setting:null;
	}

	/**
	 * Gets the redirect target after the successful
	 * storaing an edited record
	 *
	 * @return int
	 */
	public function getRedirectAfterSuccessfulEditing()
	{
		$setting = (int)$this->getSettingByCode("redirect_success_edit");
		return ($setting > 0)?$setting:null;
	}

	/**
	 * Gets the redirect target after the successful
	 * deletion of a record
	 *
	 * @return int
	 */
	public function getRedirectAfterSuccessfulDeletion()
	{
		$setting = (int)$this->getSettingByCode("redirect_success_delete");
		return ($setting > 0)?$setting:null;
	}

}
