<?php
namespace MageDeveloper\Dataviewer\Utility;

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

class CheckboxUtility
{
	/**
	 * Gets the selected checkboxes as an array
	 *
	 * @param int $resultInt
	 * @return array
	 */
	public static function getSelectedIds($resultInt, $max = 10)
	{
		$ret = array(); 
		for($i=0; $i < $max; $i++) 
		{   
			// Die Bits durchgehen und für jedes Bit den Wert (0 oder 1) ermitteln
			$ret[$i]= ($resultInt & pow(2,$i)) ? 1 : 0; // Das Array ausgeben
		}

		$selected = $ret;
		
		return $selected;
	}

	/**
	 * Gets an integer value for a selection array
	 * 
	 * @param array $selectionArray
	 * @return int
	 */
	public static function getIntForSelectionArray($selectionArray)
	{
		$res = 0;
		if(count($selectionArray) > 0) 
		{
			foreach($selectionArray as $key=>$val) 
				if ($val == 1)
					$res+=pow(2,$key);
				
		}
		
		return $res;
	}
}