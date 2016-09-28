<?php
namespace MageDeveloper\Dataviewer\UserFunc;

use MageDeveloper\Dataviewer\Utility\LocalizationUtility;
use MageDeveloper\Dataviewer\Utility\StringUtility;

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
class Field
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * FlexForm Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * Constructor
	 *
	 * @return Field
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->fieldRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\FieldRepository::class);
		$this->recordRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
		$this->flexFormService	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\FlexFormService::class);
	}

	/**
	 * Displays the generated field identifier for frontend identification
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayGeneratedFieldIdentifier(array &$config, &$parentObject)
	{
		$row = $config["row"];
		$text = ($row["variable_name"] != "")?$row["variable_name"]:$row["frontend_label"];
		$code = StringUtility::createCodeFromString($text);
		$title = LocalizationUtility::translate("formvalue_access_to_hidden_field");

		if (!$code) $code = "<em>generated on save</em>";

		$html = "";
		$html .= "<strong>{$title}</strong>";
		$html .= "<span style=\"font-family: Courier, Courier new, monospace; font-size:16px; float:left; width:100%;\" class='callout callout-info'>";
		$html .= '{record.<strong>'.$code.'</strong>}';
		$html .= "</span><br />";

		return $html;
	}

	/**
	 * Displays all available field ids for helping while
	 * using display conditions
	 *
	 * @param array $config
	 * @param array $parentObject
	 * @return string
	 */
	public function displayAvailableFieldIds(array &$config, &$parentObject)
	{
		$row = $config["row"];
		$pid = $row["pid"];
		$title = LocalizationUtility::translate("available_field_ids");
		$fields = $this->fieldRepository->findAllOnPid($pid);
		

		$html = "";
		$html .= "<strong>{$title}</strong>";
		$html .= "<span style=\"font-family: Courier, Courier new, monospace; font-size:12px; float:left; width:100%;\" class='callout callout-info'>";
		
		if(count($fields))
		{
			foreach($fields as $_field)
			{
				/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
				$html .= "[ID: {$_field->getUid()}]\t\t";
				$html .= "{$_field->getFrontendLabel()}";
				$html .= "<br />";
			
			}

			$html .= "<br />";
			$html .= "Example: FIELD:2:=:Selected Value";
		
		}
		else
		{
			$html .= "---<br />";
		}

		
		$html .= "</span><br />";

		return $html;
	}

	/**
	 * Populate fields
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFields(array &$config, &$parentObject)
	{
		$options = [];

		$fields = $this->fieldRepository->findAll(false);

		foreach($fields as $_field)
		{
			$pid = $_field->getPid();
			$label = "[{$pid}] " . $_field->getFrontendLabel();
			$options[] = [$label, $_field->getUid()];
		}

		$config["items"] = array_merge($config["items"], $options);
	}

	/**
	 * Populate fields field by record id
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function populateFieldsByRecord(array &$config, &$parentObject)
	{
		$options = [];

		if (is_array($config["row"]) && isset($config["row"]["settings.single_record_selection"]))
		{
			if(is_array($config["row"]["settings.single_record_selection"]))
				$singleRecordId = (int)reset($config["row"]["settings.single_record_selection"]);
			else
				$singleRecordId = (int)$config["row"]["settings.single_record_selection"];
				
			$record = $this->recordRepository->findByUid($singleRecordId, false);
			if ($record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
			{
				$types = $record->getDatatype()->getSortedFields();

				foreach($types as $_type=>$_fields)
				{
					$options[] = [strtoupper($_type), "--div--"];

					if(count($_fields)>0)
					{
						foreach($_fields as $_field)
						{
							/* @var \MageDeveloper\Dataviewer\Domain\Model\Field $_field */
							$tabName 	= ($_field->getTabName())?$_field->getTabName() . ">":"";
							$label	 	= "[{$_field->getUid()}] " . strtoupper($_field->getType()) . ": " . $tabName . $_field->getFrontendLabel();
							$options[] 	= [$label, $_field->getUid()];
						}
					
					
					}
				}

			}
			
			$config["items"] = array_merge($config["items"], $options);
		}

	}
}
