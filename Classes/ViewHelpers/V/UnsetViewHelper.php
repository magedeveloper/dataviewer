<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\V;

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
class UnsetViewHelper extends AbstractVarViewHelper
{
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		$this->registerArgument("name", "string", "Name/Identifier for the value", true);
		parent::initializeArguments();
	}

	/**
	 * Creates a magic model by arguments
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function render()
	{
		if ($this->templateVariableContainer->exists( self::TEMPLATE_VARIABLE_IDENTIFIER ))
		{
			$templateModel = $this->templateVariableContainer->get( self::TEMPLATE_VARIABLE_IDENTIFIER );

			$name = $this->arguments["name"];
			$templateModel->unsetData($name);
		}

		return;
	}
}