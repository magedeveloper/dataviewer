<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Textarea extends Tca\AbstractField implements Tca\FieldInterface
{
    /**
     * Gets the sql create statement
     *
     * @return string
     */
    public function getSqlCreateStatement(): string
    {
        if ($this->getField()->getDatabaseType() != '') {
            return $this->getField()->getDatabaseType();
        }
        return 'mediumtext';
    }

    /**
     * @return string
     */
    public function getVariableType(): string
    {
        return 'string';
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => [
                'type' => 'text',
                'size' => 30,
                'wrap' => $this->getField()->getConfig('wrap'),
                'cols' => $this->getField()->getConfig('cols'),
                'rows' => $this->getField()->getConfig('rows'),
            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }

    /**
     * Merges the field configuration to the tca
     *
     * @param array $tca
     * @return array
     */
    public function mergeConfigurationToTca(array $tca): array
    {
        $tca = parent::mergeConfigurationToTca($tca);

        // max
        if ($max = $this->getField()->getConfig('max')) {
            $tca['config']['max'] = $max;
        }

        // is_in
        if ($isIn = $this->getField()->hasEval('is_in')) {
            $tca['config']['is_in'] = $isIn;
        }

        // range
        if ($rangeLower = $this->getField()->hasEval('range')) {
            $tca['config']['range'] = ['lower' => $this->getField()->getConfig('range_lower'),
                'upper' => $this->getField()->getConfig('range_upper')];
        }

        // renderType
        if ($renderType = $this->getField()->getConfig('renderType')) {
            $tca['config']['renderType'] = $renderType;

            if ($renderType == 't3editor') {
                $tca['config']['format'] = $this->getField()->getConfig('format')??'html';
            }
        }

        // placeholder
        if($placeholder = $this->getField()->getConfig('placeholder')) {
            $tca['config']['placeholder'] = $placeholder;
        }

        // autocomplete
        if($autocomplete = $this->getField()->getConfig('autocomplete')) {
            $tca['config']['autocomplete'] = true;
        }

        return $tca;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        $value = parent::getDefaultValue();

        if (is_array($value)) {
            return implode(', ', $value);
        }

        return '';
    }
}
