<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

use TYPO3\CMS\Core\SingletonInterface;

class SessionServiceContainer implements SingletonInterface
{
    /**
     * @var int
     */
    protected $targetUid;

    /**
     * @var FilterSessionService
     */
    protected $filterSessionService;

    /**
     * @var LetterSessionService
     */
    protected $letterSessionService;

    /**
     * @var PagerSessionService
     */
    protected $pagerSessionService;

    /**
     * @var SearchSessionService
     */
    protected $searchSessionService;

    /**
     * @var SortSessionService
     */
    protected $sortSessionService;

    /**
     * @var SelectSessionService
     */
    protected $selectSessionService;

    /**
     * @var SessionService
     */
    protected $sessionService;

    /**
     * @param FilterSessionService $filterSessionService
     */
    public function injectFilterSessionService(FilterSessionService $filterSessionService)
    {
        $this->filterSessionService = $filterSessionService;
    }

    /**
     * @param LetterSessionService $letterSessionService
     */
    public function injectLetterSessionService(LetterSessionService $letterSessionService)
    {
        $this->letterSessionService = $letterSessionService;
    }

    /**
     * @param PagerSessionService $pagerSessionService
     */
    public function injectPagerSessionService(PagerSessionService $pagerSessionService)
    {
        $this->pagerSessionService = $pagerSessionService;
    }

    /**
     * @param SearchSessionService $searchSessionService
     */
    public function injectSearchSessionService(SearchSessionService $searchSessionService)
    {
        $this->searchSessionService = $searchSessionService;
    }

    /**
     * @param SortSessionService $sortSessionService
     */
    public function injectSortSessionService(SortSessionService $sortSessionService)
    {
        $this->sortSessionService = $sortSessionService;
    }

    /**
     * @param SelectSessionService $selectSessionService
     */
    public function injectSelectSessionService(SelectSessionService $selectSessionService)
    {
        $this->selectSessionService = $selectSessionService;
    }

    /**
     * @param SessionService $sessionService
     */
    public function injectSessionService(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }


    /**
     * Sets the target uid for all services
     *
     * @param int $targetUid
     * @return void
     */
    public function setTargetUid(int $targetUid): void
    {
        $this->targetUid = $targetUid;

        $filterSessionKey = FilterSessionService::SESSION_PREFIX_KEY;
        $letterSessionKey = LetterSessionService::SESSION_PREFIX_KEY;
        $pagerSessionKey  = PagerSessionService::SESSION_PREFIX_KEY;
        $searchSessionKey = SearchSessionService::SESSION_PREFIX_KEY;
        $sortSessionKey   = SortSessionService::SESSION_PREFIX_KEY;
        $selectSessionKey = SelectSessionService::SESSION_PREFIX_KEY;

        $this->filterSessionService->setPrefixKey("{$filterSessionKey}-{$targetUid}");
        $this->letterSessionService->setPrefixKey("{$letterSessionKey}-{$targetUid}");
        $this->pagerSessionService->setPrefixKey("{$pagerSessionKey}-{$targetUid}");
        $this->searchSessionService->setPrefixKey("{$searchSessionKey}-{$targetUid}");
        $this->sortSessionService->setPrefixKey("{$sortSessionKey}-{$targetUid}");
        $this->selectSessionService->setPrefixKey("{$selectSessionKey}-{$targetUid}");
    }

    /**
     * Gets the target uid
     *
     * @return int
     */
    public function getTargetUid(): int
    {
        return $this->targetUid;
    }

    /**
     * Returns the filter session service
     *
     * @return FilterSessionService
     */
    public function getFilterSessionService(): FilterSessionService
    {
        return $this->filterSessionService;
    }

    /**
     * Returns the letter session service
     *
     * @return LetterSessionService
     */
    public function getLetterSessionService(): LetterSessionService
    {
        return $this->letterSessionService;
    }

    /**
     * Returns the pager session service
     *
     * @return PagerSessionService
     */
    public function getPagerSessionService(): PagerSessionService
    {
        return $this->pagerSessionService;
    }

    /**
     * Returns the search session service
     *
     * @return SearchSessionService
     */
    public function getSearchSessionService(): SearchSessionService
    {
        return $this->searchSessionService;
    }

    /**
     * Returns the sort session service
     *
     * @return SortSessionService
     */
    public function getSortSessionService(): SortSessionService
    {
        return $this->sortSessionService;
    }

    /**
     * Returns the select session service
     *
     * @return SelectSessionService
     */
    public function getSelectSessionService(): SelectSessionService
    {
        return $this->selectSessionService;
    }

    /**
     * Returns the session service
     *
     * @return SessionService
     */
    public function getSessionService(): SessionService
    {
        return $this->sessionService;
    }
}
