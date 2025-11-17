<?php
namespace Aix\Dataviewer\UserFunc;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class Extension
{
    /**
     * Populate extensions
     * @param array $config Configuration Array
     * @param mixed $parentObject Parent Object
     * @return void
     */
    public function populateLoadedExtensions(array &$config, &$parentObject): void
    {
        $options = [];
        $extList = ExtensionManagementUtility::getLoadedExtensionListArray();
        sort($extList);

        foreach($extList as $_i=>$_ext) {
            $options[] = [
                'label' => $_ext,
                'value' => $_ext
            ];
        }

        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $options);
        } else {
            $config['items'] = $options;
        }
    }

}
