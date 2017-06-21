<?php
namespace MageDeveloper\Dataviewer\Factory;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;

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
class ValueFactory implements SingletonInterface
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Fieldtype Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * RecordValue Repository
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordValueRepository
	 * @inject
	 */
	protected $recordValueRepository;

	/**
	 * Fieldvalue Cache
	 * 
	 * @var array
	 */
	protected $fieldvalues = [];

	/**
	 * Creates a value object
	 *
	 * @param Field $field
	 * @param Record $record
	 * @param null  $recordValue
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Value
	 */
	public function create(Field $field, Record $record, $recordValue = null)
	{
		$value = new \MageDeveloper\Dataviewer\Domain\Model\Value();

		// Find a recordvalue by cond
		$recordValue = $this->recordValueRepository->findOneByRecordAndField($record, $field);

		if(!$recordValue instanceof RecordValue)
		{
			$recordValue = $this->objectManager->get(RecordValue::class);
			$recordValue->setRecord($record);
		}

		$type = $field->getType();
		$fieldvalue = $this->getFieldvalueByType($type);

		$fieldvalue->setField($field);
		$fieldvalue->setRecordValue($recordValue);
		$fieldvalue->setValue($recordValue->getValueContent());

		$value->setFieldvalue($fieldvalue);
		$value->setRecordValue($recordValue);
		$value->setField($field);

		return $value;
	}

	/**
	 * Gets a fieldvalue by a given type
	 *
	 * @param string $type
	 * @return \MageDeveloper\Dataviewer\Form\FieldValue\AbstractFieldvalue
	 */
	public function getFieldvalueByType($type)
	{
		if(isset($this->fieldvalues[$type]))
			return $this->fieldvalues[$type];
	
		$config		= $this->fieldtypeSettingsService->getFieldtypeConfiguration($type);
		$valueClass	= $config->getValueClass();

		// In case the value class doesn't exist, we fallback to our general value class
		if(!$this->objectManager->isRegistered($valueClass))
			$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;

		$fieldvalue = $this->objectManager->get($valueClass);
		$this->fieldvalues[$type] = $fieldvalue;
		
		return $fieldvalue;
	}
	
}
