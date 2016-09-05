<?php
namespace MageDeveloper\Dataviewer\CmsLayout;

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
interface CmsLayoutInterface
{
	/**
	 * Gets the final rendered html code for the backend plugin
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Backend\View\PageLayoutView $pObj
	 * @return string
	 */
	public function getBackendPluginLayout(array $params, \TYPO3\CMS\Backend\View\PageLayoutView $pObj);
}
