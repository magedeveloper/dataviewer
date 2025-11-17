<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassUtility
{
	/**
	 * Generates a class name by a table name
	 *
	 * @param string $string
	 * @return string
	 */
	public static function underscoredToUpperCamelCaseUnderscored(string $string): string
	{
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', GeneralUtility::strtolower($string))));
		$upperCamelCase = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $upperCamelCase);
		return $upperCamelCase;
	}
}