<?php
namespace MageDeveloper\Dataviewer\Service\Settings;

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
class FieldtypeSettingsService extends PluginSettingsService implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * Registered Fieldtypes
	 * 
	 * @var array
	 */
	protected $registeredFieldtypes = [];

	/**
	 * Fieldtypes Configuration
	 * 
	 * @var array
	 */
	protected $fieldtypesConfig = [];

	/**
	 * Fieldtypes Configuration Repository
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldtypeConfigurationRepository
	 * @inject
	 */
	protected $fieldtypesConfigRepository;

	/**
	 * Gets all registered fieldtypes
	 *
	 * @return array
	 */
	public function getRegisteredFieldtypes()
	{
		if(empty($this->registeredFieldtypes))
		{
			$fieldtypesConfiguration = $this->getFieldtypesConfiguration();
			foreach($fieldtypesConfiguration as $_ftC)
				$this->registeredFieldtypes[] = $_ftC->getIdentifier();

		}
		
		return $this->registeredFieldtypes;
	}

	/**
	 * Gets the complete fieldtypes configuration from
	 * the plugin settings in typoscript
	 *
	 * @return FieldtypeConfigurationRepository
	 */
	public function getFieldtypesConfiguration()
	{
		if($this->fieldtypesConfigRepository && $this->fieldtypesConfigRepository->count())
			return $this->fieldtypesConfigRepository;
	
		$this->fieldtypesConfig = $this->getConfiguration("fieldtypes");
		
		$this->fieldtypesConfigRepository = new FieldtypeConfigurationRepository();
		if (is_array($this->fieldtypesConfig))
		{
			foreach($this->fieldtypesConfig as $_fieldtypeIdentifier=>$_fieldtypeConfiguration)
			{
				/* @var FieldtypeConfiguration $item */
				$item = $this->fieldtypesConfigRepository->getNewItemWithData($_fieldtypeConfiguration);
				$item->setIdentifier(trim($_fieldtypeIdentifier,"."));
				$this->fieldtypesConfigRepository->addItem( $item );
			}

		}
		
		return $this->fieldtypesConfigRepository;
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
		return $this->getFieldtypesConfiguration()->findByIdentifier($fieldtypeIdentifier);
	}

}
