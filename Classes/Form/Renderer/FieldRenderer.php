<?php
namespace MageDeveloper\Dataviewer\Form\Renderer;

use MageDeveloper\Dataviewer\Utility\FieldtypeConfigurationUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

use MageDeveloper\Dataviewer\Domain\Model\Field as Field;
use MageDeveloper\Dataviewer\Domain\Model\Datatype as Datatype;
use MageDeveloper\Dataviewer\Domain\Model\Record as Record;
use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValue;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue as RecordValue;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;

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
class FieldRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Record to use for values
	 *
	 * @var Record
	 */
	protected $record;

	/**
	 * Field
	 * 
	 * @var Field
	 */
	protected $field;

	/**
	 * Parameter Array
	 *
	 * @var array
	 */
	protected $parameterArray = [];

	/**
	 * Rendered tca
	 * 
	 * @var array
	 */
	protected $tca = [];

	/**
	 * Fieldtype Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\FieldtypeSettingsService
	 * @inject
	 */
	protected $fieldtypeSettingsService;

	/**
	 * Reflection Service
	 * 
	 * @var \TYPO3\CMS\Extbase\Reflection\ReflectionService
	 * @inject
	 */
	protected $reflectionService;

	/**
	 * Sets the record
	 *
	 * @param Record $record
	 * @return FieldRenderer
	 */
	public function setRecord(Record $record)
	{
		$this->record = $record;
		return $this;
	}

	/**
	 * Gets the record
	 *
	 * @return Record
	 */
	public function getRecord()
	{
		return $this->record;
	}

	/**
	 * Gets the field
	 * 
	 * @return Field
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * Sets the field
	 * 
	 * @param Field $field
	 */
	public function setField(Field $field)
	{
		$this->field = $field;
	}
	
	/**
	 * Gets the parameter array
	 *
	 * @return array
	 */
	public function getParameterArray()
	{
		return $this->parameterArray;
	}

	/**
	 * Sets the parameter array
	 * 
	 * @param array $parameterArray
	 * @return void
	 */
	public function setParameterArray(array $parameterArray = [])
	{
		$this->parameterArray = $parameterArray;
	}

	/**
	 * Gets a value from the
	 * parameter array
	 *
	 * @param string $parameter
	 * @return mixed
	 */
	public function getParameter($parameter)
	{
		if (array_key_exists($parameter, $this->parameterArray))
			return $this->parameterArray[$parameter];

		return;
	}

	/**
	 * Sets a parameter value to the 
	 * parameter array
	 * 
	 * @param string $parameter
	 * @param mixed $value
	 * @return void
	 */
	public function setParameter($parameter, $value)
	{
		$this->parameterArray[$parameter] = $value;
	}

	/**
	 * Gets the base item form element name
	 *
	 * @return string
	 */
	public function getFormElementName()
	{
		return (string)$this->getParameter("itemFormElName");
	}

	/**
	 * Sets the form element name
	 * 
	 * @param string $formElementName
	 * @return void
	 */
	public function setFormElementName($formElementName)
	{
		$this->parameterArray["itemFormElName"] = $formElementName;
	}

	/**
	 * Renders a field header
	 *
	 * @return string
	 */
	public function renderHeader()
	{
		// Process help html for popup toolbox
		$help = $this->_getHelpHtml();
	
		$html = "";
		$html .= "<label for=\"data[tx_dataviewer_domain_model_record][{$this->getRecord()->getUid()}][{$this->getField()->getFieldName()}]\"><span class=\"t3-help-link\" data-description=\"{$help}\" data-title=\"{$this->getField()->getFrontendLabel()}\" href=\"#\"><abbr class=\"t3-help-teaser\"><strong>{$this->getField()->getFrontendLabel()}</strong></abbr></span></label>";

		if (strlen($this->getField()->getDescription()) > 0)
			$html .= $this->getMessageHtml($this->getField()->getDescription(), null, FlashMessage::NOTICE);

		if (!$this->getField()->hasFieldValues()) 
			$html .= $this->getMessageHtml(Locale::translate("field_has_no_field_values"), null, FlashMessage::ERROR);

		return $html;
	}

	/**
	 * Processes the html code for the popup help on each field label
	 * 
	 * @return string
	 */
	protected function _getHelpHtml()
	{
		$fieldtype = $this->getField()->getType();
		$fieldtypeConfiguration = $this->fieldtypeSettingsService->getFieldtypeConfiguration( $fieldtype );
		$valueClass = $fieldtypeConfiguration->getValueClass();

		if (!$this->objectManager->isRegistered($valueClass))
			$valueClass = \MageDeveloper\Dataviewer\Form\Fieldvalue\General::class;

		/* @var \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface $fieldValue */
		$fieldvalue = $this->objectManager->get($valueClass);

		$returnType = null;
		if($fieldvalue instanceof \MageDeveloper\Dataviewer\Form\Fieldvalue\FieldvalueInterface)
		{
			$methodTagsValues = $this->reflectionService->getMethodTagsValues($valueClass, "getFrontendValue");
			if(isset($methodTagsValues["return"]))
			{
				$returnType = reset( $methodTagsValues["return"] );
			}

		}
		
		$iconIdentifier = "extensions-dataviewer-{$fieldtype}";
		$icon = $this->iconFactory->getIcon($iconIdentifier);
		$icon->setSize(Icon::SIZE_SMALL);
		
		$iconHtml = htmlentities($icon->render());
		
		$help = "";
		$help = "
			{$iconHtml}&nbsp;".strtoupper($fieldtype)."
			<hr style='background-color:lightgrey;padding:0;margin:6px 0;' />
			Fluid: {$this->getField()->getIdentification()}
			<br />
			Id: {$this->getField()->getUid()}
		";

		if(!is_null($returnType))
		{
			$help .= "<hr style='background-color:lightgrey;padding:0;margin:6px 0;' />@return {$returnType}";
		}
		
		return $help;
	}

	/**
	 * Directly renders a field
	 * 
	 * @return array
	 */
	public function render()
	{
		/* @var \MageDeveloper\Dataviewer\Domain\Model\Fieldtype $fieldtype */
		$fieldtypeConfiguration = FieldtypeConfigurationUtility::getFieldtypeConfiguration($this->getField()->getType());
		$fieldClass = $fieldtypeConfiguration->getFieldClass();

		/* @var $fieldObj \MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype */
		if ($fieldClass == "" || !$this->objectManager->isRegistered($fieldClass))
		{
			$message = Locale::translate("field_class_does_not_exist", [$fieldClass]);
			$this->addBackendFlashMessage($message, null, FlashMessage::ERROR);
			return false;
		}

		/* @var \MageDeveloper\Dataviewer\Form\Fieldtype\AbstractFieldtype $fieldtype */
		$row = $this->getParameter("row");
		$fieldtype = $this->objectManager->get($fieldClass);
		$fieldtype->setRecord($this->getRecord());
		$fieldtype->setField($this->getField());
		$fieldtype->setRecordId($row["uid"]);
		$fieldtype->setPid($row["pid"]);
		
		$rendered = $fieldtype->render();
		
		if (isset($rendered["html"]) && $rendered["html"])
			$rendered["html"] = $this->_wrapFieldDiv($rendered["html"]);
		
		return $rendered;
	}

	/**
	 * Gets debug information about a field
	 * 
	 * @return string
	 */
	public function getDebugInformation()
	{
		$debugInformation = "";
		$debugInformation .= "DATATYPE: {$this->getRecord()->getDatatype()->getUid()}";
		$debugInformation .= " | ";
		$debugInformation .= "RECORD: {$this->getRecord()->getUid()}";
		$debugInformation .= " | ";
		$debugInformation .= "FIELD: {$this->getField()->getUid()}";
		
		return $this->getMessageHtml($debugInformation, "Debug", FlashMessage::WARNING);
	}

	/**
	 * Wraps default div around a field html
	 *
	 * @param string $html
	 * @return string
	 */
	protected function _wrapFieldDiv($html)
	{
		// Preparation for tooltip functionality
		// data-toggle="tooltip" data-html="true" data-placement="top" data-title="{$this->getField()->getDescription()}"
		
		$wrapped = "";
		$wrapped .= "<div class=\"dataviewer-field dataviewer-field-{$this->getField()->getCode()} {$this->getField()->getColumnWidth()}\">";

		// Header
		if ($this->getField()->getShowTitle())
			$wrapped .= $this->renderHeader();

		$wrapped .= "<div class=\"t3js-formengine-validation-marker t3js-formengine-palette-field\">";
		$wrapped .= "<span class=\"t3-form-field-container\">";
		$wrapped .= "<div class=\"t3-form-field-item\">";

		$wrapped .= $html;
		//$wrapped .= $this->getDebugInformation();
			
		$wrapped .= "</div>";
		$wrapped .= "</span>";
		$wrapped .= "</div>";

		$wrapped .= "</div>";

		return $wrapped;
	}

	/**
	 * Gets the rendered tca
	 * 
	 * @return array
	 */
	public function getTca()
	{
		return $this->tca;
	}
}
