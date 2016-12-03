<?php
namespace MageDeveloper\Dataviewer\Service\Settings;

/**
 * MageDeveloper Dataviewer Extension
 * -----------------------------------
 *
 * @category    TYPO3 Extension
 * @package     MageDeveloper\Dataviewer
 * @author		Bastian Zagar
 * @copyright   Magento Developers / magedeveloper.de <kontakt@magedeveloper.de>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractSettingsService implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * @var mixed
	 */
	protected $settings = null;

	/**
	 * @var mixed
	 */
	protected $settingsOverride = null;

	/**
	 * @var mixed
	 */
	protected $configuration = null;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Content Object Renderer
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 * @inject
	 */
	protected $contentObjectRenderer;

	/**
	 * Returns all settings.
	 *
	 * @param string $extensionName Extension Key
	 * @param string $pluginName Plugin Name
	 * @return array
	 */
	public function getSettings($extensionName = null, $pluginName = null)
	{
		if(is_array($this->settingsOverride))
			return $this->settingsOverride;
			
		$uid = 0;
		$contentObj = $this->configurationManager->getContentObject();
		if ($contentObj)
			$uid = $contentObj->data["uid"];
			
		if($uid > 0)
		{
			if ($uid > 0 && !isset($this->settings[$uid]))
			{
				$this->settings[$uid] = $this->configurationManager->getConfiguration(
					\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
					$extensionName,
					$pluginName
				);
			}

			return $this->settings[$uid];
		}
		
		// We reload the settings here
		return $this->configurationManager->getConfiguration(
			\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
			$extensionName,
			$pluginName
		);	
	}

	/**
	 * Sets the settings manually
	 *
	 * @param array $settings
	 * @return void
	 */
	public function setSettings(array $settings = [])
	{
		$this->settingsOverride = $settings;
	}

	/**
	 * Returns configuration.
	 *
	 * @param string $extensionName Extension Key
	 * @param string $pluginName Plugin Name
	 * @return array
	 */
	public function getFullConfiguration($extensionName = null, $pluginName = null)
	{
		if ($this->configuration === NULL)
		{
			$this->configuration = $this->configurationManager->getConfiguration(
				\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
				$extensionName,
				$pluginName
			);

		}

		return $this->configuration;
	}

	/**
	 * Sets the configuration
	 *
	 * @param array $configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration)
	{
		$this->configuration = $configuration;
	}

	/**
	 * Get configuration setting
	 *
	 * @param string $extensionName Name of the extension
	 * @param string $path Path to configuration
	 * @return mixed
	 */
	public function getExtensionConfiguration($extensionName, $path)
	{
		$nodes = explode('.', $path);
		$fullConfiguration = $this->getFullConfiguration();

		$config = null;
		$current = 0;
		$depth = count($nodes);
		$config = $fullConfiguration["plugin."][$extensionName."."];

		while (is_array($config))
		{
			$node = ($current+1 < $depth)?$nodes[$current].'.':$nodes[$current];

			if(array_key_exists($node, $config))
			{
				$config = $config[$node];
				$current++;
			}
			else
			{
				if (array_key_exists($node.'.', $config))
				{
					$config = $config["{$node}."];
				}
				else
				{
					$config = "";
				}

				break;
			}

		}

		return $config;
	}

	/**
	 * Gets a setting by code
	 *
	 * @param string $code Setting Code
	 * @return string|null
	 */
	public function getSettingByCode($code)
	{
		$settings = $this->getSettings();
		
		if (is_array($settings) && isset($settings[$code]))
		{
			return $settings[$code];
		}

		return null;
	}

	/**
	 * Gets the template partial path from configuration
	 *
	 * @return string
	 */
	public function getPartialPaths()
	{
		$path = "view.partialRootPaths";
		return $this->getConfiguration($path);
	}

	/**
	 * Gets the template path from configuration
	 *
	 * @return string
	 */
	public function getTemplatePaths()
	{
		$path = "view.templateRootPaths";
		return $this->getConfiguration($path);
	}

	/**
	 * Gets the layout path from configuration
	 *
	 * @return string
	 */
	public function getLayoutPaths()
	{
		$path = "view.layoutRootPaths";
		return $this->getConfiguration($path);
	}
}
