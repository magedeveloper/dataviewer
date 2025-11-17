<?php
declare(strict_types=1);

namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Icon\DataviewerIconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class Icon
{
	/**
	 * Gets icons
	 *
	 * @param array $config Configuration Array
	 * @param mixed $parentObject Parent Object
	 * @return array
     * @throws InvalidConfigurationTypeException
	 */
	public function displayIconSelection(array &$config, &$parentObject): string
	{
		$fieldName		= $config["itemFormElName"];
		$fieldId		= $config["itemFormElID"];
		$value			= $config["itemFormElValue"];
		$checked		= ($value == "")?"checked":"";
		$onChange       = $config["fieldChangeFunc"]["TBE_EDITOR_fieldChanged"];
        //onchange=\"{$onChange}\"

		/* @var DataviewerIconRegistry $dvIconRegistry */
		$dvIconRegistry = GeneralUtility::makeInstance(DataviewerIconRegistry::class);
		$icons = $dvIconRegistry->getIcons(['EXT:dataviewer/Resources/Public/Icons/Datatype'],'extensions-dataviewer-',false);

		$html = "";

        $border = "1px solid #c0c0c0";
        if ($checked == "checked") {
            $border = "2px solid #000";
        }

		// Empty - Default Icon
		$html .= "<div style=\"width:50px; height: 30px; border: {$border}; margin:0 3px 3px 0; padding: 3px; \">";
		$html .= "<input type=\"radio\" {$checked} id=\"{$fieldId}\" name=\"{$fieldName}\" value=\"\" id=\"empty\" style=\"float:left; margin-right:4px; \">";
		$html .= "<label for=\"empty\" style=\"display:block; width: 22px; float: left;\">" . "</label>";
		$html .= "</div>";

		$i = 1;
		foreach ($icons as $_hash=>$_file) {
			$file = GeneralUtility::getFileAbsFileName($_file);
			if (file_exists($file)) {
				$imageSize = getimagesize($file);
                if(!is_array($imageSize)) {
                    continue;
                }
				$xS = $imageSize[0];
				$yS = $imageSize[1];
				$checked = ($value == $_hash)? "checked" : "";
                $border = "1px solid #c0c0c0";
                if ($value == $_hash) {
                    $border = "2px solid #000";
                }

				$img = PathUtility::getAbsoluteWebPath($file);
				if ($xS <= 22 && $yS <= 22) {
					$html .= "<div style=\"width:50px; height: 30px; float: left; border: {$border}; margin:0 3px 3px 0; padding: 3px; \">";
					$html .= "<input type=\"radio\" {$checked} id=\"{$fieldId}_{$i}\" name=\"{$fieldName}\" value=\"{$_hash}\" id=\"{$_hash}\" style=\"float:left; margin-right:4px; \">";
					$html .= "<label for=\"{$_hash}\" style=\"display:block; width: 22px; float: left;\">" . "<img src=\"{$img}\" border=\"0\" title=\"extensions-dataviewer-{$_hash}\">" . "</label>";
					$html .= "</div>";
				}

				$i++;
			}
		}
		return $html;
	}
}
