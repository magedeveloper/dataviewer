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

class ArrayUtility
{
	/**
	 * Gets an array value by a given path
	 *
	 * @param array $array Array to search
	 * @param string $path Path for array
	 * @return mixed
	 */
	public static function getArrayValueByPath(array $array, $path)
	{
		$divided = StringUtility::explodeSeparatedString($path);

		$func = function($arr, $k) {
			return $arr[$k];
		};

		$newArr = $array;
		foreach ($divided as $_key)
		{
			$newArr = $func($newArr, $_key);
		}

		return $newArr;
	}

	/**
	 * Recursive finds a key in an array and
	 * returns its value
	 * 
	 * @param string $needle
	 * @param array $haystack
	 * @return mixed|bool
	 */
	public static function recursiveFindKey($needle, $haystack)
	{
		$iterator  = new \RecursiveArrayIterator($haystack);
		$recursive = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
		$found = [];
		foreach ($recursive as $key => $value) {
			if ($key === $needle) {
				array_push($found, $value);
			}
		}
		
		return $found;
	}

	/**
	 * Parses an xml to an array
	 *
	 * @param string $xml Input XML String
	 * @return array
	 */
	public static function xml2array($xml)
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $xml, $vals);
		xml_parser_free($xml_parser);
		// wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie 
		$_tmp='';
		foreach ($vals as $xml_elem)
		{
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_level!=1 && $x_type == 'close')
			{
				if (isset($multi_key[$x_tag][$x_level]))
					$multi_key[$x_tag][$x_level]=1;
				else
					$multi_key[$x_tag][$x_level]=0;
			}
			if ($x_level!=1 && $x_type == 'complete')
			{
				if ($_tmp==$x_tag)
					$multi_key[$x_tag][$x_level]=1;
				$_tmp=$x_tag;
			}
		}

		foreach ($vals as $xml_elem)
		{
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_type == 'open')
				$level[$x_level] = $x_tag;
			$start_level = 1;
			$php_stmt = '$xml_array';
			if ($x_type=='close' && $x_level!=1)
				$multi_key[$x_tag][$x_level]++;
			while($start_level < $x_level)
			{
				$php_stmt .= '[$level['.$start_level.']]';
				if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
					$php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
				$start_level++;
			}
			$add='';
			if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete'))
			{
				if (!isset($multi_key2[$x_tag][$x_level]))
					$multi_key2[$x_tag][$x_level]=0;
				else
					$multi_key2[$x_tag][$x_level]++;
				$add='['.$multi_key2[$x_tag][$x_level].']';
			}
			if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes',$xml_elem))
			{
				if ($x_type == 'open')
					$php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
				else
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
				eval($php_stmt_main);
			}
			if (array_key_exists('attributes',$xml_elem))
			{
				if (isset($xml_elem['value']))
				{
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
					eval($php_stmt_main);
				}
				foreach ($xml_elem['attributes'] as $key=>$value)
				{
					$php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
					eval($php_stmt_att);
				}
			}
		}

		return $xml_array;
	}
}
