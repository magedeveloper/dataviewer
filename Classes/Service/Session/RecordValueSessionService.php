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
	 * @return $this
	 */
	public function store($recordId, array $fieldArray)
	{
		foreach($fieldArray as $_fieldId=>$_fieldValue)
		{
			$key = "{$recordId}-{$_fieldId}";
			$this->writeToSession($_fieldValue, $key);
		}
		
		return $this;
	}

	/**
	 * Sets a session value for a field
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @param $value
	 * @return FieldValueSessionService
	 */
	public function setValueForRecordField(Record $record, Field $field, $value)
	{
		$key = $record->getUid() . "-" . $field->getUid();
		return $this->writeToSession($value, $key);
	}

	/**
	 * Gets an according session value for a specific
	 * field instance
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return mixed
	 */
	public function getValueForRecordField(Record $record, Field $field)
	{
		$recordUid = ($record->getUid())?$record->getUid():"NEW";
		$key = $recordUid . "-" . $field->getUid();
		return $this->restoreFromSession($key);
	}

	/**
	 * Reset a session value for a specific field
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return FieldValueSessionService
	 */
	public function resetForRecordField(Record $record, Field $field)
	{
		$key = $record->getUid() . "-" . $field->getUid();
		return $this->cleanUpSession($key);
	}

	/**
	 * Resets session values for a whole record
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @return FieldValueSessionService
	 */
	public function resetForRecord(Record $record)
	{
		$datatype = $record->getDatatype();
		
		if($datatype instanceof \MageDeveloper\Dataviewer\Domain\Model\Datatype)
		{
			$fields = $datatype->getFields();
			
			foreach($fields as $_field)
				$this->resetForRecordField($record, $_field);
			
		}
		
		return $this;
	}

	/**
	 * Restores data from the session
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function restoreFromSession($key)
	{
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
		$this->sessionObject->setAndSaveSessionData($this->prefixKey, []);
		return $this;;
	}
}
