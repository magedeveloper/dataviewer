<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Page;

use MageDeveloper\Dataviewer\ViewHelpers\AbstractViewHelper;

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
class TitleViewHelper extends AbstractViewHelper
{
	const PREPEND_TITLE 	= "prepend";
	const APPEND_TITLE 		= "append";
	const REPLACE_TITLE		= "replace";

	/**
	 * Set the page title
	 *
	 * @param string $mode Mode for adding the title to the existing one or new one
	 * @param string $glue The glue to add the custom title
	 * @return void
	 */
	public function render($mode = self::REPLACE_TITLE, $glue = " - ")
	{
		$renderedContent = $this->renderChildren();

		$existingTitle = $GLOBALS["TSFE"]->page["title"];

		if ($mode === self::PREPEND_TITLE && !empty($existingTitle))
		{
			$newTitle = $renderedContent.$glue.$existingTitle;
		}
		else if ($mode === self::APPEND_TITLE && !empty($existingTitle))
		{
			$newTitle = $existingTitle.$glue.$renderedContent;
		}
		else
		{
			$newTitle = $renderedContent;
		}

		$GLOBALS["TSFE"]->page["title"] = $newTitle;
		$GLOBALS["TSFE"]->indexedDocTitle = $newTitle;

		return;
	}

}