<?php
namespace MageDeveloper\Dataviewer\Service\Session;

use MageDeveloper\Dataviewer\Configuration\ExtensionConfiguration as Configuration;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
class LetterSessionService extends SessionService
{
	/**
	 * Session Prefix Key
	 * @var string
	 */
	const SESSION_PREFIX_KEY = "tx-dataviewer-letter";

	/**
	 * Session Keys
	 *
	 * @var string
	 */
	const SESSION_KEY_LETTER		= "tx-dataviewer-letter-selection";
	const SESSION_KEY_LETTER_FIELD	= "tx-dataviewer-letter-field";

	/**
	 * Sets the selected letter to the session
	 *
	 * @param string $letter
	 * @return LetterSessionService
	 */
	public function setSelectedLetter($letter)
	{
		return $this->writeToSession($letter, self::SESSION_KEY_LETTER);
	}

	/**
	 * Gets the selected letter from the session
	 *
	 * @return string
	 */
	public function getSelectedLetter()
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
	public function setLetterSelectionField($field)
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
