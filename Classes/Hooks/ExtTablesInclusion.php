<?php
namespace MageDeveloper\Dataviewer\Hooks;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class processes select/multiselect fields with the suggest wizard active
 * to inject the field configuration into the GLOBALS.
 * 
 * This helps to run the default SuggestWizard without any additional configuration
 */

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
class ExtTablesInclusion implements \TYPO3\CMS\Core\Database\TableConfigurationPostProcessingHookInterface
{
	/**
	 * Object Manager
	 *
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
	 * Fieldtype Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Field Configuration
	 * 
	 * @var array
	 */
	protected $fieldConfig = [];

	/**
	 * Constructor
	 *
	 * @return ExtTablesInclusion
	 */
	public function __construct()
	{
		$this->objectManager    = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
	
		$this->fieldtypeSettingsService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService::class);
	}

	/**
	 * Function which may process data created / registered by extTables
	 * scripts (f.e. modifying TCA data of all extensions)
	 *
	 * @return void
	 */
	public function processData()
	{
		// We only need to modify the GLOBALS in backend environment
		if (TYPO3_MODE !== "BE") {
			return; 
		}
		
		if(!ExtensionManagementUtility::isLoaded("dataviewer"))
			return;

		// Get all fields of type select/multiselect with the checkbox Suggest Wizard active
		// Process the tca of all fields and inject the rendered tca
		// into the globals
		$types = ["select","multiselect"];
	
		// We need to create a dirty try-catch here, since we have nothing better to check for existence of many different needs
		try {
			$fields = $this->fieldRepository->findByTypes($types);
			
			// We quick load the fieldtype configuration for these types to
			// restore the information in our loop later
			foreach($types as $_type)
			{
				if(!isset($this->fieldConfig[$_type]))
				{
					$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration($_type);

					if($fieldtypeConfiguration)
					{
						$class = $fieldtypeConfiguration->getFieldClass();
						$this->fieldConfig[$_type] = $class;
					}
				}
			}

			// We need a blank record
			$record = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\Record::class);
			foreach($fields as $_field)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
				$fieldId = $_field->getUid();
				$type = $_field->getType();
				$class = $this->fieldConfig[$type];

				if($this->objectManager->isRegistered($class))
				{
					/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype $fieldtype */
					$fieldtype = $this->objectManager->get($class);
					$fieldtype->formDataProviders = [];
					$fieldtype->setField($_field);
					$fieldtype->setRecord($record);

					// Removing type to prevent items generation
					$_field->setType("");

					$tca = $fieldtype->buildTca();
					$_field->setType($type);

					$config = $tca["processedTca"]["columns"][$fieldId]["config"];

					// Injecting the virtual tca into the globals for later usage
					$GLOBALS["TCA"]["tx_dataviewer_domain_model_record"]["columns"][$fieldId]["config"] = $config;
					
				}
			}

		} catch (\Exception $e)
		{
			return;
		}
		
	}

}

