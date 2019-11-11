<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\DatabaseDriver;

include_once(__DIR__ . "/symbini.php");
include_once("$SERVER_ROOT/config/dbconnection.php");
require_once("$SERVER_ROOT/vendor/autoload.php");

include_once ("$SERVER_ROOT/models/Adminlanguages.php");
include_once ("$SERVER_ROOT/models/Agents.php");
include_once ("$SERVER_ROOT/models/Fmchecklists.php");
include_once ("$SERVER_ROOT/models/Fmchklsttaxalink.php");
include_once ("$SERVER_ROOT/models/Images.php");
include_once ("$SERVER_ROOT/models/Institutions.php");
include_once ("$SERVER_ROOT/models/Kmcharacters.php");
include_once ("$SERVER_ROOT/models/Kmcharheading.php");
include_once ("$SERVER_ROOT/models/Kmcs.php");
include_once ("$SERVER_ROOT/models/Omcollections.php");
include_once ("$SERVER_ROOT/models/Omoccurrences.php");
include_once ("$SERVER_ROOT/models/Taxa.php");
include_once ("$SERVER_ROOT/models/Taxaenumtree.php");
include_once ("$SERVER_ROOT/models/Taxauthority.php");
include_once ("$SERVER_ROOT/models/Taxavernaculars.php");
include_once ("$SERVER_ROOT/models/Users.php");


class SymbosuEntityManager {

  // Doctrine config
  private static $isDevMode = true;
  private static $proxyDir = null;
  private static $cache = null;
  private static $useSimpleAnnotationReader = false;
  private static $EntityManager = null;

  private static function getMetaConfig() {
    global $SERVER_ROOT;

    if (SymbosuEntityManager::$cache == null) {
      SymbosuEntityManager::$cache = new ArrayCache();
    }

    return Setup::createAnnotationMetadataConfiguration(
      array("$SERVER_ROOT/config/models"),
      SymbosuEntityManager::$isDevMode,
      SymbosuEntityManager::$proxyDir,
      SymbosuEntityManager::$cache,
      SymbosuEntityManager::$useSimpleAnnotationReader
    );
  }

  private static function getDbConfig() {
    $dbParams = MySQLiConnectionFactory::getConParams("readonly");
    return array(
      "dbname" => $dbParams["database"],
      "user" => $dbParams["username"],
      "password" => $dbParams["password"],
      "host" => $dbParams["host"],
      "driver" => "pdo_mysql"
    );
  }

  /**
   * @return \Doctrine\ORM\EntityManager
   * @throws \Doctrine\ORM\ORMException
   */
  public static function getEntityManager() {
    if (SymbosuEntityManager::$EntityManager === null) {
      SymbosuEntityManager::$EntityManager = EntityManager::create(
        SymbosuEntityManager::getDbConfig(),
        SymbosuEntityManager::getMetaConfig()
      );
    }
    return SymbosuEntityManager::$EntityManager;
  }
}
?>
