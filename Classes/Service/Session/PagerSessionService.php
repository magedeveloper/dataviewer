<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

class PagerSessionService extends SessionService
{
    /**
     * Session Prefix Key
     * @var string
     */
    const SESSION_PREFIX_KEY = "tx-dataviewer-pager";

    /**
     * Session Keys
     *
     * @var string
     */
    const SESSION_KEY_PAGE = "tx-dataviewer-page-selection";
    const SESSION_KEY_PER_PAGE = "tx-dataviewer-page-per-page";
    const SESSION_KEY_RECORD_COUNT = "tx-dataviewer-record-count";


    /**
     * Sets the selected page to the session
     *
     * @param int $page
     * @return PagerSessionService
     */
    public function setSelectedPage(int $page): PagerSessionService
    {
        return $this->writeToSession($page, self::SESSION_KEY_PAGE);
    }

    /**
     * Gets the selected letter from the session
     *
     * @return int
     */
    public function getSelectedPage(): int
    {
        return (int)$this->restoreFromSession(self::SESSION_KEY_PAGE);
    }

    /**
     * Sets the results per page
     *
     * @param int $perPage
     * @return $this
     */
    public function setPerPage(int $perPage): PagerSessionService
    {
        $this->writeToSession($perPage, self::SESSION_KEY_PER_PAGE);

        return $this;
    }

    /**
     * Gets the result number per page
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return (int)$this->restoreFromSession(self::SESSION_KEY_PER_PAGE);
    }

    /**
     * Sets the record count
     *
     * @param int $count
     * @return $this
     */
    public function setRecordCount(int $count): PagerSessionService
    {
        return $this->writeToSession($count, self::SESSION_KEY_RECORD_COUNT);
    }

    /**
     * Gets the record count
     *
     * @return int
     */
    public function getRecordCount(): int
    {
        return (int)$this->restoreFromSession(self::SESSION_KEY_RECORD_COUNT);
    }

    /**
     * Resets all pager settings
     *
     * @return PagerSessionService
     */
    public function reset(): PagerSessionService
    {
        $this->setSelectedPage(null);
        $this->setPerPage(null);

        return $this;
    }
}
