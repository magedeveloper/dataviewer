<?php
namespace MageDeveloper\Dataviewer\TypoScript;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Utility\DebugUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
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
class UserFunc
{
	/**
	 * Reference to the parent (calling) cObject set from TypoScript
	 */
	public $cObj;

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Standalone View
	 *
	 * @var \TYPO3\CMS\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $standaloneView;

	/**
	 * Plugin Settings Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Constructor
	 *
	 * @return UserFunc
	 */
	public function __construct()
	{
		$this->objectManager    = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->standaloneView   = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);
		$this->settingsService  = $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Settings\Plugin\PluginSettingsService::class);
		$this->recordRepository = $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
	}

	/**
	 * Renders text from an dynamic record
	 *
	 * @param string $content Empty String (no content to process)
	 * @param array $conf TypoScript Configuration
	 * @return string
	 */
	public function renderTextFromRecord($content, $conf)
	{
		// Variable Names
		$recordVariableName = $this->settingsService->getRecordVarName();
		$recordsVariableName = $this->settingsService->getRecordsVarName();

		// Additional Parameters
		$variables = []; $parameters = [];
		if (isset($conf["parameter."]) && is_array($conf["parameter."])) {
			$parameters = $conf["parameter."];

			$notAllowedVarNames = [
				$recordVariableName,
				$recordsVariableName
			];

			$vars = array_keys($conf["parameter."]);

			foreach($vars as $_k=>$_v) {
				if(!preg_match('/.*\./', $_k) && !in_array($_k, $notAllowedVarNames)) {
					unset($vars[$_k]);
				}
				else
				{
					if (isset($parameters[$_v."."])) {
						$type = $parameters[$_v];
						$tsConf = $parameters[$_v."."];
						// We found a qualified typoscript structure here
						$variables[$_v] = $this->cObj->stdWrap($type, $tsConf);
					} else {
						// No typoscript structure, so we inject to content plain
						$variables[$_v] = $parameters[$_v];
					}
				}
			}

		}

		if(isset($parameters[$recordsVariableName]))
		{
			$records = [];
			$recordUids = GeneralUtility::trimExplode(",", $parameters[$recordsVariableName]);

			foreach($recordUids as $_uid)
				$records[] = $this->recordRepository->findByUid($_uid, true);

			// Assigning the record
			$this->standaloneView->assign($recordsVariableName, $records);
		}
		else
		{
			if(isset($parameters[$recordVariableName]))
				$record = $this->recordRepository->findByUid($parameters[$recordVariableName]);
			else
				$record = $this->_getRecordFromParameters();

			// Assigning the record
			$this->standaloneView->assign($recordVariableName, $record);
		}

		$this->standaloneView->assignMultiple($variables);

		$value = $conf["value"];

		$rendered = $this->_renderSource($value);
		return $rendered;
	}

	/**
	 * Renders a source fluid code
	 *
	 * @param string $source
	 * @return string
	 */
	protected function _renderSource($source)
	{
		$rendered = "";
		try {
			$this->standaloneView->setTemplateSource($source);
			$rendered = $this->standaloneView->render();
		}
		catch (\Exception $e)
		{

		}

		return $rendered;
	}


	/**
	 * Gets the current selected record from
	 * the params
	 *
	 * @return Record|null
	 */
	protected function _getRecordFromParameters()
	{
		// We need to determine the current record that is dynamically called here
		$dvParams = GeneralUtility::_GET("tx_dataviewer_record");

		if(!$dvParams || !is_array($dvParams) || !isset($dvParams["record"]))
			return null;

		$recordUid = (int)$dvParams["record"];
		$record = $this->recordRepository->findByUid($recordUid, true);

		if($record instanceof Record)
			return $record;

		return null;
	}

}
