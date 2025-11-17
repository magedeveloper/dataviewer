<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Session;

class LetterSessionService extends SessionService
{
    /**
     * Session Prefix Key
     *
     * @var string
     */
    const SESSION_PREFIX_KEY = "tx-dataviewer-letter";

    /**
     * Session Keys
     *
     * @var string
     */
    const SESSION_KEY_LETTER = "tx-dataviewer-letter-selection";
    const SESSION_KEY_LETTER_FIELD = "tx-dataviewer-letter-field";

    /**
     * Sets the selected letter to the session
     *
     * @param string $letter
     * @return LetterSessionService
     */
    public function setSelectedLetter(string $letter): LetterSessionService
    {
        return $this->writeToSession($letter, self::SESSION_KEY_LETTER);
    }

    /**
     * Gets the selected letter from the session
     *
     * @return string
     */
    public function getSelectedLetter(): string
    {
        return (string)$this->restoreFromSession(self::SESSION_KEY_LETTER);
    }

    /**
     * Sets the letter selection field id to the
     * session
     *
     * @param int|string $field
     * @return LetterSessionService
     */
    public function setLetterSelectionField($field): LetterSessionService
    {
        return $this->writeToSession($field, self::SESSION_KEY_LETTER_FIELD);
    }

    /**
     * Gets the letter selection field from
     * the session
     *
     * @return int|string
     */
    public function getLetterSelectionField()
    {
        return $this->restoreFromSession(self::SESSION_KEY_LETTER_FIELD);
    }
}
