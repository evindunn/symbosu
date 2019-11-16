<?php
include_once("../../config/symbini.php");

include_once("$SERVER_ROOT/config/SymbosuEntityManager.php");
include_once("$SERVER_ROOT/classes/Functional.php");
include_once("$SERVER_ROOT/classes/TaxaManager.php");

$result = [];

$CLID_GARDEN_ALL = 54;

function getTaxon($tid) {
  $em = SymbosuEntityManager::getEntityManager();
  $taxaRepo = $em->getRepository("Taxa");
  $taxaModel = $taxaRepo->find($tid);

  $taxa = TaxaManager::fromModel($taxaModel);

  $result = [
    "tid" => $tid,
    "sciname" => '',
    "images" => [],
    "vernacular" => [
      "basename" => '',
      "names" => []
    ],
    "isGardenTaxa" => false
  ];

  if ($taxa != null) {
    $result["sciname"] = $taxa->getSciname();
    $result["images"] = $taxa->getImages();
    $result["vernacular"] = [
      "basename" => $taxa->getBasename(),
      "names" => $taxa->getVernacularNames()
    ];
    $result["isGardenTaxa"] = $taxa->isGardenTaxa();
  }

  return $result;
}

$result = [];
if (array_key_exists("taxon", $_GET) && is_numeric($_GET["taxon"])) {
  $result = getTaxon($_GET["taxon"]);
}

// Begin View
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($result, JSON_NUMERIC_CHECK);
?>