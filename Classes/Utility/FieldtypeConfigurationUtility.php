<?php
namespace MageDeveloper\Dataviewer\Utility;

use MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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

class FieldtypeConfigurationUtility
{
	/**
	 * Fieldtypes Configuration Array
	 * 
	 * @var array
	 */
	public static $fieldtypesConfiguration = [];

	/**
	 * Gets the plugin settings service
	 * 
	 * @return \MageDeveloper\Dataviewer\Service\Settings\PluginSettingsService
	 */
	public static function getFieldtypeSettingsService()
	{
		/* @var ObjectManager $objectManager */
		/* @var FieldtypeSettingsService $fieldtypeSettingsService */
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
		$fieldtypeSettingsService = $objectManager->get(FieldtypeSettingsService::class);
		return $fieldtypeSettingsService;
	}

	/**
	 * Gets the fieldtypes configuration from the
	 * typoscript settings
	 * 
	 * @return array
	 */
	public static function getFieldtypesConfiguration()
	{
		if (empty(self::$fieldtypesConfiguration))
		{
			$fieldtypeSettingsService = self::getFieldtypeSettingsService();
			self::$fieldtypesConfiguration = $fieldtypeSettingsService->getFieldtypesConfiguration();
		}
	
		return self::$fieldtypesConfiguration;
	}

	/**
	 * Gets a fieldtype configuration by a fieldtype
	 * 
	 * @param string $fieldtype
	 * @return \MageDeveloper\Dataviewer\Domain\Model\FieldtypeConfiguration
	 */
	public static function getFieldtypeConfiguration($fieldtype)
	{
		/* @var \MageDeveloper\Dataviewer\Domain\Repository\FieldtypeConfigurationRepository $fieldtypesConfigurationRepository */
		$fieldtypesConfigurationRepository = self::getFieldtypesConfiguration();
		foreach($fieldtypesConfigurationRepository as $_ft)
			if ($_ft->getIdentifier() == $fieldtype)
				return $_ft;

		return $fieldtypesConfigurationRepository->getNewEmptyItem();
	}
	
	/**
	 * Gets an integer value for a selection array
	 *
	 * @return array
	 */
	public static function getDsConfig()
	{
		$defaultDsConfiguration = [
			"default" => 'FILE:EXT:dataviewer/Configuration/FlexForms/Fieldtype/Empty.xml',
		];

		$fieldtypesConfiguration = self::getFieldtypesConfiguration();
		
		$dsConfig = [];
		foreach($fieldtypesConfiguration as $_ftC)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $_ftC */
			if ($_ftC->getFlexConfiguration())
				$dsConfig[$_ftC->getIdentifier()] = "FILE:".$_ftC->getFlexConfiguration();
		}

		$configuration = array_merge($defaultDsConfiguration, $dsConfig);
		return $configuration;
	}

	/**
	 * Gets icons of all fieldtypes
	 *
	 * @return array
	 */
	public static function getIcons()
	{
		$fieldtypesConfiguration = self::getFieldtypesConfiguration();

		//PATH_site
		//PATH_typo3
		
		$icons = [];
		foreach($fieldtypesConfiguration as $_ftC)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $_ftC */
			$icon = $_ftC->getIcon();
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $_ftC */
			$icons["extensions-dataviewer-".$_ftC->getIdentifier()] = $icon;
		}
		
		return $icons;
	}

	/**
	 * Gets an array with the registered fieldtypes
	 * 
	 * @return array
	 */
	public static function getFieldtypes()
	{
		$fieldtypesConfiguration = self::getFieldtypesConfiguration();
		
		$fieldtypes = [];
		foreach($fieldtypesConfiguration as $_ftC)
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $_ftC */
			$fieldtypes[] = $_ftC->getIdentifier();
		}
		
		return $fieldtypes;	
	}
}
