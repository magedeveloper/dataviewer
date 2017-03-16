<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

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
class UserFunc
extends AbstractFieldtype
implements FieldtypeInterface
{
	/**
	 * Plugin Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;


	/**
	 * Variable Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
	 * @inject
	 */
	protected $variableRepository;

	/**
	 * Gets the view model
	 *
	 * @return \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
	 */
	protected function _getView()
	{
		$row = $this->getDatabaseRow();
		$pid = $row["pid"];
		$variableIds = [];

		$standaloneView = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);
		$variables = $this->variableRepository->findByStoragePids([$pid]);
		foreach($variables as $_variable)
			$variableIds[] = $_variable->getUid();

		/* @var \MageDeveloper\Dataviewer\Controller\RecordController $controller */
		$controller = $this->objectManager->get(\MageDeveloper\Dataviewer\Controller\RecordController::class);
		$variables = $controller->prepareVariables($variableIds);
		$standaloneView->assignMultiple($variables);
		
		$record = $this->recordRepository->findByUid( $this->getRecordId() );
		
		$recordVariableName = $this->pluginSettingsService->getRecordVarName();
		$standaloneView->assign($recordVariableName, $record);

		return $standaloneView;
	}

	/**
	 * Initializes all form data providers to
	 * $this->formDataProviders
	 *
	 * Will be executed in order of the added providers!
	 *
	 * @return void
	 */
	public function initializeFormDataProviders()
	{
	}

	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$fieldName 					= $this->getField()->getFieldName();
		$tableName 					= "tx_dataviewer_domain_model_record";
		$value 						= $this->getValue();
		$databaseRow 				= $this->getDatabaseRow();
		$databaseRow[$fieldName] 	= $value;

		$tca = [
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => [
				"columns" => [
					$fieldName => [
						"exclude" => (int)$this->getField()->isExclude(),
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "user",
							"userFunc" => $this->getField()->getConfig("userFunc"),
							"parameters" => $this->getPreparedParameters(),
						],
					],
				],
			],
			"inlineStructure" => [],
		];

		$this->tca = $tca;
		return $this->tca;
	}
	
	/**
	 * Gets the prepared parameters with the conversion of
	 * all available template markers
	 * 
	 * @return array
	 */
	public function getPreparedParameters()
	{
		$view = $this->_getView();
		
		$plainParameters = $this->getField()->getConfig("parameters");
		$parameters = [];
		
		if(is_array($plainParameters))
		{
			foreach($plainParameters as $_parameter)
			{
				$name = $_parameter["parameters"]["parameter_name"];
				$value = $_parameter["parameters"]["parameter_value"];
				$value = $view->renderSource($value);
				
				$parameters[$name] = $value;
			}
		
		}
		
		return $parameters;
	}

    /**
     * Renders a field
     *
     * @return array
     */
    public function render()
    {
        if($this->getField()->getConfig("showInBackend"))
        {
           return parent::render();
        }

        return [
            'additionalJavaScriptPost' => [],
            'additionalJavaScriptSubmit' => [],
            'additionalHiddenFields' => [],
            'additionalInlineLanguageLabelFiles' => [],
            'stylesheetFiles' => [],
            'requireJsModules' => [],
            'extJSCODE' => '',
            'inlineData' => [],
            'html' => "",
        ];
    }
}
