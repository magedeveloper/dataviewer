<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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
class Page extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * Page Repository
	 * 
	 * @var \TYPO3\CMS\Frontend\Page\PageRepository
	 * @inject
	 */
	protected $pageRepository;

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
		$value 			= $this->getValue();
		$values 		= GeneralUtility::trimExplode(",", $value, true);
		$searchFields 	= $GLOBALS["TCA"]["pages"]["ctrl"]["searchFields"];

		$searchString = "";
		
		foreach($values as $_pageId)
		{
			$valueArr = BackendUtility::getRecord("pages", $value, $searchFields);
			if(is_array($valueArr))
				$searchString .= implode(",", $valueArr);

		}
			
		return $searchString;	
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
	 * @return mixed
	 */
	public function getFrontendValue()
	{
		$value 			= $this->getValue();
		$values 		= GeneralUtility::trimExplode(",", $value, true);
		
		$pages = [];
		foreach($values as $_pageId)
		{
			$page = $this->pageRepository->getPage($_pageId, true);
			$pages[] = $page;	
		}
		
		return $pages;
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
        $values = $this->getFrontendValue();
        $stringArray = [];

        foreach($values as $_value)
        {
            $stringArray[] = $_value["title"];
        }

        return $stringArray;
    }
}
