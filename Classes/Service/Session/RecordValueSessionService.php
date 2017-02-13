<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
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
class RecordValueSessionService
{
	/**
	 * Prefix Key
	 * @var string
	 */
	protected $prefixKey = "tx_dataviewer_recordvalue_session";

	/**
	 * Class constructor.
	 *
	 * @return RecordValueSessionService
	 * @throws \Exception
	 */
	public function __construct()
	{
		$this->sessionObject =  $GLOBALS['BE_USER'];
	}

	/**
	 * Store a complete field array to the
	 * session for later usage
	 * 
	 * @param int|string $recordId
	 * @param array $fieldArray
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	public function store($recordId, array $fieldArray)
	{
		if(!is_numeric($recordId)) $recordId = "NEW";
		
		$this->writeToSession($fieldArray, $recordId);
		return $this;
	}

	/**
	 * Resets the session values for a 
	 * specific record id
	 * 
	 * @param string $recordId
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	public function resetForRecordId($recordId)
	{
		if(!is_numeric($recordId)) $recordId = "NEW";
		return $this->cleanUpSession($recordId);
	}

	/**
	 * Retrieves a stored value by a given
	 * recordId and fieldId
	 * 
	 * @param string $recordId
	 * @param int $fieldId
	 * @return mixed
	 */
	public function getStoredValueForRecordIdAndFieldId($recordId, $fieldId)
	{
		if(!is_numeric($recordId)) $recordId = "NEW";
		
		$values = $this->restoreFromSession($recordId);
		
		if(is_array($values) && array_key_exists($fieldId, $values))
			return $values[$fieldId];
			
		return null;	
	}

	/**
	 * Restores data from the session
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function restoreFromSession($key)
	{
		$sessionData = null;
		if($this->sessionObject)
			$sessionData = $this->sessionObject->getSessionData( $this->prefixKey );
		
		return (is_array($sessionData) && array_key_exists($key, $sessionData))?unserialize($sessionData[$key]):null;
	}

	/**
	 * Writes data to the session
	 *
	 * @param mixed $object Object to write to the session
	 * @param string $key Identifier for the session
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	protected function writeToSession($object, $key)
	{
		if(!$this->sessionObject)
			return $this;
		
		$sessionData = $this->sessionObject->getSessionData( $this->prefixKey );

		if(!is_array($sessionData))	$sessionData = [];
		
		$sessionData[$key] = serialize($object);
		$this->sessionObject->setAndSaveSessionData($this->prefixKey, $sessionData);
		return $this;
	}

	/**
	 * Cleans a variable that is stored in the session
	 *
	 * @param string $key
	 * @return \MageDeveloper\Dataviewer\Service\Session\SessionService
	 */
	protected function cleanUpSession($key)
	{
		if($this->sessionObject)
			$this->sessionObject->setAndSaveSessionData($this->prefixKey, []);
			
		return $this;
	}
}
