<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Page;

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
class GetViewHelper extends \MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper
{
	/**
	 * Fetch a page by id
	 * 
	 * @param int $id Id of the record to fetch
	 * @return array
	 */
	public function render($id)
	{
		$content = "";
		$res = $GLOBALS["TYPO3_DB"]->exec_SELECTquery(
			"*",
			"pages",
			"uid = {$id}",
			"uid ASC"
		);
		
		$row = $GLOBALS["TYPO3_DB"]->sql_fetch_assoc($res);
		$GLOBALS["TYPO3_DB"]->sql_free_result($res);
	
		if(!is_array($row))	
			$row = [];
			
		return $row;	
	}

}
