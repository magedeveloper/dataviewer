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
class SelectSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-select";

	/**
	 * Session Keys for Selection
	 * @var string
	 */
	const SESSION_KEY_SELECT_RECORDS	= "tx-dataviewer-select-records";

	/**
	 * Set selected records to the session
	 * 
	 * @param array $selectedRecordIds
	 * @return SelectSessionService
	 */
	public function setSelectedRecords(array $selectedRecordIds)
	{
		return $this->writeToSession($selectedRecordIds, self::SESSION_KEY_SELECT_RECORDS);
	}

	/**
	 * Gets the selected records from the session
	 * 
	 * @return array
	 */
	public function getSelectedRecords()
	{
		$selectedRecords = $this->restoreFromSession(self::SESSION_KEY_SELECT_RECORDS);
		
		if(!is_array($selectedRecords))
			$selectedRecords = array();
			
		return $selectedRecords;	
	}

	/**
	 * Resets all selected records
	 * 
	 * @return SelectSessionService
	 */
	public function reset()
	{
		$this->setSelectedRecords(array());
		return $this;
	}

}
