<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

class StringUtility
{
	/**
	 * Possible string dividers
	 *
	 * @var array
	 */
	protected static $dividers = [
		';',
		'/',
		'.',
		"\\",
		":",
		"-"
	];

	/**
	 * Gets exploded and trimmed values by
	 * a separated string
	 *
	 * @param string $string String to separate
	 * @param array $allowedSeparators List of allowed separators
	 * @return array
	 */
	public static function explodeSeparatedString(string $string, array $allowedSeparators = []): array
	{
		if (strlen($string)) {

			$dividers = self::$dividers;

			if (!empty($allowedSeparators)) {
				$dividers = $allowedSeparators;
            }

			// Check that the divider of the ids is comma separation
			foreach ($dividers as $divider) {
                $string = str_replace($divider, ',', $string);
			}

			$exploded = array_map('trim',explode(",",$string));

			return $exploded;
		}

		return [];
	}

	/**
	 * Creates a usage friendly code from a given string
	 *
	 * @param $string Entry string
	 * @return string
	 */
	public static function createCodeFromString(string $string): string
	{
		$attrName = utf8_decode($string);
		$attrCode = strtolower($attrName);
		$attrCode = str_replace(" ","", $attrCode);

		$removable_values = [
			";" 	=> 	"",
			":" 	=> 	"",
			"/" 	=> 	"",
			"\\" 	=> 	"",
			"\""	=>  "",
            "/"     =>  "",
			"'"		=>	"",
			":"		=>  "",
			"."		=>	"",
			"("		=>	"",
			")"		=>	"",
			"+"		=>	"",
			"&"		=>  "",
			"@"		=>  "at",
			"ö" 	=> 	"oe",
			"ä" 	=> 	"ae",
			"ü" 	=> 	"ue",
			"ß"		=>	"ss",
			utf8_decode("ö") 	=> 	"oe",
			utf8_decode("ä") 	=> 	"ae",
			utf8_decode("ü") 	=> 	"ue",
			utf8_decode("ß")	=>	"ss",
			utf8_encode("ö") 	=> 	"oe",
			utf8_encode("ä") 	=> 	"ae",
			utf8_encode("ü") 	=> 	"ue",
			utf8_encode("ß")	=>	"ss",
			"Ö" 	=> 	"oe",
			"Ä" 	=> 	"ae",
			"Ü" 	=> 	"ue",
			utf8_decode("Ö") 	=> 	"oe",
			utf8_decode("Ä") 	=> 	"ae",
			utf8_decode("Ü") 	=> 	"ue",
			utf8_encode("Ö") 	=> 	"oe",
			utf8_encode("Ä") 	=> 	"ae",
			utf8_encode("Ü") 	=> 	"ue",
			"," 	=> 	"",
			"-"		=>	"",
			"--"	=>	"",
			"-_"	=>	"",
			"---"	=>	"",
			"__" 	=> 	"",
			"___" 	=> 	"",
			"____" 	=> 	"",
		];

		//$attrCode = preg_replace('/[^a-zA-Z0-9_]/u', '_', $attrCode);
		$attrCode = strtr($attrCode,$removable_values);

		if (is_numeric(substr($attrCode, 0, 1))) {
			$attrCode = 'i'.$attrCode;
		}

		$attrCode = str_replace('___', '_', $attrCode);
		$attrCode = str_replace('__', '_', $attrCode);
		$attrCode = substr($attrCode, 0, 250);
		$attrCode = trim($attrCode, '_');

		return $attrCode;
	}

}
