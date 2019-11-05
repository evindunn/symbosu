<?php
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

$result = [];


function get_lg_image($tid) {
  $sql = get_select_statement("images", [ ImageTbl::$URL ]);
  $sql .= 'WHERE ' . ImageTbl::$TID . " = $tid ";
  $sql .= 'ORDER BY ' . ImageTbl::$SORT_SEQUENCE . ' LIMIT 1;';
  $res = run_query($sql);

  if (count($res) > 0 && key_exists(ImageTbl::$URL, $res[0])) {
    $result = $res[0][ImageTbl::$URL];
    return resolve_img_path($result);
  }

  return "";
}

function getTaxon($tid) {
  $sql = get_select_statement(
    "taxa t",
    [
      't.' . TaxaTbl::$SCINAME,
      'v.' . TaxaVernacularTbl::$VERNACULAR_NAME
    ]
  );
  $sql .= 'LEFT JOIN taxavernaculars v ON v.' . TaxaVernacularTbl::$TID . ' = t.' . TaxaTbl::$TID . ' ';
  $sql .= 'WHERE t.' . TaxaTbl::$TID . " = $tid";

  $result = run_query($sql)[0];
  $result["image"] = get_lg_image($tid);

  return $result;
}

if (array_key_exists("taxon", $_GET) && is_numeric($_GET["taxon"])) {
  $result = getTaxon($_GET["taxon"]);
}

// Begin View
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($result, JSON_NUMERIC_CHECK);
?>