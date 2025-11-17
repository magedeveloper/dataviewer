<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class Input extends Textarea
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
        return 'varchar(255) DEFAULT \'\' NOT NULL';
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
                'type' => ($this->getField()->getConfig('type')!='')?$this->getField()->getConfig('type'):'input',
                'size' => $this->getField()->getConfig('size') ?? 300,
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

        // renderType
        if ($this->getField()->getConfig('valuePicker')) {
            $items = $this->getItems();
            foreach($items as $_item) {
                $tca['config']['valuePicker']['items'][] = [$_item['label'],$_item['value']];
            }
        }

        return $tca;
    }
}
