<?php
namespace MageDeveloper\Dataviewer\Form\Fieldvalue;

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
interface FieldvalueInterface
{
    /**
     * Gets the optimized value for the field
     *
     * @return string
     */
    public function getValueContent();

    /**
     * Gets the optimized search string for the field
     *
     * @return string
     */
    public function getSearch();

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
    public function getFrontendValue();

    /**
     * Gets the value or values as a plain string-array for
     * usage in different positions to show
     * and use it when needed as a string
     *
     * @return array
     */
    public function getValueArray();
}
