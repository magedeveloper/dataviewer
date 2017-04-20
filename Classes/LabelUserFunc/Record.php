<?php
namespace MageDeveloper\Dataviewer\LabelUserFunc;

use \MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;

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
class Record
{
	/**
	 * Object Manager
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

    /**
     * Backend Session Service
     *
     * @var \MageDeveloper\Dataviewer\Service\Session\BackendSessionService
     * @inject
     */
    protected $backendSessionService;

	/**
	 * Constructor
	 *
	 * @return Record
	 */
	public function __construct()
	{
		$this->objectManager 	= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
		$this->recordRepository	= $this->objectManager->get(\MageDeveloper\Dataviewer\Domain\Repository\RecordRepository::class);
        $this->backendSessionService 	= $this->objectManager->get(\MageDeveloper\Dataviewer\Service\Session\BackendSessionService::class);
    }

	/**
	 * UserFunc for Field Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj)
	{
		if (isset($pObj["row"]))
		{
			$row = $pObj["row"];
			
			$this->backendSessionService->setAccordingPid($row["pid"]);
			$record = $this->recordRepository->findByUid($row["uid"], false, $row["sys_language_uid"]);

			if ($record instanceof \MageDeveloper\Dataviewer\Domain\Model\Record)
			{
				$title = "";
				$datatype = $record->getDatatype();
				if (!$datatype instanceof \MageDeveloper\Dataviewer\Domain\Model\Datatype)
					$title .= Locale::translate("datatype_not_selected");
				else
					$title .= $datatype->getName();

				if ($record->getTitle())
					$title = $record->getTitle();
					
				if($row["sys_language_uid"] > 0)
					$title = $row["title"];	
					
                $sortBy = $this->backendSessionService->getSortBy();
                $addInfo = (bool)$this->backendSessionService->getAddInfo();
                
                $additional = "";

                if($addInfo) 
                {
                	if(is_numeric($sortBy))
					{
						$value = $record->getValueByFieldId($sortBy);
						$plainValue = $value->getValue();
						
						if($value->getField() instanceof \MageDeveloper\Dataviewer\Domain\Model\Field)
						{
							if(!is_string($plainValue) && $value->getFieldvalue() instanceof \MageDeveloper\Dataviewer\Domain\Model\FieldValue)
								$plainValue = $value->getFieldvalue()->getSearch();

							$additional = "[".$value->getField()->getFrontendLabel().": ".$plainValue."] ";
						}
					}
                	else
					{
						$additional = "[".$sortBy.": ".$record->_getCleanProperty($sortBy)."] ";
					}

                }
                
				$pObj["title"] = $additional . $title;
			}
		}
	}

}
