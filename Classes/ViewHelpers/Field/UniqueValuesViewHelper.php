<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Field;

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
class UniqueValuesViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Fieldtype Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Gets unique values for a field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return array
	 */
	public function render(\MageDeveloper\Dataviewer\Domain\Model\Field $field)
	{
		$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration($field->getType());
		$class = $fieldtypeConfiguration->getFieldClass();

		if($this->objectManager->isRegistered($class))
		{
			/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\FieldtypeInterface $fieldtypeModel */
			$fieldtypeModel = $this->objectManager->get($class);

			// We need to init a new blank record here
			$record = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\Record::class);

			$fieldtypeModel->setField($field);
			$fieldtypeModel->setRecord($record);

			$values = $fieldtypeModel->getAllFieldItems($field);

			return $values;
		}

		return [];
	}

}
