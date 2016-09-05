<?php
namespace MageDeveloper\Dataviewer\UserFunc;

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
class Icon
{
	/**
	 * Gets icons
	 *
	 * @param array $config Configuration Array
	 * @param array $parentObject Parent Object
	 * @return array
	 */
	public function displayIconSelection(array &$config, &$parentObject)
	{
		$fieldName		= $config["itemFormElName"];
		$fieldId		= $config["itemFormElID"];
		$value			= $config["itemFormElValue"];
		$checked		= ($value == "")?"checked":"";

		$icons = \MageDeveloper\Dataviewer\Utility\IconUtility::getIcons();
		
		$html = "";

		// Empty - Default Icon
		$html .= "<div style=\"width:50px; height: 30px; float: left; border: 1px solid #c0c0c0; margin:0 3px 3px 0; padding: 3px; \">";
		$html .= "<input type=\"radio\" {$checked} name=\"{$fieldName}\" value=\"\" id=\"empty\" style=\"float:left; margin-right:4px; \">";
		$html .= "<label for=\"empty\" style=\"display:block; width: 22px; float: left;\">" . "</label>";
		$html .= "</div>";

		foreach($icons as $_hash=>$_file)
		{
			$imageSize = getimagesize($_file);
			$xS = $imageSize[0];
			$yS = $imageSize[1];
			$checked = ($value == $_hash)? "checked" : "";

			if ($xS <= 22 && $yS <= 22)
			{
				$html .= "<div style=\"width:50px; height: 30px; float: left; border: 1px solid #c0c0c0; margin:0 3px 3px 0; padding: 3px; \">";
				$html .= "<input type=\"radio\" {$checked} name=\"{$fieldName}\" value=\"{$_hash}\" id=\"{$_hash}\" style=\"float:left; margin-right:4px; \">";
				$html .= "<label for=\"{$_hash}\" style=\"display:block; width: 22px; float: left;\">" . "<img src=\"{$_file}\" border=\"0\" title=\"{$_file}\">" . "</label>";
				$html .= "</div>";
			}

		}

		return $html;
	}
}
