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
class ValidatorConfiguration extends MagicModel
{
	/**
	 * Validator Identifer
	 *
	 * @var string
	 */
	protected $identifier;

	/**
	 * Class Name for validator
	 * 
	 * @var string
	 */
	protected $validatorClass;

	/**
	 * Label
	 *
	 * @var string
	 */
	protected $label;

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
	public function getValidatorClass()
	{
		return $this->validatorClass;
	}

	/**
	 * @param string $validatorClass
	 */
	public function setValidatorClass($validatorClass)
	{
		$this->validatorClass = $validatorClass;
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
