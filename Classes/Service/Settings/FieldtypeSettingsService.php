<?php
namespace MageDeveloper\Dataviewer\Service\Settings;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService; 
use MageDeveloper\Dataviewer\Domain\Repository\FieldtypeConfigurationRepository;
use MageDeveloper\Dataviewer\Domain\Model\FieldtypeConfiguration;

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
class FieldtypeSettingsService extends PluginSettingsService
{
	/**
	 * Gets all registered fieldtypes
	 *
	 * @return array
	 */
	public function getRegisteredFieldtypes()
	{
		$registeredFieldtypes = [];
		$fieldtypesConfiguration = $this->getFieldtypesConfiguration();
		
		foreach($fieldtypesConfiguration as $_ftC)
			$registeredFieldtypes[] = $_ftC->getIdentifier();
		
		return $registeredFieldtypes;
	}

	/**
	 * Gets the complete fieldtypes configuration from
	 * the plugin settings in typoscript
	 *
	 * @return FieldtypeConfigurationRepository
	 */
	public function getFieldtypesConfiguration()
	{
		$fieldtypesConfiguration = $this->getConfiguration("fieldtypes");

		$fieldtypesRepository = new FieldtypeConfigurationRepository();
		if (is_array($fieldtypesConfiguration))
		{
			foreach($fieldtypesConfiguration as $_fieldtypeIdentifier=>$_fieldtypeConfiguration)
			{
				/* @var FieldtypeConfiguration $item */
				$item = $fieldtypesRepository->getNewItemWithData($_fieldtypeConfiguration);
				$item->setIdentifier(trim($_fieldtypeIdentifier,"."));
				$fieldtypesRepository->addItem( $item );
			}

		}

		return $fieldtypesRepository;
	}

	/**
	 * Gets the according fieldtype configuration by
	 * a given fieldtype identifier
	 *
	 * @param string $fieldtypeIdentifier
	 * @return FieldtypeConfiguration
	 */
	public function getFieldtypeConfiguration($fieldtypeIdentifier)
	{
		$fieldtypesConfiguration = $this->getFieldtypesConfiguration();
		return $fieldtypesConfiguration->findByIdentifier($fieldtypeIdentifier);
	}
	
}
