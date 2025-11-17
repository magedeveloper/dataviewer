<?php
namespace Aix\Dataviewer\Icon;

use Aix\Dataviewer\Configuration\ExtensionConfiguration;
use Aix\Dataviewer\Utility\StringUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

class DataviewerIconRegistry
{
    /**
     * @var IconRegistry
     */
    protected $iconRegistry;

    /**
     * @return IconRegistry
     */
    protected function _getIconRegistry()
    {
        if (!$this->iconRegistry) {
            $this->iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        }

        return $this->iconRegistry;
    }

    /**
     * Registers all existing datatypes with
     * icons
     * @return void
     */
    public function registerDatatypeIcons(): void
    {
        ///////////////////////////////////////////////////////////
        // We generate page icons for each datatype, that exists //
        ///////////////////////////////////////////////////////////
        $datatypes = [];
        try {
            /* @var \TYPO3\CMS\Core\Database\Connection $query */
            $query = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(ExtensionConfiguration::EXTENSION_DATATYPE_TABLE);

            $datatypes = $query->select(['uid', 'name', 'icon'], 'tx_dataviewer_domain_model_datatype',['deleted'=>0])->fetchAllAssociative();

        } catch (\Exception $e) {
            $datatypes = [];
        }

        try {
            $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
                'label' => 'LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:tx_dataviewer',
                'value' => '--div--',
            ];

            $icons = $this->getIcons(['EXT:dataviewer/Resources/Public/Icons/Datatype']);
            $bitmapProviderClassName = BitmapIconProvider::class;

            // We need to add the selected datatype icon to the registry
            foreach ($datatypes as $_datatype) {

                if($_datatype['icon'] === '') {
                    continue;
                }

                $iconId = 'dataviewer-datatype-'.$_datatype['uid'];

                // Register icon
                if (!$this->_getIconRegistry()->isRegistered($iconId)) {
                    $source = $icons['extensions-dataviewer-'.$_datatype['icon']];
                    if(!is_null($source)) {
                        $this->_getIconRegistry()->registerIcon($iconId, $bitmapProviderClassName, ['source' => $source]);
                    }
                }

                if (!isset($GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']["contains-dataviewer-{$iconId}"])) {
                    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
                        'label' => $_datatype['name'],
                        'value' => $iconId,
                        'icon' => $iconId
                    ];
                    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']["contains-{$iconId}"] = $iconId;
                }
            }

            $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
                'label' => 'LLL:EXT:backend/Resources/Private/Language/locallang_view_help.xlf:TOC_extensions',
                'value' => '--div--',
            ];

        } catch(Exception $e) {
            // We need to ignore exceptions here in case the table does not exist
            // No exception printing here
        }
    }

    /**
     * Registers all general dataviewer icons
     * that are delivered within this package
     * @return void
     */
    public function registerDataviewerIcons(): void
    {
        $iconsDatatypes = $this->getIcons(['EXT:dataviewer/Resources/Public/Icons/Datatype']);
        $iconsFields = $this->getIcons(['EXT:dataviewer/Resources/Public/Icons/Field'],'extensions-dataviewer-field-');
        $bitmapProviderClassName = BitmapIconProvider::class;

        $icons = array_merge($iconsDatatypes,$iconsFields);
        foreach ($icons as $_id=>$_location) {
            if (!$this->_getIconRegistry()->isRegistered($_id)) {
                $this->_getIconRegistry()->registerIcon(
                    $_id,
                    $bitmapProviderClassName,
                    ['source' => $_location]
                );
            }
        }
    }

    /**
     * Gets all typeicon classes for datatypes
     * @return array
     */
    public function getDatatypeTypeiconClasses(): array
    {
        $icons = $this->getIcons(['EXT:dataviewer/Resources/Public/Icons/Datatype']);
        $classes = [];

        foreach ($icons as $_iconFile) {
            $pathinfo 		= pathinfo($_iconFile);
            $code           = StringUtility::createCodeFromString($pathinfo['filename']);
            //$code = str_replace('_','-', $code);
            $classes[$code] = "extensions-dataviewer-{$code}";
        }

        // Default Icon
        $classes['default'] = 'dataviewer-datatype-icon-svg';
        return $classes;
    }

    /**
     * Gets all typeicon classes for fields
     * @return array
     */
    public function getFieldTypeIconClasses(): array
    {
        $icons = $this->getIcons(['EXT:dataviewer/Resources/Public/Icons/Field'], 'extensions-dataviewer-field-');

        foreach ($icons as $_iconFile) {
            $pathinfo = pathinfo($_iconFile);
            $code = StringUtility::createCodeFromString($pathinfo['filename']);
            //$code = str_replace('_','-', $code);
            $classes[$code] = "extensions-dataviewer-field-{$code}";
        }

        $classes['default'] = 'dataviewer-field-icon-svg';

        return $classes;
    }

    /**
     * Gets all icons from the typo3/gfx folder
     *
     * @param array $paths
     * @param string $prefix
     * @param bool $includePrefix
     * @param bool $convertPaths
     * @return array
     * @throws InvalidConfigurationTypeException
     */
    public function getIcons(array $paths = [], string $prefix = 'extensions-dataviewer-', bool $includePrefix = true, bool $convertPaths = true): array
    {
        if (!$includePrefix) {
            $prefix = '';
        }

        $icons = [];
        foreach ($paths as $_path) {
            // We check all paths for icons and add them to the registry
            $path = GeneralUtility::getFileAbsFileName($_path);
            $files = GeneralUtility::getAllFilesAndFoldersInPath([], $path, 'gif,png');

            foreach ($files as $_iconFile) {
                $iconPath = $path;
                if ($convertPaths == false) {
                    $iconPath = $_path;
                }

                $pathinfo = pathinfo($_iconFile);
                $filename = basename($_iconFile);
                $_path = trim($_path, '/');
                $code = StringUtility::createCodeFromString($pathinfo['filename']);
                $code = "{$prefix}{$code}";
                //$code = str_replace('_','-', $code);
                $icons[$code] = $iconPath.'/'.$filename;
            }
        }

        // Plugin Icons
        $icons['dataviewer-icon-dataviewer-record'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/Record.svg';
        //$icons['dataviewer-icon-dataviewer-search'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-search.gif';
        //$icons['dataviewer-icon-dataviewer-letter'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-letter.gif';
        //$icons['dataviewer-icon-dataviewer-sort'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-sort.gif';
        //$icons['dataviewer-icon-dataviewer-filter'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-filter.gif';
        //$icons['dataviewer-icon-dataviewer-select'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-select.gif';
        //$icons['dataviewer-icon-dataviewer-form'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-form.gif';
        //$icons['dataviewer-icon-dataviewer-pager'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/dataviewer-pager.gif';

        // Extension Icon
        $icons['dataviewer-ext-icon-svg'] = 'EXT:dataviewer/ext_icon.svg';
        // Toolbar Icon
        $icons['dataviewer-toolbar-icon-svg'] = 'EXT:dataviewer/Resources/Public/Icons/toolbar_icon.svg';
        // Datatype Icon
        $icons['dataviewer-datatype-icon-svg'] = 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/datatype.svg';
        // Field Icon
        $icons['dataviewer-field-icon-svg'] = 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/field.svg';
        // FieldValue Icon
        $icons['dataviewer-fieldvalue-icon-svg'] = 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/fieldvalue.svg';
        // Variable Icon
        $icons['dataviewer-variable-icon-svg'] = 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/variable.svg';
        // Extension Icon
        $icons['dataviewer-ext-icon-svg'] = 'EXT:dataviewer/ext_icon.svg';

        /****************************
         * Additional Icons
         ***************************/
        $additionalIcons = [];

        // Plugin Icons
        $additionalIcons['dataviewer-icon-dataviewer-record']  = 'EXT:dataviewer/Resources/Public/Icons/Plugins/Record.svg';
        $additionalIcons['dataviewer-icon-dataviewer-list']    = 'EXT:dataviewer/Resources/Public/Icons/Plugins/List.svg';
        $additionalIcons['dataviewer-icon-dataviewer-detail']  = 'EXT:dataviewer/Resources/Public/Icons/Plugins/Detail.svg';
        $additionalIcons['dataviewer-icon-dataviewer-dynamic'] = 'EXT:dataviewer/Resources/Public/Icons/Plugins/Dynamic.svg';
        $additionalIcons['dataviewer-icon-dataviewer-plain']   = 'EXT:dataviewer/Resources/Public/Icons/Plugins/Plain.svg';

        if (class_exists(ConfigurationManagerInterface::class)) {
            $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
            $configuration        = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

            if (isset($configuration['plugin.']['tx_dataviewer.']['fieldtypes.']) && is_array($configuration['plugin.']['tx_dataviewer.']['fieldtypes.'])) {
                $fieldtypes = GeneralUtility::removeDotsFromTS($configuration['plugin.']['tx_dataviewer.']['fieldtypes.']);
                foreach ($fieldtypes as $_t => $_ft) {
                    $ft = strtolower($_t);
                    //$ft = str_replace('_','-', $ft);
                    $ftIcon                            = (isset($_ft['icon'])) ? $_ft['icon'] : 'EXT:dataviewer/Resources/Public/Icons/Domain/Model/Field.gif';
                    $additionalIcons["{$prefix}{$ft}"] = $ftIcon;
                }
            }
        }

        foreach ($additionalIcons as $_identifier => $_path) {
            $icons[$_identifier] = GeneralUtility::getFileAbsFileName($_path);
        }

        return $icons;
    }

    /**
     * Gets an specific icon by hash
     * @param string $hash
     * @return string
     * @throws InvalidConfigurationTypeException
     */
    public function getIconByHash(string $hash): string
    {
        $icons = $this->getIcons();
        foreach ($icons as $_hash=>$icon) {
            if ($_hash == $hash) {
                return $icon;
            }
        }
        return '';
    }
}
