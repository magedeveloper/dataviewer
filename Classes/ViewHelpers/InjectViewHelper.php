<?php
namespace MageDeveloper\Dataviewer\ViewHelpers;

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

/**
 * Class InjectViewHelper
 *
 * @package MageDeveloper\Dataviewer\ViewHelpers
 * 
 * This class injects previous selected records to the current templateVariableContainer.
 * With this ViewHelper, you can use previous used records in your own fluid template.
 * It is necessary to include a Record-Plugin before this is used.
 * 
 * You may include the Record-Plugin without any template to get a clean injection of
 * the records and the template variables, that you've also selected in the plugin.
 * 
 * The parameter 'sourceUid' needs to point on the Record-Plugin, that you are using 
 * on the same site.
 */ 
class InjectViewHelper extends AbstractViewHelper
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-inject-records";

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Variable Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\VariableRepository
	 * @inject
	 */
	protected $variableRepository;

	/**
	 * Session Service Container
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SessionServiceContainer
	 * @inject
	 */
	protected $sessionServiceContainer;

	/**
	 * Record Controller
	 * 
	 * @var \MageDeveloper\Dataviewer\Controller\RecordController
	 * @inject
	 */
	protected $recordController;

	/**
	 * FlexForm Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

	/**
	 * Injects desired records to the current template
	 *
	 * @param int $sourceUid
	 * @return void
	 */
	public function render($sourceUid)
	{
		$sourceUid = (int)$sourceUid;
		
		if($sourceUid <= 0)
			return;

		// We need to obtain the valid record ids from the session
		$this->sessionServiceContainer->setTargetUid($sourceUid);

		// The list record array
		$record = $this->_getContentRecord($sourceUid);
		$recordIds = $this->sessionServiceContainer->getInjectorSessionService()->getActiveRecordIds();

		if(is_array($record) && is_array($recordIds))
		{
			$records = null;
			if(!empty($recordIds))
			{
				// Extract Storage Pids
				$storagePids = GeneralUtility::trimExplode(",", $record["pages"], true);

				// We get the records from the session and assign them to our variable container
				$records = $this->recordRepository->findByUids($recordIds, $storagePids);
			}
		
			// We get the assigned variables, load them, and assign them to our variable container
			$flexArr = $this->flexFormService->convertFlexFormContentToArray($record["pi_flexform"]);
			if(is_array($flexArr["settings"]) && isset($flexArr["settings"]["variable_injection"]))
			{
				$varIds = GeneralUtility::trimExplode(",", $flexArr["settings"]["variable_injection"], true);
				$variables = $this->recordController->prepareVariables($varIds);
				foreach($variables as $_vN=>$_vC)
					$this->templateVariableContainer->add($_vN, $_vC);
					
			}

			$this->templateVariableContainer->add($this->pluginSettingsService->getRecordsVarName(), $records);
		}

		return;	
	}

	/**
	 * Gets a content record by a given uid
	 * 
	 * @param int $uid
	 * @return array|null
	 */	
	protected function _getContentRecord($uid)
	{
		return reset($GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', "tt_content", "uid = {$uid}"));
	}

}
