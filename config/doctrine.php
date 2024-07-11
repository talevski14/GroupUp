<?php
// bootstrap.php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . "/../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__."/../src/models"),
    isDevMode: true,
);

$dbConf = [
    'driver' => 'pdo_mysql',
    'dbname' => 'groupup',
    'user' => 'groupup',
    'password' => 'secret',
    'host' => 'host.docker.internal',
    'port' => '3306',
    'charset' => "utf8mb4"
];

// configuring the database connection
$connection = DriverManager::getConnection($dbConf, $config);

// obtaining the entity manager
$entityManager = new EntityManager($connection, $config);


return $entityManager;