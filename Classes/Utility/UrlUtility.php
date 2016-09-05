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
 
class UrlUtility
{
	/**
	 * Extracts an id from an url
	 *
	 * @param string $url
	 * @return int
	 */
	public static function extractPidFromUrl($url)
	{
		$parsedUrl = parse_url($url, PHP_URL_QUERY);
		parse_str($parsedUrl, $params);

		$id = 0;
		if (isset($params["id"]))
			$id = $params["id"];

		return (int)$id;
	}

}
