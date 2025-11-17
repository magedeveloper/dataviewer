<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class ColorPicker extends Input
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
        return "varchar(7) DEFAULT '' NOT NULL";
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['type'] = 'color';
        return $this->mergeConfigurationToTca($tca);
    }
}
