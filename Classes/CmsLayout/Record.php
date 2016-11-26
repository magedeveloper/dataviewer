<?php
namespace MageDeveloper\Dataviewer\CmsLayout;

use \MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class Record extends AbstractCmsLayout
{
	/**
	 * The correct list type for this layout view
	 * 
	 * @var string
	 */
	protected $listType = "dataviewer_record";

	/**
	 * Gets the backend layout
	 *
	 * @param string $listType
	 * @param array $config Configuration
	 * @param array $additionalVariables
	 * @return string
	 */
	public function getBackendLayout($listType, array $config, array $additionalVariables = [])
	{
		$flex = $this->flexFormService->convertFlexFormContentToArray($config["row"]["pi_flexform"]);
		$templateSelection = $flex["settings"]["template_selection"];
		$templateFile = $this->pluginSettingsService->getPredefinedTemplateById($templateSelection);
		
		$additionalVariables["template_selection_file"] = $templateFile;
		return parent::getBackendLayout($listType, $config, $additionalVariables);
	}

}
