<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

class SearchSessionService extends SessionService
{
    /**
     * Session Prefix Key
     * @var string
     */
    const SESSION_PREFIX_KEY = "tx-dataviewer-search";

    /**
     * Session Keys for Sorting
     * @var string
     */
    const SESSION_KEY_SEARCH_FIELDS = "tx-dataviewer-search-fields";
    const SESSION_KEY_SEARCH_STRING = "tx-dataviewer-search-string";
    const SESSION_KEY_SEARCH_TYPE = "tx-dataviewer-search-type";

    /**
     * Set the search fields
     * with [
     *   field_id
     *     field_condition
     * ]
     *
     * @param array $searchFields
     * @return SearchSessionService
     */
    public function setSearchFields(array $searchFields = []): SearchSessionService
    {
        return $this->writeToSession($searchFields, self::SESSION_KEY_SEARCH_FIELDS);
    }

    /**
     * Get the searchfields stored in session
     *
     * @return array
     */
    public function getSearchFields(): array
    {
        if (is_array($this->restoreFromSession(self::SESSION_KEY_SEARCH_FIELDS))) {
            return $this->restoreFromSession(self::SESSION_KEY_SEARCH_FIELDS);
        }

        return [];
    }

    /**
     * Sets the search string to the session
     *
     * @param string $searchString
     * @return SearchSessionService
     */
    public function setSearchString(string $searchString): SearchSessionService
    {
        return $this->writeToSession($searchString, self::SESSION_KEY_SEARCH_STRING);
    }

    /**
     * Gets the search string stored in the session
     *
     * @return string
     */
    public function getSearchString(): string
    {
        return (string)$this->restoreFromSession(self::SESSION_KEY_SEARCH_STRING);
    }

    /**
     * Sets the search type
     *
     * @param string $searchType
     * @return SearchSessionService
     */
    public function setSearchType(string $searchType): SearchSessionService
    {
        return $this->writeToSession($searchType, self::SESSION_KEY_SEARCH_TYPE);
    }

    /**
     * Gets the search type stored in session
     *
     * @return string
     */
    public function getSearchType(): string
    {
        return (string)$this->restoreFromSession(self::SESSION_KEY_SEARCH_TYPE);
    }

    /**
     * Resets all search credentials
     *
     * @return SearchSessionService
     */
    public function reset(): SearchSessionService
    {
        $this->setSearchFields([]);
        $this->setSearchType('');
        $this->setSearchString('');

        return $this;
    }

}
