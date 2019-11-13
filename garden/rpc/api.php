<?php

  use Doctrine\Common\Collections\ArrayCollection;

  include_once("../../config/symbini.php");

  global $SERVER_ROOT, $CHARSET;

  include_once($SERVER_ROOT . "/classes/Functional.php");
  include_once($SERVER_ROOT . "/meta/tables/fmchecklists.php");
  include_once($SERVER_ROOT . "/meta/tables/fmchklsttaxalink.php");
  include_once($SERVER_ROOT . "/meta/tables/images.php");
  include_once($SERVER_ROOT . "/meta/tables/kmcs.php");
  include_once($SERVER_ROOT . "/meta/tables/kmdescr.php");
  include_once($SERVER_ROOT . "/meta/tables/taxa.php");
  include_once($SERVER_ROOT . "/meta/tables/taxavernaculars.php");

  include_once($SERVER_ROOT . "/config/SymbosuEntityManager.php");
  include_once($SERVER_ROOT . "/models/Fmchecklists.php");

  /**
   * Returns canned searches for the react page
   */
  function get_canned_searches() {
    $em = SymbosuEntityManager::getEntityManager();
    $checklistRepo = $em->getRepository("Fmchecklists");
    $gardenChecklists = $checklistRepo->findBy([ "parentclid" => Fmchecklists::$CLID_GARDEN_ALL ]);
    $results = [];

    foreach ($gardenChecklists as $cl) {
      array_push($results, [
        "clid" => $cl->getClid(),
        "name" => $cl->getName(),
        "iconUrl" => $cl->getIconurl(),
        "description" => $cl->getTitle()
      ]);
    }

    return $results;
  }

  function get_garden_characteristics($cids=[]) {
    $em = SymbosuEntityManager::getEntityManager();
    $characteristicRepo = $em->getRepository("Kmcharacters");
    $csQuery = $characteristicRepo->createQueryBuilder("c")
      ->distinct()
      ->innerJoin("Kmdescr", "d", "WITH", "c.cid = d.cid")
      ->innerJoin("Fmchklsttaxalink", "tl", "WITH", "d.tid = tl.tid")
      ->where("tl.clid = " . Fmchecklists::$CLID_GARDEN_ALL);

    if (count($cids) > 0) {
      $csQuery->andWhere($csQuery->expr()->in("c.cid", ":cids"));
      $csQuery->setParameter("cids", $cids);
    }

    $results = [];
    foreach ($csQuery->getQuery()->execute() as $characteristic) {
      $states = $characteristic->getStates()->toArray();
      $statesMap = [];

      foreach ($states as $cs) {
        $statesMap[$cs->getCs()] = $cs->getCharstatename();
      }

      $results[$characteristic->getCid()] = [
        "charname" => $characteristic->getCharname(),
        "states" => $statesMap
      ];
    }

    return $results;
  }

  /**
   * Returns all unique taxa with thumbnail urls
   * @params $_GET
   */
  function get_garden_taxa($params) {
    $memory_limit = ini_get("memory_limit");
    ini_set("memory_limit", "1G");
    set_time_limit(0);

    $search = null;
    if (key_exists("search", $params) && $params["search"] !== "" && $params["search"] !== null) {
      $search = strtolower(preg_replace("/[;()-]/", '', $params["search"]));
    }

    $em = SymbosuEntityManager::getEntityManager();
    $taxaRepo = $em->getRepository("Taxa");

    $results = [];

    // All taxa that belong to Garden checklist
    $gardenTaxa = $taxaRepo->createQueryBuilder("t")
      ->innerJoin("Fmchklsttaxalink", "tl", "WITH", "t.tid = tl.tid")
      ->innerJoin("Fmchecklists", "cl", "WITH", "tl.clid = cl.clid")
      ->where("cl.parentclid = " . Fmchecklists::$CLID_GARDEN_ALL)
      ->getQuery()
      ->execute();

    // All Checklists for a given tid
    $clQuery = $em->createQueryBuilder()
      ->select(["cl.clid"])
      ->from("Fmchklsttaxalink", "tl")
      ->innerJoin("Fmchecklists", "cl", "WITH", "tl.clid = cl.clid")
      ->where("tl.tid = :tid")
      ->andWhere("cl.parentclid = " . Fmchecklists::$CLID_GARDEN_ALL);

    // The thumbnail url for a given tid
    $thumbnailQuery = $em->createQueryBuilder()
      ->select(["i.thumbnailurl"])
      ->from("Images", "i")
      ->where("i.tid = :tid")
      ->orderBy("i.sortsequence")
      ->setMaxResults(1);

    // Attributes for the taxa
    $attributeQuery = $em->createQueryBuilder()
      ->select(["c.cid", "s.cs"])
      ->from("Kmdescr", "d")
      ->innerJoin("Kmcs", "s", "WITH", "(d.cid = s.cid AND d.cs = s.cs)")
      ->innerJoin("Kmcharacters", "c", "WITH", "d.cid = c.cid")
      ->where("d.tid = :tid");
    $attributeQuery = $attributeQuery
      ->andWhere($attributeQuery->expr()->in("d.cid", Kmdescr::$GARDEN_CIDS));

    foreach ($gardenTaxa as $taxa) {
      $tid = $taxa->getTid();
      $clQuery->setParameter("tid", $tid);
      $thumbnailQuery->setParameter("tid", $tid);
      $attributeQuery->setParameter("tid", $tid);

      $attribs = $attributeQuery->getQuery()->execute();
      $allAttribs = [];
      foreach ($attribs as $attrib) {
        $cid = $attrib["cid"];
        if (!array_key_exists($cid, $allAttribs)) {
          $allAttribs[$cid] = [];
        }
        array_push($allAttribs[$cid], $attrib["cs"]);
      }

      array_push($results, [
        "tid" => $tid,
        "sciName" => $taxa->getSciname(),
        "basename" => $taxa->getBasename(),
        "vernacularNames" => $taxa->getVernacularNames(),
        "thumbnailUrl" => resolve_img_path($thumbnailQuery->getQuery()->execute()[0]["thumbnailurl"]),
        "checklists" => array_map(function($cl) { return $cl["clid"]; }, $clQuery->getQuery()->execute()),
        "characteristics" => $allAttribs
      ]);
    }

    ini_set("memory_limit", $memory_limit);
    set_time_limit(30);

    if ($search !== null) {

    }

    return $results;
  }

  $searchResults = [];
  if (key_exists("canned", $_GET) && $_GET["canned"] === "true") {
    $searchResults = get_canned_searches();
  } else if (key_exists("attr", $_GET) && is_numeric($_GET['attr'])) {
    $searchResults = get_all_attrib_vals(intval($_GET['attr']));
  } else if (key_exists("chars", $_GET)) {
    if ($_GET["chars"] === "true") {
      $searchResults = get_garden_characteristics();
    } else {
      $searchResults = get_garden_characteristics(explode(',', $_GET["chars"]));
    }
  }
  else {
    $searchResults = get_garden_taxa($_GET);
  }

  // Begin View
  header("Content-Type: application/json; charset=UTF-8");
  echo json_encode($searchResults, JSON_NUMERIC_CHECK);
?>

