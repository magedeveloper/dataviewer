<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class Page extends Group
{
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
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
            ],
        ];

        return $this->mergeConfigurationToTca($tca);
    }
}
