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
class ValidatorConfigurationRepository extends MagicRepository
{
	/**
	 * Item object class name
	 * @var string
	 */
	protected $_itemObjectClass = 'MageDeveloper\\Dataviewer\\Domain\\Model\\ValidatorConfiguration';

	/**
	 * Finds an validator configuration by an identifier
	 *
	 * @param string $identifier
	 * @return \MageDeveloper\Dataviewer\Domain\Model\ValidatorConfiguration|null
	 */
	public function findByIdentifier($identifier)
	{
		foreach($this->getItems() as $_item)
			if ($_item->getIdentifier() == $identifier)
				return $_item;

		return;
	}
}
