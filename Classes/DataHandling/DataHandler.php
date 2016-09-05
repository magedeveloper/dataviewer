<?php
namespace MageDeveloper\Dataviewer\DataHandling;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\ArrayUtility;
use MageDeveloper\Dataviewer\Utility\LocalizationUtility as Locale;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class DataHandler extends \TYPO3\CMS\Core\DataHandling\DataHandler
{
	/**
	 * Gets array value of the flexform
	 *
	 * @param array $value
	 * @param Record $record
	 * @param Field $field
	 * @return array
	 */
	public function getFlexformValue($value, Record $record, Field $field)
	{
		if (!is_array($value)) return array();

		$actionCMDs = GeneralUtility::_GP('_ACTION_FLEX_FORMdata');
		if (is_array($actionCMDs))
		{
			$path = "tx_dataviewer_domain_model_record/{$record->getUid()}/{$field->getUid()}";
			$actionCMDsDataNode = ArrayUtility::getArrayValueByPath($actionCMDs, $path);
			$this->_ACTION_FLEX_FORMdata($value, $actionCMDsDataNode);
		}

		return $value;
	}

	/**
	 * Actions for flex form element (move, delete)
	 * allows to remove and move flexform sections
	 *
	 * @param array $valueArray by reference
	 * @param array $actionCMDs
	 */
	public function _ACTION_FLEX_FORMdata(&$valueArray, $actionCMDs) {
		if (is_array($valueArray) && is_array($actionCMDs)) {
			foreach ($actionCMDs as $key => $value) {
				if ($key == '_ACTION') {

					// First, check if there are "commands":
					if (current($actionCMDs[$key]) !== '') {
						asort($actionCMDs[$key]);
						$newValueArray = array();

						foreach ($actionCMDs[$key] as $idx => $order) {


							// Just one reflection here: It is clear that when removing elements from a flexform, then we will get lost files unless we act on this delete operation by traversing and deleting files that were referred to.
							if ($order != 'DELETE') {
								$newValueArray[$idx] = $valueArray[$idx];
							}
							unset($valueArray[$idx]);
						}
						$valueArray = $valueArray + $newValueArray;
					}
				} elseif (is_array($actionCMDs[$key]) && isset($valueArray[$key])) {
					$this->_ACTION_FLEX_FORMdata($valueArray[$key], $actionCMDs[$key]);
				}
			}
		}
	}
}
