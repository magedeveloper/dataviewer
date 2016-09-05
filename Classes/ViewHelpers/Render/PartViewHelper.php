<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Render;

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
class PartViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Render Method
	 *
	 * @param mixed $part
	 * @return void
	 */
	public function render($part)
	{
		$partClass = get_class($part);

		/* @var \MageDeveloper\Dataviewer\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);

		switch($partClass)
		{
			case \MageDeveloper\Dataviewer\Domain\Model\RecordValue::class:
				/* @var \MageDeveloper\Dataviewer\Domain\Model\RecordValue $part */
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Value $value */
				$record = $part->getRecord();
				$field  = $part->getField();
				$values = $record->getValues();

				foreach($values as $value)
					if($value->getField()->getUid() == $field->getUid())
						$part = $value;

				if(!$part instanceof \MageDeveloper\Dataviewer\Domain\Model\Value)
					break;
					
			case \MageDeveloper\Dataviewer\Domain\Model\Value::class:
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Value $part */
				$field = $part->getField();
				$templateFile = $field->getTemplatefile();
				$view->setTemplatePathAndFilename($templateFile);
				$view->assign("part", $part);
				break;
			case \MageDeveloper\Dataviewer\Domain\Model\Record::class:
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Record $part */
				$templateFile = $part->getDatatype()->getTemplatefile();
				$view->setTemplatePathAndFilename($templateFile);
				$view->assign("record", $part);
				break;
			default:
				break;
		}

		// Assign all previous assigned variables to the new view
		$view->assignMultiple($this->templateVariableContainer->getAll());

		if($view->canRender($this->controllerContext) && $view->getTemplatePathAndFilename())
			return $view->render();

		return;
	}
}
