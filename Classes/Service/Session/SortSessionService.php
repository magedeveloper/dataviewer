<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use \MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar

 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SortSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-sort";

	/**
	 * Session Keys for Sorting
	 * @var string
	 */
	const SESSION_KEY_SORT_ORDER 	= "tx-dataviewer-sort-order";
	const SESSION_KEY_SORT_BY	 	= "tx-dataviewer-sort-by";
	const SESSION_KEY_PER_PAGE	 	= "tx-dataviewer-sort-per-page";
	const SESSION_KEY_IS_SET		= "tx-dataviewer-sort-is-set";

	/**
	 * Checks if any orderings are set
	 * 
	 * @return bool
	 */
	public function hasOrderings()
	{
		if ($this->getSortField() && $this->getSortOrder())
			return true;

		return false;
	}

	/**
	 * Sets the sort order
	 *
	 * @param string $order
	 * @return SortSessionService
	 */
	public function setSortOrder($order = QueryInterface::ORDER_ASCENDING)
	{
		$this->writeToSession($order, self::SESSION_KEY_SORT_ORDER);
		return $this;
	}

	/**
	 * Sets the sort by
	 *
	 * @param string $sortBy
	 * @return $this
	 */
	public function setSortField($sortBy)
	{
		$this->writeToSession($sortBy, self::SESSION_KEY_SORT_BY);
		return $this;
	}

	/**
	 * Gets the sort order
	 *
	 * @return string
	 */
	public function getSortOrder()
	{
		$sortOrder 		= $this->restoreFromSession(self::SESSION_KEY_SORT_ORDER);

		if ($sortOrder && !is_null($sortOrder))
			return $sortOrder;

		return QueryInterface::ORDER_ASCENDING;
	}

	/**
	 * Gets the sort by setting from session
	 *
	 * @return string
	 */
	public function getSortField()
	{
		return $this->restoreFromSession(self::SESSION_KEY_SORT_BY);
	}

	/**
	 * Sets the results per page
	 *
	 * @param int $perPage
	 * @return $this
	 */
	public function setPerPage($perPage)
	{
		$this->writeToSession($perPage, self::SESSION_KEY_PER_PAGE);
		return $this;
	}

	/**
	 * Gets the result number per page
	 *
	 * @return int
	 */
	public function getPerPage()
	{
		return $this->restoreFromSession(self::SESSION_KEY_PER_PAGE);
	}

}
