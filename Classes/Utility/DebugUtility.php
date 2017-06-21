<?php
namespace MageDeveloper\Dataviewer\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use MageDeveloper\Dataviewer\Fluid\View\StandaloneView;

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

class DebugUtility
{
	/**
	 * Gets a standalone view instance
	 * 
	 * @return StandaloneView
	 */
	public static function getStandaloneView()
	{
		/* @var ObjectManager $objectManager */
		$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
		/* @var StandaloneView $view */
		$view = $objectManager->get(StandaloneView::class);
		return $view;
	}

	/**
	 * Generates a class name by a table name
	 *
	 * @param mixed $variable
	 * @return string
	 */
	public static function debugVariable($variable, $title = null)
	{
		$view = self::getStandaloneView();
		$view->assign("variable", $variable);
		
		$titleAttr = "";
		if(!is_null($title))
			$titleAttr = "title=\"{$title}\"";
		
		$source = "<f:debug inline=\"1\" {$titleAttr}>{variable}</f:debug>";
		$rendered = $view->renderSource($source);
		return $rendered;
	}

	/**
	 * Logs content to a file that will be created in
	 * the root of the instance and has the current date
	 * in its name
	 * 
	 * @param string $content
	 * @param bool $clear Clear the file before write
	 * @return void
	 */
	public static function log($content, $clear = false)
	{
		$file = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName("".date("Y-m-d")."_dataviewer.log");

		if($clear == true)
			file_put_contents($file, "");
	
		$dateStr = date("Y-m-d H:i:s");
		file_put_contents($file, "___[{$dateStr}]___".str_repeat("_", 20)."\r\n", FILE_APPEND);
		file_put_contents($file, $content."\r\n", FILE_APPEND);
	}
}
