<?php

declare(strict_types=1);
namespace Aix\Dataviewer\Service\Settings;

use Aix\Dataviewer\Domain\Model\Field;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FieldSettingsService extends AbstractSettingsService implements SingletonInterface
{
	/**
	 * Field Configuration
	 *
	 * @var array
	 */
	protected $fieldConfig = [];

	/**
	 * Gets the complete field configuration from
	 * the plugin settings in typoscript
	 * @return array
	 */
	public function getFieldConfiguration(): array
	{
	    $fieldConfiguration = $this->getConfiguration('plugin.tx_dataviewer.fieldtypes');
        return GeneralUtility::removeDotsFromTS($fieldConfiguration);
	}

	/**
	 * Gets the according field configuration by
	 * a given field type identifier
	 *
	 * @param string $type
	 * @return array
	 */
	public function getFieldTypeConfiguration(string $type): array
	{
	    $fieldConfig =  $this->getFieldConfiguration();
	    if (array_key_exists($type, $fieldConfig)) {
	        return $fieldConfig[$type];
        }
	    return [];
	}

    /**
     * Gets the tca flexform configuration
     * @return array
     */
	public function getTcaFlexFormConfiguration(): array
    {
        $dsConfig = [];

        $typesConfiguration = $this->getFieldConfiguration();
        foreach ($typesConfiguration as $_id=>$_config) {
            if (isset($_config['flexform'])) {
                $dsConfig[$_id] = 'FILE:'.$_config['flexform'];
            }
        }

        return $dsConfig;
    }

    /**
     * @param Field $field
     * @return string|null
     */
    public function getValueGeneratorClass(Field $field): ?string
    {
        $fieldConfiguration = $this->getFieldConfiguration();
        if(isset($fieldConfiguration[$field->getType()]['value']) && $fieldConfiguration[$field->getType()]['value'] != '') {
            return $fieldConfiguration[$field->getType()]['value'];
        }
        return null;
    }

    /**
     * Gets an array with all declared field types that
     * are configured with a value generator class
     * @return array
     */
    public function getFieldTypesWithValueGenerator(): array
    {
        $fieldTypes = [];
        $fieldConfiguration = $this->getFieldConfiguration();

        foreach($fieldConfiguration as $fT=>$_fc) {
            if(isset($_fc['value'])) {
                if(class_exists($_fc['value'])) {
                    $fieldTypes[] = $fT;
                }
            }
        }

        return $fieldTypes;
    }
}
