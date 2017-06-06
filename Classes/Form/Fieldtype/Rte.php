<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue;
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
class Rte extends Text
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
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\PageTsConfig::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\InitializeProcessedTca::class;
		$this->formDataProviders[] = \MageDeveloper\Dataviewer\Form\FormDataProvider\InitBackendUser::class;
		$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEffectivePid::class;

		if(\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8007000)
			$this->formDataProviders[] = \TYPO3\CMS\Backend\Form\FormDataProvider\TcaText::class;

		$this->formDataProviders[] = \MageDeveloper\Dataviewer\Form\FormDataProvider\UnsetBackendUser::class;	
		parent::initializeFormDataProviders();
	}

	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca()
	{
		$fieldName 					= $this->getField()->getUid()."_rte";
		$tableName 					= "tx_dataviewer_domain_model_record";
		$value 						= $this->getValue();
		$databaseRow 				= $this->getDatabaseRow();
		$databaseRow[$fieldName] 	= $value;

		$tca = [
			"tableName" => $tableName,
			"databaseRow" => $databaseRow,
			"fieldName" => $fieldName,
			"effectivePid" => (int)$this->getField()->getConfig("pid_config"),
			"processedTca" => [
				"columns" => [
					$fieldName => [
						"exclude" => (int)$this->getField()->isExclude(),
						"label" => $this->getField()->getFrontendLabel(),
						"config" => [
							"type" => "text",
							"eval" => $this->getField()->getEval(),
							"placeholder" => $this->getField()->getConfig("placeholder"),
							"enableRichtext" => true,
						],
					],
				],
			],
			"inlineStructure" => [],
		];

		$this->prepareTca($tca);
		$this->tca = $tca;
		
		return $this->tca;
	}

}
