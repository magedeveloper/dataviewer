<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Model\FieldValue;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Domain\Repository\FieldRepository;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Utility\TypoScriptUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;

abstract class AbstractField
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var FieldRepository
     */
    protected $fieldRepository;

    /**
     * @var TypoScriptUtility
     */
    protected $typoScriptUtility;

    /**
     * @var null|Field
     */
    protected $field = null;

    /**
     * @var null|Datatype
     */
    protected $datatype;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->datatypeRepository   = GeneralUtility::makeInstance(DatatypeRepository::class);
        $this->fieldRepository		= GeneralUtility::makeInstance(FieldRepository::class);
        $this->typoScriptUtility    = GeneralUtility::makeInstance(TypoScriptUtility::class);
    }

    /**
     * @return StandaloneView
     * @throws Exception
     */
    public function getView(): StandaloneView
    {
        return GeneralUtility::makeInstance(StandaloneView::class);
    }

    /**
     * Gets the field
     *
     * @return Field|null
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * Sets the field
     *
     * @param Field $field
     * @return void
     */
    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    /**
     * @return Datatype|null
     */
    public function getDatatype(): ?Datatype
    {
        return $this->datatype;
    }

    /**
     * @param Datatype $datatype
     */
    public function setDatatype(Datatype $datatype): void
    {
        $this->datatype = $datatype;
    }

    /**
     * Merges the field configuration to the tca
     *
     * ADDS DEFAULT TCA CONFIGURATION VALUES TO ALL FIELDS
     *
     * @param array $tca
     * @return array
     * @throws Exception
     */
    public function mergeConfigurationToTca(array $tca): array
    {
        if ($this->getField()->getRequestUpdate()) {
            $tca["onChange"] = "reload";
        }

        if($fieldEval = $this->getField()->getEval()) {
            $prevEval = GeneralUtility::trimExplode(',', ($tca['config']['eval']??''), true);
            $fieldEval = GeneralUtility::trimExplode(',', $fieldEval, true);
            $eval = array_merge($prevEval, $fieldEval);
            $tca['config']['eval'] = implode(',',$eval);
        }

        // displayCond
        if ($displayCond = $this->getField()->getDisplayCond()) {
            $tca['displayCond'] = $displayCond;
        }

        // Setting defaults
        $defaultValue = $this->getDefaultValue();
        $view = $this->getView();
        $view->assignMultiple($this->_getDefaultViewVariables());

        if(is_array($defaultValue)) {
            if(!empty($defaultValue)) {
                foreach ($defaultValue as $i => $_dV) {
                    $defaultValue[$i] = $view->renderSource($_dV);
                }
                $tca['config']['default'] = $defaultValue;
            }
        } else if (is_string($defaultValue) && $defaultValue != '') {
            $tca['config']['default'] = $view->renderSource($defaultValue);
        }

        // description
        if($description = $this->getField()->getDescription()) {
            $tca['description'] = $description;
        }

        // exclude
        if($this->getField()->getL10nExclude() == true) {
            $tca['l10n_mode'] = 'exclude';
        }

        // nullable
        if ($nullable = $this->getField()->getConfig('nullable')) {
            $tca['config']['nullable'] = true;
        }

        // required
        if ($required = $this->getField()->getConfig('required')) {
            $tca['config']['required'] = true;
        }

        return $tca;
    }

    /**
     * Gets a tca message array with
     * calling a userfunc for displaying
     * an alert error message with given
     * string
     *
     * @param string $message
     * @param string $severity
     * @return array
     */
    public function getMessageTca(string $label, string $message, string $severity = 'danger'): array
    {
        return [
            'exclude' => 1,
            'label' => $label,
            'config' => [
                'type' => 'dvuser',
                'userFunc' => 'Aix\\Dataviewer\\UserFunc\\Text->displayMessage',
                'parameters' => [
                    'message' => $message,
                    'severity' => $severity,
                ],
            ],
        ];
    }

    /**
     * Gets the values for the field
     * @return mixed
     */
    public function getDefaultValue()
    {
        $hasDefault = false;
        // We need to retrieve the default value content for the field
        $fieldValues = $this->getField()->getFieldValues();
        $values = [];
        foreach ($fieldValues as $_fieldValue) {
            $default = $this->_getDefaultValue($_fieldValue, 0, true);
            if (!is_null($default)) {
                $hasDefault = true;
                if (is_array($default)) {
                    $values = array_merge($values, $default);
                } else {
                    $values[] = $default;
                }
            }
        }

        if(!$hasDefault) {
            return null;
        }

        return $values;
    }


    /**
     * Determines the value of the field
     *
     * @param FieldValue $fieldValue
     * @param int $position Position of the value
     * @param bool $returnFull Return full content
     * @return mixed
     */
    protected function _getDefaultValue(FieldValue $fieldValue, int $position = 0, bool $returnFull = false)
    {
        // If it pretends to be empty, we return null
        if ($fieldValue->getPretendsEmpty()) {
            return null;
        }

        $defaultValue = null;

        if ($fieldValue->isDefault()) {
            switch ($fieldValue->getType()) {
                case FieldValue::TYPE_TYPOSCRIPT:
                    $typoscript = $fieldValue->getValueContent();
                    $tsValue = $this->typoScriptUtility->getTypoScriptValue($typoscript);
                    $defaultValue = $tsValue;
                    break;
                case FieldValue::TYPE_DATABASE:
                    $values = $this->fieldRepository->findEntriesForFieldValue($fieldValue);
                    $valueArr = [];
                    foreach ($values as $_value) {
                        $valueArr[] = implode(', ', array_values($_value));
                    }

                    $defaultValue = $valueArr;
                    break;
                default:
                    // Check if we can explode with pipe '|' the value to retrieve its correct value
                    $exploded = explode('|', $fieldValue->getValueContent());
                    $value = $exploded[0];
                    if (count($exploded) > 1) {
                        $value = $exploded[1];
                    }
                    $defaultValue = [$value];
                    break;
            }
        }

        if ($returnFull) {
            return $defaultValue;
        }

        if (is_array($defaultValue) && !is_null($position)) {
            return $defaultValue[$position];
        } else if (is_array($defaultValue) && is_null($position)) {
            return reset($defaultValue);
        } else if (is_string($defaultValue) && strlen($defaultValue)) {
            return $defaultValue;
        }

        return null;
    }

    /**
     * Processes a string and checks for label and
     * value, and returns an array with evaluated
     * label and value
     * return [
     *  'label' => '',
     *  'value' => '',
     * ]
     *
     * @param string $string
     * @return array
     */
    protected function _processLabelValueString(string $string): array
    {
        $exploded = explode('|', $string);

        $label = $exploded[0];
        $value = $exploded[0];
        if (count($exploded) > 1) {
            $value = $exploded[1];
        }

        return [
            'label' => $label,
            'value' => $value,
        ];
    }

    /**
     * Gets an array with items from fieldvalues
     * even when they are from database
     * So this will contain all possible values,
     * that a field can have
     *
     * @return array
     */
    public function getItems(): array
    {
        $fieldValues = $this->getField()->getFieldValues();
        $items = [];
        foreach ($fieldValues as $_fieldValue) {
            /* @var \Aix\Dataviewer\Domain\Model\FieldValue $_fieldValue */
            $itemsFromFieldValue = $this->getFieldValueItems($_fieldValue);
            foreach ($itemsFromFieldValue as $_it) {
                $items[] = $_it;
            }
        }

        return $items;
    }

    /**
     * Gets all items that a fieldvalue
     * contains. It generates all values and
     * puts them together into an array.
     *
     * @param FieldValue $fieldValue
     * @return array
     */
    public function getFieldValueItems(FieldValue $fieldValue): array
    {
        $items = [];

        /* @var FieldValue $fieldValue */
        switch ($fieldValue->getType())
        {
            case FieldValue::TYPE_FIXED_VALUE:
                $labelValueArr = $this->_processLabelValueString($fieldValue->getValueContent());
                if ($fieldValue->getPretendsEmpty()) {
                    $labelValueArr['value'] = null;
                }

                $items[] = [
                    'label' => $labelValueArr['label'],
                    'value' => $labelValueArr['value']
                ];
                break;
            case FieldValue::TYPE_DATABASE:
                $databaseContent = $this->fieldRepository->findEntriesForFieldValue($fieldValue);
                $fluidCode = $fieldValue->getValueContent();
                $view = $this->getView();
                $view->setTemplateSource($fluidCode);
                foreach ($databaseContent as $_values) {
                    if (is_array($_values)) {
                        $view->assignMultiple($_values);
                    }
                    $value = $view->render();
                    $labelValueArr = $this->_processLabelValueString($value);

                    if ($fieldValue->getPretendsEmpty()) {
                        $labelValueArr['value'] = null;
                    }

                    $items[] = [
                        'label' => $labelValueArr['label'],
                        'value' => $labelValueArr['value']
                    ];
                }
                break;
            case FieldValue::TYPE_TYPOSCRIPT:
                $typoScript = $fieldValue->getValueContent();
                $value = $this->typoScriptUtility->getTypoScriptValue($typoScript);
                $labelValueArr = $this->_processLabelValueString($value);

                if ($fieldValue->getPretendsEmpty()) {
                    $labelValueArr['value'] = null;
                }

                $items[] = [
                    'label' => $labelValueArr['label'],
                    'value' => $labelValueArr['value']
                ];
                break;
            default:
                break;

            /*
            case FieldValue::TYPE_FIELDVALUES:
                $field = $fieldValue->getFieldContent();
                if ($field instanceof FieldModel) {
                    $fieldItems = $this->getAllFieldItems($field);
                    if (!empty($fieldItems)) {
                        foreach ($fieldItems as $value) {
                            $items[] = [$value, $value];
                        }
                    }
                }
                break;
            */
        }

        return $items;
    }

    /**
     * Gets the default view variables for rendering
     * values with fluid support
     *
     * @return array
     */
    protected function _getDefaultViewVariables(): array
    {
        return [
            'field' => $this->getField(),
            'datatype' => $this->getDatatype(),
        ];
    }

}
