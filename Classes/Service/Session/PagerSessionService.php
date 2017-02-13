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
class PagerSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-pager";

	/**
	 * Session Keys
	 *
	 * @var string
	 */
	const SESSION_KEY_PAGE			= "tx-dataviewer-page-selection";
	const SESSION_KEY_PER_PAGE	 	= "tx-dataviewer-page-per-page";
	const SESSION_KEY_RECORD_COUNT	= "tx-dataviewer-record-count";


	/**
	 * Sets the selected page to the session
	 *
	 * @param int $page
	 * @return PagerSessionService
	 */
	public function setSelectedPage($page)
	{
		return $this->writeToSession($page, self::SESSION_KEY_PAGE);
	}

	/**
	 * Gets the selected letter from the session
	 *
	 * @return int
	 */
	public function getSelectedPage()
	{
		return $this->restoreFromSession(self::SESSION_KEY_PAGE);
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

	/**
	 * Sets the record count
	 * 
	 * @param int $count
	 * @return void
	 */
	public function setRecordCount($count)
	{
		$this->writeToSession($count, self::SESSION_KEY_RECORD_COUNT);
	}

	/**
	 * Gets the record count
	 * 
	 * @return int|null
	 */
	public function getRecordCount()
	{
		return $this->restoreFromSession(self::SESSION_KEY_RECORD_COUNT);
	}

	/**
	 * Resets all pager settings
	 *
	 * @return PagerSessionService
	 */
	public function reset()
	{
		$this->setSelectedPage(null);
		$this->setPerPage(null);
		return $this;
	}
}
