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
	 * Constructor
	 *
	 * @return Text
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->variableRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\VariableRepository::class);
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
			
		return "<div class=\"alert alert-danger\">{$message}</div>";
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
		$pages = GeneralUtility::trimExplode(",", $row["pages"]);
		$pids = [];
		$markers = [];
		
		foreach($pages as $_page) 
		{
			preg_match('/(?<table>.*)_(?<uid>[0-9]{0,11})|.*/', $_page, $match);
			$pids[] = $match["uid"];
		}
		
		$variables = $this->variableRepository->findByStoragePids($pids);

		/* @var Variable $_variable */		
		foreach($variables as $_variable)
			$markers[] = [
				"name" => $_variable->getVariableName(),
				"type" => LocalizationUtility::translate("variable_type.{$_variable->getType()}"),
			];
		
		$config["parameters"]["markers"] = $markers;
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
				
				return $view->render();
			}
		}
		
		return;
	}
	
}
