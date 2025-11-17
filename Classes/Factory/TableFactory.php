<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Factory;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\SchemaException;
use InvalidArgumentException;
use RuntimeException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Schema\Exception\StatementException;
use TYPO3\CMS\Core\Database\Schema\Exception\UnexpectedSignalReturnValueTypeException;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Aix\Dataviewer\Configuration\ExtensionConfiguration;

class TableFactory implements SingletonInterface
{
    /**
     * SQL Reader
     *
     * @var SqlReader
     */
    protected $sqlReader;

    /**
     * Schema Migrator
     *
     * @var SchemaMigrator
     */
    protected $schemaMigrator;

    /**
     * Connection
     *
     * @var Connection
     */
    protected $connection;

    /**
     * @param SqlReader $sqlReader
     */
    public function injectSqlReader(SqlReader $sqlReader): void
    {
        $this->sqlReader = $sqlReader;
    }

    /**
     * @param SchemaMigrator $schemaMigrator
     */
    public function injectSchemaMigrator(SchemaMigrator $schemaMigrator): void
    {
        $this->schemaMigrator = $schemaMigrator;
    }

    /**
     * Gets the database connection model
     *
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        if (!$this->connection) {
            /* @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $this->connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        }

        return $this->connection;
    }

    /**
     * Checks if a table exists
     *
     * @param string $tableName
     * @return bool
     * @throws Exception
     */
    public function tableExists(string $tableName): bool
    {

        return $this->getConnection()->createSchemaManager()->tablesExist($tableName);
    }

    /**
     * Suggest a table name by a given datatype name
     *
     * @param string $datatypeName
     * @return string
     */
    public function suggestTableNameByDatatypeName(string $datatypeName): string
    {
        $parts = GeneralUtility::trimExplode(' ', $datatypeName, true);
        $parts = array_map('strtolower', $parts);
        return 'tx_dataviewer_domain_model_record_'.implode('_',$parts);
    }

    /**
     * Checks if a table is a record table, that
     * was set in a datatype
     *
     * @param string $tableName
     * @return bool
     * @throws Exception
     */
    public function isRecordTable(string $tableName): bool
    {
        // We need to check all datatypes, if the according tablename is set somewhere
        return ($this->getConnection()->select(["uid"],ExtensionConfiguration::EXTENSION_DATATYPE_TABLE,["tablename"=>$tableName])->rowCount() > 0);
    }

    /**
     * Gets all fields of a table
     *
     * @param string $tableName
     * @return array
     * @throws Exception
     */
    public function getTableColumns(string $tableName): array
    {
        $columns = [];

        if ($this->tableExists($tableName)) {
            $columnsList = $this->getConnection()->createSchemaManager()->listTableColumns($tableName);
            if (is_array($columnsList) && count($columnsList) > 0) {
                $columns = array_keys($columnsList);
            }
        }

        return $columns;
    }

    /**
     * Gets all missing columns
     *
     * @param string $tableName
     * @param Datatype $datatype
     * @return array
     * @throws Exception
     */
    public function getMissingColumns(string $tableName, Datatype $datatype): array
    {
        $columns = $this->getTableColumns($tableName);
        $missingColumns = [];
        foreach ($datatype->getFields() as $_field) {
            if (!in_array($_field->getCode(),$columns)) {
                $missingColumns[] = $_field;
            }
        }

        return $missingColumns;
    }

    /**
     * Checks if a table needs an update
     *
     * @param string $tableName
     * @param Datatype $datatype
     * @return bool
     * @throws Exception
     */
    public function tableNeedsUpdate(string $tableName, Datatype $datatype): bool
    {
        $missingColumns = $this->getMissingColumns($tableName, $datatype);
        return (count($missingColumns)>0);
    }

    /**
     * Checks if a tablename is allowed
     *
     * @param string $tableName
     * @return bool
     */
    public function isAllowedTablename(string $tableName): bool
    {
        if ($tableName == '') {
            return false;
        }

        return true;
    }

    /**
     * Gets an array with update statements for
     * a given CREATE TABLE statement
     *
     * @param string $createStatement
     * @return array
     */
    public function getSqlStatements(string $createStatement): array
    {
        return $this->sqlReader->getCreateTableStatementArray($createStatement);
    }

    /**
     * @param string $tableName
     * @return Column[]
     * @throws Exception
     */
    public function getTableLayout(string $tableName): array
    {
        return $this->getConnection()->createSchemaManager()->listTableColumns($tableName);
    }

    /**
     * Gets an array with update statements for
     * a given CREATE TABLE statement
     *
     * @param array $sqlStatements
     * @return array
     * @throws Exception
     * @throws StatementException
     * @throws SchemaException
     * @throws UnexpectedSignalReturnValueTypeException
     */
    public function getUpdateStatements(array $sqlStatements): array
    {
        $updateStatements = $this->schemaMigrator->getUpdateSuggestions($sqlStatements);
        return array_merge_recursive(...array_values($updateStatements));
    }

    /**
     * Gets selected statements from
     * update statements
     *
     * @param array $updateStatements
     * @return array
     */
    public function getSelectedStatements(array $updateStatements): array
    {
        $selectedStatements = [];
        foreach (['add', 'change', 'create_table', 'change_table'] as $action) {
            if (empty($updateStatements[$action])) {
                continue;
            }
            $selectedStatements = array_merge(
                $selectedStatements,
                array_combine(array_keys($updateStatements[$action]), array_fill(0, count($updateStatements[$action]), true))
            );
        }

        return $selectedStatements;
    }

    /**
     * Gets a CREATE TABLE statement for a given
     * datatype
     *
     * @param Datatype $datatype
     * @param string $tableName Target Table Name
     * @return string
     */
    public function getCreateTableStatementByDatatype(Datatype $datatype, string $tableName): string
    {
        /* @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateFile = "EXT:dataviewer/Resources/Private/Init/CREATE_STATEMENT.sql";
        $templateFile = GeneralUtility::getFileAbsFileName($templateFile);
        $standaloneView->setTemplatePathAndFilename($templateFile);
        $standaloneView->setFormat("sql");
        $standaloneView->assign("datatype", $datatype);
        $standaloneView->assign("tableName", $tableName);
        return $standaloneView->render();
    }

    /**
     * Migrates table information
     *
     * @param array $updateStatements
     * @param array $selectedStatements
     * @return array
     * @throws DBALException
     * @throws SchemaException
     * @throws StatementException
     * @throws UnexpectedSignalReturnValueTypeException
     */
    public function migrate(array $updateStatements, array $selectedStatements): array
    {
        return $this->schemaMigrator->migrate($updateStatements, $selectedStatements);
    }

    /**
     * Perform add/change/create operations on tables and fields in an optimized,
     * non-interactive, mode using the original doctrine SchemaManager ->toSaveSql()
     * method.
     *
     * @param string[] $statements The CREATE TABLE statements
     * @param bool $createOnly Only perform changes that add fields or create tables
     * @return array[] Error messages for statements that occurred during the installation procedure.
     * @throws DBALException
     * @throws SchemaException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedSignalReturnValueTypeException
     * @throws StatementException
     */
    public function install(array $statements, bool $createOnly): array
    {
        return $this->schemaMigrator->install($statements, $createOnly);
    }

    /**
     * @param string $tableName
     * @return void
     * @throws Exception
     */
    public function dropTable(string $tableName): void
    {
        $this->getConnection()->createSchemaManager()->dropTable($tableName);
    }
}