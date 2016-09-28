<?php
namespace MageDeveloper\Dataviewer\Validation;

use MageDeveloper\Dataviewer\Domain\Model\Field;
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

class FieldValidation
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Field Model
	 * 
	 * @var Field
	 */
	protected $field;

	/**
	 * FlexForm Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * Validation Settings Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Settings\ValidationSettingsService
	 * @inject
	 */
	protected $validationSettingsService;

	/**
	 * Constructor
	 *
	 * @return FieldValidation
	 */
	public function __construct()
	{
		$this->objectManager 		= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->flexFormService		= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
		$this->validationSettingsService = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\ValidationSettingsService::class);
	}

	/**
	 * Gets the field model
	 * 
	 * @return Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field model
	 * 
	 * @param Field $field
	 * @return void
	 */
	public function setField($field)
	{
		$this->field = $field;
	}

	/**
	 * Gets the validation configuration
	 * This configuration is generated through the
	 * field configuration
	 * 
	 * @return array
	 */
	public function getValidationConfiguration()
	{
		$validationFlex = $this->getField()->getValidation();
		$config = $this->flexFormService->convertFlexFormContentToArray($validationFlex);
		$validationConfiguration = [];

		if (isset($config["field"]) && is_array($config["field"]))
		{
			foreach($config["field"] as $_validation)
			{
				$validation = $_validation["validation"];
				$vC = str_replace(",","&", $validation["validation_configuration"]);
				$vC = str_replace(" ","", $vC);
				parse_str($vC, $parsed);
				$validation["validation_configuration"] = $parsed;
				
				$validationConfiguration[] = $validation;
			}
		}
		
		return $validationConfiguration;
	}

	/**
	 * Main Method to validate a field model
	 *
	 * @param mixed $value Value to validate against
	 * @return array
	 * @throws \InvalidClassException
	 */
	public function validate($value)
	{
		$validationErrors = [];
		$validationConfiguration = $this->getValidationConfiguration();
		
		foreach($validationConfiguration as $_validate)
		{
			$validatorId = $_validate["validator"];
			$options = $_validate["validation_configuration"];
			
			/* @var \MageDeveloper\Dataviewer\Domain\Model\ValidatorConfiguration $validatorConfiguration */
			$validatorConfiguration = $this->validationSettingsService->getValidatorConfiguration($validatorId);
			$class = $validatorConfiguration->getValidatorClass();
			
			if (!$this->objectManager->isRegistered($class))
				throw new \InvalidClassException($class);
			
			/* @var \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator $validator */
			$validator = $this->objectManager->get($class, $options);
			/* @var \TYPO3\CMS\Extbase\Error\Result $result */
			$result = $validator->validate($value);
			
			foreach($result->getErrors() as $_error) 
			{
				/* @var \TYPO3\CMS\Extbase\Error\Error $_error */
				$validationErrors[] = $_error;
			}	
		}
		
		return $validationErrors;
	}
}
