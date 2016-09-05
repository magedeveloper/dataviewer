<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Field;

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
	 * Field Repository
	 *
	 * @var \MageDeveloper\Dataviewer\Domain\Repository\FieldRepository
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * Fetch a field by id
	 *
	 * @param int $id Id of the record to fetch
	 * @param bool $includeHidden Include hidden records
	 * @return \MageDeveloper\Dataviewer\Domain\Model\Field
	 */
	public function render($id, $includeHidden = false)
	{
		$field = $this->fieldRepository->findByUid($id, !$includeHidden);
		return $field;
	}

}
