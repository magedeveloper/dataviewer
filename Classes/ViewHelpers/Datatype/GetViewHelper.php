<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Datatype;

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
	 * Datatype Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\DatatypeRepository
	 * @inject
	 */
	protected $datatypeRepository;

	/**
	 * Fetch a datatype by id
	 * 
	 * @param int $id Id of the record to fetch
	 * @param bool $includeHidden Include hidden records
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Datatype
	 */
	public function render($id, $includeHidden = false)
	{
		$record = $this->datatypeRepository->findByUid($id, !$includeHidden);
		return $record;
	}

}
