<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
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
class Category extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Category Repository
	 * 
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
	 * @inject
	 */
	protected $categoryRepository;

	/**
	 * Gets categories by ids
	 * 
	 * @param array $ids
	 * @return array
	 */
	protected function _getCategoriesByIds(array $ids)
	{
		$categories = [];
		
		foreach($ids as $_catId)
		{
			$category = $this->categoryRepository->findByUid($_catId);
			if($category instanceof \TYPO3\CMS\Extbase\Domain\Model\Category)
				$categories[] = $category;

		}
				
		return $categories;
	}

	/**
	 * Gets the optimized value for the field
	 *
	 * @return string
	 */
	public function getValueContent()
	{
		return $this->getValue();
	}

	/**
	 * Gets the optimized search string for the field
	 *
	 * @return string
	 */
	public function getSearch()
	{
		$value = $this->getValue();
		$categoryIds = GeneralUtility::trimExplode(",", $value, true);
		$categories = $this->_getCategoriesByIds($categoryIds);

		$categoryStringValue = "";
		foreach($categories as $_cat)
			$categoryStringValue .= $_cat->getTitle().",";
		
		return trim($categoryStringValue,",");
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
	 * @return \TYPO3\CMS\Extbase\Domain\Model\Category[]
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		$categoryIds = GeneralUtility::trimExplode(",", $value, true);
		
		return $this->_getCategoriesByIds($categoryIds);
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
        $categories = $this->getFrontendValue();
        $stringArray = [];

        foreach($categories as $_category)
        {
            $stringArray[] = $_category->getTitle();
        }

        return $stringArray;
    }
}
