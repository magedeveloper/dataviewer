<?php
namespace MageDeveloper\Dataviewer\LabelUserFunc;

use \MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class Variable
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Variable Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
	 * @inject
	 */
	protected $variableRepository;

	/**
	 * Constructor
	 *
	 * @return Variable
	 */
	public function __construct()
	{
		$this->objectManager	 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->variableRepository			= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\VariableRepository::class);
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
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Variable $variable */
			$variable = $this->variableRepository->findByUid($row["uid"], false);

			if ($variable instanceof \MageDeveloper\Dataviewer\Domain\Model\Variable)
			{
				$type = $variable->getType();
				$name = $variable->getVariableName();
				$pid = $variable->getPid();
				$typeStr = Locale::translate("variable_type.{$type}");
				if ($typeStr)
					$pObj["title"] = "[{$pid}]" . " " . $typeStr . " '{$name}'";

			}

		}

	}

}
