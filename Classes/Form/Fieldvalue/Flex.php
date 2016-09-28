<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

use MageDeveloper\Dataviewer\Domain\Model\Field;
use MageDeveloper\Dataviewer\Utility\CheckboxUtility;
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
class Flex extends AbstractFieldvalue implements FieldvalueInterface
{
	/**
	 * FlexForm Service
	 *
	 * @var \MageDeveloper\Dataviewer\Service\FlexFormService
	 * @inject
	 */
	protected $flexFormService;

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
		$flexArr = $this->flexFormService->convertFlexFormContentToArray($value);
		return json_encode($flexArr);
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
	 * @return array
	 */
	public function getFrontendValue()
	{
		$value = $this->getValue();
		return $this->flexFormService->convertFlexFormContentToArray($value);
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
        // This is so much dynamic, that we can't convert any information to a string
        // for filling our arrays, so we give up here
        return [];
    }
}
