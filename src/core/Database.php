<?php

namespace Core;
use PDO;
class Database
{
    public $connection;
    public $statement;

    public function __construct($pdo)
    {
        $this->connection = $pdo;
    }

    public function query($query, $params = [])
    {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);

        return $this;
    }

    public function find()
    {
        return $this->statement->fetch();
    }

    public function get()
    {
        return $this->statement->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }


}