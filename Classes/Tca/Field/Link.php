<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca\Field;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Link extends Input
{
    /**
     * Gets built tca array
     *
     * @return array
     */
    public function getTca(): array
    {
        $tca = parent::getTca();
        $tca['config']['type'] = 'link';

        // allowedTypes
        if ($allowedTypes = $this->getField()->getConfig('allowedTypes')) {
            $delimeters = [
                "|",
                "/",
                ".",
                "-",
                ";",
            ];

            $tca['config']['allowedTypes'] = str_replace($delimeters,',',$allowedTypes);
            $tca['config']['allowedTypes'] = GeneralUtility::trimExplode(',',$allowedTypes,true);
        }

        return $this->mergeConfigurationToTca($tca);
    }
}
