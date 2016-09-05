<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

use MageDeveloper\Dataviewer\Domain\Model\FieldValue;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\Record;

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
class RecordValueRepository extends AbstractRepository
{
	/**
	 * Finds a record value by given record and field
	 *
	 * @param Record $record
	 * @param Field $field
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record|null
	 */
	public function findOneByRecordAndField(Record $record, Field $field)
	{
		$query = $this->createQueryWithSettings();

		return $query->matching(
			$query->logicalAnd(
				$query->equals("record", $record->getUid()),
				$query->equals("field", $field->getUid())
			)
		)->execute()->getFirst();
	}
	
	/**
	 * Finds a recordvalue by some parameters
	 * 
	 * @param Record $record
	 * @param FieldValue $fieldValue
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record|null
	 */
	public function findOneByRecordAndFieldValue(Record $record, FieldValue $fieldValue)
	{
		$query = $this->createQueryWithSettings();

		return $query->matching(
			$query->logicalAnd(
				$query->equals("record", $record->getUid()),
				$query->equals("field", $fieldValue->getField()->getUid()),
				$query->equals("field_value", $fieldValue->getUid())
			)
		)->execute()->getFirst();
	}

	/**
	 * Finds record values by value content 'like'
	 *
	 * @param $valueContent
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByValueContent($valueContent)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(false);

		return $query->matching($query->like("value_content", "%{$valueContent}%"))->execute();
	}

	/**
	 * Finds record values by field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByField(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(false);
		$querySettings->setIgnoreEnableFields(true);
		$querySettings->setRespectSysLanguage(false);

		return $query->matching($query->equals("field", $field->getUid()))->execute();
	}

	/**
	 * Finds record values by field id
	 *
	 * @param int $fieldId
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByFieldId($fieldId)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(false);
		$querySettings->setIgnoreEnableFields(true);
		$querySettings->setRespectSysLanguage(false);

		return $query->matching($query->equals("field", $fieldId))->execute();
	}

	/**
	 * Finds record values by field value id
	 *
	 * @param int $fieldValueId
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByFieldValueId($fieldValueId)
	{
		$query = $this->createQuery();
		$querySettings = $query->getQuerySettings();
		$querySettings->setRespectStoragePage(false);
		$querySettings->setIgnoreEnableFields(true);
		$querySettings->setRespectSysLanguage(false);

		return $query->matching($query->equals("field_value", $fieldValueId))->execute();
	}
}
