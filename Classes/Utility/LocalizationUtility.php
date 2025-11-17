<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

class LocalizationUtility
{
	/**
	 * Translates
	 *
	 * @param string $key
	 * @param array $arguments
	 * @return string
	 */
	public static function translate(string $key, array $arguments = null): string
	{
		if (!is_array($arguments)) {
			$arguments = [$arguments];
        }

		return (string)\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, \Aix\Dataviewer\Configuration\ExtensionConfiguration::EXTENSION_KEY, $arguments);
	}
}
