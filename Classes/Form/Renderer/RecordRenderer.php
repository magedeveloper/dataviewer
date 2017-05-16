<?php

namespace MageDeveloper\Dataviewer\Form\Renderer;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use MageDeveloper\Dataviewer\Domain\Model\Field as Field;
use MageDeveloper\Dataviewer\Domain\Model\Record as Record;
use MageDeveloper\Dataviewer\Domain\Model\Datatype as Datatype;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
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
	 * RecordValue Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\RecordValueSessionService
	 * @inject
	 */
	protected $recordValueSessionService;

	/**
	 * Backend Access Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Backend\BackendAccessService
	 * @inject
	 */
	protected $backendAccessService;
	
	/**
	 * Constructor
	 *
	 * @return RecordRenderer
	 */
	public function __construct()
	{
		parent::__construct();
		$this->formResultCompiler 			= $this->objectManager->get(\TYPO3\CMS\Backend\Form\FormResultCompiler::class);
		$this->fieldRenderer 				= $this->objectManager->get(\MageDeveloper\Dataviewer\Form\Renderer\FieldRenderer::class);
		$this->recordValueSessionService 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\RecordValueSessionService::class);
		$this->backendAccessService			= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Backend\BackendAccessService::class);
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
	    $start = microtime(true);
	
		$contentHtml = "";

		$row = $params["row"];
		
		// Id of the record
		$recordUid = $row["uid"];
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
			
			// We only deliver a script for the datatype selection box,
			// that helps us determine the selected datatype, when
			// the record is loaded again
			return $this->_getDatatypeSelectionScriptHtml($recordUid);
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

		///////////////////////////////////////////////////////////////////////
		// Manipulate the field conf to get the record id in the record_content
		///////////////////////////////////////////////////////////////////////
		$baseFormName				= $params["itemFormElName"];
		$itemFormElName 			= $baseFormName . "[{$record->getUid()}]";
		$baseRecordFormName 		= str_replace("[record_content]", "", $baseFormName);
		$params["itemFormElName"] 	= $baseFormName . "[{$record->getUid()}]";

		// Assign current title
		$record->setTitle($row["title"]);
		
		// Inject the title from the session
		if(!is_numeric($recordUid) && $row["title"] == "")
		{
			$sessionTitle = $this->recordValueSessionService->getStoredValueForRecordIdAndFieldId($recordUid, "title");
			$record->setTitle($sessionTitle);
		}
		
		// Assign the datatype to the record
		$record->setDatatype($datatype);

		/* @var PageRenderer $pageRenderer */
		$pageRenderer = $this->objectManager->get(PageRenderer::class);
		
		// Add tooltip possibility to the page
		$pageRenderer->loadRequireJsModule("TYPO3/CMS/Recordlist/Tooltip");

		// Adding the current record to the field renderer
		$this->fieldRenderer->setRecord($record)
							->setParameterArray($params);

		if (!$record->hasTitleField())
			$contentHtml .= $this->renderRecordTitleField($record, str_replace("[record_content]", "", $baseFormName));


		///////////////////////////////////////////////////////////////////////
		// FIELD RENDERING
		///////////////////////////////////////////////////////////////////////
		$tabConfigurationArray = $datatype->getTabConfigurationArray();
		$renderedFields = []; $bottomParts = []; $topParts = [];
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

					if(\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8007000)
						$this->formResultCompiler->addCssFiles();
					else
						$topParts[$_field->getUid()] = $this->formResultCompiler->JStop();

					$bottomParts[$_field->getUid()] = $this->formResultCompiler->printNeededJSFunctions();
				}
			}
			else
			{
				$message = Locale::translate("field_has_no_field_values", $_field->getFrontendLabel());
				$fieldHtml .= "<br /><div class=\"alert alert-danger field-error\" role=\"alert\">{$message}</div>";
			}
			
			$renderedFields[$_field->getUid()] = $fieldHtml;
			$this->resetFormResultCompiler();

		} // END FOREACH
		
		// Prepare Tabs
		foreach($tabConfigurationArray as $i=>$_tabConfigArr)
		{
			foreach($tabConfigurationArray[$i]["content"] as $_id=>$content)
				if(array_key_exists($_id, $renderedFields))
					$tabConfigurationArray[$i]["content"][$_id] = $renderedFields[$_id];
				else
					unset($tabConfigurationArray[$i]["content"][$_id]);

			$content = implode("", $tabConfigurationArray[$i]["content"]);
			
			if($content)
			{
				$tabConfigurationArray[$i]["content"] = $content;
				$tabConfigurationArray[$i]["content"].="<div style=\"clear:both;\"></div>";
			}
			else
			{
				// No content, so we remove the tab here
				unset($tabConfigurationArray[$i]);
			}
				
		}

		// Backward compatibility
		if(empty($tabConfigurationArray))
		{
			$message = Locale::translate("please_configure_tabs_and_assign_fields");
			return $this->getMessageHtml($message, null, FlashMessage::WARNING);
		}
		
		///////////////////////////////////////////////////////////////////////

		$backgroundColor = ($record->getDatatype() && $record->getDatatype()->getColor())?"style=\"background-color:{$record->getDatatype()->getColor()};\"":"";

        $end = microtime(true) - $start;

		// Add stylesheet file to the formResultCompiler
		$path = GeneralUtility::getFileAbsFileName("EXT:dataviewer/Resources/Public/Css/dataviewer-backend.css");
		$css = PathUtility::getAbsoluteWebPath($path);
		$this->formResultCompiler->mergeResult(
			["stylesheetFiles" => [$css],
			 "additionalJavaScriptPost" => [],
			 "additionalJavaScriptSubmit" => [],
			 "additionalHiddenFields" => [],
			]
		);

		$top = "";
		if(\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8007000)
			$this->formResultCompiler->addCssFiles();
		else
			$top = $this->formResultCompiler->JStop();
		

		// Finalization
		$html =
			"<div class=\"dataviewer-record dataviewer-record-{$record->getUid()}\" $backgroundColor>" .
			$this->renderHeader($datatype)				.
			$top						 						.
			implode("\r\n", $topParts)				.
			"<div class=\"dataviewer-content\">"				.
			$contentHtml 										.
			$this->renderTabMenu($tabConfigurationArray, "dataviewer-tabs")	.
			"</div>"											.
			implode("\r\n", $bottomParts)			.
			//$this->formResultCompiler->printNeededJSFunctions()	.
			"<input type=\"hidden\" name=\"{$baseRecordFormName}[datatype]\" value=\"{$datatype->getUid()}\" />".
			"<div class=\"clear\"></div>"						.
			"</div>" 
			."<!-- Rendering Time: " . $end . "-->"
		;

		// Fix for Datatype Selection when creating a new record
		// --------------------------------------------------------------------------
		// We need to add the selected datatype to the reloadUrl (form-url) in order
		// to reload the form with the selected datatype
		$html .= $this->_getDatatypeSelectionScriptHtml($recordUid);

		// Resetting the stored record values for cleaing up the
		// form on the end
		$this->recordValueSessionService->resetForRecordId($recordUid);
		//$html = "";
		return $html;
	}

	/**
	 * This delivers a script for the datatype selector box that
	 * adds a hidden datatype field with the selected datatypeId,
	 * which delivers the selected id on the form post that
	 * happens when the selectbox is changed.
	 * 
	 * @param int|string $recordId
	 * @return string
	 */
	protected function _getDatatypeSelectionScriptHtml($recordId)
	{
		$html = "";
		$selectName = "select[name=\"data[tx_dataviewer_domain_model_record][{$recordId}][datatype]\"]";

		$html .= "
				<script type=\"text/javascript\">
					(function (jQuery) {
						jQuery('{$selectName}').on('change', function(e){
							e.preventDefault();
							var eventTarget = jQuery(e.target),
								datatypeId = eventTarget.val(),
								form = jQuery('form[name=\"editform\"');
							
							if (form.find('input[name=\"datatype\"]').val())
							{
								form.find('input[name=\"datatype\"]').val(datatypeId);
							}
							else 
							{
								form.append('<input type=\"hidden\" name=\"datatype\" value=\"'+datatypeId+'\" />');
							}
							
							return;
						});
						
					})(TYPO3.jQuery);
				</script>
			";
			
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
		$header .= $this->_getExtensionInformationHtml();

		$iconIdentifier = "extensions-dataviewer-{$datatype->getIcon()}";
		$icon = $this->iconFactory->getIcon($iconIdentifier);
		$icon->setSize(Icon::SIZE_SMALL);
		
		$header .= "<div id=\"datatype-title\" title=\"Datatype: {$datatype->getUid()}\">";
		$header .= $icon->render();
		$header .= "<h1>{$datatype->getName()}</h1>";
		$header .= "</div>";

		// Information Message
		if (strlen($datatype->getDescription()) > 0)
			$header .= $this->getMessageHtml($datatype->getDescription(), Locale::translate("tx_dataviewer_domain_model_datatype.description"), FlashMessage::INFO);

		$header .= "<hr style=\"color:#c0c0c0; background-color:#c0c0c0; height:1px; border:0;\" />";
		$header .= "</div>";

		return $header;
	}

	/**
	 * Gets the html for the extension information
	 * 
	 * @return string
	 */
	protected function _getExtensionInformationHtml()
	{
		if($this->backendAccessService->disableDataViewerLogo())
			return "";
			
		$tooltipMessage = Locale::translate("support_information");	
			
	
		$version = ExtensionManagementUtility::getExtensionVersion("dataviewer");
		$logoUrl = $this->backendAccessService->getLogoUrl();
		$supportEmail = $this->backendAccessService->getSupportEmail();
	
		$html = "";
		$html .= "<div style=\"float:right\" data-toggle=\"tooltip\" data-html=\"true\" data-placement=\"top\" data-title=\"{$tooltipMessage}\">";
		$html .= "<div style=\"font-family:Share, Verdana, Arial, Helvetica, sans-serif; float:left; text-align:right; font-size:13px; margin-right:10px; line-height:1.2; margin-top:-3px;\">";
		$html .= "<strong>v{$version}</strong><br /><a href=\"mailto:{$supportEmail}\">{$supportEmail}</a>";
		$html .= "</div>";
		$html .= "<img src=\"{$logoUrl}\" border=\"0\" title=\"DataViewer {$version}\" style=\"height:22px; float:right;\" />";
       
		$html .= "</div>";
		
		

		return $html;
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
		$titleLabel = Locale::translate("LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.title");

		$placeholder = "";
		
		if ($record->getDatatype())
			$placeholder = Locale::translate("entry", $record->getDatatype()->getName());

		$title .= "<label for=\"{$fieldName}[title]\">{$titleLabel}</label>";
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
		$view->assignMultiple([
			'id' => $domId,
			'items' => $menuItems,
			'defaultTabIndex' => $defaultTabIndex,
			'wrapContent' => false,
			'storeLastActiveTab' => true,
		]);
		return $view->render();
	}
	

}
