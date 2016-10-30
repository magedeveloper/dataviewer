<?php
namespace MageDeveloper\Dataviewer\Service\Settings;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService;
use TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException;

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
class ValidationSettingsService extends PluginSettingsService
{
	/**
	 * Gets all registered fieldtypes
	 *
	 * @return array
	 */
	public function getRegisteredValidators()
	{
		$validatorConfiguration = $this->getValidatorsConfiguration();
		$registeredValidators = [];
		foreach($validatorConfiguration as $_vC)
			$registeredValidators[] = $_vC->getIdentifier();
	
		return $registeredValidators;
	}

	/**
	 * Gets the complete validator configuration from
	 * the plugin settings in typoscript
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\ValidatorRepository
	 */
	public function getValidatorsConfiguration()
	{
		$validatorConfiguration = $this->getConfiguration("validators");

		$validatorRepository = new \MageDeveloper\Dataviewer\Domain\Repository\ValidatorConfigurationRepository();
		if (is_array($validatorConfiguration))
		{
			foreach($validatorConfiguration as $_validatorId=>$_validatorConfiguration)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $item */
				$item = $validatorRepository->getNewItemWithData($_validatorConfiguration);
				$item->setIdentifier(trim($_validatorId,"."));
				$validatorRepository->addItem( $item );
			}

		}
		
		return $validatorRepository;
	}

	/**
	 * Gets the according validator configuration by
	 * a given validator identifier
	 *
	 * @param string $validatorId
	 * @return \MageDeveloper\Dataviewer\Domain\Model\FieldtypeConfiguration
	 * @throws NoSuchValidatorException
	 */
	public function getValidatorConfiguration($validatorId)
	{
		$fieldtypesConfiguration = $this->getValidatorsConfiguration();
		$validatorConfiguration = $fieldtypesConfiguration->findByIdentifier($validatorId);

		if($validatorConfiguration instanceof \MageDeveloper\Dataviewer\Domain\Model\FieldtypeConfiguration)
			throw new NoSuchValidatorException("Validator '{$validatorId}' not found", 1477375422);

		return $validatorConfiguration;
	}

}
