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

  # Basic characteristics
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
  
  $CHARACTERISTICS_ALL = [
    # Basic characteristics
    $CID_SUNLIGHT,
    $CID_MOISTURE,
    $CID_WIDTH,
    $CID_HEIGHT,

    # Plant features
    $CID_FLOWER_COLOR,
    $CID_BLOOM_MONTHS,
    $CID_WILDLIFE_SUPPORT,
    $CID_LIFESPAN,
    $CID_FOLIAGE_TYPE,
    $CID_PLANT_TYPE,
  
    # Growth & maintenance
    $CID_LANDSCAPE_USES,
    $CID_CULTIVATION_PREFS,
    $CID_BEHAVIOR,
    $CID_PROPAGATION,
    $CID_EASE_GROWTH,
  
    # Beyond the garden
    $CID_HABITAT,
    $CID_ECOREGION,
  ];

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

  function get_garden_characteristics($cid) {
    $em = SymbosuEntityManager::getEntityManager();
    $charStateRepo = $em->getRepository("Kmcs");
    $csQuery = $charStateRepo->findBy([ "cid" => $cid ], ["sortsequence" => "ASC"]);
    return array_map(function($cs) { return $cs->getCharstatename(); }, $csQuery);
  }

  /**
   * Returns all unique taxa with thumbnail urls
   * @params $_GET
   */
  function get_garden_taxa($params) {
    global $CHARACTERISTICS_ALL;
    global $CID_WIDTH, $CID_HEIGHT;
    global $CID_MOISTURE, $CID_SUNLIGHT;
    global $CID_FLOWER_COLOR, $CID_BLOOM_MONTHS, $CID_WILDLIFE_SUPPORT, $CID_LIFESPAN, $CID_FOLIAGE_TYPE, $CID_PLANT_TYPE;
    global $CID_LANDSCAPE_USES, $CID_CULTIVATION_PREFS, $CID_BEHAVIOR, $CID_PROPAGATION, $CID_EASE_GROWTH;
    global $CID_ECOREGION, $CID_HABITAT;

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
    $gardenTaxaQuery = $taxaRepo->createQueryBuilder("t")
      ->innerJoin("Fmchklsttaxalink", "tl", "WITH", "t.tid = tl.tid")
      ->innerJoin("Fmchecklists", "cl", "WITH", "tl.clid = cl.clid")
      ->where("cl.parentclid = " . Fmchecklists::$CLID_GARDEN_ALL);

    if ($search !== null) {
      $gardenTaxaQuery
        ->innerJoin("Taxavernaculars", "tv", "WITH", "t.tid = tv.tid")
        ->andWhere($gardenTaxaQuery->expr()->orX(
          $gardenTaxaQuery->expr()->like("t.sciname", ":search"),
          $gardenTaxaQuery->expr()->like("tv.vernacularname", ":search")
        ))
        ->groupBy("t.tid")
        ->setParameter("search", "$search%");
    }

    $gardenTaxa = $gardenTaxaQuery->getQuery()->execute();

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
      ->innerJoin("Kmcharacters", "c", "WITH", "d.cid = c.cid")
      ->where("d.tid = :tid");
    $attributeQuery = $attributeQuery
      ->andWhere($attributeQuery->expr()->in("d.cid", $CHARACTERISTICS_ALL));

    foreach ($gardenTaxa as $taxa) {
      $tid = $taxa->getTid();
      $clQuery->setParameter("tid", $tid);
      $thumbnailQuery->setParameter("tid", $tid);
      $attributeQuery->setParameter("tid", $tid);

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

      $attribs = $attributeQuery->getQuery()->execute();
      foreach ($attribs as $attrib) {
        $attr_key = $attrib["cid"];
        $attr_val = $attrib["charstatename"];
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

      // TODO: This could definitely be done better
      array_push($results, array_merge(
          $attr_array,
          [
            "tid" => $tid,
            "sciName" => $taxa->getSciname(),
            "vernacular" => [
              "basename" => $taxa->getBasename(),
              "names" => $taxa->getVernacularNames(),
            ],
            "image" => resolve_img_path($thumbnailQuery->getQuery()->execute()[0]["thumbnailurl"]),
            "checklists" => array_map(function ($cl) {
              return $cl["clid"];
            }, $clQuery->getQuery()->execute()),
          ]
        )
      );
    }

    ini_set("memory_limit", $memory_limit);
    set_time_limit(30);
    return $results;
  }

  $searchResults = [];
  if (key_exists("canned", $_GET) && $_GET["canned"] === "true") {
    $searchResults = get_canned_searches();
  } else if (key_exists("attr", $_GET) && is_numeric($_GET['attr'])) {
    $searchResults = get_garden_characteristics(intval($_GET['attr']));
  } else {
    $searchResults = get_garden_taxa($_GET);
  }

  // Begin View
  header("Content-Type: application/json; charset=UTF-8");
  echo json_encode($searchResults, JSON_NUMERIC_CHECK);
?>

