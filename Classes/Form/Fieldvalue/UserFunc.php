<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

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
class UserFunc 
extends AbstractFieldvalue
implements FieldvalueInterface
{
	/**
	 * Calls the userFunc and delivers the result
	 * 
	 * @return mixed
	 */
	private function _callUserFunc()
	{
		$userFunc = $this->getField()->getConfig("userFunc");
		$fieldtype = $this->getFieldtype();
		$fieldtype->setRecord($this->getRecord());
		$params = $fieldtype->getPreparedParameters();

		$params = [
			"parameters" => $params,
		];

		return GeneralUtility::callUserFunction($userFunc, $params, $this);
	}


	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getFrontendValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		return $this->getFrontendValue();
	}

	/**
	 * Gets the final frontend value, that is
	 * pushed in {record.field.value}
	 *
	 * This or these values are the most different
	 * part of the whole output, so if you handle
	 * this, you need to have some knowledge,
	 * what value is returned.
	 *
	 * @return string
	 */
	public function getFrontendValue()
	{
		return $this->_callUserFunc();
	}

	/**
	 * Gets the value or values as a plain string-array for
	 * usage in different possitions to show
	 * and use it when needed as a string
	 *
	 * @return array
	 */
	public function getValueArray()
	{
		return [$this->getFrontendValue()];
	}
}
