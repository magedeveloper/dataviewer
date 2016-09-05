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
class GetViewHelper extends AbstractVarViewHelper
{
	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments()
	{
		$this->registerArgument("name", "string", "Name/Identifier to fetch the value", true);
		parent::initializeArguments();
	}

	/**
	 * Creates a magic model by arguments
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function render()
	{
		$name = $this->arguments["name"];
	
		if (strpos($name, '.') === FALSE) 
		{
			if ($this->templateVariableContainer->exists( $this->arguments["name"] ))
			{
				$var = $this->templateVariableContainer->get( $this->arguments["name"] );
				return $var;
			}


			if (!$this->templateVariableContainer->exists( self::TEMPLATE_VARIABLE_IDENTIFIER ))
			{
				$model = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\MagicModel::class);
				$this->templateVariableContainer->add( self::TEMPLATE_VARIABLE_IDENTIFIER, $model);
			}

			$templateModel = $this->templateVariableContainer->get( self::TEMPLATE_VARIABLE_IDENTIFIER );

			$name = $this->arguments["name"];

			return $templateModel->getData($name);
			
		} 
		else 
		{
			$vars = $this->templateVariableContainer->getAll();
			$var = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($vars, $name);
			return $var;
		}
		
		return null;
	}
}