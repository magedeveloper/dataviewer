<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use Aix\Dataviewer\Tca;

class Rte extends Textarea implements Tca\FieldInterface
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
        return 'text';
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
                "enableRichtext" => true,
                "richtextConfiguration" => $this->getField()->getConfig("richtextConfiguration"),

            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }
}
