<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Configuration;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

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
class GetViewHelper extends AbstractViewHelper
{
	/**
	 * Gets a configuration setting
	 *
	 * @param \string $path Path to configuration setting
	 * @return void
	 */
	public function render($path)
	{
		return $this->pluginSettingsService->getConfiguration($path);
	}
}