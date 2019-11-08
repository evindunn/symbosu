<?php
// list_products.php
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;

require_once "./config/SymbosuEntityManager.php";

$entityManager = SymbosuEntityManager::getEntityManager();

// fetch metadata
$driver = new DatabaseDriver(
  $entityManager->getConnection()->getSchemaManager()
);

$specificTables = [
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('adminlanguages'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('agents'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('fmchecklists'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('fmchklsttaxalink'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('images'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('institutions'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('omoccurrences'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('omcollections'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('kmcs'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('kmcharacters'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('kmcharheading'),
//  $entityManager->getConnection()->getSchemaManager()->listTableDetails('kmdescr'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('taxa'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('taxauthority'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('taxaenumtree'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('taxavernaculars'),
  $entityManager->getConnection()->getSchemaManager()->listTableDetails('users'),
];

$driver->setTables($specificTables, []);
$entityManager->getConfiguration()->setMetadataDriverImpl($driver);

$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('bit', 'integer');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('point', 'array');
$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('polygon', 'array');

$cmf = new DisconnectedClassMetadataFactory($entityManager);
$cmf->setEntityManager($entityManager);
$classes = $driver->getAllClassNames();
$metadata = $cmf->getAllMetadata();

$generator = new Doctrine\ORM\Tools\EntityGenerator();
$generator->setUpdateEntityIfExists(true);
$generator->setGenerateStubMethods(true);
$generator->setGenerateAnnotations(true);
$generator->generate($metadata, __DIR__ . '/models');
?>