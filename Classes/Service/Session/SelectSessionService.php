<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

class SelectSessionService extends SessionService
{
    /**
     * Session Prefix Key
     * @var string
     */
    const SESSION_PREFIX_KEY = "tx-dataviewer-select";

    /**
     * Session Keys for Selection
     * @var string
     */
    const SESSION_KEY_SELECT_RECORDS = "tx-dataviewer-select-records";

    /**
     * Set selected records to the session
     *
     * @param array $selectedRecordIds
     * @return SelectSessionService
     */
    public function setSelectedRecords(array $selectedRecordIds): SelectSessionService
    {
        return $this->writeToSession($selectedRecordIds, self::SESSION_KEY_SELECT_RECORDS);
    }

    /**
     * Gets the selected records from the session
     *
     * @return array
     */
    public function getSelectedRecords(): array
    {
        $selectedRecords = $this->restoreFromSession(self::SESSION_KEY_SELECT_RECORDS);

        if (!is_array($selectedRecords)) {
            $selectedRecords = [];
        }

        return $selectedRecords;
    }

    /**
     * Returns the status of a previous set
     * record selection. If nothing was set,
     * we return false
     *
     * @return bool
     */
    public function isSetSelectedRecords(): bool
    {
        $selectedRecords = $this->restoreFromSession(self::SESSION_KEY_SELECT_RECORDS);

        if (is_array($selectedRecords)) {
            return true;
        }

        return false;
    }

    /**
     * Resets all selected records
     *
     * @return SelectSessionService
     */
    public function reset(): SelectSessionService
    {
        $this->setSelectedRecords([]);

        return $this;
    }

}
