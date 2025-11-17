<?php
namespace Aix\Dataviewer\Service\Session;

use \Aix\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
		return $this->restoreFromSession(self::SESSION_KEY_SORT_ORDER);
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


}
