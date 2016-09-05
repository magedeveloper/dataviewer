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
class FieldtypeConfiguration extends MagicModel
{
	/**
	 * Fieldtype Identifer
	 * 
	 * @var string
	 */
	protected $identifier;

	/**
	 * Label
	 * 
	 * @var string
	 */
	protected $label;

	/**
	 * Fieldtype Field Class
	 * 
	 * @var string
	 */
	protected $fieldClass;

	/**
	 * Fieldtype Value Class
	 * 
	 * @var string
	 */
	protected $valueClass;

	/**
	 * Evaluation Class Name
	 * 
	 * @var string
	 */
	protected $evalClass;

	/**
	 * Fieldtype Icon Url
	 * 
	 * @var string
	 */
	protected $icon;

	/**
	 * Fieldtype Additional XML Flexform Configuration
	 * 
	 * @var string
	 */
	protected $flexConfiguration;
	
	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
	}

	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getFieldClass()
	{
		return $this->fieldClass;
	}

	/**
	 * @param string $fieldClass
	 */
	public function setFieldClass($fieldClass)
	{
		$this->fieldClass = $fieldClass;
	}

	/**
	 * @return string
	 */
	public function getValueClass()
	{
		return $this->valueClass;
	}

	/**
	 * @param string $valueClass
	 */
	public function setValueClass($valueClass)
	{
		$this->valueClass = $valueClass;
	}

	/**
	 * @return string
	 */
	public function getEvalClass()
	{
		return $this->evalClass;
	}

	/**
	 * @param string $evalClass
	 */
	public function setEvalClass($evalClass)
	{
		$this->evalClass = $evalClass;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getFlexConfiguration()
	{
		return $this->flexConfiguration;
	}

	/**
	 * @param string $flexConfiguration
	 */
	public function setFlexConfiguration($flexConfiguration)
	{
		$this->flexConfiguration = $flexConfiguration;
	}
	
}
