<?php

declare(strict_types=1);

namespace EcomDev\Magento2TestEssentials;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use PDO;

final readonly class DatabaseUtility
{
    private function __construct(private ResourceConnection $resourceConnection)
    {
    }

    public static function create(ObjectManager $objectManager): self
    {
        return new self(
            $objectManager->create(ResourceConnection::class)
        );
    }

    public function isTableExists(
        string $tableName,
        string $connectionName = ResourceConnection::DEFAULT_CONNECTION
    ): bool {
        return $this->connection($connectionName)
            ->isTableExists($this->resourceConnection->getTableName($tableName));
    }

    /**
     * Populate table with data
     *
     * @param list<string> $columns
     * @param list<list<scalar>> $data
     */
    public function populateTable(
        string $tableName,
        array $columns,
        array $data,
        string $connectionName = ResourceConnection::DEFAULT_CONNECTION
    ): void {
        $this->connection($connectionName)
            ->insertArray(
                $this->resourceConnection->getTableName($tableName),
                $columns,
                $data
            );
    }

    /**
     * Fetches table data from table
     *
     * @param list<string> $columns
     * @param array<string, scalar|array<scalar>> $conditions
     *
     * @return list<list<scalar>>
     */
    public function tableData(
        string $tableName,
        array $columns,
        array $conditions = [],
        string $connectionName = ResourceConnection::DEFAULT_CONNECTION
    ): array {
        $connection = $this->connection($connectionName);
        $this->disableStringify($connection);

        $select = $connection->select()
            ->from($this->resourceConnection->getTableName($tableName), $columns);

        foreach ($conditions as $condition => $value) {
            // @phpstan-ignore-next-line
            $select->where($condition, $value);
        }

        $result = [];

        $statement = $connection->prepare((string)$select);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_NUM)) {
            $result[] = $row;
        }

        $this->restoreStringify($connection);
        return $result;
    }

    /**
     * Wrapper for connection retrieval with proper type hint
     *
     * @return Mysql
     */
    private function connection(string $connectionName): AdapterInterface
    {
        // @phpstan-ignore-next-line
        return $this->resourceConnection->getConnection($connectionName);
    }

    /**
     * Makes connection preserve type of fetched data
     *
     * @param Mysql $adapter
     */
    private function disableStringify(AdapterInterface $adapter): void
    {
        $this->stringifyAttribute($adapter, false);
    }

    /**
     * Stringify attribute on adapter
     *
     * @param Mysql $adapter
     */
    private function stringifyAttribute(AdapterInterface $adapter, bool $value): void
    {
        $connection = $adapter->getConnection();
        // @phpstan-ignore-next-line
        $connection->setAttribute(PDO\Mysql::ATTR_STRINGIFY_FETCHES, $value);
    }

    /**
     * Restore default Magento behavior
     *
     * @param Mysql $adapter
     */
    private function restoreStringify(AdapterInterface $adapter): void
    {
        $this->stringifyAttribute($adapter, false);
    }
}
