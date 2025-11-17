<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers;

use Aix\Dataviewer\Service\Settings\Plugin\PluginSettingsService;


abstract class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
	/**
	 * Plugin Settings Service
	 *
	 * @var PluginSettingsService
	 */
	protected $pluginSettingsService;

    /**
     * @param PluginSettingsService $pluginSettingsService
     */
	public function injectPluginSettingsService(PluginSettingsService $pluginSettingsService)
    {
        $this->pluginSettingsService = $pluginSettingsService;
    }
}
