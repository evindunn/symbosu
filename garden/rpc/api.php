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
      ->select(["d.cid", "s.charstatename"])
      ->from("Kmdescr", "d")
      ->innerJoin("Kmcs", "s", "WITH", "(d.cid = s.cid AND d.cs = s.cs)")
      ->where("d.tid = :tid");

    foreach ($gardenTaxa as $taxa) {
      $tid = $taxa->getTid();
      $clQuery->setParameter("tid", $tid);
      $thumbnailQuery->setParameter("tid", $tid);
      $attributeQuery->setParameter("tid", $tid);

      $attribs = $attributeQuery->getQuery()->execute();
      $allAttribs = [];
      foreach ($attribs as $attrib) {
        if (!array_key_exists($attrib["cid"], $allAttribs)) {
          $allAttribs[$attrib["cid"]] = [];
        }
        array_push($allAttribs[$attrib["cid"]], $attrib["charstatename"]);
      }

      array_push($results, [
        "tid" => $tid,
        "sciname" => $taxa->getSciname(),
        "basename" => $taxa->getBasename(),
        "vernacularNames" => $taxa->getVernacularNames(),
        "thumbnailUrl" => $thumbnailQuery->getQuery()->execute()[0]["thumbnailurl"],
        "checklists" => array_map(function($cl) { return $cl["clid"]; }, $clQuery->getQuery()->execute()),
        "attributes" => $allAttribs
      ]);
    }

    ini_set("memory_limit", $memory_limit);
    set_time_limit(30);

    if ($search !== null) {

    }


//    # Select all garden taxa that have some sort of name
//    $sql = get_select_statement(
//        "taxa",
//        [
//            't.' . TaxaTbl::$TID,
//            't.' . TaxaTbl::$SCINAME
//        ]
//    );
//    // Abbreviation for 'taxa' table name
//    $sql .= 't ';
//
//    $sql .= 'LEFT JOIN taxavernaculars v ON t.' . TaxaTbl::$TID . ' = v.' . TaxaVernacularTbl::$TID . ' ';
//    $sql .= 'RIGHT JOIN fmchklsttaxalink chk ON t.' . TaxaTbl::$TID . ' = chk.' . FmChecklistTaxaLinkTbl::$TID . ' ';
//    $sql .= 'WHERE chk.' . FmChecklistTaxaLinkTbl::$CLID . " = Fmchecklists::$CLID_GARDEN_ALL ";
//
//    if ($search === null) {
//      $sql .= 'AND (t.' . TaxaTbl::$SCINAME . ' IS NOT NULL ';
//      $sql .= 'OR v.' . TaxaVernacularTbl::$VERNACULAR_NAME . ' IS NOT NULL) ';
//    }
//    else {
//      $sql .= 'AND (lower(t.' . TaxaTbl::$SCINAME . ") LIKE \"$search%\" ";
//      $sql .= 'OR lower(v. ' . TaxaVernacularTbl::$VERNACULAR_NAME . ") LIKE \"$search%\") ";
//    }
//
//    $sql .= 'GROUP BY t.' . TaxaTbl::$TID . ' ';
//    $sql .= 'ORDER BY v.' . TaxaVernacularTbl::$VERNACULAR_NAME;
//
//    $resultsTmp = run_query($sql);
//    $results = [];

//    // Populate image urls
//    foreach ($resultsTmp as $result) {
//      $result = array_merge($result, get_attribs($result["tid"]));
//      $result["image"] = get_thumbnail($result["tid"]);
//
//      $result["checklists"] = [];
//      $clidsTemp = get_checklists($result["tid"]);
//      foreach ($clidsTemp as $clid) {
//        array_push($result["checklists"], $clid[FmChecklistTbl::$CLID]);
//      }
//
//      $result["vernacular"] = [];
//      $result["vernacular"]["names"] = [];
//      $vernacularsTmp = get_vernacular_names($result["tid"]);
//      foreach ($vernacularsTmp as $vn) {
//        $basename_is_set = array_key_exists("basename", $result["vernacular"]);
//
//        if (!$basename_is_set && strtolower($vn[TaxaVernacularTbl::$LANGUAGE]) === 'basename') {
//          $result["vernacular"]["basename"] = $vn[TaxaVernacularTbl::$VERNACULAR_NAME];
//        } else {
//          array_push($result["vernacular"]["names"], $vn[TaxaVernacularTbl::$VERNACULAR_NAME]);
//        }
//      }
//      $result['vernacular']['names'] = array_unique($result["vernacular"]["names"]);
//
//      array_push($results, $result);
//    }

    return $results;
  }

  $searchResults = [];
  if (key_exists("canned", $_GET) && $_GET["canned"] === "true") {
    $searchResults = get_canned_searches();
  } else if (key_exists("attr", $_GET) && is_numeric($_GET['attr'])) {
    $searchResults = get_all_attrib_vals(intval($_GET['attr']));
  } else {
    $searchResults = get_garden_taxa($_GET);
  }

  // Begin View
  header("Content-Type: application/json; charset=UTF-8");
  echo json_encode($searchResults, JSON_NUMERIC_CHECK);
?>

