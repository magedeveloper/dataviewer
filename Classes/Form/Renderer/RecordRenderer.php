<?php

namespace MageDeveloper\Dataviewer\Form\Renderer;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

use MageDeveloper\Dataviewer\Domain\Model\Field as Field;
use MageDeveloper\Dataviewer\Domain\Model\Record as Record;
use MageDeveloper\Dataviewer\Domain\Model\Datatype as Datatype;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;


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
class RecordRenderer extends AbstractRenderer implements RendererInterface
{
	/**
	 * Field Renderer
	 *
	 * @var \MageDeveloper\Dataviewer\Form\Renderer\FieldRenderer
	 * @inject
	 */
	protected $fieldRenderer;

	/**
	 * Form Result Compiler
	 *
	 * @var \TYPO3\CMS\Backend\Form\FormResultCompiler
	 * @inject
	 * @internal
	 */
	protected $formResultCompiler;

	/**
	 * Icon Factory
	 * 
	 * @var \TYPO3\CMS\Core\Imaging\IconFactory
	 * @inject
	 */
	protected $iconFactory;

	/**
	 * Constructor
	 *
	 * @return RecordRenderer
	 */
	public function __construct()
	{
		parent::__construct();
		$this->formResultCompiler 	= $this->objectManager->get(\TYPO3\CMS\Backend\Form\FormResultCompiler::class);
		$this->fieldRenderer 		= $this->objectManager->get(\MageDeveloper\Dataviewer\Form\Renderer\FieldRenderer::class);
		$this->iconFactory			= $this->objectManager->get(\TYPO3\CMS\Core\Imaging\IconFactory::class);
	}

	/**
	 * 
	 */
	public function resetFormResultCompiler()
	{
		$this->formResultCompiler = $this->objectManager->get(\TYPO3\CMS\Backend\Form\FormResultCompiler::class);
	}

	/**
	 * Generates the record form for dataviewer records
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Backend\Form\Element\UserElement $userElement
	 * @return string
	 */
	public function render(&$params, &$userElement)
	{
		$contentHtml = "";

		$row = $params["row"];
		$datatypeUid = (int)reset($row["datatype"]);
		
		// For new record links with given datatype id
		if($datatypeUid <= 0 && isset($_GET["datatype"])) 
		{
			$datatypeUid = (int)$_GET["datatype"];
			$params["row"]["datatype"] = $datatypeUid;
		}
		
		// Error when no datatype is selected
		if (!is_numeric($datatypeUid) || $datatypeUid <= 0)
		{
			$message = Locale::translate("please_select_datatype_to_continue");
			$this->addBackendFlashMessage($message);
			return;
		}

		/* @var \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype */
		$datatype = $this->datatypeRepository->findByUid($datatypeUid, false);

		// Error when datatype wasn't found
		if (!$datatype instanceof \MageDeveloper\Dataviewer\Domain\Model\Datatype)
		{
			$message = Locale::translate("datatype_not_found");
			$this->addBackendFlashMessage($message);
			return;
		}

		// Error if datatype has not any fields
		if (!$datatype->hasFields())
		{
			$message = Locale::translate("datatype_has_no_fields");
			return $this->getMessageHtml($message, null, FlashMessage::INFO);
		}

		// Id of the record
		$recordUid = $row["uid"];

		/* @var Record $record */
		$record = $this->recordRepository->findByUid($recordUid, false);

		// Create a blank record if the record doesn't exist
		if (!$record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
		{
			// Generating a blank record
			$record = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Model\Record::class);
			/*
			// Setting the datatype to the record model
			$record->setDatatype($datatype);
			$this->recordRepository->add($record);
			
			$persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
			// Storing the record
			$persistenceManager->persistAll();*/
		}

		// Add stylesheet file to the formResultCompiler
		$css = ExtensionManagementUtility::extRelPath("dataviewer") . "Resources/Public/Css/dataviewer-backend.css";
		$this->formResultCompiler->mergeResult(
			array("stylesheetFiles" => array($css),
				  "additionalJavaScriptPost" => array(),
				  "additionalJavaScriptSubmit" => array(),
				  "additionalHiddenFields" => array(),
			)
		);

		///////////////////////////////////////////////////////////////////////
		// Manipulate the field conf to get the record id in the record_content
		///////////////////////////////////////////////////////////////////////
		$baseFormName				= $params["itemFormElName"];
		$itemFormElName 			= $baseFormName . "[{$record->getUid()}]";
		$baseRecordFormName 		= str_replace("[record_content]", "", $baseFormName);
		$params["itemFormElName"] 	= $baseFormName . "[{$record->getUid()}]";

		// Assign current title
		$record->setTitle($row["title"]);
		
		// Adding the current record to the field renderer
		$this->fieldRenderer->setRecord($record)
							->setParameterArray($params);

		if (!$record->hasTitleField())
			$contentHtml .= $this->renderRecordTitleField($record, str_replace("[record_content]", "", $baseFormName));

		///////////////////////////////////////////////////////////////////////
		// FIELD RENDERING
		///////////////////////////////////////////////////////////////////////
		$menuItems = array(); $bottomParts = array(); $topParts = array();
		foreach($datatype->getFields() as $_field)
		{
			$this->fieldRenderer->setField($_field);
		
			/* @var Field $_field */
			$fieldHtml 	= "";

			if ($_field->hasFieldValues())
			{
				// We only generate to field content if
				// we found field values
				try 
				{
					$renderResults = $this->fieldRenderer->render();
				} 
				catch (\Exception $e)
				{
					$fieldHtml .= "<br /><div class=\"alert alert-danger\" role=\"alert\">{$e->getMessage()}<br />{$e->getFile()}:{$e->getLine()}</div>";
				}
				if (is_array($renderResults))
				{
					if ($renderResults["html"])
						$fieldHtml .= $renderResults["html"];
						
					$this->formResultCompiler->mergeResult($renderResults);
					$topParts[$_field->getUid()] = $this->formResultCompiler->JStop();
					$bottomParts[$_field->getUid()] = $this->formResultCompiler->printNeededJSFunctions();
				
				}
			}
			else
			{
				$message = Locale::translate("field_has_no_field_values", $_field->getFrontendLabel());
				$fieldHtml .= "<br /><div class=\"alert alert-danger field-error\" role=\"alert\">{$message}</div>";
			}
			
			// Tab Information
			$tabName = $_field->getTabName();
			$matches = array();
			preg_match('/^((?P<id>[0-9]{1,5})[\:.\-_])?(?P<label>.*)$/', $tabName, $matches);
			
			$tabId 	 = $matches["id"];
			$label	 = $matches["label"];
			$tabId	= ($tabId>0)?$tabId:$label;
			
			$menuItems[$tabId]["label"] = $label;
			$menuItems[$tabId]["content"] .= $fieldHtml;
			
			//$menuItems[$tabId]["description"] = "dsafdsa";
			
			$this->resetFormResultCompiler();

		} // END FOREACH

		// Sorting
		ksort($menuItems);

		// Remove empty menuItems
		foreach($menuItems as $_tI=>$_mI) {
			if ($_mI["content"] == "")
				unset($menuItems[$_tI]);
			else				
				$menuItems[$_tI]["content"] .= "<div class=\"clear\"></div>";
		}
		
		///////////////////////////////////////////////////////////////////////

		$backgroundColor = ($record->getDatatype() && $record->getDatatype()->getColor())?"style=\"background-color:{$record->getDatatype()->getColor()};\"":"";

		// Finalization
		$html =
			"<div class=\"dataviewer-record dataviewer-record-{$record->getUid()}\" $backgroundColor>" .
			$this->renderHeader($datatype)						.
			//$this->formResultCompiler->JStop() 					.
			implode("\r\n", $topParts)							.
			$contentHtml 										.
			$this->renderTabMenu($menuItems, "dataviewer-tabs")	.
			implode("\r\n", $bottomParts)						.
			//$this->formResultCompiler->printNeededJSFunctions()	.
			"<input type=\"hidden\" name=\"{$baseRecordFormName}[datatype]\" value=\"{$datatype->getUid()}\" />".
			"<div class=\"clear\"></div>"						.

			"</div>"
		;
		
		return $html;
	}

	/**
	 * Renders the header of a given datatype
	 *
	 * @param Datatype $datatype
	 * @return string
	 */
	public function renderHeader(Datatype $datatype)
	{
		$header = "";
		$header .= "<div class=\"dataviewer-header\">";

		$iconUrl = \MageDeveloper\Dataviewer\Utility\IconUtility::getIconByHash($datatype->getIcon());

		$uid = $datatype->getUid();
		$header .= "<img src=\"../typo3/{$iconUrl}\" alt=\"Datatype: {$uid}\" title=\"Datatype: {$uid}\" border=\"0\" style=\"float:left; margin: 5px 5px 0 0; \"/>";
		$header .= "<h1>{$datatype->getName()}</h1>";

		// Information Message
		if (strlen($datatype->getDescription()) > 0)
			$header .= $this->getMessageHtml($datatype->getDescription(), Locale::translate("tx_dataviewer_domain_model_datatype.description"), FlashMessage::INFO);

		$header .= "<hr style=\"color:#c0c0c0; background-color:#c0c0c0; height:1px; border:0;\" />";
		$header .= "</div>";

		return $header;
	}

	/**
	 * Renders the title input field
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record
	 * @param string $fieldName
	 * @return string
	 */
	public function renderRecordTitleField(Record $record, $fieldName)
	{
		$title = "";
		$title .= "<div class=\"dataviewer-record-title\">";

		$recordTitle = $record->getTitle();
		$titleLabel = Locale::translate("record_title");

		$placeholder = "";
		
		if ($record->getDatatype())
			$placeholder = $record->getDatatype()->getName() . " " . Locale::translate("entry", $record->getUid());

		$title .= "<label for=\"{$fieldName}[title]\">{$titleLabel}:</label>";
		$title .= "<input name=\"{$fieldName}[title]\" value=\"{$recordTitle}\" placeholder=\"{$placeholder}\" class=\"dataviewer-record-title-input\" />";

		$title .= "</div>";

		return $title;
	}

	/**
	 * Render tabs with label and content. Used by TabsContainer and FlexFormTabsContainer.
	 * Re-uses the template Tabs.html which is also used by ModuleTemplate.php.
	 *
	 * @param array $menuItems Tab elements, each element is an array with "label" and "content"
	 * @param string $domId DOM id attribute, will be appended with an iteration number per tab.
	 * @return string
	 */
	protected function renderTabMenu(array $menuItems, $domId, $defaultTabIndex = 1)
	{
		$templatePathAndFileName = 'EXT:backend/Resources/Private/Templates/DocumentTemplate/Tabs.html';
		$view = GeneralUtility::makeInstance(StandaloneView::class);
		$view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templatePathAndFileName));
		$view->assignMultiple(array(
			'id' => $domId,
			'items' => $menuItems,
			'defaultTabIndex' => $defaultTabIndex,
			'wrapContent' => false,
			'storeLastActiveTab' => true,
		));
		return $view->render();
	}

}
