<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class DateTime extends Date
{
    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['format'] = 'datetime';
        return $this->mergeConfigurationToTca($tca);
    }
}
