<?php
namespace MageDeveloper\Dataviewer\Form\Fieldtype;

use MageDeveloper\Dataviewer\Domain\Model\FieldValue as FieldValue;
use MageDeveloper\Dataviewer\Domain\Model\RecordValue as RecordValue;

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
interface FieldtypeInterface
{
	/**
	 * Initializes all form data providers to
	 * $this->formDataProviders
	 * 
	 * Will be executed in order of the added providers! 
	 * 
	 * @return void
	 */
	public function initializeFormDataProviders();

	/**
	 * Gets built tca array
	 *
	 * @return array
	 */
	public function buildTca();

}
