<?php
namespace MageDeveloper\Dataviewer\Domain\Model;

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

/**
 * @method mixed getId()
 */
class MagicModel implements \ArrayAccess
{
	/**
	 * internal data storage array
	 * @var array
	 */
	protected $_data = array();

	/**
	 * data changes flag (true after setData | unsetData call)
	 * @var boolean
	 */
	private $_hasChangedData = false;

	/**
	 * cache for formated names
	 * @var string array
	 */
	private $_formatNameCache = array();

	/**
	 * Constructor
	 * 
	 * @param array $data
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function __construct($data = array())
	{
		if( !empty($data) )
			$this->_data = $this->_lowerArrayKeys($data);

		return $this;
	}

	/**
	 * Checks whether the object is empty
	 * 
	 * @return boolean
	 */
	public function isEmpty()
	{
		return empty($this->_data);
	}

	/**
	 * Check if data is changed
	 * @return boolean
	 */
	public function hasChangedData()
	{
		if( $this->_hasChangedData )
		{
			$this->_hasChangedData = false;
			return true;
		}

		return false;
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
		switch( substr($method, 0, 3) )
		{
			case "get":
				$key = $this->_underscore(substr($method,3));
				$data = $this->getData($key, isset($args[0]) ? $args[0] : null);
				return $data;

			case "set":
				$key = $this->_underscore(substr($method,3));
				$result = $this->setData($key, isset($args[0]) ? $args[0] : null);
				return $result;

			case "uns":
				$key = $this->_underscore(substr($method,3));
				$result = $this->unsetData($key);
				return $result;

			case "has":
				$key = $this->_underscore(substr($method,3));
				return isset($this->_data[$key]);
		}

		throw new \Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
	}

	/**
	 * Set data to the Object
	 * @param string|array $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setData($key, $value=null)
	{
		if( !$this->callEvent("beforeSet", $key, $value) )
			return $this;

		$this->_hasChangedData = true;
		
		$estFunc = "set".ucfirst($key);
		if (method_exists($this, $estFunc))
			$this->{$estFunc}($value);
		else
			if( is_array($key) )
				$this->_data = $key;
			else
				$this->_data[$key] = $value;

		$this->callEvent("afterSet", $key, $value);

		return $this;
	}

	/**
	 * Get data from Object
	 * @param string $key
	 * @param string|int $index
	 * @return mixed
	 */
	public function getData($key = "", $index = null)
	{
		$default = null;

		if( !$this->callEvent("beforeGet", $key) )
			return $default;

		if( $key === "" )
		{
			$this->callEvent("afterGet", $key, $this->_data);
			return $this->_data;
		}

		// accept a/b/c as ['a']['b']['c']
		if( strpos($key, "/") )
		{
			$keyArr = explode("/", $key);
			$data = $this->_data;

			foreach( $keyArr as $k )
			{
				if( $k === '' )
				{
					$this->callEvent("afterGet", $key, $default);
					return $default;
				}

				if( is_array($data) )
				{
					if( !isset($data[$k]) )
					{
						$this->callEvent("afterGet", $key, $default);
						return $default;
					}

					$data = $data[$k];
				}
				elseif( $data instanceof MagicModel )
				{
					$data = $data->getData($k);
				}
				else
				{
					$this->callEvent("afterGet", $key, $default);
					return $default;
				}

			}

			$this->callEvent("afterGet", $key, $default);
			return $data;
		}

		// legacy functionality for $index
		if( isset($this->_data[$key]) )
		{
			if( is_null($index) )
			{
				$this->callEvent("afterGet", $key, $this->_data[$key]);
				return $this->_data[$key];
			}

			$value = $this->_data[$key];

			if( is_array($value) )
			{
				if( isset($value[$index]) )
				{
					$this->callEvent("afterGet", $key, $value[$index]);
					return $value[$index];
				}

				$this->callEvent("afterGet", $key, $default);
				return $default;
			}
			elseif( is_string($value) )
			{
				$arr = explode("\n", $value);
				$data = isset($arr[$index]) && (!empty($arr[$index]) || strlen($arr[$index]) > 0) ? $arr[$index] : null;

				$this->callEvent("afterGet", $key, $data);
				return $data;

			}
			elseif( $value instanceof MagicModel )
			{
				$this->callEvent("afterGet", $key, $value->getData($index));
				return $value->getData($index);
			}

			$this->callEvent("afterGet", $key, $default);
			return $default;
		}

		$this->callEvent("afterGet", $key, $default);
		return $default;
	}

	/**
	 * Check if some or specific data is available
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key = "")
	{
		if( !$this->callEvent("beforeHas", $key) )
			return false;

		if( empty($key) || !is_string($key) )
		{
			$has = !empty($this->_data);

			$this->callEvent("afterHas", $key, $has);
			return $has;
		}

		$has = array_key_exists($key, $this->_data);

		$this->callEvent("afterHas", $key, $has);
		return $has;
	}

	/**
	 * Unset data from the object
	 * @param string $key
	 * @return $this
	 */
	public function unsetData($key=null)
	{
		if( !$this->callEvent("beforeUns", $key) )
			return $this;

		$this->_hasChangedData = true;

		if( is_null($key) )
			$this->_data = array();
		else
			unset($this->_data[$key]);

		$this->callEvent("afterUns", $key);

		return $this;
	}

	/**
	 * Add data to the object
	 * @param array $arr
	 * @return Object
	 */
	public function addData(array $arr)
	{
		foreach( $arr as $index => $value )
		{
			if( strtoupper($index) == $index )
				$index = strtolower($index);

			$this->setData($index, $value);
		}

		return $this;
	}

	/**
	 * lower all array keys if they're complete uppercase
	 * @param array $array
	 * @return array
	 */
	protected function _lowerArrayKeys($array)
	{
		$lowered = array();

		foreach( $array as $key => $values )
			if( is_array($values) )
				$lowered[strtolower($key)] = $this->_lowerArrayKeys($values);
			else
				if( strtoupper($key) == $key )
					$lowered[strtolower($key)] = $values;
				else
					$lowered[$key] = $values;

		return $lowered;
	}

	/**
	 * Converts field names for Setters and Getters
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name)
	{
		if( isset($this->_formatNameCache[$name]) )
			return $this->_formatNameCache[$name];

		$result = strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));

		$this->_formatNameCache[$name] = $result;
		return $result;
	}

	/**
	 * Allocate if Method exists and invoke if available
	 * @param string $method
	 * @param string $param1
	 * @param mixed $param2
	 * @return boolean
	 */
	private function callEvent($method, $param1 = NULL, $param2 = NULL)
	{
		if( is_callable(array($this, $method)) && method_exists($this, $method) )
			return $this->{$method}($param1, $param2);

		return true;
	}

	/**
	 * Convert Object data to Array
	 * @param array $arrAttributes
	 * @return array
	 */
	public function toArray(array $arrAttributes = array())
	{
		if( empty($arrAttributes) )
			return $this->_data;

		$arrRes = array();

		foreach( $arrAttributes as $attribute )
			$arrRes[$attribute] = $this->getData($attribute);

		return $arrRes;
	}

	/**
	 * Convert Object data to JSON
	 * @param array $arrAttributes
	 * @return string
	 */
	public function toJson(array $arrAttributes = array())
	{
		$arrData = $this->toArray($arrAttributes);
		$json = json_encode($arrData);

		return $json;
	}

	/**
	 * Will use $format as an template and substitute {{key}} for attributes
	 * @param string $format
	 * @return string
	 */
	public function toString($format = '')
	{
		if( empty($format) )
			return implode(', ', $this->getData());
		else
		{
			preg_match_all('/\{\{([a-z0-9_]+)\}\}/is', $format, $matches);

			foreach( $matches[1] as $var )
				$format = str_replace("{{" . $var . "}}", $this->getData($var), $format);

			return $format;
		}
	}

	/**
	 * Implementation of ArrayAccess::offsetSet()
	 * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}

	/**
	 * Implementation of ArrayAccess::offsetExists()
	 * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetUnset()
	 * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetGet()
	 * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

}
