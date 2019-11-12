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


  $CLID_GARDEN_ALL = 54;

  $CID_SUNLIGHT = 680;
  $CID_MOISTURE = 683;
  $CID_WIDTH = 738;
  $CID_HEIGHT = 140;

  # Plant features
  $CID_FLOWER_COLOR = 612;
  $CID_BLOOM_MONTHS = 165;
  $CID_WILDLIFE_SUPPORT = 685;
  $CID_LIFESPAN = 136;
  $CID_FOLIAGE_TYPE = 100;
  $CID_PLANT_TYPE = 137;

  # Growth & maintenance
  $CID_LANDSCAPE_USES = 679;
  $CID_CULTIVATION_PREFS = 767;
  $CID_BEHAVIOR = 688;
  $CID_PROPAGATION = 670;
  $CID_EASE_GROWTH = 684;

  # Beyond the garden
  $CID_HABITAT = 163;
  $CID_ECOREGION = 19;

  /**
   * @param $cid int Attribute ID in db
   * @return string[] Array of all distinct values for the cid
   */
  function get_all_attrib_vals($cid) {
    $sql = 'SELECT DISTINCT ' . KmcsTbl::$CHAR_STATE_NAME . ' FROM kmcs ';
    $sql .= 'WHERE ' . KmcsTbl::$CID . " = $cid";

    $results = [];
    $res_tmp = run_query($sql);
    foreach ($res_tmp as $r) {
      array_push($results, $r[KmcsTbl::$CHAR_STATE_NAME]);
    }

    sort($results);
    return $results;
  }

  function get_attribs($tid) {
    global $CID_WIDTH, $CID_HEIGHT;
    global $CID_MOISTURE, $CID_SUNLIGHT;
    global $CID_FLOWER_COLOR, $CID_BLOOM_MONTHS, $CID_WILDLIFE_SUPPORT, $CID_LIFESPAN, $CID_FOLIAGE_TYPE, $CID_PLANT_TYPE;
    global $CID_LANDSCAPE_USES, $CID_CULTIVATION_PREFS, $CID_BEHAVIOR, $CID_PROPAGATION, $CID_EASE_GROWTH;
    global $CID_ECOREGION, $CID_HABITAT;

    $all_attr_sql = get_select_statement(
        "kmdescr",
        [
            'kmdescr.' . KmDescrTbl::$CID . ' as attr_key',
            'lower(kmcs.' . KmcsTbl::$CHAR_STATE_NAME . ') as attr_val'
        ]
    );
    $all_attr_sql .= 'INNER JOIN kmcs on ';
    $all_attr_sql .= '(kmdescr.' . KmDescrTbl::$CID . ' = kmcs.' . KmcsTbl::$CID . ' ';
    $all_attr_sql .= 'AND kmdescr.' . KmDescrTbl::$CS . ' = kmcs.' . KmcsTbl::$CS . ') ';
    $all_attr_sql .= 'WHERE kmdescr.' . KmDescrTbl::$TID . " = $tid";

    $attr_res = run_query($all_attr_sql);
    $attr_array = [
      "height" => [],
      "width" => [],
      "sunlight" => [],
      "moisture" => [],
      "features" => [
        "flower_color" => [],
        "bloom_months" => [],
        "wildlife_support" => [],
        "lifespan" => [],
        "foliage_type" => [],
        "plant_type" => []
      ],
      "growth_maintenance" => [
        "landscape_uses" => [],
        "cultivation_prefs" => [],
        "behavior" => [],
        "propagation" => [],
        "ease_growth" => [],
      ],
      "beyond_garden" => [
        "eco_region" => [],
        "habitat" => []
      ]
    ];

    foreach ($attr_res as $attr) {
      $attr_key = intval($attr["attr_key"]);
      $attr_val = $attr["attr_val"];
      switch ($attr_key) {
        case $CID_HEIGHT:
          array_push($attr_array["height"], intval($attr_val));
          break;
        case $CID_WIDTH:
          array_push($attr_array["width"], intval($attr_val));
          break;
        case $CID_SUNLIGHT:
          array_push($attr_array["sunlight"], $attr_val);
          break;
        case $CID_MOISTURE:
          array_push($attr_array["moisture"], $attr_val);
          break;
        case $CID_FLOWER_COLOR:
          array_push($attr_array["features"]["flower_color"], $attr_val);
          break;
        case $CID_BLOOM_MONTHS:
          array_push($attr_array["features"]["bloom_months"], $attr_val);
          break;
        case $CID_WILDLIFE_SUPPORT:
          array_push($attr_array["features"]["wildlife_support"], $attr_val);
          break;
        case $CID_LIFESPAN:
          array_push($attr_array["features"]["lifespan"], $attr_val);
          break;
        case $CID_FOLIAGE_TYPE:
          array_push($attr_array["features"]["foliage_type"], $attr_val);
          break;
        case $CID_PLANT_TYPE:
          array_push($attr_array["features"]["plant_type"], $attr_val);
          break;
        case $CID_LANDSCAPE_USES:
          array_push($attr_array["growth_maintenance"]["landscape_uses"], $attr_val);
          break;
        case $CID_CULTIVATION_PREFS:
          array_push($attr_array["growth_maintenance"]["cultivation_prefs"], $attr_val);
          break;
        case $CID_BEHAVIOR:
          array_push($attr_array["growth_maintenance"]["behavior"], $attr_val);
          break;
        case $CID_PROPAGATION:
          array_push($attr_array["growth_maintenance"]["propagation"], $attr_val);
          break;
        case $CID_EASE_GROWTH:
          array_push($attr_array["growth_maintenance"]["ease_growth"], $attr_val);
          break;
        case $CID_ECOREGION:
          array_push($attr_array["beyond_garden"]["eco_region"], $attr_val);
          break;
        case $CID_HABITAT:
          array_push($attr_array["beyond_garden"]["habitat"], $attr_val);
          break;
        default:
          break;
      }
    }

    foreach (["width", "height"] as $k) {
      if (count($attr_array[$k]) > 1) {
        $tmp = [min($attr_array[$k]), max($attr_array[$k])];
        $attr_array[$k] = $tmp;
      }
    }

    return $attr_array;
  }

  /**
   * Returns canned searches for the react page
   */
  function get_canned_searches() {
    global $CLID_GARDEN_ALL;

    $sql = get_select_statement(
        "fmchecklists",
        [
          FmChecklistTbl::$CLID,
          FmChecklistTbl::$NAME,
          FmChecklistTbl::$ICON_URL,
          FmChecklistTbl::$TITLE . ' as description',
        ]
    );
    $sql .= 'WHERE ' . FmChecklistTbl::$PARENT_CLID . ' = ' . $CLID_GARDEN_ALL;
    return run_query($sql);
  }

  /**
   * Returns all unique taxa with thumbnail urls
   * @params $_GET
   */
  function get_garden_taxa($params) {
    global $CLID_GARDEN_ALL;

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

    $gardenTaxa = $taxaRepo->createQueryBuilder("t")
      ->innerJoin("Fmchklsttaxalink", "tl", "WITH", "t.tid = tl.tid")
      ->innerJoin("Fmchecklists", "cl", "WITH", "tl.clid = cl.clid")
      ->where("cl.parentclid = $CLID_GARDEN_ALL")
      ->getQuery()
      ->execute();

    $clQuery = $em->createQueryBuilder()
      ->select(["cl.clid"])
      ->from("Fmchklsttaxalink", "tl")
      ->innerJoin("Fmchecklists", "cl", "WITH", "tl.clid = cl.clid")
      ->where("tl.tid = :tid")
      ->andWhere("cl.parentclid = $CLID_GARDEN_ALL");

    $thumbnailQuery = $em->createQueryBuilder()
      ->select(["i.thumbnailurl"])
      ->from("Images", "i")
      ->where("i.tid = :tid")
      ->orderBy("i.sortsequence")
      ->setMaxResults(1);

    $counter = 0;

    foreach ($gardenTaxa as $taxa) {
      $tid = $taxa->getTid();
      $clQuery->setParameter("tid", $tid);
      $thumbnailQuery->setParameter("tid", $tid);

      array_push($results, [
        "tid" => $tid,
        "sciname" => $taxa->getSciname(),
        "basename" => $taxa->getBasename(),
        "vernacularNames" => $taxa->getVernacularNames(),
        "thumbnailUrl" => $thumbnailQuery->getQuery()->execute()[0]["thumbnailurl"],
        "checklists" => array_map(function($cl) { return $cl["clid"]; }, $clQuery->getQuery()->execute())
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
//    $sql .= 'WHERE chk.' . FmChecklistTaxaLinkTbl::$CLID . " = $CLID_GARDEN_ALL ";
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

