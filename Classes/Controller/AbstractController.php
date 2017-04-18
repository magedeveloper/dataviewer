<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Variable;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

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
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	/**
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $pluginSettingsService;

	/**
	 * persistenceManager
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * Signal/Slot Dispatcher
	 * 
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	/**
	 * TypoScript Utility
	 *
	 * @var \MageDeveloper\Dataviewer\Utility\TypoScriptUtility
	 * @inject
	 */
	protected $typoScriptUtility;

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
	 * Authentication Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Auth\AuthenticationService
	 * @inject
	 */
	protected $authenticationService;

	/**
	 * Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SessionService
	 * @inject
	 */
	protected $sessionService;

	/**
	 * Plugin Cache Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Cache\PluginCacheService
	 * @inject
	 */
	protected $pluginCacheService;
	
	/**
	 * Gets the extension name
	 * 
	 * @return string
	 */
	public function getExtensionName()
	{
		return $this->controllerContext->getRequest()->getControllerExtensionName();
	}
	
	/**
	 * Gets the extension key
	 * 
	 * @return string
	 */
	public function getExtensionKey()
	{
		return $this->controllerContext->getRequest()->getControllerExtensionKey();
	}
	
	/**
	 * Gets the plugin name
	 * 
	 * @return string
	 */
	public function getPluginName()
	{
		return $this->controllerContext->getRequest()->getPluginName();
	}

	/**
	 * Redirects to the current TYPO3 Page
	 *
	 * @param int|null $redirectPid Redirect Page Id
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 * @return void
	 */
	protected function _redirectToPid($redirectPid = null, array $arguments = array())
	{
		if (is_null($redirectPid))
		{
			$redirectPid = $this->configurationManager->getContentObject()->data["pid"];
			if (isset($GLOBALS["TSFE"]))
				$redirectPid = $GLOBALS["TSFE"]->id;
		}

		$this->uriBuilder->setTargetPageUid($redirectPid);
		$this->uriBuilder->setArguments($arguments);
		$this->redirectToURI($this->uriBuilder->build());

		exit();
	}

	/**
	 * Checks for targetUid
	 * 
	 * @return bool
	 */
	protected function _checkTargetUid()
	{
		$targetUid = $this->pluginSettingsService->getTargetContentUid();
		$pluginTargetUid = ($this->request->hasArgument("targetUid"))?$this->request->getArgument("targetUid"):null;

		// Prevent Plugin from storing search to other target uid's
		if(is_null($pluginTargetUid) || ($targetUid != $pluginTargetUid))
			return false;
			
		return true;
	}

	/**
	 * Injects and prepares variables
	 *
	 * @param array $ids Ids
	 * @return array
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 */
	public function prepareVariables(array $ids)
	{
		// Denied Variable Names
		$deniedVariableNames = [
			"record",
			"records",
			"part",
		];

		$variables = [];
		
		foreach($ids as $_id)
		{
			/* @var Variable $variable */
			$variable = $this->variableRepository->findByUid($_id, true);

			if($variable instanceof Variable)
			{
				$name = $variable->getVariableName();
				
				if(in_array($name, $deniedVariableNames))
					throw new InvalidArgumentNameException("Variable must not be named '".implode("' or '", $deniedVariableNames)."'!");

				$type = $variable->getType();

				switch($type)
				{
					case Variable::VARIBALE_TYPE_TYPOSCRIPT:
						$value = $variable->getVariableValue();
						$rendered = $this->typoScriptUtility->getTypoScriptValue($value);
						$variables[$name] = $rendered;
						break;
					case Variable::VARIABLE_TYPE_TYPOSCRIPT_VAR:
						$value = "10 < {$name}";
						$rendered = $this->typoScriptUtility->getTypoScriptValue($value);
						$variables[$name] = $rendered;
						break;
					case Variable::VARIABLE_TYPE_GET:
						$variables[$name] = null;
						if(isset($_GET[$name]))
						{
							$value = GeneralUtility::_GET($name);

                            if(is_array($value))
                            {
                                $value = array_map(function($v) {
                                    return GeneralUtility::removeXSS($v);
                                }, $value);
                            }
                            else
                            {
                                $value = GeneralUtility::removeXSS($value);
                            }

							$variables[$name] = $value;
						}
						break;
					case Variable::VARIABLE_TYPE_POST:
						$variables[$name] = null;
						if(isset($_GET[$name]))
						{
							$value = GeneralUtility::_POST($name);

                            if(is_array($value))
                            {
                                $value = array_map(function($v) {
                                    return GeneralUtility::removeXSS($v);
                                }, $value);
                            }
                            else
                            {
                                $value = GeneralUtility::removeXSS($value);
                            }
                            
							$variables[$name] = $value;
						}
						break;
					case Variable::VARIABLE_TYPE_RECORD:
						$variables[$name] = $variable->getRecord();
						break;
					case Variable::VARIABLE_TYPE_RECORD_FIELD:
						$field = $variable->getField();
						
						if(!is_null($field))
						{
							$record = $variable->getRecord();
							$value = $record->getValueByField($field);
							$variables[$name] = $value;
						}
						
						break;
					case Variable::VARIABLE_TYPE_DATABASE:
						$fields = GeneralUtility::trimExplode(",", $variable->getColumnName());
						$table = $variable->getTableContent();
						$where = $variable->getWhereClause();
						$result = $this->fieldRepository->rawQuery($fields, $table, $where);
						$variables[$name] = $result;
						break;
					case Variable::VARIABLE_TYPE_FRONTEND_USER:
						$feUser = null;
						if($this->authenticationService->isLoggedIn())
							$feUser = $this->authenticationService->getFrontendUser();
							
						$variables[$name] = $feUser;
						break;
					case Variable::VARIABLE_TYPE_SERVER:
						$env = $variable->getServer();
						$variables[$name] = $_SERVER[$env];
						break;
					case Variable::VARIABLE_TYPE_DYNAMIC_RECORD:
						$record = null;
						if ($this->request->hasArgument("record")) 
						{
							$recordUid = $this->request->getArgument("record");
							$record = $this->recordRepository->findByUid($recordUid, true);
							
							if(!$record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
								$record = $recordUid;
						}
						
						$variables[$name] = $record;
						break;
					case Variable::VARIABLE_TYPE_USER_SESSION:
						$sessionKey = $variable->getSessionKey();
						$this->sessionService->setPrefixKey($sessionKey);
						$variables[$name] = $this->sessionService->getData($name);
						break;
					case Variable::VARIABLE_TYPE_PAGE:
						/* @var \TYPO3\CMS\Frontend\Page\PageRepository $pageRepository */
						$pageRepository = $this->objectManager->get(\TYPO3\CMS\Frontend\Page\PageRepository::class);
						$variables[$name] = $pageRepository->getPage($variable->getPage());
						break;
					case Variable::VARIABLE_TYPE_USERFUNC:
						$userFunc = $variable->getUserFunc();

						$params = [
							"parameters" => [
								"variable" => $variable,
							],
						];

						if ($this->request->hasArgument("record"))
						{
							$recordUid = $this->request->getArgument("record");
							$record = $this->recordRepository->findByUid($recordUid, true);

							if(!$record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
								$record = $recordUid;
								
							$params["parameters"]["record"] = $record;	
						}
						//$variables[$name] =
						$userFuncResult = GeneralUtility::callUserFunction($userFunc, $params, $this);
						$variables[$name] = $userFuncResult;
						break;
					case Variable::VARIABLE_TYPE_FIXED:
					default:
						$variables[$name] = $variable->getVariableValue();
						break;
				}
			}
		}

		///////////////////////////////////////////////////////////////////////////
		// Signal-Slot for manipulating the variables that are added to the view //
		///////////////////////////////////////////////////////////////////////////
		$this->signalSlotDispatcher->dispatch(
			__CLASS__,
			"prepareVariables",
			[
				&$variables,
				&$this,
			]
		);

		// Assign the rendered variables to the current view
		return $variables;
	}

	/**
	 * Override of view initialization
	 * 
	 * @param ViewInterface $view
	 * @return void
	 */
	protected function initializeView(ViewInterface $view)
	{
		$cObj = $this->configurationManager->getContentObject();
			
		if ($cObj instanceof \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer)
			$this->view->assign("cObj", $cObj->data);

		$this->view->assign("baseUrl", $GLOBALS["TSFE"]->baseURL);

		parent::initializeView($view); 
	}

	/**
	 * Checks if the request has dataviewer arguments
	 * at all
	 * 
	 * @return bool
	 */
	protected function _hasDataviewerArguments()
	{
		$get = $_GET;
		$keys = array_keys($get);
		
		foreach($keys as $_key)
			if(strpos($_key, "tx_dataviewer") !== false)
				return true;
		
		return false;
	}
}
