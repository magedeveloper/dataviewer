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
class Field
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
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Constructor
	 *
	 * @return Field
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldRepository	 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
	}

	/**
	 * UserFunc for Field Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj)
	{
		if (isset($pObj["row"]))
		{
			$row = $pObj["row"];
			$field = $this->fieldRepository->findByUid($row["uid"], false);

			if ($field instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
			{
				$pid = $field->getPid();
				$code = $field->getCode();
				$label = ($field->getFrontendLabel())?$field->getFrontendLabel():"[".Locale::translate("no_label")."]";
				$pObj["title"] = "[{$pid}] " . strtoupper($field->getType()) . ": " . $label . " {".$code."}";
			}

		}

	}

}
