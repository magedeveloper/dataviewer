<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;

use MageDeveloper\Dataviewer\Service\Session\FilterSessionService;
use MageDeveloper\Dataviewer\Service\Session\LetterSessionService;
use MageDeveloper\Dataviewer\Service\Session\SearchSessionService;
use MageDeveloper\Dataviewer\Service\Session\SortSessionService;
use MageDeveloper\Dataviewer\Service\Session\SessionService;

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
class SessionServiceContainer
{
	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Target Uid
	 * 
	 * @var int
	 */
	protected $targetUid;

	/**
	 * Filter Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\FilterSessionService
	 * @inject
	 */
	protected $filterSessionService;

	/**
	 * Letter Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\LetterSessionService
	 * @inject
	 */
	protected $letterSessionService;

	/**
	 * Search Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\SearchSessionService
	 * @inject
	 */
	protected $searchSessionService;

	/**
	 * Sort Session Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\Session\SortSessionService
	 * @inject
	 */
	protected $sortSessionService;

	/**
	 * Select Session Service
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SelectSessionService
	 * @inject
	 */
	protected $selectSessionService;

	/**
	 * Injector SessionService
	 * 
	 * @var \MageDeveloper\Dataviewer\Service\Session\SessionService
	 * @inject
	 */
	protected $injectorSessionService;

	/**
	 * Sets the target uid for all services
	 * 
	 * @param int $targetUid
	 * @return SessionServiceContainer
	 */
	public function setTargetUid($targetUid)
	{
		$filterSessionKey	= FilterSessionService::SESSION_PREFIX_KEY;
		$letterSessionKey	= LetterSessionService::SESSION_PREFIX_KEY;
		$searchSessionKey	= SearchSessionService::SESSION_PREFIX_KEY;
		$sortSessionKey		= SortSessionService::SESSION_PREFIX_KEY;
		$selectSessionKey	= SelectSessionService::SESSION_PREFIX_KEY;
		$injectorSessionKey = \MageDeveloper\Dataviewer\ViewHelpers\InjectViewHelper::SESSION_PREFIX_KEY;
	
		$this->filterSessionService->setPrefixKey("{$filterSessionKey}-{$targetUid}");
		$this->letterSessionService->setPrefixKey("{$letterSessionKey}-{$targetUid}");
		$this->searchSessionService->setPrefixKey("{$searchSessionKey}-{$targetUid}");
		$this->sortSessionService->setPrefixKey("{$sortSessionKey}-{$targetUid}");
		$this->selectSessionService->setPrefixKey("{$selectSessionKey}-{$targetUid}");
		$this->injectorSessionService->setPrefixKey("{$injectorSessionKey}-{$targetUid}");
	}

	/**
	 * Returns the filter session service
	 * 
	 * @return FilterSessionService
	 */
	public function getFilterSessionService()
	{
		return $this->filterSessionService;
	}

	/**
	 * Returns the letter session service
	 * 
	 * @return LetterSessionService
	 */
	public function getLetterSessionService()
	{
		return $this->letterSessionService;
	}

	/**
	 * Returns the search session service
	 * 
	 * @return SearchSessionService
	 */
	public function getSearchSessionService()
	{
		return $this->searchSessionService;
	}

	/**
	 * Returns the sort session service
	 * 
	 * @return SortSessionService
	 */
	public function getSortSessionService()
	{
		return $this->sortSessionService;
	}

	/**
	 * Returns the select session service
	 * 
	 * @return SelectSessionService
	 */
	public function getSelectSessionService()
	{
		return $this->selectSessionService;
	}

	/**
	 * Returns the injector session service
	 * 
	 * @return SessionService
	 */
	public function getInjectorSessionService()
	{
		return $this->injectorSessionService;
	}	
}
