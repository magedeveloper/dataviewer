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
class FieldValueSessionService extends SessionService
{
	/**
	 * Prefix Key
	 * @var string
	 */
	protected $prefixKey = "tx_dataviewer_field";

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
		$key = $record->getUid() . "-" . $field->getUid();
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
		
		if($datatype)
		{
			$fields = $datatype->getFields();
			
			foreach($fields as $_field)
				$this->resetForRecordField($record, $_field);
			
		}
		
		return $this;
	}
}
