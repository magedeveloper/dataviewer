<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Radio extends Select implements Tca\FieldInterface
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
        if (!$this->getField()->getIsObjectStorage() && strpos($this->getField()->getFrontendType(), '\\') !== false) {
            return $this->getField()->getFrontendType();
        }

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
                'type' => 'radio',
                'items' => $this->getItems(),
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
        // Is maybe too much here, but we let this do the parent routine
        $tca = parent::mergeConfigurationToTca($tca);

        // itemsProcFunc
        if ($itemsProcFunc = $this->getField()->getConfig('itemsProcFunc')) {
            $tca['config']['itemsProcFunc'] = $itemsProcFunc;
        }

        return $tca;
    }
}
