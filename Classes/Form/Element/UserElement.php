<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Form\Element;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Form\Element\UserElement as BackendFormUserElement;

class UserElement extends BackendFormUserElement
{
    /**
     * Additional ResultArray
     * for merging information
     * that needs to be processed
     *
     * @var array
     */
    public $additionalResultArray = [];

    /**
     * @param string $identifier
     * @return mixed
     */
    public function getData(string $identifier)
    {
        return $this->data[$identifier];
    }

    /**
     * User defined field type
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render(): array
    {
        $parameterArray = $this->data['parameterArray'];
        $parameterArray['table'] = $this->data['tableName'];
        $parameterArray['field'] = $this->data['fieldName'];
        $parameterArray['row'] = $this->data['databaseRow'];
        $parameterArray['parameters'] = isset($parameterArray['fieldConf']['config']['parameters']) ? $parameterArray['fieldConf']['config']['parameters']: [];
        $resultArray = $this->initializeResultArray();
        $result = GeneralUtility::callUserFunction(
            $parameterArray['fieldConf']['config']['userFunc'],
            $parameterArray,
            $this
        );

        if(is_string($result)) {
            $resultArray['html'] = $result;
        } else {
            $resultArray['html'] = '';
        }

        return array_merge($resultArray, $this->additionalResultArray);
    }
}