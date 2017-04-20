<?php

namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use MageDeveloper\Dataviewer\Domain\Model\Datatype;
use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field as Field;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
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
class PrepareSelectTreeTca extends PrepareDataviewerTca implements FormDataProviderInterface
{
	/**
	 * Prepares the correct databaseRow value for the tree items with
	 * using the PrepareDataviewerTca Value and
	 * simply exploding it, putting it back, and returning the old
	 * array
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		$overallResult = parent::addData($result);
		$fieldId = (int)GeneralUtility::_GET("fieldName");
		
		// We only modify the databaseRow, when our field is an id from the dataviewer fields
		if(is_numeric($fieldId) && $fieldId > 0) 
		{
			$value = $overallResult["databaseRow"][$fieldId];
			$result["databaseRow"][$fieldId] = GeneralUtility::trimExplode(",", $value, false);
		}
		
		return $result;
	}
}
