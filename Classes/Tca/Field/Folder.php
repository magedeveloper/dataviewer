<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

class Folder extends Group
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
                'type' => 'folder',
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

        // default
        if ($default = $this->getField()->getConfig('default')) {
            $tca['config']['elementBrowserEntryPoints'] = [
                '_default' => $default,
            ];
        }

        return $tca;
    }
}
