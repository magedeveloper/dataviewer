<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

use MageDeveloper\Dataviewer\Domain\Model\Value;

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
class ValueRepository 
{
	/**
	 * Single Values
	 * 
	 * @var \MageDeveloper\Dataviewer\Domain\Model\Value[]
	 */
	protected $values;

	/**
	 * Adds a value to the repository
	 * 
	 * @param Value $value
	 * @return ValueRepository
	 */
	public function addValue(Value $value)
	{
		$code = $value->getField()->getCode();
		$this->values[$code] = $value;
		
		return $this;
	}

	/**
	 * Gets an specific value
	 * by a given code
	 *
	 * @param string $code
	 * @return Value
	 */
	public function getValueByCode($code)
	{
		if($this->values)
			foreach($this->values as $_value)
				if($_value->getField()->getCode() == $code)
					return $_value;

		return new Value();
	}

	/**
	 * Gets an specific value
	 * by a given field id
	 *
	 * @param int $id
	 * @return Value
	 */
	public function getValueByFieldId($id)
	{
		if($this->values)
			foreach($this->values as $_value)
				if($_value->getField()->getUid() == $id)
					return $_value;

		return new Value();
	}

	/**
	 * Sets the values
	 * 
	 * @param array $values
	 * @return ValueRepository
	 */
	public function setValues(array $values)
	{
		$this->values = $values;
		return $this;
	}

	/**
	 * Converts field names for Setters and Getters
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		$result = strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));
		return $result;
	}

	/**
	 * Set/Get attribute wrapper
	 * @param string $method
	 * @param array $args
	 * @throws \Exception
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		$key = $this->_underscore(substr($method,3));
		switch( substr($method, 0, 3) )
		{
			case "get":
				return $this->getValueByCode($key);
				break;
		}
		return;
	}
}
