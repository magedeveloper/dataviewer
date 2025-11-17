<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Service\Settings;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

abstract class AbstractSettingsService implements SingletonInterface
{
  /**
   * @var array
   */
  protected $configuration = null;

  /**
   * @var ConfigurationManager
   */
  //protected $configurationManager;

  /**
   * @var BackendConfigurationManager
   */
  protected $backendConfigurationManager;

  /**
   * @param BackendConfigurationManager $backendConfigurationManager
   * @return void
   */
  public function injectBackendConfigurationManager(BackendConfigurationManager $backendConfigurationManager): void
  {
    $this->backendConfigurationManager = $backendConfigurationManager;
  }

  /**
   * @param ConfigurationManager $configurationManager
   */
  //public function injectConfigurationManager(ConfigurationManager $configurationManager)
  //{
  //  $this->configurationManager = $configurationManager;
  //}

  /**
   * Returns all settings.
   *
   * @param string $path Configuration Path
   * @return array
   */
  public function getConfiguration(string $path)
  {
    $request = GeneralUtility::makeInstance(ServerRequest::class);
    $this->backendConfigurationManager->setRequest($request);
    $config = $this->backendConfigurationManager->getTypoScriptSetup();
    if (!is_array($config)) {
      return [];
    }

    try {
      $config = GeneralUtility::removeDotsFromTS($config);
      $value = ArrayUtility::getValueByPath($config, $path, '.');
      return $value;
    } catch (\Exception $e) {
    }

    return [];
  }

  /**
   * Gets the template partial path from configuration
   *
   * @return string
   */
  public function getPartialPaths(): array
  {
    $path = 'plugin.tx_dataviewer.view.partialRootPaths';
    $config = $this->getConfiguration($path);

    if (!is_array($config)) {
      $config = [100 => 'EXT:dataviewer/Resources/Private/Partials/'];
    }

    return $config;
  }

  /**
   * Gets the template path from configuration
   *
   * @return string
   */
  public function getTemplatePaths(): array
  {
    $path = 'plugin.tx_dataviewer.view.templateRootPaths';
    $config = $this->getConfiguration($path);

    if (!is_array($config)) {
      $config = [100 => 'EXT:dataviewer/Resources/Private/Templates/'];
    }

    return $config;
  }

  /**
   * Gets the layout path from configuration
   *
   * @return array|string
   */
  public function getLayoutPaths(): array
  {
    $path = 'plugin.tx_dataviewer.view.layoutRootPaths';
    $config = $this->getConfiguration($path);

    if (!is_array($config)) {
      $config = [100 => 'EXT:dataviewer/Resources/Private/Layouts/'];
    }

    return $config;
  }
}
