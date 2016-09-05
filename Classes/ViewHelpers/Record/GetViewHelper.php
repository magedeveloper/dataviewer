<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Record;

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
	 * Record Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\RecordRepository
	 * @inject
	 */
	protected $recordRepository;

	/**
	 * Fetch a record by id
	 * 
	 * @param int $id Id of the record to fetch
	 * @param bool $includeHidden Include hidden records
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Record
	 */
	public function render($id, $includeHidden = false)
	{
		$record = $this->recordRepository->findByUid($id, !$includeHidden);
		return $record;
	}

}
