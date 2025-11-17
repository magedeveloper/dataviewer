<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Controller\Backend;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ClassController extends AbstractBackendController
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @var PluginSettingsService
     */
    protected $pluginSettingsService;

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
     * Class Status
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function classStatusAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $datatypeId = $parsedBody["datatypeId"];
        $datatype = $this->datatypeRepository->findByUid($datatypeId);
        $success = false;
        $view = $this->getView();

        $templateFile = GeneralUtility::getFileAbsFileName("EXT:dataviewer/Resources/Private/Templates/UserFunc/Class/Status.html");
        $view->setTemplatePathAndFilename($templateFile);

        if ($datatype instanceof Datatype) {
            $suggestedFullQualifiedClassName = $datatype->getFullyQualifiedClassName();

            try {
                $variables = [
                    "className" => $suggestedFullQualifiedClassName,
                    "classValid" => $this->classFactory->classValid($datatype),
                    "datatype"  => $datatype,
                    "classFileName" => $datatype->getClassFilePath(),
                    "repositoryFileName" => $datatype->getRepositoryFilePath(),
                    "classActual" => $this->classFactory->classActual($datatype),
                ];

                $view->assignMultiple($variables);
                $output = $view->render();
                $success = true;
            } catch (\Exception $e) {
                $output = $e->getMessage();
            }
        } else {

            $success = true;

            $variables = [
                "isNew" => true,
            ];

            $view->assignMultiple($variables);
            $output = $view->render();
        }

        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(json_encode(["success" => $success,"html" => $output]));
        return $response;
    }

    /**
     * Migrates a class
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function classMigrateAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->clearAutoloadAndCache();

        $parsedBody = $request->getParsedBody();
        $datatypeId = $parsedBody["datatypeId"];
        $datatype = $this->datatypeRepository->findByUid($datatypeId);

        $suggestedFullQualifiedClassName = null;
        $destinationFile = null;
        if ($datatype instanceof Datatype) {

            // Build Domain Model
            $domainModelClassFileBuilt = $this->classFactory->buildDomainModelClassFile($datatype);

            // Build Domain Repository
            $domainRepositoryClassFileBuild = $this->classFactory->buildDomainRepositoryClassFile($datatype);

            $this->clearAutoloadAndCache();

            $view = $this->getView();
            $templateFile = GeneralUtility::getFileAbsFileName("EXT:dataviewer/Resources/Private/Templates/UserFunc/Class/Migrate.html");
            $view->setTemplatePathAndFilename($templateFile);

            $variables = [
                "error" => ($domainModelClassFileBuilt == true && $domainRepositoryClassFileBuild == true),
                "className" => $datatype->getFullyQualifiedClassName(),
            ];

            $view->assignMultiple($variables);
            $contents = $view->render();

            $response = GeneralUtility::makeInstance(Response::class);
            $response->getBody()->write(json_encode(["success" => true,"html" => $contents]));

            return $response;
        }

        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(json_encode(["success" => true,"html" => '']));

        return $response;
    }

    /**
     * Migrates a class
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function classDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $datatypeId = $parsedBody["datatypeId"];
        $datatype = $this->datatypeRepository->findByUid($datatypeId);

        $suggestedFullQualifiedClassName = null;
        $destinationFile = null;
        if ($datatype instanceof Datatype) {
            $domainModelClassFilename = GeneralUtility::getFileAbsFileName($datatype->getClassFilePath());
            $domainRepositoryClassFilename = GeneralUtility::getFileAbsFileName($datatype->getRepositoryFilePath());
            @unlink($domainModelClassFilename);
            @unlink($domainRepositoryClassFilename);
        }

        // Dump autoload, if TYPO3 is not in composer mode
        if (!defined('TYPO3_COMPOSER_MODE')) {
            $this->classFactory->dumpAutoload();
        }

        return $this->classStatusAction($request);
    }
}