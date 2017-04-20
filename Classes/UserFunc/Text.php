<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Domain\Model\Variable;
use MageDeveloper\Dataviewer\Fluid\View\StandaloneView;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
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
class Text
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

    /**
     * Variable Repository
     *
     * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
     * @inject
     */
    protected $variableRepository;

    /**
     * Record Repository
     *
     * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
     * @inject
     */
    protected $recordRepository;

    /**
     * Plugin Settings Service
     * 
     * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
     * @inject
     */
	protected $pluginSettingsService;

    /**
     * FlexForm Service
     *
     * @var \MageDeveloper\Dataviewer\Service\FlexFormService
     * @inject
     */
    protected $flexFormService;

	/**
	 * Constructor
	 *
	 * @return Text
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->variableRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\VariableRepository::class);
	    $this->pluginSettingsService    = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
	    $this->flexformService          = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
        $this->recordRepository		    = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
    }

	/**
	 * Just display nothing :)
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayNothing(array &$config, &$parentObject)
	{
		return "";
	}

	/**
	 * Just display nothing :)
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayNoConfigurationMessage(array &$config, &$parentObject)
	{
		$message = LocalizationUtility::translate("message.this_field_has_no_configuration");
		return "<div class=\"message message-alert\">{$message}</div>";
	}

	/**
	 * Display a simple error text in backend
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayErrorText(array &$config, &$parentObject)
	{
		$message = "Error @ {$config["itemFormElName"]}";
	
		$parameters = $config["parameters"];
		if(isset($parameters["message"]))
			$message = $parameters["message"];
			
		$severity = "danger";
		if(isset($parameters["severity"]))
			$severity = $parameters["severity"];	
			
		return "<div class=\"alert alert-{$severity}\">{$message}</div>";
	}

    /**
     * Display available markers for field filter value
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function displayAvailableMarkers(array &$config, &$parentObject)
    {
        $row = $config["row"];
        $pages = (isset($row["pages"]))?$row["pages"]:$row["pid"];
        $parameters = (isset($config["parameters"]))?$config["parameters"]:[];
        
		if(!is_array($pages))
			$pages = GeneralUtility::trimExplode(",", $pages, true);
        
		$pids = [];
		foreach($pages as $_page)
		{
			//preg_match('/(?<table>.*)_(?<uid>[0-9]{0,11})|.*/', $_page, $match);
			$pids[] = $_page["uid"];
		}

        $variables = $this->variableRepository->findByStoragePids($pids);

		$markers = [];
        if(isset($parameters["includeRecord"]) && $parameters["includeRecord"] == true)
            $markers[] = [
                "name" => $this->pluginSettingsService->getRecordVarName(),
                "type" => LocalizationUtility::translate("tx_dataviewer_domain_model_record"),
            ];

        /* @var Variable $_variable */
        foreach($variables as $_variable)
            $markers[] = [
                "name" => $_variable->getVariableName(),
                "type" => LocalizationUtility::translate("variable_type.{$_variable->getType()}"),
            ];
            
        if(count($markers) == 0)
		{
			$config["parameters"]["message"] = LocalizationUtility::translate("message.no_markers_found_on_storage_pids");
			$config["parameters"]["severity"] = "info";
			return $this->displayErrorText($config, $parentObject);
		}

        $config["parameters"]["markers"] = $markers;
        return $this->displayTemplate($config, $parentObject);
    }

    /**
     * Display a query preview from Field/Value Filter Settings
     *
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return array
     */
    public function displayQueryPreview(array &$config, &$parentObject)
    {
        $row = $config["row"];
        $flexform = $row["pi_flexform"];
        
        $flex = $this->flexformService->walkFlexFormNode($flexform);
        $path = "data/field_value_filter_setting/lDEF/settings/field_value_filter";
        $filters = \MageDeveloper\Dataviewer\Utility\ArrayUtility::getArrayValueByPath($flex, $path);
        
        $preparedFilters = [];
        foreach($filters as $_id=>$_filter)
        {
            $preparedFilters[] = [
                "filter_combination"    => reset($_filter["filters"]["filter_combination"]),
                "field_id"              => reset($_filter["filters"]["field_id"]),
                "filter_condition"      => reset($_filter["filters"]["filter_condition"]),
                "field_value"           => $_filter["filters"]["field_value"],
                "filter_field"          => reset($_filter["filters"]["filter_field"]),
            ];
        
        }
        
        $statement = $this->recordRepository->getStatementByAdvancedConditions($preparedFilters);
        
        preg_match_all('/\(.*\)/Usi', $statement, $matches);
        
        // Predefined different colors for highlighting query parts
        $colors = [
            "#5295E6",
            "#059D54",
            "#9D9105",
            "#DF3907",
            "#DB0EFF",
            "#BB0EFF",
            "#FF4242",
            "#FF4288",
            "#4268FF",
            "#A7FF42",
        ];
        
        if(isset($matches[0]))
        {
            $i = 0;
            foreach($matches[0] as $_match)
            {
                $color = $colors[$i];
                $colored = "<div style=\"display:inline;color:{$color};\">{$_match}</div>";
                $statement = str_replace($_match, $colored, $statement);
                $i++;
                
                if($i > count($colors)) $i = 0;
            }
        }
        
        $config["parameters"]["statement"] = $statement;
        return $this->displayTemplate($config, $parentObject);
    }

	/**
	 * Display a rendered template from a
	 * given path by parameters -> template
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayTemplate(array &$config, &$parentObject)
	{
		$parameters = $config["parameters"];
		$template = (isset($parameters["template"]))?$parameters["template"]:null;
		
		if(!is_null($template))
		{
			$templateFile = GeneralUtility::getFileAbsFileName($template);
			if(file_exists($templateFile))
			{
				/* @var StandaloneView $view */
				$view = $this->objectManager->get(StandaloneView::class);
				$view->setTemplatePathAndFilename($templateFile);
				$view->assignMultiple($parameters);
				$view->assign("config", $config);
				
				return $view->render();
			}
		}
		
		return;
	}

}
