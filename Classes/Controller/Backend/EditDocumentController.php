<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Controller\Backend;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Factory\TableFactory;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Service\Backend\BackendAccessService;
use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;

class EditDocumentController extends \TYPO3\CMS\Backend\Controller\EditDocumentController
{
    /**
     * @var StandaloneView
     */
    protected $standaloneView;

    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var PluginSettingsService
     */
    protected $pluginSettingsService;

    /**
     * @var BackendAccessService
     */
    protected $backendAccessService;

    /**
     * @var TableFactory
     */
    protected $tableFactory;

    /**
     * @param StandaloneView $standaloneView
     */
    public function injectStandaloneView(StandaloneView $standaloneView)
    {
        $this->standaloneView = $standaloneView;
    }

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * @param PluginSettingsService $pluginSettingsService
     */
    public function injectPluginSettingsService(PluginSettingsService $pluginSettingsService)
    {
        $this->pluginSettingsService = $pluginSettingsService;
    }

    /**
     * @param BackendAccessService $backendAccessService
     */
    public function injectBackendAccessService(BackendAccessService $backendAccessService)
    {
        $this->backendAccessService = $backendAccessService;
    }

    /**
     * @param TableFactory $tableFactory
     */
    public function injectTableFactory(TableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
    }

    /**
     * Main dispatcher entry method registered as 'record_edit' end point
     *
     * @param ServerRequestInterface $request the current request
     * @return ResponseInterface the response with the content
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public function mainAction(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $view->setUiBlock(true);
        $view->setTitle($this->getShortcutTitle($request));

        // Unlock all locked records
        BackendUtility::lockRecords();
        if ($response = $this->preInit($request)) {
            return $response;
        }

        // Process incoming data via DataHandler?
        $parsedBody = $request->getParsedBody();
        if ((
                $this->doSave
                || isset($parsedBody['_savedok'])
                || isset($parsedBody['_saveandclosedok'])
                || isset($parsedBody['_savedokview'])
                || isset($parsedBody['_savedoknew'])
                || isset($parsedBody['_duplicatedoc'])
            )
            && $request->getMethod() === 'POST'
            && $response = $this->processData($view, $request)
        ) {
            return $response;
        }

        $this->init($request);

        /************************************************************************************************************
         * DATAVIEWER CUSTOM BACKEND LAYOUT
         ***********************************************************************************************************/
        // Obtain necessary record information
        $queryParams = $request->getQueryParams();
        $tableName = key($queryParams['edit']);
        $recordId = key($queryParams['edit'][$tableName]);
        $recordId = ($queryParams['edit'][$tableName][$recordId] !== 'new') ? $recordId : null;

        $view->assign('dataviewerContext', false);

        $datatype = $this->datatypeRepository->findOneByTablename($tableName);
        if ($datatype instanceof Datatype) {

            // Obtaining general view variables
            $record = BackendUtility::getRecord($tableName, $recordId);
            $sysLanguage = null;
            if($record !== null) {
                $sysLanguage = BackendUtility::getRecord('sys_language', $record['sys_language_uid']);
            }
            $variables = [
                'datatype' => $datatype,
                'record' => $record,
                'logoUrl' => $this->backendAccessService->getLogoUrl(),
                'supportEmail' => $this->backendAccessService->getSupportEmail(),
                'supportMessage' => $this->backendAccessService->disableSupportMessage(),
                'version' => ExtensionManagementUtility::getExtensionVersion('dataviewer'),
                'language' => $sysLanguage,
                'dataviewerContext' => true,
            ];

            $view->setModuleId('dataviewer-edit');
            $view->setModuleName('dataviewer-edit-document');

            $view->assignMultiple($variables);
        }
        /************************************************************************************************************/

        if ($request->getMethod() === 'POST') {
            // In case save&view is requested, we have to add this information to the redirect
            // URL, since the ImmediateAction will be added to the module body afterwards.
            if (isset($parsedBody['_savedokview'])) {
                $this->R_URI = rtrim($this->R_URI, '&') .
                    HttpUtility::buildQueryString([
                        'showPreview' => true,
                        'popViewId' => $parsedBody['popViewId'] ?? $this->getPreviewPageId(),
                    ], (empty($this->R_URL_getvars) ? '?' : '&'));
            }
            return new RedirectResponse($this->R_URI, 302);
        }

        $view->assign('bodyHtml', $this->main($view, $request));
        return $view->renderResponse('Form/EditDocument');
    }
}