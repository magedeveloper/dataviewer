<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use MageDeveloper\Dataviewer\Utility\IconUtility;
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
class Datatype
{
	/**
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Constructor
	 *
	 * @return Datatype
	 */
	public function __construct()
	{
		$this->objectManager 				= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->datatypeRepository		= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository::class);
	}

	/**
	 * Populate datatypes
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateDatatypesAction(array &$config, &$parentObject)
	{
		$pageId = (int)$config["row"]["pid"];

		$options = [];
		$usedIds = [];
		
		if(GeneralUtility::_GET("datatype"))
		{
			$dId = (int)GeneralUtility::_GET("datatype");
			$datatype = $this->datatypeRepository->findByUid($dId, false);
			$icon = IconUtility::getIconByHash($datatype->getIcon());
			$options[] = [$datatype->getInfo(), $datatype->getUid(), $icon];
		}
		else
		{
			$options[] = ["", ""];
		}

		$datatypesLocalPage = $this->datatypeRepository->findAllOnPid($pageId);
		if ($datatypesLocalPage->count())
		{
			$options[] = [Locale::translate("on_this_page"), "--div--"];

			foreach($datatypesLocalPage as $_datatype)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $_datatype */
				$icon = IconUtility::getIconByHash($_datatype->getIcon());
				$options[] = [$_datatype->getInfo(), $_datatype->getUid(), $icon];
				$usedIds[] = $_datatype->getUid();
			}
		}

		$datatypesOtherPages = $this->datatypeRepository->findAll(false);

		if ($datatypesOtherPages->count())
		{
			$headerSet = false;
			foreach($datatypesOtherPages as $_datatype)
			{
				if ($_datatype->getPid() !== $config["row"]["pid"] && !in_array($_datatype->getUid(), $usedIds))
				{
					if ($headerSet === false)
					{
						$options[] = [Locale::translate("on_other_pages"), "--div--"];
						$headerSet = true;
					}

					/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $_datatype */
					$icon = IconUtility::getIconByHash($_datatype->getIcon());
					$options[] = [$_datatype->getInfo(), $_datatype->getUid(), $icon];
				}

			}
		}

		//if (is_array($config["items"]))
		//	$config["items"] = array_merge($config["items"], $options);
		//else
			$config["items"] = $options;

	}
}
