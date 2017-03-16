<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Record;
use MageDeveloper\Dataviewer\Domain\Model\Field;
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
class MultiSelect extends Select
{
	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return array
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		$ids = GeneralUtility::trimExplode(",", $value, true);
		$table = $this->getField()->getConfig("foreign_table");
		$modelClass = $this->getField()->getConfig("modelClass");
			
		$items = [];
	
		foreach($ids as $_id)
		{
			$item = $this->getItemById($_id, $table, $modelClass);
			
			if($item instanceof Record || is_array($item))
				$items[] = $item;
			else
			    $items[] = $_id;	
		}
				
		return $items;
	}

}
