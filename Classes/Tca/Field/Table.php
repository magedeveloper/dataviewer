<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class Table extends Textarea
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
        return 'mediumtext DEFAULT \'\' NOT NULL';
    }

    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['renderType'] = 'textTable';
        return $this->mergeConfigurationToTca($tca);
    }
}
