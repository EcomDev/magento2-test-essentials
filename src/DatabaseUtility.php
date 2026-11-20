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
            $select->where($condition, $value);
        }

        $result = [];

        $statement = $connection->prepare($select);
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_NUM)) {
            $result[] = $row;
        }

        $this->restoreStringify($connection);
        return $result;
    }

    private function connection(string $connectionName): AdapterInterface
    {
        $connection = $this->resourceConnection->getConnection($connectionName);
        return $connection;
    }

    /**
     * Makes connection preserve type of fetched data
     *
     * @param Mysql $adapter
     */
    private function disableStringify(AdapterInterface $adapter): void
    {
        $adapter->getConnection()->setAttribute(PDO\Mysql::ATTR_STRINGIFY_FETCHES, false);
    }

    /**
     * Restore default Magento behavior
     *
     * @param Mysql $adapter
     */
    private function restoreStringify(AdapterInterface $adapter): void
    {
        $adapter->getConnection()->setAttribute(PDO\Mysql::ATTR_STRINGIFY_FETCHES, true);
    }
}
