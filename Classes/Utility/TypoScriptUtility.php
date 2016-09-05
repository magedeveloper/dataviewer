<?php
namespace MageDeveloper\Dataviewer\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

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

class TypoScriptUtility
{
	/**
	 * TypoScript Parser
	 *
	 * @var \TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser
	 * @inject
	 */
	protected $typoScriptParser;

	/**
	 * Content Object Renderer
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 * @inject
	 */
	protected $contentObjectRenderer;

	/**
	 * Gets a typoscript value from
	 * given typoscript
	 * 
	 * @param string $typoScript
	 * @return mixed
	 */
	public function getTypoScriptValue($typoScript)
	{
		$this->typoScriptParser->parse($typoScript);
		$parsed = $this->typoScriptParser->setup;

		if (TYPO3_MODE === 'BE')
			$this->_simulateFrontendEnvironment();

		$final = $this->contentObjectRenderer->cObjGet($parsed);
		
		if (is_array($final))
			return implode(PHP_EOL, $final);
			
		return (string)$final;					
	}

	/**
	 * Sets the $TSFE->cObjectDepthCounter in Backend mode
	 * This somewhat hacky work around is currently needed because the cObjGetSingle() function of \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer relies on this setting
	 *
	 * @return void
	 */
	protected function _simulateFrontendEnvironment()
	{
		$GLOBALS['TSFE'] = new \stdClass();
		$GLOBALS['TSFE']->cObjectDepthCounter = 100;
	}
}