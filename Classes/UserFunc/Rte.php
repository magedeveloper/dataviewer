<?php
declare(strict_types=1);

namespace Aix\Dataviewer\UserFunc;

class Rte
{
	/**
	 * Populate fields
	 *
	 * @param array $config Configuration Array
	 * @param mixed $parentObject Parent Object
	 * @return void
	 */
	public function populateRteConfigurationPresets(array &$config, &$parentObject): void
	{
		$options = [];

		$rteConfigurations = $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets'];
		$rteConfigurationIds = array_keys($rteConfigurations);

		foreach ($rteConfigurationIds as $_configId) {
            $options[] = [
                'label' => $_configId,
                'value' => $_configId
            ];
        }

		$config["items"] = $options;
	}
}
