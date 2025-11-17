<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Controller\Backend;

use Aix\Dataviewer\Domain\Model\AbstractRecordModel;
use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Repository\AbstractRepository;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Utility\LocalizationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class QueryBuilderController extends AbstractBackendController
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * @param IconFactory $iconFactory
     */
    public function injectIconFactory(IconFactory $iconFactory)
    {
        $this->iconFactory = $iconFactory;
    }

    /**
     * Get querybuilder configuration
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function getConfigurationAction(ServerRequestInterface $request)
    {
        $parameters = $request->getParsedBody();
        $pids = ($parameters['pids']) ?? [];
        $languageUid = (int)$parameters['languageUid'];
        if (!is_array($pids)) {
            $pids = [];
        }

        $datatypeUid = (int)$parameters['datatype'];
        $filters = $this->_getFilters($datatypeUid, $pids, $languageUid);

        /* @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);

        $configuration = [
            'filters' => $filters,
            'allow_empty' => true,
            'select_placeholder' => '',
            'operators' => [
                /* Default */
                [
                    'type' => 'equal',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.default'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_equal',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.default'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                /* Empty */
                [
                    'type' => 'is_empty',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.empty'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'is_not_empty',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.empty'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'is_null',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.empty'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'is_not_null',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.empty'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                /* String */
                [
                    'type' => 'begins_with',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_begins_with',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'contains',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_contains',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'ends_with',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_ends_with',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.strings'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                /* Multiple */
                [
                    'type' => 'in',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.multiple'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_in',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.multiple'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'in_list_equal',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.multiple'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'in_list_contains',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.multiple'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'in_list_like',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.multiple'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                /* Numbers */
                [
                    'type' => 'less',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'less_or_equal',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'greater',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'greater_or_equal',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 1,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'between',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 2,
                    'apply_to' => ['string'],
                ],
                [
                    'type' => 'not_between',
                    'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:querybuilder.operators.group.numbers'),
                    'nb_inputs' => 2,
                    'apply_to' => ['string'],
                ],
            ],
        ];

        $responseBody = json_encode($configuration,JSON_UNESCAPED_UNICODE);
        $response->withHeader('Content-Type','application/json')->getBody()->write($responseBody);

        return $response;
    }

    /**
     * Gets query builder filters
     * @param int $datatypeUid
     * @param array $pids
     * @param int|null $languageUid
     * @return array
     */
    protected function _getFilters(int $datatypeUid, array $pids = [], $languageUid = null): array
    {
        $filters = [];
        $filters = array_merge($filters, $this->getDefaultFilters());

        if($datatypeUid > 0) {
            $datatype = $this->datatypeRepository->findByUid($datatypeUid, false);
            /* @var Datatype $_datatype */
            if($datatype instanceof Datatype) {

                $defaultFilterValues = $this->_getValuesByPids($datatype, $pids, $languageUid);
                if(!empty($defaultFilterValues)) {
                    $defaultFilter = [
                        'id' => 'FIELD:uid',
                        'label' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:flexform.selection_by_single_records'),
                        'type' => 'string',
                        'input' => 'checkbox',
                        'vertical' => true,
                        'placeholder' => 'title',
                        'operators' => ['in','not_in'],
                        'values' => $defaultFilterValues,
                        'optgroup' => "[{$datatype->getUid()}] {$datatype->getName()}",
                    ];
                    $filters[] = $defaultFilter;
                }

                $fields = $datatype->getFields();
                foreach($fields as $_field) {
                    /* @var Field $_field */
                    $filterConfig = $this->_getFilterByField($_field);
                    $filterConfig['optgroup'] = "[{$datatype->getUid()}] {$datatype->getName()}";
                    $filters[] = $filterConfig;
                }
            }
        }

        return $filters;
    }

    /**
     * @param Datatype $datatype
     * @param array $pids
     * @param int|null $languageUid
     * @return array
     */
    protected function _getValuesByPids(Datatype $datatype, array $pids = [], $languageUid = null): array
    {
        /* @var AbstractRepository $repository */
        $repository = $datatype->getRepository();
        if (!$repository instanceof AbstractRepository) {
            return [];
        }

        if(!empty($pids)) {
            $items = $repository->findAllOnPids($pids, false, $languageUid);
        } else {
            $items = $repository->findAll();
        }

        $values = [];
        foreach($items as $_item) {
            /* @var AbstractRecordModel $_item */

            // Pain check for removing workspace records
            // TODO: integrate workspace id here to get correct records
            // This removes workspace compatibility for this filer
            $record = BackendUtility::getRecord($datatype->getTablename(), $_item->getUid(), 't3ver_state');
            if ($record['t3ver_state'] !== 0) {
                continue;
            }

            if ($_item->getHidden() === true) {
                $icon = $this->iconFactory->getIcon('extensions-dataviewer-' . $datatype->getIcon(), Icon::SIZE_SMALL, 'overlay-hidden');
            } else {
                $icon = $this->iconFactory->getIcon('extensions-dataviewer-' . $datatype->getIcon(), Icon::SIZE_SMALL);
            }
            $values[$_item->getUid()] = $icon . ' ' . '['.$_item->getUid().']'.' '.$_item->getTitle();
        }

        return $values;
    }

    /**
     * Gets default filters
     * @return array
     */
    public function getDefaultFilters(): array
    {
        return [
            [
                'id' => 'uid',
                'label' => 'uid',
                'type' => 'string',
                'input' => 'text',
                'placeholder' => '123',
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            [
                'id' => 'pid',
                'label' => 'pid',
                'type' => 'string',
                'input' => 'text',
                'placeholder' => '123',
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            [
                'id' => 'crdate',
                'label' => 'crdate',
                'type' => 'string',
                'input' => 'text',
                'placeholder' => '1608671782',
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            [
                'id' => 'tstamp',
                'label' => 'tstamp',
                'type' => 'string',
                'input' => 'text',
                'placeholder' => '1608671782',
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            [
                'id' => 'title',
                'label' => 'title',
                'type' => 'string',
                'input' => 'text',
                'placeholder' => 'title',
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            /*
            [
                'id' => 'datatype',
                'label' => 'datatype',
                'type' => 'string',
                'input' => 'select',
                'multiple' => true,
                'values' => $this->_getDatatypeValues(),
                'optgroup' => LocalizationUtility::translate('LLL:EXT:dataviewer/Resources/Private/Language/locallang.xlf:default'),
            ],
            */
        ];
    }

    /**
     * Gets all values for existing
     * datatypes
     * @return array
     * @internal
     */
    protected function _getDatatypeValues(): array
    {
        $datatypes = $this->datatypeRepository->findAll(false);
        $values = [];
        foreach($datatypes as $_datatype) {
            /* @var Datatype $_datatype */
            $values[$_datatype->getUid()] =  $_datatype->getName();
        }

        return $values;
    }


    /**
     * Gets filter configuration by a given field
     * @param Field $field
     * @return array
     */
    protected function _getFilterByField(Field $field): array
    {
        if($field->getIsObjectStorage()) {

            $class = trim($field->getFrontendType(), '\\');
            /* @var \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $model */
            $model = GeneralUtility::makeInstance($class);
            $properties = $model->_getProperties();
            unset($properties['flexFormService']);

            $filter = [
                'id' => $field->getVariableName(),
                'label' => "[{$field->getUid()}] {$field->getFrontendLabel()} ".'{'.$field->getVariableName().'}',
                'type' => 'string',
                'input' => 'FUNC',
            ];

            foreach($properties as $_propertyName=>$_val) {
                $filter['options'][$field->getVariableName().'.'.$_propertyName] = $_propertyName;
            }

            return $filter;
        }

        return [
            'id' => $field->getVariableName(),
            'label' => "[{$field->getUid()}] {$field->getFrontendLabel()} ".'{'.$field->getVariableName().'}',
            'type' => 'string',
            'input' => 'text',
            'value_separator' => ',',
            'placeholder' => $field->getConfig('placeholder'),
        ];
    }

}