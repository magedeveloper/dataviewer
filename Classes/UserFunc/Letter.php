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
class Letter
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Letter Selection Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\LetterSettingsService
	 * @inject
	 */
	protected $letterSettingsService;

	/**
	 * Constructor
	 *
	 * @return Letter
	 */
	public function __construct()
	{
		$this->objectManager 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->letterSettingsService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\LetterSettingsService::class);
	}

	/**
	 * Populate flexform letter selection
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateLetters(array &$config, &$parentObject)
	{
		$letters = $this->letterSettingsService->getLetters();

		$options = [];
		foreach($letters as $_letter)
		{
			$options[] = [$_letter, $_letter];
		}

		if (is_array($config["items"]))
			$config["items"] = array_merge($config["items"], $options);
		else
			$config["items"] = $options;

		return $config;
	}

}
