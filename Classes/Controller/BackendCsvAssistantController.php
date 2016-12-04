<?php
namespace MageDeveloper\Dataviewer\Controller;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Utility\DebugUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Resource\Exception\FileOperationErrorException;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Extbase\Utility\ArrayUtility;

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
class BackendCsvAssistantController extends BackendController
{
	/**
	 * Delimeters
	 * 
	 * @var array
	 */
	protected $delimeters = [
		";",
		":",
		"-",
		"_",
		"|",
		"/",
		"\\",
		"%",
		"!",
		"\\t",
	];

	/**
	 * Field Enclosures
	 * 
	 * @var array
	 */
	protected $fieldEnclosures = [
		" ",
		"'",
		'"',
	];

    /**
     * Record Factory
     *
     * @var \MageDeveloper\Dataviewer\Factory\RecordFactory
     * @inject
     */
    protected $recordFactory;

	/**
	 * Initial CSV Import Assistant Method to
	 * import csv data
	 *
	 * -------------------------------------------------------
	 * Step 1 - Initial method / Select a Page
	 * -------------------------------------------------------
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->_storeLastAction();
	
		if($this->currentPageId > 0)
			$this->forward("datatype");

	}

	/**
	 * Action for selecting a target datatype that
	 * will be used for importing the csv data
	 * 
	 * -------------------------------------------------------
	 * Step 2 - Select Datatype
	 * -------------------------------------------------------
	 *
	 * @return void
	 */
	public function datatypeAction()
	{
		if(TYPO3_MODE != "BE") die();
	
		$datatypesRepository = $this->datatypeRepository->findAll(false);

		$datatypes = [];
		foreach($datatypesRepository as $_datatype)
			$datatypes[$_datatype->getUid()] = "[{$_datatype->getPid()}] ".$_datatype->getName();

		$this->view->assign("datatypes", $datatypes);
	}

	/**
	 * Action for selecting a file for the import.
	 * The selected datatype is retrieved by an POST Argument.
	 *
	 * -------------------------------------------------------
	 * Step 3 - Select a file to import
	 * -------------------------------------------------------
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @return void
	 */
	public function fileAction(\MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype)
	{
		if(TYPO3_MODE != "BE") die();
		
        $this->view->assign("delimeters", $this->delimeters);
        $this->view->assign("fieldEnclosures", $this->fieldEnclosures);
		$this->view->assign("datatype", $datatype);
	}

	/**
	 * Action for assigning columns from the csv file to the according fields of
	 * the datatype
	 *
	 * -------------------------------------------------------
	 * Step 4 - Assign csv columns to fields
	 * -------------------------------------------------------
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @param array $file
	 * @param int $delimeter
	 * @param int $fieldEnclosure
	 * @param int $headerLine
	 * @param bool $importValidationFailed
	 * @return void
	 */
	public function assignAction(\MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype, array $file, $delimeter, $fieldEnclosure, $headerLine, $importValidationFailed)
	{
		if(TYPO3_MODE != "BE") die();
		
		$delimeter = $this->delimeters[$delimeter];
		$fieldEnclosure = $this->fieldEnclosures[$fieldEnclosure];
	
        if(isset($file["tmp_name"]) && file_exists($file["tmp_name"]))
        {
        	$tmpFile = GeneralUtility::upload_to_tempfile($file["tmp_name"]);
			$csvArray = $this->_generateArrayByCsv($tmpFile, $delimeter, $fieldEnclosure, (bool)$headerLine);
			
			if(empty($csvArray))
			{
				$message = Locale::translate("module.file_empty");
				$this->addFlashMessage($message, "", FlashMessage::ERROR);
				$this->forward("file");
			}
			
			$columns = [];
			$columns[""] = Locale::translate("module.csv_not_assigned");
			
			if($headerLine == "1")
			{
				$headerColumns = array_keys(reset($csvArray));
				$headerColumns = array_combine(array_values($headerColumns), array_values($headerColumns));
				
				$columns = array_merge($columns, $headerColumns);
			}
			else
			{
				$columns = array_merge($columns, range(0, count(reset($csvArray))));
			}
			
			$csvColumns = $columns;
			unset($csvColumns[""]);
			
			$this->view->assign("csvColumns", $csvColumns);
			$this->view->assign("columns", $columns);
			$this->view->assign("datatype", $datatype);
			$this->view->assign("file", $tmpFile);
			$this->view->assign("csv", $csvArray);
			$this->view->assign("delimeter", $delimeter);
			$this->view->assign("fieldEnclosure", $fieldEnclosure);
			$this->view->assign("headerLine", $headerLine);
			$this->view->assign("importValidationFailed", $importValidationFailed);
			return;
        }

		$message = Locale::translate("module.no_file_given");
		$this->addFlashMessage($message, "", FlashMessage::ERROR);
		$this->forward("file");

	}

	/**
	 * Main Action for starting the import with the posted data
	 *
	 * -------------------------------------------------------
	 * Step 5 - Import CSV Data
	 * -------------------------------------------------------
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype
	 * @param string $file
	 * @param string $delimeter
	 * @param string $fieldEnclosure
	 * @param bool $headerLine
	 * @param bool $importValidationFailed
	 * @throws \TYPO3\CMS\Core\Resource\Exception\InvalidFileException
	 * @return void
	 */
	public function importAction(\MageDeveloper\Dataviewer\Domain\Model\Datatype $datatype, $file, $delimeter, $fieldEnclosure, $headerLine, $importValidationFailed)
	{
		if(TYPO3_MODE != "BE") die();
		
		if(!strpos($file, "/typo3temp/"))
			throw new InvalidFileException("File not accessible!");
		
		$arguments = $this->request->getArguments();
		
		$assignedFields = []; $customValues = [];
		foreach($arguments as $_argN=>$_argV)
		{
			// Check for assigned 
			$preg = preg_match("/field_\\d/Usi", $_argN);
			if ($preg && strlen($_argV) > 0)
			{
				$fieldId = str_replace("field_", "", $_argN);
				$assignedFields[$fieldId] = $_argV;
			}

			// Check for custom value
			$preg = preg_match("/custom_.*/Usi", $_argN);
			if ($preg && strlen($_argV) > 0)
			{
				$fieldId = str_replace("custom_", "", $_argN);
				$customValues[$fieldId] = $_argV;
			}
			
		}
		
		
		// Retrieve CSV File Contents
		$csvArray = $this->_generateArrayByCsv($file, $delimeter, $fieldEnclosure, $headerLine);

		$import = []; $i = 0;
		foreach($csvArray as $_values)
		{	
			foreach($_values as $_rowId=>$_value)
			{
				foreach($assignedFields as $_fieldId=>$_assignedRowId)
				{
					if($_rowId == $_assignedRowId)
						$import[$i][$_fieldId] = $_value;

				}

				if($_rowId == "title")
					$import[$i]["title"] = $_value;

			}

			$import[$i] = ArrayUtility::arrayMergeRecursiveOverrule($import[$i], $customValues);
			$i++;
			
		}
		
		$log = []; $i = 0;
		foreach($import as $_fieldArr)
		{
			// Initial Array building
			$log[$i]["messages"] = [];
			$log[$i]["recordId"] = null;
			$log[$i]["hasErrors"] = null;

			$record = $this->recordFactory->create($_fieldArr, $datatype, false, $importValidationFailed);
			
			if(empty($this->recordFactory->getValidationErrors()))
			{
				$this->recordRepository->add($record);
				$this->persistenceManager->persistAll();

				// Record successfully created, we add the record id to the log
				$log[$i]["recordId"] = $record->getUid();
			}
			else
			{
				if($importValidationFailed === true)
				{
					$this->recordRepository->add($record);
					$this->persistenceManager->persistAll();

					// Record successfully created, we add the record id to the log
					$log[$i]["recordId"] = $record->getUid();
				}
			
				$log[$i]["hasErrors"] = true;
				if($importValidationFailed)
					$log[$i]["messages"]["Import"][] = Locale::translate("module.import_error");
				else
					$log[$i]["messages"]["Import"][] = Locale::translate("record_not_saved");

				$validationErrors = $this->recordFactory->getValidationErrors();
				foreach($validationErrors as $_field=>$_fieldErrors)
				{
					/* @var \TYPO3\CMS\Extbase\Validation\Error $_error */
					foreach($_fieldErrors as $_error)
					{
						$log[$i]["messages"][$_field][] = $_error->getMessage();
					}
				}
			}
			
			$i++;
			
		}
		
		// Remove the temporary file
		//GeneralUtility::unlink_tempfile($file);
		
		// Redirect to the log viewer with the log information
		$this->forward("review", null, null, ["log"=>$log]);
	}

	/**
	 * Main Action for starting the import with the posted data
	 *
	 * -------------------------------------------------------
	 * Step 6 - Review final log data
	 * -------------------------------------------------------
	 *
	 * @return void
	 */
	public function reviewAction()
	{
		if(TYPO3_MODE != "BE") die();

		$log = $this->request->getArgument("log");
		$this->view->assign("log", $log);
	}

	/**
	 * Generates an optimized array from csv file contents
	 *
	 * @param string $file
	 * @param string $delimeter
	 * @param string $fieldEnclosure
	 * @param bool $headerLine
	 * @return array
	 * @throws \TYPO3\CMS\Core\Resource\Exception\FileOperationErrorException
	 */
	protected function _generateArrayByCsv($file, $delimeter, $fieldEnclosure, $headerLine)
	{
		try
		{
			$fileContents = file_get_contents($file);
		}
		catch (\Exception $e)
		{
			throw new FileOperationErrorException($e->getMessage());
		}
		
		if(!$fileContents)
		{
			return [];
		}
	
		$csvArray = \TYPO3\CMS\Core\Utility\CsvUtility::csvToArray($fileContents, $delimeter, $fieldEnclosure);

		$header = [];
		if($headerLine)
		{
			$header = reset(array_slice($csvArray, 0, 1));
			$csvArray = array_slice($csvArray, 1);

			foreach($csvArray as $i=>$_item)
				$csvArray[$i] = array_combine($header, $_item);

		}
		else
		{
			unset($csvArray[0]);
		}
		
		return $csvArray;
	}
}
