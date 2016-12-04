<?php
namespace MageDeveloper\Dataviewer\eID;

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
class Dispatcher
{
	/**
	 * Array of all request Arguments
	 *
	 * @var array
	 */
	protected $requestArguments = [];

	/**
	 * Extension Name
	 *
	 * @var string
	 */
	protected $extensionName;

	/**
	 * The plugin name
	 *
	 * @var string
	 */
	protected $pluginName;

	/**
	 * Controller Name
	 *
	 * @var string
	 */
	protected $controllerName;

	/**
	 * Action Name
	 *
	 * @var string
	 */
	protected $actionName;

	/**
	 * Vendor Name
	 *
	 * @var string
	 */
	protected $vendorName;

	/**
	 * Arguments
	 *
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * Page UID
	 *
	 * @var int
	 */
	protected $pageUid;

	/**
	 * Object Manager
	 * 
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Configuration Loader
	 *
	 * @var \MageDeveloper\Dataviewer\eID\ConfigurationLoader
	 * @inject
	 */
	protected $configurationLoader;

	/**
	 * Constructor
	 *
	 * @return Dispatcher
	 */
	public function __construct()
	{
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

		// Initialize the TYPO3 Frontend via ConfigurationLoader
		$this->configurationLoader	= $this->objectManager->get(\MageDeveloper\Dataviewer\eID\ConfigurationLoader::class);

		$arguments = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST("arguments");
		
		if (isset($arguments["pid"]) && $arguments["pid"] > 0)
			$this->configurationLoader->setPageId((int)$arguments["pid"]);

		$this->configurationLoader->initGlobals();
	}

	/**
	 * Initialized and dispatches actions
	 * Call this function if you want to use this dispatcher "standalone"
	 * 
	 * @return void
	 */
	public function initAndDispatch()
	{
		$this->initCallArguments()->dispatch();
	}

	/**
	 * Initializes TSFE
	 *
	 * @return \MageDeveloper\Dataviewer\eID\Dispatcher
	 */
	public function initTSFE()
	{
		$this->configurationLoader->initGlobals();
		return $this;
	}

	/**
	 * Called by ajax
	 * Builds extbase context and returns the response
	 *
	 * @return string
	 */
	public function dispatch()
	{
		$configuration = [
			"extensionName" => $this->extensionName,
			"pluginName"	=> $this->pluginName,
		];	

		/* @var \TYPO3\CMS\Extbase\Core\Bootstrap $bootstrap */
		/* @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $bootstrap->cObj */
		$bootstrap = $this->objectManager->get(\TYPO3\CMS\Extbase\Core\Bootstrap::class);
		$bootstrap->initialize($configuration);
		$bootstrap->cObj = $this->objectManager->get(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);

		/* @var \TYPO3\CMS\Extbase\Mvc\Web\Request $request */
		/* @var \TYPO3\CMS\Extbase\Mvc\Web\Response $response */
		$request 	= $this->buildRequest();
		$response 	= $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Response::class);

		/* @var \TYPO3\CMS\Extbase\Mvc\Dispatcher $dispatcher */
		$dispatcher = $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Dispatcher::class);
		$dispatcher->dispatch($request, $response);

		return $response->getContent();
	}

	/**
	 * Prepare all call arguments that are valid
	 *
	 * @return \MageDeveloper\Dataviewer\eID\Dispatcher
	 */
	public function initCallArguments()
	{
		$request = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST();
		
		$this->setRequestedArguments($request);

		$this->extensionName 	= $this->requestArguments["extensionName"];
		$this->pluginName		= $this->requestArguments["pluginName"];
		$this->controllerName	= $this->requestArguments["controllerName"];
		$this->actionName		= $this->requestArguments["actionName"];
		$this->vendorName 		= $this->requestArguments["vendorName"];

		$this->arguments		= $this->requestArguments["arguments"];

		if (!is_array($this->arguments))
			$this->arguments = [];

		return $this;
	}
	
	/**
	 * Sets the requested arguments
	 *
	 * @param array $request Request
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidRequestTypeException
	 * @return void
	 */
	public function setRequestedArguments($request)
	{
		parse_str($request["arguments"], $arguments);
		$request["arguments"] = $arguments;
	
		$validArguments = [
			"extensionName",
			"pluginName",
			"controllerName",
			"actionName",
			"vendorName",
			"arguments",
		];
		
		foreach ($validArguments as $argument)
		{
			if ($request[$argument])
				$this->requestArguments[$argument] 	= $request[$argument];
				
		}
		
	}

	/**
	 * Builds a request instance
	 *
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	public function buildRequest()
	{
		/* @var \TYPO3\CMS\Extbase\Mvc\Web\Request $request */
		$request = $this->objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Request::class);

		$request->setPluginName($this->pluginName);
		$request->setControllerExtensionName($this->extensionName);
		$request->setControllerName($this->controllerName);
		$request->setControllerActionName($this->actionName);
		$request->setControllerVendorName($this->vendorName);
		$request->setArguments($this->arguments);

		return $request;
	}

}
