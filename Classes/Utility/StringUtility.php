<?php
namespace MageDeveloper\Dataviewer\Utility;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class StringUtility
{
	/**
	 * Possible string dividers
	 *
	 * @var array
	 */
	protected static $dividers = array(
		';',
		'/',
		'.',
		"\\",
		":",
		"-"
	);

	/**
	 * Gets exploded and trimmed values by
	 * a separated string
	 *
	 * @param string $string String to separate
	 * @param array $allowedSeparators List of allowed separators
	 * @return array
	 */
	public static function explodeSeparatedString($string, $allowedSeparators = array())
	{
		if (strlen($string))
		{
			$dividers = self::$dividers;
		
			if (!empty($allowedSeparators))
				$dividers = $allowedSeparators;
		
			// Check that the divider of the ids is comma separation
			foreach ($dividers as $divider) {
					$string = str_replace($divider, ',', $string);
			}

			$exploded = array_map('trim',explode(",",$string));

			return $exploded;
		}

		return array();
	}

	/**
	 * Creates a usage friendly code from a given string
	 *
	 * @param $string Entry string
	 * @return string
	 */
	public static function createCodeFromString($string)
	{
		$attrName = utf8_decode($string);
		$attrCode = strtolower($attrName);
		$attrCode = str_replace(" ","", $attrCode);

		$removable_values = array(
			";" 	=> 	"",
			":" 	=> 	"",
			"/" 	=> 	"",
			"\\" 	=> 	"",
			"\""	=>  "",
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
		);

		//$attrCode = preg_replace('/[^a-zA-Z0-9_]/u', '_', $attrCode);
		$attrCode = strtr($attrCode,$removable_values);

		if (is_numeric(substr($attrCode, 0, 1)))
		{
			$attrCode = 'i'.$attrCode;
		}

		$attrCode = str_replace('___', '_', $attrCode);
		$attrCode = str_replace('__', '_', $attrCode);

		$attrCode = substr($attrCode, 0, 250);

		$attrCode = trim($attrCode, '_');

		return $attrCode;
	}

}
