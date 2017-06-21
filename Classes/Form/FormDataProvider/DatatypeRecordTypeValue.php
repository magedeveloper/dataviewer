<?php
namespace MageDeveloper\Dataviewer\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

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
class DatatypeRecordTypeValue implements FormDataProviderInterface
{
	/**
	 * Transforms the recordTypeValue information to a datatype key in the
	 * result set
	 *
	 * @param array $result
	 * @return array
	 */
	public function addData(array $result)
	{
		if($result["tableName"] == "tx_dataviewer_domain_model_record")
		{
			// We extract the recordTypeValue from the result, because we
			// just injected our current datatype uid in this field
			// and we need to return a empty value that
			// it should have
			if(isset($result["recordTypeValue"]) && is_numeric($result["recordTypeValue"]))
			{
				$datatypeUid = (int)$result["recordTypeValue"];
				$result["databaseRow"]["datatype"] = $datatypeUid;
				$result["recordTypeValue"] = "";
			}

		}

		return $result;
	}

}
