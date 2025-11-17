<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class DynamicInput extends Flex
{
    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['ds']['default'] = $this->getField()->getConfig("type");
        $tca['config']['appearance'] = [
            "collapseAll" => 1,
        ];
        return $this->mergeConfigurationToTca($tca);
    }

}
