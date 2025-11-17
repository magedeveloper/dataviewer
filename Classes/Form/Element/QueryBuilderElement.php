<?php
declare(strict_types = 1);

namespace Aix\Dataviewer\Form\Element;

use Aix\Dataviewer\Utility\UrlUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class QueryBuilderElement extends AbstractFormElement
{
    /**
     * @var FlexFormService
     */
    protected $flexFormService;

    /**
     * Container objects give $nodeFactory down to other containers.
     *
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        $this->data = $data;
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * Main render method
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render(): array
    {
        // Render the HTML output
        $result = $this->initializeResultArray();
        /* @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $template = 'EXT:dataviewer/Resources/Private/Templates/UserFunc/Form/Element/QueryBuilderElement.html';
        $template = GeneralUtility::getFileAbsFileName($template);
        $view->setTemplatePathAndFilename($template);

        $css = [
            'EXT:dataviewer/Resources/Public/Css/Contrib/query-builder.default.css',
            'EXT:dataviewer/Resources/Public/Css/dataviewer-backend.css',
        ];

        $cssFiles = [];
        foreach($css as $_css) {
            $cssFiles[] = UrlUtility::getFileUrl($_css);
        }

        $view->assign('cssFiles', $cssFiles);
        $view->assign('data', $this->data);

        $randVarName = uniqid('qb_');
        $view->assign('id', $randVarName);

        if(!empty($this->data)) {
            $flexForm = $this->data['databaseRow']['pi_flexform'];
            $flexForm = $this->flexFormService->walkFlexFormNode($flexForm);
            $flexForm = $this->flexFormService->walkFlexFormNode($flexForm, 'lDEF');

            $datatype = (int)reset($flexForm['data']['general_settings']['settings']['datatype_selection']);

            // Render javascript code
            $fieldId = "{$this->data['parameterArray']['itemFormElID']}";
            $builderId = "#builder_{$this->data['parameterArray']['itemFormElID']}_{$this->data['vanillaUid']}_{$this->data['fieldName']}";
            $pages = array_column($this->data['databaseRow']['pages'], 'uid');
            $languageUid = is_array($this->data['databaseRow']['sys_language_uid'])?reset($this->data['databaseRow']['sys_language_uid']):$this->data['databaseRow']['sys_language_uid'];

            $view->assignMultiple([
               'fieldId' => $fieldId,
               'builderId' => $builderId,
               'pages' => $pages,
               'languageUid' => $languageUid,
               'datatype' => $datatype,
            ]);
        }

        $result['html'] = $view->render();
        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create('jquery-extendext');
        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create('query-builder');
        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create('query-builder-templates');
        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create('@aix/dataviewer/QueryBuilderElement.js')
            ->instance(
                $randVarName,           // Id
                (int)$datatype,                // Datatype Id
                $fieldId,                      // Value Field Id
                $languageUid,                  // Language Uid
                $pages                         // Page Ids
        );

        return $result;
    }

}