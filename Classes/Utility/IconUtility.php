<?php
namespace MageDeveloper\Dataviewer\Utility;

use MageDeveloper\Dataviewer\Utility\FieldtypeConfigurationUtility;
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

class IconUtility
{
	/**
	 * Gets all icons from the typo3/gfx folder
	 *
	 * @param bool $includePrefix
	 * @param string $prefix
	 * @return array
	 */
	public static function getIcons($includePrefix = true, $prefix = "extensions-dataviewer-")
	{
		if(!$includePrefix)
			$prefix = "";

		$paths   = [];
		$paths[] = "EXT:dataviewer/Resources/Public/Icons/Datatype";
		$icons   = [];

		foreach($paths as $_path)
		{
			// We check all paths for icons and add them to the registry
			$path = GeneralUtility::getFileAbsFileName($_path);
			$files = GeneralUtility::getAllFilesAndFoldersInPath([], $path, "gif,png");

			foreach($files as $_iconFile)
			{
				$filename = basename($_iconFile);
				$_path = trim($_path, "/");

				$code           = \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($filename);

				$code 			= "{$prefix}{$code}";
				$icons[$code] 	= $path."/".$filename;
			}
		}

		// Default Icon
		$icons["{$prefix}default"] 			= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Default.gif";
		$icons["{$prefix}datatype"] 		= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Datatype.gif";
		$icons["{$prefix}field"] 			= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Field.gif";
		$icons["{$prefix}fieldvalue"] 		= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/FieldValue.gif";
		$icons["{$prefix}record"] 			= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Record.gif";
		$icons["{$prefix}recordvalue"] 		= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/RecordValue.gif";
		$icons["{$prefix}variable"] 		= "EXT:dataviewer/Resources/Public/Icons/Domain/Model/Variable.gif";

		return $icons;
	}

	/**
	 * Gets all classes that are equivalent to the icon
	 *
	 * @return array
	 */
	public static function getClasses()
	{
		$icons = self::getIcons();
		$classes = [];

		foreach($icons as $_hash=>$_iconFile)
		{
			$filename 		= basename($_iconFile);
			$code           = \MageDeveloper\Dataviewer\Utility\StringUtility::createCodeFromString($filename);
			$classes[$code] = "extensions-dataviewer-{$code}";
		}

		// Default Icon
		$classes["default"] = "extensions-dataviewer-default";

		return $classes;
	}

	/**
	 * Gets an specific icon by hash
	 *
	 * @param string $hash
	 * @return string
	 */
	public static function getIconByHash($hash)
	{
		$files = self::getIcons();

		foreach($files as $_hash=>$icon)
		{
			if ($_hash == $hash)
				return $icon;
		}

		return;
	}

	/**
	 * Gets all field type icons
	 *
	 * @return array
	 */
	public static function getFieldtypeIcons()
	{
		$icons = FieldtypeConfigurationUtility::getIcons();
		return $icons;
	}

	/**
	 * Gets the icon url for a specific
	 * fieldtype
	 *
	 * @param string $fieldtype
	 * @return string
	 */
	public static function getFieldtypeIcon($fieldtype)
	{
		$icons = FieldtypeConfigurationUtility::getIcons();

		if(isset($icons[$fieldtype]))
			return $icons[$fieldtype];

		return $icons["default"];
	}

	/**
	 * Gets all field type icon classes
	 *
	 * @return array
	 */
	public static function getFieldtypeIconClasses()
	{
		$fieldtypes = FieldtypeConfigurationUtility::getFieldtypes();

		$classes = [];
		foreach($fieldtypes as $_fieldtype)
		{
			$ft = strtolower($_fieldtype);
			$classes[$ft] = "extensions-dataviewer-{$ft}";
		}

		// Default Icon
		$classes["default"] = "extensions-dataviewer-default";

		return $classes;
	}

	/**
	 * Registers all extension icons to the IconRegistry
	 *
	 * @return void
	 */
	public static function registerIcons()
	{
		$mainIcons				 = self::getIcons();
		$fieldTypeIcons 		 = self::getFieldtypeIcons();
		$icons 					 = array_merge($mainIcons, $fieldTypeIcons);
		$bitmapProviderClassName = \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class;


		/* @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

		foreach($icons as $identifier=>$source)
			$iconRegistry->registerIcon($identifier, $bitmapProviderClassName, ["source" => $source]);

		return;
	}
}
