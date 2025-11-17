<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Middleware;

use Aix\Dataviewer\Configuration\ExtensionConfiguration;
use Aix\Dataviewer\Service\Settings\FieldSettingsService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class FieldtypeConfigurationMiddleware implements MiddlewareInterface
{
    /**
     * TCA Cache Service
     *
     * @var FieldSettingsService
     */
    protected $fieldSettingsService;

    /**
     * @param FieldSettingsService $fieldSettingsService
     */
    public function injectFieldSettingsService(FieldSettingsService $fieldSettingsService)
    {
        $this->fieldSettingsService = $fieldSettingsService;
    }

    /**
     * Middleware to inject field configuration into globals
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $fieldFlexformConfig = $this->fieldSettingsService->getTcaFlexFormConfiguration();

        $GLOBALS["TCA"][ExtensionConfiguration::EXTENSION_FIELD_TABLE]['columns']['field_conf']['config']['ds'] =
            array_merge($GLOBALS["TCA"][ExtensionConfiguration::EXTENSION_FIELD_TABLE]['columns']['field_conf']['config']['ds'], $fieldFlexformConfig);

        return $handler->handle($request);
    }
}
