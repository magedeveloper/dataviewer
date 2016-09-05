<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Backend\Form\Container\SingleFieldContainer;
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
class Flex extends AbstractFieldtype implements FieldtypeInterface
{
	/**
	 * Initializes all form data providers to
	 * $this->formDataProviders
	 *
	 * Will be executed in order of the added providers!
	 *
	 * @return void
	 */
	public function initializeFormDataProviders()
	{
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexFetch::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class;
		parent::initializeFormDataProviders();
	}
	
	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$fieldName 					= $this->getField()->getUid();
		$tableName 					= "tx_dataviewer_domain_model_record";
		$value 						= $this->getValue(true);
		$databaseRow 				= $this->getDatabaseRow();
		$databaseRow[$fieldName] 	= $this->getValue(false);
		
		
		if($value == "")
		{
			$message = Locale::translate("no_flexform_content_given");
			return $this->getErrorTca($message);
		}
		
		
		$tca = array(
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"processedTca" => array(
				"columns" => array(
					$fieldName => array(
						"exclude" => 1,
						"label" => $this->getField()->getFrontendLabel(),
						"config" => array(
							"type" => "flex",
							"ds" => array(
								"default" => $value,
							),	
						),
					),
				),
			),
			"inlineStructure" => array(),
		);

		$this->prepareTca($tca);
		$this->tca = $tca;
		return $this->tca;
	}
}
