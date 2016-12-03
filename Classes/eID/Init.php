<?php
namespace MageDeveloper\Dataviewe\eID;

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

/**
 * This is the main initialization procedure to render ajax requests made with
 * the DataViewerAjaxRequest-Class
 */ 
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("dataviewer") . "Classes/eID/Dispatcher.php";

/* @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

/* @var \MageDeveloper\Dataviewer\eID\Dispatcher $dispatcher */
$dispatcher 	= $objectManager->get(\MageDeveloper\Dataviewer\eID\Dispatcher::class);

if(	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
	) 
{
	echo $dispatcher->initCallArguments()->dispatch();
}
