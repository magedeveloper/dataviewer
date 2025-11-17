<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Session\UserSessionManager;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TypoScript\AST\AstBuilder;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Core\TypoScript\IncludeTree\IncludeNode\RootInclude;
use TYPO3\CMS\Core\TypoScript\IncludeTree\IncludeNode\StringInclude;
use TYPO3\CMS\Core\TypoScript\IncludeTree\IncludeNode\TsConfigInclude;
use TYPO3\CMS\Core\TypoScript\IncludeTree\StringTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Traverser\ConditionVerdictAwareIncludeTreeTraverser;
use TYPO3\CMS\Core\TypoScript\IncludeTree\TreeFromLineStreamBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\TsConfigTreeBuilder;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeAstBuilderVisitor;
use TYPO3\CMS\Core\TypoScript\IncludeTree\Visitor\IncludeTreeConditionMatcherVisitor;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\TypoScript\Tokenizer\LosslessTokenizer;
use TYPO3\CMS\Core\TypoScript\TypoScriptStringFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;


class TypoScriptUtility
{
    /**
     * Gets a typoscript value from
     * given typoscript
     *
     * @param string $typoScript
     * @return mixed
     */
    public function getTypoScriptValue(string $typoScript)
    {
        try {
            $hash = md5($typoScript);
            /** @var TypoScriptStringFactory $typoScriptStringFactory */
            $typoScriptStringFactory = GeneralUtility::makeInstance(TypoScriptStringFactory::class);
            $rootNode = $typoScriptStringFactory->parseFromStringWithIncludes("dataviewer-typoscript-rendering-$hash", $typoScript);

            /** @var ServerRequest $request */
            $request = GeneralUtility::makeInstance(ServerRequest::class);
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $contentObjectRenderer->setRequest($request);
            $result = $contentObjectRenderer->cObjGet($rootNode->toArray());
            return (string)$result;
        } catch (\Exception $e) {
            return '';
        }
    }

}
