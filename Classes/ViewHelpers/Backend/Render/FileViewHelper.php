<?php
declare(strict_types=1);

namespace Aix\Dataviewer\ViewHelpers\Backend\Render;

use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Driver\Mysqli\Exception\StatementError;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class FileViewHelper extends AbstractRenderViewHelper
{
    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @param FileRepository $fileRepository
     */
    public function injectFileRepository(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Arguments initialization
     * @throws Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'Uid of the file', true);
        $this->registerArgument('table', 'string', 'Name of the table', true);
        $this->registerArgument('field', 'string', 'Name of the relation field', true);
    }

    /**
     * @return string
     * @throws StatementError
     * @throws DoctrineDBALDriverException
     */
    public function render(): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file_reference');

        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        /* @var \Doctrine\DBAL\Driver\Mysqli\MysqliStatement $res */
        $res = $queryBuilder
            ->select('uid')
            ->from('sys_file_reference')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid_foreign',
                    $queryBuilder->createNamedParameter($this->arguments['uid'], PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'tablenames',
                    $queryBuilder->createNamedParameter($this->arguments['table'], PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'fieldname',
                    $queryBuilder->createNamedParameter($this->arguments['field'], PDO::PARAM_STR)
                )
            )
            ->orderBy('sorting_foreign')
            ->execute();

        $result = $res->fetchAllAssociative();
        if(isset($result['uid'])) {
            $fileReference = $this->fileRepository->findFileReferenceByUid($result['uid']);

            $string = '';
            if($fileReference instanceof FileReference) {
                $string .= $this->iconFactory->getIconForFileExtension($fileReference->getExtension(),Icon::SIZE_SMALL);
                $string .= ' ';
                $string .= $fileReference->getPublicUrl();
            } else {
                $string .= 'File not found!';
            }

            return $string;
        }

        return '';
    }
}
