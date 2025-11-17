<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Flex extends Tca\AbstractField implements Tca\FieldInterface
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
        $defaultValue = $this->getDefaultValue();
        if(is_array($defaultValue)) {
            $defaultValue = implode('', $defaultValue);
        }

        $tca = [
            'exclude' => (int)$this->getField()->isExclude(),
            'label' => $this->getField()->getFrontendLabel(),
            'config' => [
                'type' => 'flex',
                'ds' => [
                    'default' => $defaultValue,
                ],
            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }

    /**
     * Gets the values for the field
     * @return string
     */
    public function getDefaultValue()
    {
        $fieldValues = $this->getField()->getFieldValues();
        $values = [];
        foreach ($fieldValues as $_fieldValue) {
            $default = $this->_getDefaultValue($_fieldValue, 0, true);
            if (!is_null($default)) {
                if (is_array($default)) {
                    $values = array_merge($values, $default);
                } else {
                    $values[] = $default;
                }
            }
        }

        return (string)implode('', $values);
    }

}
