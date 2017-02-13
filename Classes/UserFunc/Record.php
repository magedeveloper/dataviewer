<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class Record
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Constructor
	 *
	 * @return Record
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->recordRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->fieldRepository          = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
	}

	/**
	 * Populate records
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateRecordsAction(array &$config, &$parentObject)
	{
		$options = [];
		$records = $this->recordRepository->findAll([], false, true, ["pid" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING]);
		foreach($records as $_record)
		{
			$pid = $_record->getPid();
			$label = "[{$pid}] " . $_record->getTitle() . " (Uid: {$_record->getUid()})";
			$options[] = [$label, $_record->getUid()];
		}

		$config["items"] = array_merge($config["items"], $options);
	}

	/**
	 * Gets a value from a dynamic record
	 *
	 * @param string $info
	 * @param array $config
	 * @return string|void
	 */
	public function getDynamicRecordValue($info, $config)
	{
		$parameters = $config["parameter."];

		if(isset($parameters["uid"]))
		{
			$uid = (int)$parameters["uid"];
		}
		else
		{
			$recordInfo = GeneralUtility::_GP("tx_dataviewer_record");
			$uid = $recordInfo["record"];
		}

		$record = $this->recordRepository->findByUid($uid, false);

		if($record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
		{
			$fieldUidOrName = $parameters["field"];
			$func = "get".ucwords($fieldUidOrName);

			if(method_exists($record, $func))
				return $record->{$func}();

			$field = $this->fieldRepository->findOneByVariableName($fieldUidOrName);
			if(!$field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
			{
				$field = $this->fieldRepository->findByUid($fieldUidOrName, false);
			}

			// We finally check if we got a field now
			if($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
			{
				$value = $record->getValueByField($field);
				return $value->getValue();
			}

		}

		return;
	}
}
