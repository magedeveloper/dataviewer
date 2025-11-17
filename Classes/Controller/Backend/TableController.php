<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Controller\Backend;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Factory\TableFactory;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;
use Throwable;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TableController extends AbstractBackendController
{
    /**
     * @var TableFactory
     */
    protected $tableFactory;

    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var PluginSettingsService
     */
    protected $pluginSettingsService;

    /**
     * @param TableFactory $tableFactory
     */
    public function injectTableFactory(TableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
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
     * Get a view instance
     * @return StandaloneView
     */
    public function getView(): StandaloneView
    {
        /* @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setLayoutRootPaths($this->pluginSettingsService->getLayoutPaths());
        $view->setTemplateRootPaths($this->pluginSettingsService->getTemplatePaths());
        $view->setPartialRootPaths($this->pluginSettingsService->getPartialPaths());

        return $view;
    }

    /**
     * Shows or hides a record through an AJAX call
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \TYPO3\CMS\Core\Database\Schema\Exception\StatementException
     * @throws \TYPO3\CMS\Core\Database\Schema\Exception\UnexpectedSignalReturnValueTypeException
     */
    public function tableStatusAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $tableName = strip_tags($parsedBody['tableName']);
        $datatypeId = $parsedBody['datatypeId'];
        $tableExists = false;

        if(!is_null($tableName) && $tableName != '') {
            $tableExists = $this->tableFactory->tableExists($tableName);
        }

        $datatype = null;
        $createStatement = null;
        $updateStatements = [];
        $missingColumns = [];
        $tableLayout = null;
        if (is_numeric($datatypeId)) {
            /* @var Datatype $datatype */
            $datatype = $this->datatypeRepository->findByUid((int)$datatypeId);
            if ($datatype instanceof Datatype && $tableExists) {
                try {
                    $createStatement = $this->tableFactory->getCreateTableStatementByDatatype($datatype, $tableName);
                    $sqlStatements = $this->tableFactory->getSqlStatements($createStatement);
                    $updateStatements = $this->tableFactory->getUpdateStatements($sqlStatements);
                    $missingColumns = $this->tableFactory->getMissingColumns($tableName, $datatype);
                    $tableLayout = $this->tableFactory->getTableLayout($tableName);
                } catch (\Exception $e) {
                    $response = GeneralUtility::makeInstance(Response::class);
                    $response->getBody()->write(json_encode(['success' => false,'html' => $e->getMessage()]));
                    return $response;
                }
            }
        }

        $view = $this->getView();
        $templateFile = GeneralUtility::getFileAbsFileName('EXT:dataviewer/Resources/Private/Templates/UserFunc/Table/Status.html');
        $view->setTemplatePathAndFilename($templateFile);

        $tableNeedsUpdate = (count($missingColumns)>0);
        if(is_array($updateStatements) && array_key_exists('change', $updateStatements) && is_array($updateStatements['change']) && !empty($updateStatements['change'])) {
            $tableNeedsUpdate = true;
        }

        $variables = [
            'tableName' => $tableName,
            'tableExists' => $tableExists,
            'datatype'  => $datatype,
            'createStatement' => $createStatement,
            'updateStatements' => $updateStatements,
            'tableNeedsUpdate' => $tableNeedsUpdate,
            'missingColumns' => $missingColumns,
            'tableLayout' => $tableLayout,
            'tableNameWrong' => !$this->tableFactory->isAllowedTablename($tableName),
        ];

        $view->assignMultiple($variables);
        $output = $view->render();

        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(json_encode(['success' => true,'html' => $output]));
        return $response;
    }

    /**
     * Deletes a table
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \TYPO3\CMS\Core\Database\Schema\Exception\StatementException
     * @throws \TYPO3\CMS\Core\Database\Schema\Exception\UnexpectedSignalReturnValueTypeException
     */
    public function tableDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $tableName = strip_tags($parsedBody['tableName']);
        $tableExists = false;

        if(!is_null($tableName) && $tableName != '') {
            $tableExists = $this->tableFactory->tableExists($tableName);
        }

        if($tableExists) {
            try {
                $this->tableFactory->dropTable($tableName);
            } catch (\Exception $e) {}
        }

        return $this->tableStatusAction($request);
    }

    /**
     * Migrates a table
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function tableMigrateAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->clearAutoloadAndCache();

        $parsedBody = $request->getParsedBody();
        $tableName = strip_tags($parsedBody['tableName']);
        $datatypeId = (int)$parsedBody['datatypeId'];
        $datatype = $this->datatypeRepository->findByUid($datatypeId);

        $view = $this->getView();
        $templateFile = 'EXT:dataviewer/Resources/Private/Templates/UserFunc/Table/Migrate.html';
        $templateFile = GeneralUtility::getFileAbsFileName($templateFile);
        $view->setTemplatePathAndFilename($templateFile);

        try {
            $createStatement    = $this->tableFactory->getCreateTableStatementByDatatype($datatype, $tableName);
            $sqlStatements      = $this->tableFactory->getSqlStatements($createStatement);
            $updateStatements   = $this->tableFactory->getUpdateStatements($sqlStatements);
            $selectedStatements = $this->tableFactory->getSelectedStatements($updateStatements);
        } catch (\Exception $e) {
            $response = GeneralUtility::makeInstance(Response::class);
            $response->getBody()->write(json_encode(['success' => false, 'html' => $e->getMessage()]));
            return $response;
        }

        ////////////////////////////////////////////////////////////
        // MIGRATION ROUTINE
        ////////////////////////////////////////////////////////////
        try {
            $result = $this->tableFactory->migrate($sqlStatements, $selectedStatements);
        } catch (\Exception $e) {
            $response = GeneralUtility::makeInstance(Response::class);
            $response->getBody()->write(json_encode(['success' => false, 'html' => $e->getMessage()]));
            return $response;
        }
        ////////////////////////////////////////////////////////////

        $this->clearAutoloadAndCache();

        $variables = [
            'tableName' => $tableName,
            'datatype'  => $datatype,
            'createStatement' => $createStatement,
            'updateStatements' => $updateStatements,
            'result' => $result,
        ];

        $view->assignMultiple($variables);
        $output = $view->render();

        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(json_encode(['success' => true,'html' => $output]));
        return $response;
    }
}