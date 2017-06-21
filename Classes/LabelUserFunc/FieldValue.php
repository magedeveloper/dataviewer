<?php
namespace MageDeveloper\Dataviewer\LabelUserFunc;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class FieldValue
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldValueRepository
	 * @inject
	 */
	protected $fieldValueRepository;

	/**
	 * Fieldtype Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Constructor
	 *
	 * @return FieldValue
	 */
	public function __construct()
	{
		$this->objectManager	 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldValueRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldValueRepository::class);
		$this->fieldtypeSettingsService		= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * UserFunc for FieldValue Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj)
	{
		if (isset($pObj["row"]))
		{
			$row = $pObj["row"];
			$fieldValue = $this->fieldValueRepository->findByUid($row["uid"], false);

			if ($fieldValue instanceof \MageDeveloper\Dataviewer\Domain\Model\FieldValue)
			{
				$type = $fieldValue->getType();
				$typeStr = Locale::translate("type.{$type}");
				if ($typeStr)
					$pObj["title"] = $typeStr;
					
				if ($fieldValue->isDefault())
				{
					$default = Locale::translate("default");
					$pObj["title"] .= " [{$default}]";	
				}
				
			}

		}

	}

}
