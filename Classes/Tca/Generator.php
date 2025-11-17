<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Tca;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Model\Field;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Exception\TcaGeneratorException;
use Aix\Dataviewer\Factory\TableFactory;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Aix\Dataviewer\Icon\DataviewerIconRegistry;
use Aix\Dataviewer\Service\Cache\TcaCacheService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use Aix\Dataviewer\Service\Backend\FlashMessageService;

class Generator implements MiddlewareInterface
{
    /**
     * Field Repository
     *
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * Table Factory
     *
     * @var TableFactory
     */
    protected $tableFactory;

    /**
     * Yaml File Loader
     *
     * @var YamlFileLoader
     */
    protected $yamlFileLoader;

    /**
     * Standalone View
     *
     * @var StandaloneView
     */
    protected $standaloneView;

    /**
     * @var DataviewerIconRegistry
     */
    protected $dataviewerIconRegistry;

    /**
     * TCA Cache Service
     *
     * @var TcaCacheService
     */
    protected $tcaCacheService;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var FlashMessageService
     */
    protected $backendFlashMessageService;

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * @param TableFactory $tableFactory
     */
    public function injectTableFactory(TableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
    }

    /**
     * @param YamlFileLoader $yamlFileLoader
     */
    public function injectYamlFileLoader(YamlFileLoader $yamlFileLoader)
    {
        $this->yamlFileLoader = $yamlFileLoader;
    }

    /**
     * @param DataviewerIconRegistry $dataviewerIconRegistry
     */
    public function injectDataviewerIconRegistry(DataviewerIconRegistry $dataviewerIconRegistry)
    {
        $this->dataviewerIconRegistry = $dataviewerIconRegistry;
    }

    /**
     * @param TcaCacheService $tcaCacheService
     */
    public function injectTcaCacheService(TcaCacheService $tcaCacheService)
    {
        $this->tcaCacheService = $tcaCacheService;
    }

    /**
     * @param ResponseFactory $responseFactory
     * @return void
     */
    public function injectResponseFactory(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param PageRenderer $pageRenderer
     * @return void
     */
    public function injectPageRenderer(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param FlashMessageService $backendFlashMessageService
     * @return void
     */
    public function injectBackendFlashMessageService(FlashMessageService $backendFlashMessageService)
    {
        $this->backendFlashMessageService = $backendFlashMessageService;
    }

    /**
     * @return StandaloneView
     */
    public function getStandaloneView(): StandaloneView
    {
        if (!$this->standaloneView) {
            $this->standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        }

        return $this->standaloneView;
    }

    /**
     * Process TCA Configuration
     *
     * @param bool $cached
     * @return void
     */
    public function processTca(bool $cached = false): void
    {
        $tcaDefaultFile = GeneralUtility::getFileAbsFileName('EXT:dataviewer/Resources/Private/Init/tx_dataviewer_domain_model_default.yaml');

        // We raw-create the tca information here and are ignoring all errors that could happen, just
        // to let TYPO3 live\
        try {

            $tcaDefaultYaml = file_get_contents($tcaDefaultFile);
            $this->getStandaloneView()->setTemplateSource($tcaDefaultYaml);

            $typeiconClasses = $this->dataviewerIconRegistry->getIcons(['EXT:dataviewer/Resources/Public/Icons/Datatype'],'extensions-dataviewer-', true, false);
            $keys = array_keys($typeiconClasses);
            $values = array_map(function($value) { return 'extensions-dataviewer-'.$value; }, $keys);
            $icons = array_combine($keys, $values);

            // We need to fetch all datatype to get the assigned tables and generate the tca of the according fields
            $datatypes = $this->datatypeRepository->findAll(false);

            if ($datatypes instanceof QueryResultInterface) {
                foreach ($datatypes as $_datatype) {

                    /* @var Datatype $_datatype */
                    $tableName = $_datatype->getTablename();

                    $datatypeCacheIdentifier = "Tca_Datatype_{$_datatype->getUid()}";
                    $cacheTcaForDatatype = $_datatype->getCacheTca();
                    if ($cacheTcaForDatatype && $this->tcaCacheService->has($datatypeCacheIdentifier)) {
                        // Main raw tca frame is located in cache
                        $datatypeTca = $this->tcaCacheService->get($datatypeCacheIdentifier);
                    } else {
                        // We need to create a new tca frame and put it into cache
                        // We need to check if the table exists
                        $tableExists = $this->tableFactory->tableExists($tableName);

                        if (!$tableExists) {
                            continue;
                        }

                        // Assigning datatype icon as default for this type
                        $icons['default'] = "extensions-dataviewer-{$_datatype->getIcon()}";

                        $variables = [
                            'datatype' => $_datatype,
                            'tableName' => $tableName,
                            'typeiconClasses' => $icons,
                            'fields' => implode(',', array_keys($_datatype->getApproachableFields())),
                            'iconFile' => $typeiconClasses['extensions-dataviewer-'.$_datatype->getIcon()],
                        ];

                        $this->getStandaloneView()->assignMultiple($variables);
                        $this->getStandaloneView()->setTemplateSource($tcaDefaultYaml);

                        try {
                            $datatypeTca = Yaml::parse($this->getStandaloneView()->render());
                            $this->tcaCacheService->set($datatypeCacheIdentifier, $datatypeTca);
                        } catch (Exception $e) {
                            throw new \Exception($e->getMessage());
                        }
                    }

                    if (empty($datatypeTca)) {
                        continue;
                    }

                    // Fields TCA Generation is out of the cache currently to achieve more dynamic behaviour
                    // We will perhaps modify this in the future
                    $fields = $_datatype->getFields();
                    foreach ($fields as $_field) {
                        /* @var \Aix\Dataviewer\Domain\Model\Field $_field */
                        if ($_field instanceof Field) {
                            $cacheTcaForField = ($cacheTcaForDatatype)?true:$_field->getCacheTca();
                            $fieldCacheIdentifier = "Tca_Field_{$_field->getUid()}";

                            $fieldTca = [];
                            if ($cacheTcaForField && $this->tcaCacheService->has($fieldCacheIdentifier)) {
                                // Main raw tca frame is located in cache
                                $fieldTca = $this->tcaCacheService->get($fieldCacheIdentifier);
                            } else {
                                try {
                                    /* @var \Aix\Dataviewer\Tca\AbstractField */
                                    $tcaModel = $_field->getTca();
                                    if (is_null($tcaModel)) {
                                        continue;
                                    }
                                    $tcaModel->setDatatype($_datatype);
                                    $fieldTca = $tcaModel->getTca();
                                    $this->tcaCacheService->set($fieldCacheIdentifier, $fieldTca);
                                } catch (Exception $e) {
                                    throw new TcaGeneratorException($e->getMessage(), $e->getCode());
                                }
                            }

                            $datatypeTca['columns'][$_field->getCode()] = $fieldTca;
                        }
                    }

                    if (!array_key_exists($tableName, $GLOBALS['TCA']) || !is_array($GLOBALS['TCA'][$tableName])) {
                        $GLOBALS['TCA'][$tableName] = [];
                    }

                    $GLOBALS['TCA'][$tableName] = $datatypeTca;
                }
            }

        } catch (Exception $e)	{
            throw new TcaGeneratorException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * middleware for just receiving data from guided tours frontend
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->processTca();
        } catch (TcaGeneratorException $e) {
            $message = 'Message: ' . $e->getMessage() . "\r\n" . "in File " . $e->getFile() . ":" . $e->getLine();
            $title = 'Latest Dataviewer Tca Generator Error';
            $this->backendFlashMessageService->addFlashMessage($message, $title, ContextualFeedbackSeverity::ERROR, $request);
        }

        $response = $handler->handle($request);
        return $response;
    }

}
