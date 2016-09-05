<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\ArrayUtility;
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
class Validation
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * Validation Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\ValidationSettingsService
	 * @inject
	 */
	protected $validationSettingsService;

	/**
	 * Flexform Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexformService;

	/**
	 * Constructor
	 *
	 * @return Validation
	 */
	public function __construct()
	{
		$this->objectManager 				= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->validationSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\ValidationSettingsService::class);
		$this->flexformService				= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
	}
	
	/**
	 * Populate validation selection
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateValidators(array &$config, &$parentObject)
	{
		$validationConfigurationRepository = $this->validationSettingsService->getValidatorsConfiguration();
		$options = array();
		foreach($validationConfigurationRepository as $_vC)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\ValidatorConiguration $_vC */
			$options[] = array($_vC->getLabel(),$_vC->getIdentifier());
		}

		if (is_array($config["items"]))
			$config["items"] = array_merge($config["items"], $options);
		else
			$config["items"] = $options;
	}

}
