<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class SearchSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-search";

	/**
	 * Session Keys for Sorting
	 * @var string
	 */
	const SESSION_KEY_SEARCH_FIELDS		= "tx-dataviewer-search-fields";
	const SESSION_KEY_SEARCH_STRING		= "tx-dataviewer-search-string";
	const SESSION_KEY_SEARCH_TYPE		= "tx-dataviewer-search-type";

	/**
	 * Set the search fields
	 * with [
	 *   field_id
	 * 	 field_condition
	 * ]
	 * 
	 * @param array $searchFields
	 * @return SearchSessionService
	 */
	public function setSearchFields(array $searchFields = [])
	{
		return $this->writeToSession($searchFields, self::SESSION_KEY_SEARCH_FIELDS);
	}

	/**
	 * Get the searchfields stored in session
	 * 
	 * @return array
	 */
	public function getSearchFields()
	{
		if(is_array($this->restoreFromSession(self::SESSION_KEY_SEARCH_FIELDS)))
			return $this->restoreFromSession(self::SESSION_KEY_SEARCH_FIELDS);
			
		return [];	
	}

	/**
	 * Sets the search string to the session
	 * 
	 * @param string $searchString
	 * @return SearchSessionService
	 */
	public function setSearchString($searchString)
	{
		return $this->writeToSession($searchString, self::SESSION_KEY_SEARCH_STRING);
	}

	/**
	 * Gets the search string stored in the session
	 * 
	 * @return string
	 */
	public function getSearchString()
	{
		$string = (string)$this->restoreFromSession(self::SESSION_KEY_SEARCH_STRING);
		$GLOBALS['TYPO3_DB']->quoteStr($string, 'tx_dataviewer_domain_model_recordvalue');
		return $string;
	}

	/**
	 * Sets the search type
	 * 
	 * @param string $searchType
	 * @return SearchSessionService
	 */
	public function setSearchType($searchType)
	{
		return $this->writeToSession($searchType, self::SESSION_KEY_SEARCH_TYPE);
	}

	/**
	 * Gets the search type stored in session
	 * 
	 * @return string
	 */
	public function getSearchType()
	{
		return $this->restoreFromSession(self::SESSION_KEY_SEARCH_TYPE);
	}

	/**
	 * Resets all search credentials
	 * 
	 * @return SearchSessionService
	 */
	public function reset()
	{
		$this->setSearchFields([]);
		$this->setSearchType(null);
		$this->setSearchString(null);
		return $this;
	}

}
