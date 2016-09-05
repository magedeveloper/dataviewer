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
class SetViewHelper extends AbstractVarViewHelper
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
		$this->registerArgument("value", "mixed", "The value to be set", false, "");

		parent::initializeArguments();
	}

	/**
	 * Creates a magic model by arguments
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function render()
	{
		if (!$this->templateVariableContainer->exists( self::TEMPLATE_VARIABLE_IDENTIFIER ))
		{
			$model = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\MagicModel::class);
			$this->templateVariableContainer->add( self::TEMPLATE_VARIABLE_IDENTIFIER, $model);
		}

		$templateModel = $this->templateVariableContainer->get( self::TEMPLATE_VARIABLE_IDENTIFIER );

		if ($templateModel instanceof \MageDeveloper\Dataviewer\Domain\Model\MagicModel)
		{

			$name	= $this->arguments["name"];
			$value 	= $this->arguments["value"];

			$templateModel->setData($name, $value);
		}

		return;
	}
}