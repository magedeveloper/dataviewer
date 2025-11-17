<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Form\Value;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Repository\VariableRepository;
use Aix\Dataviewer\Service\FlexForm\FlexFormService;
use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;

abstract class AbstractValue
{
    /**
     * @var VariableRepository
     */
    protected $variableRepository;

    /**
     * @var PluginSettingsService
     */
    protected $pluginSettingsService;

    /**
     * @var FlexFormService
     */
    protected $flexFormService;

    /**
     * @var AbstractRecordModel
     */
    protected $record;

    /**
     * @var Datatype
     */
    protected $datatype;

    /**
     * @var Field
     */
    protected $field;

    /**
     * Content Object Data
     * @var array
     */
    protected $cObj = [];

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @param VariableRepository $variableRepository
     */
    public function injectVariableRepository(VariableRepository $variableRepository)
    {
        $this->variableRepository = $variableRepository;
    }

    /**
     * @param PluginSettingsService $pluginSettingsService
     */
    public function injectPluginSettingsService(PluginSettingsService $pluginSettingsService)
    {
        $this->pluginSettingsService = $pluginSettingsService;
    }

    /**
     * @param FlexFormService $flexFormService
     */
    public function injectFlexFormService(FlexFormService $flexFormService)
    {
        $this->flexFormService = $flexFormService;
    }

    /**
     * @return AbstractRecordModel
     */
    public function getRecord(): ?AbstractRecordModel
    {
        return $this->record;
    }

    /**
     * @param AbstractRecordModel $record
     */
    public function setRecord(AbstractRecordModel $record): void
    {
        $this->record = $record;
    }

    /**
     * @return Datatype
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
     * @return Field
     */
    public function getField(): ?Field
    {
        return $this->field;
    }

    /**
     * @param Field $field
     */
    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    /**
     * @return array
     */
    public function getCObj(): array
    {
        return $this->cObj;
    }

    /**
     * @param array $cObj
     */
    public function setCObj(array $cObj = []): void
    {
        $this->cObj = $cObj;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables = []): void
    {
        $this->variables = $variables;
    }

}