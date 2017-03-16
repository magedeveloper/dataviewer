<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue;

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
class Database extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Field Repository
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

    /**
     * Plugin Settings Service
     *
     * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
     * @inject
     */
    protected $pluginSettingsService;

    /**
     * Variable Repository
     *
     * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
     * @inject
     */
    protected $variableRepository;

    /**
     * Gets the view model
     *
     * @return \MageDeveloper\Dataviewer\Fluid\View\StandaloneView
     */
    protected function _getView()
    {
        $standaloneView = $this->objectManager->get(\MageDeveloper\Dataviewer\Fluid\View\StandaloneView::class);

        // Check for a record and inject it to the view
        if($this->getRecord() !== false)
        {
            $record = $this->getRecord();
            $pid = $this->getRecord()->getPid();
            $variables = $this->variableRepository->findByStoragePids([$pid]);

            $variableIds = [];
            foreach($variables as $_var)
                $variableIds[] = $_var->getUid();
            
            if(count($variableIds))
            {
                /* @var \MageDeveloper\Dataviewer\Controller\RecordController $controller */
                $controller = $this->objectManager->get(\MageDeveloper\Dataviewer\Controller\RecordController::class);
                $variables = $controller->prepareVariables($variableIds);
                $standaloneView->assignMultiple($variables);
            }
        
            $recordVariableName = $this->pluginSettingsService->getRecordVarName();
            $standaloneView->assign($recordVariableName, $record);
        }

        return $standaloneView;
    }

	/**
	 * Gets a solved field value
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Field $field
	 * @return array
	 */
	public function getDatabaseItemsFromField(Field $field)
	{
		$values = [];
		$fieldValues = $field->getFieldValues();

		if($field->hasDatabaseValues())
		{
			/* @var \MageDeveloper\Dataviewer\Domain\Model\FieldValue $_fieldValue */
			foreach($fieldValues as $_fieldValue)
			{
				if($_fieldValue->getType() == FieldValue::TYPE_DATABASE)
				{
				    $plainWhereClause = $_fieldValue->getWhereClause();
				    $plainWhereClause = str_replace("WHERE ", "", $plainWhereClause);
				    $renderedWhereClause = $rendered = $this->_getView()->renderSource($plainWhereClause);;
				    
				    $_fieldValue->setWhereClause($renderedWhereClause);
				
                    try 
                    {
                        $items = $this->fieldRepository->findEntriesForFieldValue($_fieldValue);
                    }
                    catch (\Exception $e)
                    {
                        $items = [];
                    }

                    $values = array_merge($values, $items);
                }
			}

		}
		
		return $values;
	}

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->getValue();
	}

	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return array
	 */
	public function getFrontendValue()
	{
		$field = $this->getField();
		return $this->getDatabaseItemsFromField($field);
	}

	/**
	 * Gets the value or values as a plain string-array for
	 * usage in different possitions to show
	 * and use it when needed as a string
	 *
	 * @return array
	 */
	public function getValueArray()
	{
		$values = $this->getFrontendValue();
		$valueArr = [];
		
		foreach($values as $_value)
			$valueArr[] = reset($_value);
		
		return $valueArr;
	}
}
