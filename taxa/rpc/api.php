<?php
include_once("../../config/symbini.php");

include_once("$SERVER_ROOT/config/SymbosuEntityManager.php");
include_once("$SERVER_ROOT/classes/Functional.php");

$result = [];

$CLID_GARDEN_ALL = 54;

function getTaxon($tid) {
  $em = SymbosuEntityManager::getEntityManager();
  $taxaRepo = $em->getRepository("Taxa");
  $currentTaxa = $taxaRepo->find($tid);

  $imageRepo = $em->getRepository("Images");
  $taxaImages = array_map(
    function($img) {
      return [ "thumbnail" => resolve_img_path($img->getThumbnailurl()), "image" => resolve_img_path($img->getUrl()) ];
    },
    $imageRepo->findBy(["tid" => $tid], ["sortsequence" => "ASC"])
  );

  $result = [
    "tid" => $tid,
    "sciname" => $currentTaxa->getSciname(),
    "vernacular" => [
      "basename" => $currentTaxa->getBasename(),
      "names" => $currentTaxa->getVernacularnames()
    ],
    "images" => $taxaImages
  ];

  return $result;
}

if (array_key_exists("taxon", $_GET) && is_numeric($_GET["taxon"])) {
  $result = getTaxon($_GET["taxon"]);
}

// Begin View
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($result, JSON_NUMERIC_CHECK);
?>