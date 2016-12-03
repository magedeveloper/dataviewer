<?php
namespace MageDeveloper\Dataviewer\eID;

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
class ConfigurationLoader
{
	/**
	 * Object Manager
	 * 
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Page Id
	 *
	 * @var int
	 */
	protected $pageId = 1;

	/**
	 * Constructor
	 * 
	 * @return ConfigurationLoader
	 */
	public function __construct()
	{
		// TYPO3 Object Manager
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
	}

	/**
	 * Gets the current page id
	 *
	 * @return int
	 */
	public function getPageId()
	{
		return $this->pageId;
	}

	/**
	 * Sets the current page id
	 *
	 * @param int $pageId
	 * @return void
	 */
	public function setPageId($pageId)
	{
		$this->pageId = $pageId;
	}

	/**
	 * Initalize TYPO3 Globals
	 * ($GLOBALS['TSFE'])
	 *
	 * @return void
	 */
	public function initGlobals()
	{
		$GLOBALS['TT'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\TimeTracker\TimeTracker::class);

		// Frontend Controller
		$this->initFrontendController();
		// Content Object
		$this->createContentObject();
		// Template Service
		$this->initTemplateService();

		// Language Initialization
		\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();

		// Get Frontend User
		$GLOBALS["TSFE"]->initFEuser();

		// TCA Initialization
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

		// Disable Caching for the Ajax Dispatch
		$GLOBALS["TSFE"]->set_no_cache();

		$GLOBALS["TSFE"]->checkAlternativeIdMethods();
		$GLOBALS["TSFE"]->determineId();
		$GLOBALS["TSFE"]->initTemplate();
		$GLOBALS["TSFE"]->getConfigArray();

		\TYPO3\CMS\Core\Core\Bootstrap::getInstance()->loadCachedTca();
		\TYPO3\CMS\Core\Core\Bootstrap::getInstance();

		$GLOBALS["TSFE"]->settingLanguage();
		$GLOBALS["TSFE"]->settingLocale();
	}

	/**
	 * Initializes the frontend controller
	 *
	 * @return void
	 */
	public function initFrontendController()
	{
		$GLOBALS["TSFE"] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class, $TYPO3_CONF_VARS, $this->getPageId(), 0);
	}

	/**
	 * Initializes the template service
	 */
	public function initTemplateService()
	{
		$GLOBALS['TSFE']->tmpl = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TemplateService::class);
		$GLOBALS['TSFE']->tmpl->tt_track = 0; // Do not log time-performance information
	}

	/**
	 * Adds a content object to the globals
	 *
	 * @return void
	 */
	public function createContentObject()
	{
		// Frontend Controller
		$GLOBALS["TSFE"]->cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
	}

	/**
	 * Gets full configuration
	 *
	 * @return array
	 */
	public function getConfiguration()
	{
		return $GLOBALS["TSFE"]->tmpl->setup;
	}
}
