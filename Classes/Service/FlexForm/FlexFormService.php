<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\FlexForm;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlexFormService extends \TYPO3\CMS\Core\Service\FlexFormService implements SingletonInterface
{
	/**
	 * extractFlexformConfig
	 * Extract a specified flexform config by typename and fieldname
	 *
	 * @param string $conf Flexform Xml Configuration
	 * @param string $field
	 * @param string $type
	 * @return string
	 */
    public function extractFlexformConfig(array $conf, string $field, string $type): string
	{
        if (!array_key_exists('row', $conf)) {
            return '';
        }

		$flexform = GeneralUtility::xml2array($conf['row']['pi_flexform']);
		$languageKey = "lDEF";

		if (is_array($flexform)) {
			if (array_key_exists("data", $flexform) && !empty($flexform["data"]) && $flexform["data"]) {
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
    public function getSimpleArrayFromFlexForm(string $conf, string $element, string $field, string $item): array
	{
		$extractedConfigurationArray = $this->convertFlexFormContentToArray($conf);
		$simpleArray = [];

		if (isset($extractedConfigurationArray[$element])) {
			$elements = $extractedConfigurationArray[$element];

			if (is_array($elements))
				foreach ($elements as $element) {
					if (isset($element[$field]) && array_key_exists($item, $element[$field])) {
                        $simpleArray[] = $element[$field][$item];
                    }
				}
		}

		return $simpleArray;
	}

	/**
	 * Extracts flexform irre values
	 *
	 * @param mixed $flexArr
	 * @param string $sectionName
	 * @return array
	 */
    public function extractFlexformIrre($flexArr, string $sectionName): array
	{
		$values = [];

		if (is_array($flexArr)) {
			$i = 0;
			foreach ($flexArr as $_element) {
				if (isset($_element[$sectionName])) {
					$data = $_element[$sectionName];
					foreach ($data as $_name=>$_subElement) {
                        $values[$i][$_name] = $_subElement;
                    }
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
    public function extractConfiguration(array $sourceArray, string $node, string $keyField, string $valueField): array
	{
		$configuration = [];

        if (!is_array($sourceArray)) {
            return [];
        }

		foreach ($sourceArray as $_data) {
			if (array_key_exists($node, $_data)) {
				$content = $_data[$node];

				if (array_key_exists($keyField, $content) && array_key_exists($valueField, $content)) {
					$key = $content[$keyField];
					$value = $content[$valueField];
					$configuration[$key] = $value;
				}

			}
		}

		return $configuration;
	}
}
