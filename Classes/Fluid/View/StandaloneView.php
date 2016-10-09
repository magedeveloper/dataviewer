<?php
namespace MageDeveloper\Dataviewer\Fluid\View;

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
class StandaloneView extends \TYPO3\CMS\Fluid\View\StandaloneView
{
	/**
	 * Renders template source code by a given string
	 *
	 * @param string $source Template Source Code
	 * @return string
	 */
	public function renderSource($source)
	{
		$this->setTemplateSource($source);
		return $this->render();
	}
}
