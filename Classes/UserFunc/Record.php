<?php
declare(strict_types=1);

namespace Aix\Dataviewer\UserFunc;

use Aix\Dataviewer\Domain\Repository\DatatypeRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Aix\Dataviewer\Domain\Model\Datatype;

class Record
{
    /**
     * @var DatatypeRepository
     */
    protected $datatypeRepository;

    /**
     * @param DatatypeRepository $datatypeRepository
     */
    public function injectDatatypeRepository(DatatypeRepository $datatypeRepository)
    {
        $this->datatypeRepository = $datatypeRepository;
    }

    /**
     * Populate records on selected pages
     * @param array $config Configuration Array
     * @param mixed $parentObject Parent Object
     * @return array
     */
    public function populateRecordsByDatatypeSelection(array &$config, &$parentObject): void
    {
        $pages = $config["flexParentDatabaseRow"]["pages"];

        if (!is_array($pages)) {
            $pages = GeneralUtility::intExplode(",", $pages);
        }

        $datatypeUid = $config['row']['settings.datatype_selection'];

        if(is_array($datatypeUid)) {
            $datatypeUid = (int)reset($datatypeUid);
        }

        if($datatypeUid <= 0) {
            return;
        }

        /* @var Datatype $datatype */
        $datatype = $this->datatypeRepository->findByUid($datatypeUid, false);

        if($datatype instanceof Datatype) {
            $table = $datatype->getTablename();

            // Fetch all records of datatype on the selected pages
            /* @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($table);

            $result = $queryBuilder->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->in('pid', $pages)
                )
                ->andWhere('deleted = 0')
                ->andWhere('sys_language_uid = 0')
                ->execute()
                ->fetchAllAssociative();

            $options = [];
            if(!empty($result)) {
                $options[] = ["[{$datatype->getUid()}] {$datatype->getName()}x", "--div--"];
                foreach($result as $_row) {
                    $label = $_row['title'];
                    $value = $_row['uid'];
                    $options[] = [
                        'label' => $label,
                        'value' => $value,
                        'icon' => 'extensions-dataviewer-'.$_row['icon']];
                }
            }

            $config["items"] = array_merge($config["items"], $options);
        }
    }
}
