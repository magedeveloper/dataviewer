<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class CheckboxUtility
{
	/**
	 * Gets the selected checkboxes as an array
	 *
	 * @param int $resultInt
	 * @return array
	 */
	public static function getSelectedIds(int $resultInt, int $max = 10): array
	{
		$ret = [];
		for ($i=0; $i < $max; $i++)	{
			// Separate bits and emit values (0 or 1)
			$ret[$i]= ($resultInt & pow(2,$i)) ? 1 : 0;
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
	public static function getIntForSelectionArray(array $selectionArray): int
	{
		$res = 0;
		if (count($selectionArray) > 0) {
			foreach ($selectionArray as $key=>$val) {
                if ($val == 1) {
					$res+=pow(2,$key);
                }
            }
		}

		return $res;
	}
}
