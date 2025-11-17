<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

class FilterSessionService extends SessionService
{
    /**
     * Session Prefix Key
     * @var string
     */
    const SESSION_PREFIX_KEY = 'tx-dataviewer-filter';

    /**
     * Session Keys for Sorting
     * @var string
     */
    const SESSION_KEY_FILTERS = 'tx-dataviewer-filter-filters';
    const SESSION_KEY_SELECTED = 'tx-dataviewer-filter-selected';

    /**
     * Sets the selected options to the session
     *
     * @param array $selectedOptions
     * @return FilterSessionService
     */
    public function setSelectedOptions(array $selectedOptions): FilterSessionService
    {
        return $this->writeToSession($selectedOptions, self::SESSION_KEY_FILTERS);
    }

    /**
     * Gets the selected options from the session
     *
     * @return array
     */
    public function getSelectedOptions(): array
    {
        $selectedOptions = $this->restoreFromSession(self::SESSION_KEY_FILTERS);
        if (is_array($selectedOptions)) {
            return $selectedOptions;
        }

        return [];
    }

    /**
     * Gets the selected options as an cleaned array
     * with only needed keys
     *
     * @return array
     */
    public function getCleanSelectedOptions(): array
    {
        $selectedOptions = $this->getSelectedOptions();
        foreach ($selectedOptions as $_i => $_option) {
            unset($selectedOptions[$_i]['option_name']);
            unset($selectedOptions[$_i]['id']);
            unset($selectedOptions[$_i]['selected']);
        }

        return $selectedOptions;
    }

    /**
     * Removes an option from the
     * selected options
     *
     * @param string $id
     * @return FilterSessionService
     */
    public function removeOption(string $id): FilterSessionService
    {
        $selectedOptions = $this->getSelectedOptions();

        foreach ($selectedOptions as $_i => $_selectedOption) {
            if ($_selectedOption['id'] == $id) {
                unset($selectedOptions[$_i]);
            }
        }

        return $this->setSelectedOptions($selectedOptions);
    }

    /**
     * Checks if an option is selected
     *
     * @param int $fieldId
     * @param string $optionId
     * @return bool
     */
    public function checkIsSelected(int $fieldId, string $optionId): bool
    {
        $selectedOptions = $this->getSelectedOptions();

        foreach ($selectedOptions as $_option) {
            if (($_option['field_id'] == $fieldId) && ($_option['id'] == $optionId)) {
                return true;
            }
        }

        return false;
    }
}
