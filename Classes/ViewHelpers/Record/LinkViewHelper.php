<?php
namespace MageDeveloper\Dataviewer\ViewHelpers\Record;

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
class LinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * @param \MageDeveloper\Dataviewer\Domain\Model\Record $record Record Model
	 * @param int $pid target page. See TypoLink destination
	 * @param int $pageType type of the target page. See typolink.parameter
	 * @param bool $noCache set this to disable caching for the target page. You should not need this.
	 * @param bool $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
	 * @param string $section the anchor to be added to the URI
	 * @param string $format The requested format, e.g. ".html
	 * @param bool $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
	 * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param bool $absolute If set, the URI of the rendered link is absolute
	 * @param bool $addQueryString If set, the current query parameters will be kept in the URI
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @param string $class CSS Class
	 * @param string $action The action name
	 * @param string $controller Controller Name
	 * @param string $extension Extension Name
	 * @param string $plugin Plugin Name
	 * @return string Rendered link
	 */
	public function render(\MageDeveloper\Dataviewer\Domain\Model\Record $record, $pid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = [], $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = [], $class = null, $action = "dynamicDetail", $controller = "Record", $extension = "Dataviewer", $plugin = "Record")
	{
		$uriBuilder = $this->controllerContext->getUriBuilder();

		$uri = $uriBuilder->reset()
			->setTargetPageUid($pid)
			->setTargetPageType($pageType)
			->setNoCache($noCache)
			->setUseCacheHash(!$noCacheHash)
			->setSection($section)
			->setFormat($format)
			->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
			->setArguments($additionalParams)
			->setCreateAbsoluteUri($absolute)
			->setAddQueryString($addQueryString)
			->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
			->uriFor($action, ["record"=>$record], $controller, $extension, $plugin);

		$this->tag->addAttribute("href", $uri);

		if ($class !== null)
			$this->tag->addAttribute("class", $class);

		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(true);

		return $this->tag->render();
	}
	
}
