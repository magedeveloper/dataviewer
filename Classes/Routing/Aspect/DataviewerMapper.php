<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Routing\Aspect;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use Aix\Dataviewer\Tca\Generator;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Routing\Aspect\PersistedPatternMapper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *  The Mapper needs configuration of the according datatype
 *  in combination with the Dataviewer Route Enhancer, that
 *  will limit the routing to the target pages with the detail
 *  plugin.
 *
 *  See the following example for configuration
 *
 *  routeEnhancers:
 *     News:
 *         type: Dataviewer
 *         targetPages: [ 26,27,28 ]
 *         routes:
 *             - { routePath: '{url}', _controller: 'Record::dynamicDetail', _arguments: {'url': 'record'} }
 *         defaultController: 'Record::dynamicDetail'
 *         aspects:
 *             url:
 *                 type: DataviewerMapper
 *                 datatype: 3
 *                 routeFieldPattern: '^(?P<path_segment>.+)$'
 *                 routeFieldResult: '{path_segment}'
 */
class DataviewerMapper extends PersistedPatternMapper
{
    /**
     * Target Page Id
     * @var int
     */
    protected $pageId;

    /**
     * @param array $settings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings)
    {
        $datatypeUid = $settings['datatype'] ?? null;

        if(!is_numeric($datatypeUid)) {
            throw new \InvalidArgumentException('datatype must be integer', 1639584156);
        }

        $datatype = BackendUtility::getRecord('tx_dataviewer_domain_model_datatype', $datatypeUid, 'tablename');

        if(!$datatype || !array_key_exists('tablename', $datatype)) {
            throw new \InvalidArgumentException('datatype with uid \''.$datatypeUid.'\' not found', 1639584369);
        }

        $settings['tableName'] = $datatype['tablename'];

        // We need to generate the additional dataviewer tca, to correctly prefill the $GLOBALS
        //$tcaGenerator = GeneralUtility::makeInstance(Generator::class);
        //$tcaGenerator->processTca(true);

        // Because we define this by dataviewer with no direct possibility to change,
        // we manually inject this here to speed up a little bit!
        $GLOBALS['TCA'][$settings['tableName']]['ctrl']['languageField'] = 'sys_language_uid';
        $GLOBALS['TCA'][$settings['tableName']]['ctrl']['transOrigPointerField'] = 'l10n_parent';

        parent::__construct($settings);
    }

    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId): void
    {
        $this->pageId = $pageId;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $value): ?string
    {
        $result = $this->findByIdentifier($value);
        $result = $this->resolveOverlay($result);
        return $this->createRouteResult($result);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $value): ?string
    {
        if (!preg_match('#' . $this->routeFieldPattern . '#', $value, $matches)) {
            return null;
        }
        $values = $this->filterNamesKeys($matches);
        $result = $this->findByRouteFieldValues($values);
        if ($result[$this->languageParentFieldName] ?? null > 0) {
            return (string)$result[$this->languageParentFieldName];
        }
        if (isset($result['uid'])) {
            return (string)$result['uid'];
        }
        return null;
    }

}