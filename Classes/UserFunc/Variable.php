<?php
namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Variable
{
    /**
     * FlexForm Service
     * @var FlexFormService
     */
    protected $flexFormService;

    /**
     * Field Repository
     *
     * @var \Aix\Dataviewer\Domain\Repository\VariableRepository
     */
    protected $variableRepository;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->variableRepository = GeneralUtility::makeInstance(\Aix\Dataviewer\Domain\Repository\VariableRepository::class);
    }

    /**
     * Populate variables
     * @param array $config Configuration Array
     * @param array $parentObject Parent Object
     * @return void
     */
    public function populateOverrideVariables(array &$config, &$parentObject): void
    {
        $types = [
            \Aix\Dataviewer\Domain\Model\Variable::VARIBALE_TYPE_TYPOSCRIPT,
            \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_TYPOSCRIPT_VAR,
            \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_GET,
            \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_POST,
            \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_FIXED,
        ];

        $variables = $this->variableRepository->findByTypes($types);

        $options = [];
        foreach($variables as $_variable) {
            /* @var \Aix\Dataviewer\Domain\Model\Variable $_variable */
            $label = "[u:{$_variable->getUid()}|p:{$_variable->getPid()}] " . LocalizationUtility::translate("variable_type.{$_variable->getType()}") . " {{$_variable->getVariableName()}}";
            $value = $_variable->getVariableName();
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        $config['items'] = array_merge($config['items'], $options);
    }

    /**
     * Populate get/post variables
     * @param array $config
     * @param $parentObject
     */
    public function populateGetPostVariables(array &$config, &$parentObject): void
    {
        $row = (isset($config['flexParentDatabaseRow']))?$config['flexParentDatabaseRow']:$config['row'];
        $flex = $this->flexFormService->walkFlexFormNode($row['pi_flexform'], 'vDEF');
        $flex = $this->flexFormService->walkFlexFormNode($flex, 'lDEF');

        $variableIds = [];
        if(isset($flex['data']['template_settings']['settings']['variables'])) {
            $vs = $flex['data']['template_settings']['settings']['variables'];
            if(is_array($vs)) {
                $variableIds = $vs;
            } else {
                $variableIds = GeneralUtility::intExplode(',', $vs, true);
            }
        }

        $variables = [];
        if(!empty($variableIds)) {
            $variables = $this->variableRepository->findByUids($variableIds);
        }

        /* @var \Aix\Dataviewer\Domain\Model\Variable $_variable */
        $options = [];
        foreach ($variables as $_variable) {
            if($_variable->getType() == \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_GET ||
                $_variable->getType() == \Aix\Dataviewer\Domain\Model\Variable::VARIABLE_TYPE_POST) {

                $options[] = [
                    'label' => '{'.$_variable->getVariableName().'}',
                    'value' => $_variable->getUid()
                ];
            }
        }

        $config['items'] = array_merge($config['items'], $options);
    }
}
