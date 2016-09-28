<?php
namespace MageDeveloper\Dataviewer\Domain\Repository;

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
class MagicRepository implements \IteratorAggregate, \Countable, \ArrayAccess
{
	/**
	 * Filter Logics
	 * @var string
	 */
	const LOGIC_SEQ		= "seq";
	const LOGIC_EQ		= "eq";
	const LOGIC_SNEQ	= "sneq";
	const LOGIC_NEQ		= "neq";
	const LOGIC_LT		= "lt";
	const LOGIC_GT		= "gt";
	const LOGIC_LTE 	= "lte";
	const LOGIC_GTE		= "gte";
	const LOGIC_LIKE	= "like";
	const LOGIC_IN		= "in";

	/**
	 * Collection items
	 * @var Object[]
	 */
	protected $_items = [];

	/**
	 * Filters
	 * @var array
	 */
	protected $_filters = [];
	protected $_isFiltersRendered;

	/**
	 * Item object class name
	 * @var string
	 */
	protected $_itemObjectClass = 'MageDeveloper\\Dataviewer\\Domain\\Model\\MagicModel';

	/**
	 * Total Records
	 * @var int
	 */
	protected $_totalRecords;

	/**
	 * if data is set load all items in new collection
	 * @param array $data
	 *
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\MagicRepository
	 */
	public function __construct($data = NULL)
	{
		if( is_array($data) )
			foreach( $data as $object )
				$this->addItem(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->_itemObjectClass($object)));

		return $this;
	}

	/**
	 * Gets the size of the collection
	 * @return int
	 */
	public function getSize()
	{
		if( is_null($this->_totalRecords) )
			$this->_totalRecords = count($this->getItems());

		return intval($this->_totalRecords);
	}

	/**
	 * Retrieve collection first item
	 * @return Object
	 */
	public function getFirstItem()
	{
		if (count($this->_items))
		{
			reset($this->_items);
			return current($this->_items);
		}

		return new $this->_itemObjectClass();
	}

	/**
	 * Retrieve collection last item
	 * @return Object
	 */
	public function getLastItem()
	{
		if( count($this->_items) )
			return end($this->_items);

		return new $this->_itemObjectClass();
	}

	/**
	 * Retrieve collection items
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicObject[]
	 */
	public function getItems()
	{
		return $this->_items;
	}

	/**
	 * Retrieve field values from all items
	 * @param   string $colName
	 * @return  array
	 */
	public function getColumnValues($colName)
	{
		$col = [];

		foreach( $this->getItems() as $_item )
			$col[] = $_item->getData($colName);

		return $col;
	}

	/**
	 * Search all items by field value
	 * @param   string $column
	 * @param   mixed $value
	 * @return  array
	 */
	public function getItemsByColumnValue($column, $value)
	{
		$res = [];

		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		foreach( $this as $item )
			if( $item->getData($column) == $value )
				$res[] = $item;

		return $res;
	}

	/**
	 * Search first item by field value
	 * @param   string $column
	 * @param   mixed $value
	 * @return  Object || null
	 */
	public function getItemByColumnValue($column, $value)
	{
		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		foreach( $this as $item )
			if( $item->getData($column) == $value )
				return $item;

		return null;
	}

	/**
	 * Adding item to item array
	 *
	 * @param \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item Object
	 * @throws \Exception
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\MagicRepository
	 */
	public function addItem(\MageDeveloper\Dataviewer\Domain\Model\MagicModel $item)
	{
		$itemId = $this->_getItemId($item);

		if( !is_null($itemId) )
		{
			if( isset($this->_items[$itemId]) )
				throw new \RuntimeException("Item (" . get_class($item) . ") with the same id \"" . $item->getId() . "\" already exists");

			$this->_items[$itemId] = $item;
		}
		else
		{
			$this->_items[] = $item;
		}

		$this->_totalRecords++;
		return $this;
	}

	/**
	 * Gets an specific item of the collection
	 * @param int $num Number of the Item to get
	 * @return $this->_itemObjectClass
	 */
	public function getItem($num)
	{
		if( $num >= 0 && $num < count($this->_items) )
			return $this->_items[$num];

		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->_itemObjectClass([], false));
	}

	/**
	 * Retrieve item id
	 * 
	 * @param \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item
	 * @return mixed
	 */
	protected function _getItemId(\MageDeveloper\Dataviewer\Domain\Model\MagicModel $item)
	{
		return $item->getId();
	}

	/**
	 * Retrieve ids of all tems
	 * @return array
	 */
	public function getAllIds()
	{
		$ids = [];

		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		foreach( $this->getItems() as $item )
			$ids[] = $this->_getItemId($item);

		return $ids;
	}

	/**
	 * Remove item from collection by item key
	 * @param mixed $key
	 * @return $this
	 */
	public function removeItemByKey($key)
	{
		if( isset($this->_items[$key]) )
			unset($this->_items[$key]);

		return $this;
	}

	/**
	 * Clear collection
	 * @return $this
	 */
	public function clear()
	{
		$this->_items = [];
		return $this;
	}

	/**
	 * Returns if the collection is empty or not
	 * @return boolean
	 */
	public function isEmpty()
	{
		return $this->count() < 1;
	}

	/**
	 * Check if given object exists in collection
	 * @param mixed $obj
	 * @return boolean
	 */
	public function contains($obj)
	{
		foreach( $this->getItems() as $_item )
			if( $_item === $obj )
				return true;

		return false;
	}

	/**
	 * Return the first index of given object
	 * @param mixed $obj
	 * @return string
	 */
	public function indexOf($obj)
	{
		foreach( $this->_items as $k => $_item )
			if( $_item === $obj )
				return $k;

		return null;
	}

	/**
	 * Walk through the collection and run model method or external callback with optional arguments
	 * Returns array with results of callback for each item
	 * @param $callback
	 * @param array $args
	 * @return array
	 */
	public function walk($callback, array $args = [])
	{
		$results = [];
		$useItemCallback = is_string($callback) && strpos($callback, "::") === false;

		foreach( $this->getItems() as $id => $item )
		{
			if( $useItemCallback )
				$cb = [$item, $callback];
			else
			{
				$cb = $callback;
				array_unshift($args, $item);
			}

			$results[$id] = call_user_func_array($cb, $args);
		}

		return $results;
	}

	/**
	 * Walk through all given items and run model method or external callback
	 * @param $callback
	 * @param array $items
	 * @return void
	 */
	public function each($callback, $items = [])
	{
		foreach( $items as $k => $item )
			$items[$k] = call_user_func($callback, $item);
	}

	/**
	 * Return a list of keys to all objects
	 * @return array
	 */
	public function keys()
	{
		return array_keys($this->_items);
	}

	/**
	 * Check if an item with key exits
	 * @param int $key Key to check
	 * @return bool
	 */
	public function exists($key)
	{
		return isset($this->_items[$key]);
	}

	/**
	 * Setting data for all collection items
	 * @param mixed $key
	 * @param mixed $value
	 * @return \MageDeveloper\Dataviewer\Domain\Repository\MagicRepository
	 */
	public function setDataToAll($key, $value = null)
	{
		if( is_array($key) )
		{
			foreach ($key as $k=>$v)
				$this->setDataToAll($k, $v);

			return $this;
		}

		foreach( $this->getItems() as $item )
			$item->setData($key, $value);

		return $this;
	}

	/**
	 * Set collection item class name
	 * @param string $className
	 * @return self
	 */
	public function setItemObjectClass($className)
	{
		$this->_itemObjectClass = $className;
		return $this;
	}

	/**
	 * Retrieve collection empty item
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function getNewEmptyItem()
	{
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->_itemObjectClass);
	}

	/**
	 * Get a new empty item object
	 * @param array $data
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function getNewItemWithData($data = [])
	{
		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		$item = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->_itemObjectClass);
		$item->addData($data);

		return $item;
	}

	/**
	 * Counts all items
	 * @return int
	 */
	public function count()
	{
		return count($this->_items);
	}

	/**
	 * Counts all items
	 * @return int
	 */
	public function length()
	{
		return count($this->_items);
	}

	/**
	 * Convert collection to array
	 * @param array $arrRequiredFields
	 * @return array
	 */
	public function toArray($arrRequiredFields = [])
	{
		$arrItems = [];
		$arrItems["totalRecords"] = $this->getSize();
		$arrItems["items"] = [];

		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		foreach( $this as $item )
			$arrItems["items"][] = $item->toArray($arrRequiredFields);

		return $arrItems;
	}

	/**
	 * Convert items array to array for select options
	 * 
	 * @param string $valueField
	 * @param string $labelField
	 * @param array $additional Additional Options
	 * @return array
	 */
	protected function _toOptionArray($valueField = "id", $labelField = "name", $additional = [])
	{
		$res = [];
		$additional['value'] = $valueField;
		$additional['label'] = $labelField;

		/** @var \MageDeveloper\Dataviewer\Domain\Model\MagicModel $item */
		foreach( $this as $item )
		{
			$data = [];

			foreach( $additional as $code => $field )
				$data[$code] = $item->getData($field);

			$res[] = $data;
		}

		return $res;
	}

	/**
	 * Convert items array to array for select options
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray();
	}

	/**
	 * Serializes containing data
	 * 
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->_items);
	}

	/**
	 * Unserializes given data and stores it into the collection
	 * 
	 * @param mixed $data Data
	 */
	public function unserialize($data)
	{
		$this->_items = unserialize($data);
	}

	/**
	 * Retrieve item by id
	 * @param mixed $idValue
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function getItemById($idValue)
	{
		if( isset($this->_items[$idValue]) )
			return $this->_items[$idValue];

		return null;
	}

	/**
	 * Implementation of IteratorAggregate::getIterator()
	 * 
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->_items);
	}

	/**
	 * Implementation of ArrayAccess::offsetSet()
	 * 
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->_items[$offset] = $value;
	}

	/**
	 * Implementation of ArrayAccess::offsetExists()
	 * 
	 * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
	 * @param string $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->_items[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetUnset()
	 * 
	 * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->_items[$offset]);
	}

	/**
	 * Implementation of ArrayAccess::offsetGet()
	 * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
	 * @param string $offset
	 * @return \MageDeveloper\Dataviewer\Domain\Model\MagicModel
	 */
	public function offsetGet($offset)
	{
		return isset($this->_items[$offset]) ? $this->_items[$offset] : null;
	}

	/**
	 * Adds an field to filter
	 * @param string $field
	 * @param string $value
	 * @param string $logic
	 * @return $this
	 */
	public function addFieldToFilter($field, $value, $logic = self::LOGIC_EQ)
	{
		$filter = [];
		$filter["field"] = $field;
		$filter["value"] = $value;
		$filter["logic"] = strtolower($logic);

		$this->_filters[] = $filter;
		$this->_filterCollection($field, $value, $logic);

		return $this;
	}

	/**
	 * Gets a collection of filtered items
	 * 
	 * @param string $field Field Name
	 * @param mixed $value Value
	 * @param string $logic the logic to match
	 * @return $this
	 */
	private function _filterCollection($field, $value, $logic = self::LOGIC_EQ)
	{
		$filteredCollection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(self::class);

		// only convert value once
		if( $logic == self::LOGIC_IN )
			$value = is_array($value) ? $value : explode(",", $value);

		foreach( $this->_items as $item )
		{
			if( !($item instanceof \MageDeveloper\Dataviewer\Domain\Model\MagicModel) )
				continue;

			switch( $logic )
			{
				case self::LOGIC_IN:
					if( in_array($item->getData($field), $value) ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_LIKE:
					if( strpos(strtolower($item->getData($field)), strtolower($value)) !== false ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_GT:
					if( floatval($item->getData($field)) > floatval($value) ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_LT:
					if( floatval($item->getData($field)) < floatval($value) ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_GTE:
					if( floatval($item->getData($field)) >= floatval($value) ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_LTE:
					if( floatval($item->getData($field)) <= floatval($value) ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_NEQ:
					if( $item->getData($field) != $value ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_SNEQ:
					if( $item->getData($field) !== $value ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_SEQ:
					if( $item->getData($field) === $value ) $filteredCollection->addItem($item);
					break;

				case self::LOGIC_EQ:
				default:
					if( $item->getData($field) == $value ) $filteredCollection->addItem($item);
					break;

			}
		}

		$this->_isFiltersRendered = true;
		$this->_items = $filteredCollection->getItems();

		unset($filteredCollection);

		return $this;
	}


}
