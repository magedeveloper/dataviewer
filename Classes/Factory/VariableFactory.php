<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Factory;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Variable;
use Aix\Dataviewer\Domain\Repository\AbstractRepository;
use Aix\Dataviewer\Domain\Repository\FieldRepository;
use Aix\Dataviewer\Domain\Repository\VariableRepository;
use Aix\Dataviewer\Service\Auth\AuthenticationService;
use Aix\Dataviewer\Service\Backend\BackendAccessService;
use Aix\Dataviewer\Service\Fluid\ConditionService;
use Aix\Dataviewer\Service\Fluid\FluidRenderService;
use Aix\Dataviewer\Service\Session\SessionServiceContainer;
use Aix\Dataviewer\Utility\GetPostUtility;
use Aix\Dataviewer\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;

class VariableFactory implements SingletonInterface
{
    /**
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var VariableRepository
     */
    protected $variableRepository;

    /**
     * @var TypoScriptUtility
     */
    protected $typoScriptUtility;

    /**
     * @var SessionServiceContainer
     */
    protected $sessionServiceContainer;

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var BackendAccessService
     */
    protected $backendAccessService;

    /**
     * @var ConditionService
     */
    protected $conditionService;

    /**
     * @var FluidRenderService
     */
    protected $fluidRenderService;

    /**
     * Denied variable names
     * @var array
     */
    protected $deniedVariableNames = [
        'record',
        'records',
        'part',
        'cObj',
        'settings',
    ];

    /**
     * @param FieldRepository $fieldRepository
     */
    public function injectFieldRepository(FieldRepository $fieldRepository)
    {
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * @param VariableRepository $variableRepository
     */
    public function injectVariableRepository(VariableRepository $variableRepository)
    {
        $this->variableRepository = $variableRepository;
    }

    /**
     * @param TypoScriptUtility $typoScriptUtility
     */
    public function injectTypoScriptUtility(TypoScriptUtility $typoScriptUtility): void
    {
        $this->typoScriptUtility = $typoScriptUtility;
    }

    /**
     * @param SessionServiceContainer $sessionServiceContainer
     */
    public function injectSessionServiceContainer(SessionServiceContainer $sessionServiceContainer)
    {
        $this->sessionServiceContainer = $sessionServiceContainer;
    }

    /**
     * @param AuthenticationService $authenticationService
     */
    public function injectAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param BackendAccessService $backendAccessService
     */
    public function injectBackendAccessService(BackendAccessService $backendAccessService)
    {
        $this->backendAccessService = $backendAccessService;
    }

    /**
     * @param ConditionService $conditionService
     */
    public function injectConditionService(ConditionService $conditionService)
    {
        $this->conditionService = $conditionService;
    }

    /**
     * @param FluidRenderService $fluidRenderService
     */
    public function injectFluidRenderService(FluidRenderService $fluidRenderService)
    {
        $this->fluidRenderService = $fluidRenderService;
    }

    /**
     * Prepares a variable
     *
     * @param Variable $variable
     * @return mixed
     * @throws InvalidArgumentNameException
     */
    public function prepareVariableValue(Variable $variable)
    {
        $name = $variable->getVariableName();

        // Check if variable name is valid
        if (in_array($name, $this->deniedVariableNames)) {
            throw new InvalidArgumentNameException("Variable must not be named '{$name}'! Denied Variable Names: " . implode(', ', $this->deniedVariableNames));
        }

        $type = $variable->getType();

        switch ($type) {
            case Variable::VARIBALE_TYPE_TYPOSCRIPT:
                $value = $variable->getVariableValue();
                $rendered = $this->typoScriptUtility->getTypoScriptValue($value);
                return $rendered;
            case Variable::VARIABLE_TYPE_TYPOSCRIPT_VAR:
                $value = "10 < {$name}";
                $rendered = $this->typoScriptUtility->getTypoScriptValue($value);
                return $rendered;
            case Variable::VARIABLE_TYPE_GET:
            case Variable::VARIABLE_TYPE_POST:
            case Variable::VARIABLE_TYPE_GET_POST:
                $parameterName = ($variable->getParameterName() != '')?$variable->getParameterName():$variable->getVariableName();
                $value = GetPostUtility::getEnvironmentalParameterValue($parameterName, (string)$type);
                if ($value) {
                    if (is_array($value)) {
                        $value = array_map(function($v) {
                            return GetPostUtility::secureVariableGet($v);
                        }, $value);
                    } else {
                        $value = GetPostUtility::secureVariableGet((string)$value);
                    }

                    if($variable->hasValueSwitch()) {
                        $value = $this->_getValueByValueSwitch($value, $variable);
                    } else {
                        $value = $this->_getValueByValueSettings($value, $variable);
                    }

                } else {
                    $value = $this->_getValueByValueSwitch($value, $variable);
                }

                $value = $variable->castType($value);
                return $value;
            case Variable::VARIABLE_TYPE_DATABASE:
                $fields = GeneralUtility::trimExplode(",", $variable->getColumnName());
                $table = $variable->getTableContent();
                $where = $variable->getWhereClause();
                return $this->fieldRepository->rawQuery($fields, $table, $where);
            case Variable::VARIABLE_TYPE_FRONTEND_USER:
                $feUser = null;
                if ($this->authenticationService->isLoggedIn()) {
                    $feUser = $this->authenticationService->getFrontendUserAuthentication();
                }
                return $feUser;
            case Variable::VARIABLE_TYPE_SERVER:
                $env = $variable->getServer();
                return $_SERVER[$env];
            case Variable::VARIABLE_TYPE_DYNAMIC_RECORD:
                /* @var Datatype $datatype */
                $datatype = $variable->getDatatype();
                if ($datatype instanceof Datatype) {
                    $repository = $datatype->getRepository();
                    if ($repository instanceof AbstractRepository) {
                        $params = GeneralUtility::_GP('tx_dataviewer_dynamic');
                        if (is_array($params) && array_key_exists('record', $params)) {
                            $uid = (int)$params['record'];

                            return $repository->findByUid($uid);
                        }
                    }
                }
                return null;
            case Variable::VARIABLE_TYPE_USER_SESSION:
                $sessionKey = $variable->getSessionKey();
                $this->sessionServiceContainer->getSessionService()->setPrefixKey($sessionKey);
                return $this->sessionServiceContainer->getSessionService()->getData($name);
            case Variable::VARIABLE_TYPE_PAGE:
                /* @var PageRepository $pageRepository */
                $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
                return $pageRepository->getPage($variable->getPage());
            case Variable::VARIABLE_TYPE_USERFUNC:
                $userFunc = $variable->getUserFunc();

                $params = [
                    "parameters" => [
                        "variable" => $variable,
                    ],
                ];

                $userFuncResult = GeneralUtility::callUserFunction($userFunc, $params, $this);
                return $userFuncResult;
            case Variable::VARIABLE_TYPE_BACKEND_USER:
                $beUser = $this->backendAccessService->getBackendUser();
                return $beUser;
            case Variable::VARIABLE_TYPE_LANGUAGE_UID:
                $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
                $languageUid = $languageAspect->getId();
                return $languageUid;
            case Variable::VARIABLE_TYPE_SESSION_CONTAINER:
                // New session service container, with targetUid 0 to work like global
                $this->sessionServiceContainer->setTargetUid(0);
                return $this->sessionServiceContainer;
            case Variable::VARIABLE_TYPE_EXTENSION_CONFIG:
                $extension = $variable->getExtConf();
                if(ExtensionManagementUtility::isLoaded($extension)) {
                    return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get($extension);
                }
                return null;
            case Variable::VARIABLE_TYPE_TYPOSCRIPT_SETTINGS:
                /* @var ConfigurationManager $configurationManager */
                $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
                $typoscript = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
                $typoscript = GeneralUtility::removeDotsFromTS($typoscript);
                $path = $variable->getTyposcriptPath();
                try {
                    $value = ArrayUtility::getValueByPath($typoscript,$path,'.');
                    return $value;
                } catch (\InvalidArgumentException $ie) {
                } catch (\Exception $e) { }
                return null;
            case Variable::VARIABLE_TYPE_FIXED:
            default:
                return $variable->getVariableValue();
        }
    }

    /**
     * @param mixed $value
     * @param Variable $variable
     * @param string $valueFluidField
     * @param string $conditionField
     * @return mixed
     */
    protected function _getValueByValueSwitch($value, Variable $variable, $fluidValueField = 'fluid_code', $conditionField = 'condition')
    {
        $variableName = ($variable->getParameterName() != '')?$variable->getParameterName(): $variable->getVariableName();

        $valueSwitch = $variable->getValueSwitch();
        $envVariables = [
            $variableName => $value,
            'variable' => $variable,
        ];

        foreach($valueSwitch as $_switch) {
            // Check condition
            $condition = $_switch[$conditionField];
            if($this->conditionService->isValid($condition,$envVariables)) {
                $fluidCode = $_switch[$fluidValueField];
                return $this->fluidRenderService->renderFluid($fluidCode, $envVariables);
            }
        }

        return $value;
    }

    /**
     * @param Variable $variable
     * @return mixed
     */
    protected function _getValueByValueSettings($value, Variable $variable)
    {
        /* Check for allowed values in list */
        $allowedValues = $variable->getAllowedValues();
        if(!empty($allowedValues)) {
            if(is_array($value)) {
                $vA = [];
                foreach($value as $_k=>$_v) {
                    if(in_array($_v,$allowedValues)) {
                        $vA[$_k] = $_v;
                    }
                }
                $value = $vA;
            } else {
                if(!in_array($value, $allowedValues)) {
                    $value = null;
                }
            }

        }

        /* Regex value */
        $regEx = (string)$variable->getRegex();
        if($regEx != '') {
            $result = preg_match($regEx, $value);
            if ($result === 0 || $result === false) {
                $value = null;
            }
        }

        return $value;
    }
}
