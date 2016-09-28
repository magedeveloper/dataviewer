<?php
namespace MageDeveloper\Dataviewer\Service;

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
class FlexFormService extends \TYPO3\CMS\Extbase\Service\FlexFormService
{
	/**
	 * extractFlexformConfig
	 * Extract a specified flexform config by typename and fieldname
	 *
	 * @param string $conf Flexform Xml Configuration
	 * @param string $type
	 * @param string $field
	 * @return string
	 */
	public function extractFlexformConfig($conf, $field, $type)
	{
		$flexform = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($conf['row']['pi_flexform']);
		$languageKey = "lDEF";
		
		if (is_array($flexform))
		{
			if (array_key_exists("data", $flexform) && !empty($flexform["data"]) && $flexform["data"])
			{
				return $flexform["data"][$type][$languageKey][$field]["vDEF"];
			}

		}

		return '';
	}

	/**
	 * Gets a simple array from the flexform xml
	 * 
	 * @param string $conf Flexform Configuration
	 * @param string $element Element Name
	 * @param string $field Field Name
	 * @param string $item Item Name
	 * @return array
	 */
	public function getSimpleArrayFromFlexForm($conf, $element, $field, $item)
	{
		$extractedConfigurationArray = $this->convertFlexFormContentToArray($conf);
		$simpleArray = [];
		
		if (isset($extractedConfigurationArray[$element]))
		{
			$elements = $extractedConfigurationArray[$element];
			
			if (is_array($elements))
				foreach($elements as $element)
				{
					if(isset($element[$field]) && array_key_exists($item, $element[$field]))
						$simpleArray[] = $element[$field][$item];
				}
			
		
		}
		
		return $simpleArray;
		
	}

	/**
	 * Extracts flexform irre values
	 *
	 * @param array $flexArr
	 * @param string $sectionName
	 * @return array
	 */
	public function extractFlexformIrre($flexArr, $sectionName)
	{
		$values = [];

		if (is_array($flexArr))
		{
			$i = 0;
			foreach($flexArr as $_element)
			{
				if (isset($_element[$sectionName]))
				{
					$data = $_element[$sectionName];

					foreach($data as $_name=>$_subElement)
						$values[$i][$_name] = $_subElement;

				}
				$i++;
			}
		}

		return $values;
	}


	/**
	 * Extracts flexform configuration
	 *
	 * @param array $sourceArray The source array where the information shall be extracted
	 * @param string $node Node Name
	 * @param string $keyField Key Field Name
	 * @param string $valueField Value Field Name
	 * @return array
	 */
	public function extractConfiguration($sourceArray, $node, $keyField, $valueField)
	{
		$configuration = [];

		if (!is_array($sourceArray)) return [];

		foreach($sourceArray as $_data)
		{
			if (array_key_exists($node, $_data))
			{
				$content = $_data[$node];

				if (array_key_exists($keyField, $content) && array_key_exists($valueField, $content))
				{
					$key = $content[$keyField];
					$value = $content[$valueField];
					$configuration[$key] = $value;
				}

			}
		}

		return $configuration;
	}
}
