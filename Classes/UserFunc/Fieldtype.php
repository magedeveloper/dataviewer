<?php
namespace MageDeveloper\Dataviewer\UserFunc;

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
class Fieldtype
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;
	
	/**
	 * Fieldtypes Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Constructor
	 *
	 * @return Fieldtype
	 */
	public function __construct()
	{
		$this->objectManager 				= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldtypeSettingsService		= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * Populate fieldtypes
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFieldtypes(array &$config, &$parentObject)
	{
		$fieldtypesConfigurationRepository = $this->fieldtypeSettingsService->getFieldtypesConfiguration();
		$options = array();
		foreach($fieldtypesConfigurationRepository as $fieldtypeConfiguration)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $fieldtype */
			$options[] = array($fieldtypeConfiguration->getLabel(),
				               $fieldtypeConfiguration->getIdentifier(),
				               $fieldtypeConfiguration->getIcon());
		}

		if (is_array($config["items"]))
			$config["items"] = array_merge($config["items"], $options);
		else
			$config["items"] = $options;
	
	}
}
