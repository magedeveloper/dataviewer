<?php
declare(strict_types=1);

namespace Aix\Dataviewer\LabelUserFunc;

use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Service\Session\BackendSessionService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\ApplicationType;

class Record
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var BackendSessionService
     */
    protected $backendSessionService;

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * @param BackendSessionService $backendSessionService
     */
    public function injectBackendSessionService(BackendSessionService $backendSessionService)
    {
        $this->backendSessionService = $backendSessionService;
    }

	/**
	 * UserFunc for Field Label
	 *
	 * @param array $pObj Object Information
	 * @return void
	 */
	public function displayLabel(&$pObj): void
	{
        $mainRequest = $GLOBALS['TYPO3_REQUEST'];
        if (ApplicationType::fromRequest($mainRequest)->isBackend()) {
            return;
        }

		if($pObj['row']['sys_language_uid'] > 0) {
            $row = 	BackendUtility::getRecord($pObj['table'], $pObj['row']['uid'],'*');
            $pObj['row'] = $row;
        }

        $pObj['title'] = $pObj['row']['title'];
	}

}
