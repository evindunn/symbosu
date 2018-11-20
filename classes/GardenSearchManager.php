<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class GardenSearchManager {

    private $conn;
    private $searchParamsArr = array();
    private $sqlWhereArr = array();
    private $sql = '';
    private $display = '';

    function __construct(){
        $this->conn = MySQLiConnectionFactory::getCon("readonly");
    }

    public function __destruct(){
        if(!($this->conn === null)) $this->conn->close();
    }

    public function getCharacterStateArr($char,$sortseq){
        $returnArr = Array();
        $sql = 'SELECT CharStateName, cid, cs '.
            'FROM kmcs '.
            'WHERE cid = '.$char.' '.
            'ORDER BY '.($sortseq?'SortSequence':'CharStateName').' ';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[$row->CharStateName]["cid"] = $row->cid;
            $returnArr[$row->CharStateName]["cs"] = $row->cs;
        }
        $result->free();

        return $returnArr;
    }

    public function setSQLWhereArr(){
        foreach($this->searchParamsArr as $char => $charArr){
            $tempStr = '';
            if($char == 'sciname'){
                $tempArr = array();
                foreach($this->searchParamsArr[$char] as $cs){
                    $tempArr[] = '(t.SciName LIKE "'.$cs.'%")';
                }
                $tempStr = '('.implode(' OR ',$tempArr).')';
            }
            elseif($char == 'common'){
                $tempArr = array();
                foreach($this->searchParamsArr[$char] as $cs){
                    $tempArr[] = '(v.VernacularName LIKE "'.$cs.'%")';
                }
                $tempStr = '('.implode(' OR ',$tempArr).')';
            }
            elseif($char == 140 || $char == 738){ //is height or width, set range
	            $tempArr = array();
	            foreach($this->searchParamsArr[$char] as $cs){
		            list($min, $max) = explode(",", $cs);
		            $tempArr[] = '(t.TID IN(SELECT TID FROM kmdescr WHERE (CID = '.$char.' AND (CS >= '.$min.' AND CS <= '.$max.'))))';

		            //$tempArr[] = '(t.TID IN(SELECT TID FROM kmdescr WHERE (CID = '.$char.' AND CS = '.$cs.')))';
	            }
	            $tempStr = '('.implode(' OR ',$tempArr).')';
            }
            else{
                $tempStr = '(t.TID IN(SELECT TID FROM kmdescr WHERE (CID = '.$char.' AND CS IN('.implode(',',$this->searchParamsArr[$char]).'))))';
            }
            $this->sqlWhereArr[] = $tempStr;
        }
        $this->sqlWhereArr[] = '(t.TID IN(SELECT TID FROM fmchklsttaxalink WHERE CLID = 54))';
    }

    public function setSQL(){
        $this->sql = '';
        $sqlWhere = 'WHERE ('.implode(' AND ',$this->sqlWhereArr).') ';
        if($this->display == 'grid'){
            $sqlSelect = 'SELECT t.TID, t.SciName ';
            $sqlFrom = 'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.parenttid ';
            //joining with images is SLOW!  Comment out next line, and run additional query in loop
            //$sqlFrom .= 'LEFT JOIN images AS i ON (te.tid = i.tid OR t.TID = i.tid) ';
            if(isset($this->searchParamsArr['common'])) $sqlFrom .= 'LEFT JOIN taxavernaculars AS v ON t.TID = v.TID ';
            //$sqlWhere .= 'AND te.taxauthid = 1 ';
            $sqlSuffix = 'GROUP BY t.TID ORDER BY t.SciName ';
        }
        elseif($this->display == 'list'){
            $sqlSelect = 'SELECT t.TID, t.SciName, v.VernacularName, kd.CID, ks.CharStateName, ks.cs ';
            $sqlFrom = 'FROM taxa AS t LEFT JOIN taxavernaculars AS v ON t.TID = v.TID ';
	        //joining with images is SLOW!  Comment out next line, and run additional query in loop
	        $sqlFrom .= 'LEFT JOIN kmdescr AS kd ON t.TID = kd.TID ';
            $sqlFrom .= 'LEFT JOIN kmcs AS ks ON kd.CID = ks.cid AND kd.CS = ks.cs ';
	        //$sqlFrom .= 'LEFT JOIN images AS i ON (t.TID = i.tid) ';
            //$sqlWhere .= 'AND (kd.CID IN(137,681,682,690,738,684)) ';
            $sqlSuffix = 'GROUP BY t.TID ORDER BY t.SciName ';
        }
        $this->sql = $sqlSelect.$sqlFrom.$sqlWhere.$sqlSuffix;
    }

    public function getDataArr(){
        $returnArr = array();
        //echo $this->sql; exit;
        $result = $this->conn->query($this->sql);
        while($row = $result->fetch_object()){
            $tid = $row->TID;
            if(!isset($returnArr[$tid]['sciname'])) $returnArr[$tid]['sciname'] = $row->SciName;
            if($this->display == 'grid'){
            	//run query on images table to get thumbnail image.
	            $sql="SELECT i.thumbnailurl, i.url FROM images AS i WHERE tid = ".$this->conn->escape_string($tid) . " ORDER BY i.sortsequence LIMIT 1";
	            //show large image instead of thumbnail in grid, as thumb is too small
	            $imgThumbnail = $this->conn->query($sql)->fetch_object()->url;
                //prepend image domain if image does not already contain a domain
                if(array_key_exists("IMAGE_DOMAIN",$GLOBALS)){
                    if(substr($imgThumbnail,0,1)=="/") $imgThumbnail = $GLOBALS["IMAGE_DOMAIN"].$imgThumbnail;
                }
                $returnArr[$tid]['url'] = $imgThumbnail;
            }
            elseif($this->display == 'list'){
                $cid = $row->CID;
	            //run query on images table to get thumbnail image.
	            $sql="SELECT i.thumbnailurl FROM images AS i WHERE tid = ".$this->conn->escape_string($tid) . " ORDER BY i.sortsequence LIMIT 1";
	            $imgThumbnail = $this->conn->query($sql)->fetch_object()->thumbnailurl;
	            if(array_key_exists("IMAGE_DOMAIN",$GLOBALS)){
		            if(substr($imgThumbnail,0,1)=="/") $imgThumbnail = $GLOBALS["IMAGE_DOMAIN"].$imgThumbnail;
	            }
		            $returnArr[$tid]['url'] = $imgThumbnail;
                if(!isset($returnArr[$tid]['common'])) $returnArr[$tid]['common'] = $row->VernacularName;
                if(!isset($returnArr[$tid]['type']) && $cid == 137) {
	                $returnArr[$tid]['type'] = $row->CharStateName;
	                if($row->cs == 3) $returnArr[$tid]['type_class'] = "planttype1";
	                if($row->cs == 2) $returnArr[$tid]['type_class'] = "planttype2";
	                if($row->cs == 6) $returnArr[$tid]['type_class'] = "planttype3";
	                if($row->cs == 1) $returnArr[$tid]['type_class'] = "planttype4";
	                if($row->cs == 4) $returnArr[$tid]['type_class'] = "planttype5";
	                if($row->cs == 5) $returnArr[$tid]['type_class'] = "planttype6";
                }
                if(!isset($returnArr[$tid]['light']) && $cid == 681) {
                	$returnArr[$tid]['light'] = $row->CharStateName;
	                if($row->cs == 1) $returnArr[$tid]['light_class'] = "sunlight1";
	                if($row->cs == 3) $returnArr[$tid]['light_class'] = "sunlight2";
	                if($row->cs == 4) $returnArr[$tid]['light_class'] = "sunlight3";
                }
                if(!isset($returnArr[$tid]['moisture']) && $cid == 682) {
                	$returnArr[$tid]['moisture'] = $row->CharStateName;
	                if($row->cs == 1) $returnArr[$tid]['moisture_class'] = "moisture1";
	                if($row->cs == 2) $returnArr[$tid]['moisture_class'] = "moisture2";
	                if($row->cs == 3) $returnArr[$tid]['moisture_class'] = "moisture3";
	                if($row->cs == 4) $returnArr[$tid]['moisture_class'] = "moisture5";
	                if($row->cs == 5) $returnArr[$tid]['moisture_class'] = "moisture4";
                }
                if(!isset($returnArr[$tid]['ease']) && $cid == 684) $returnArr[$tid]['ease'] = $row->CharStateName;
                if(!isset($returnArr[$tid]['maxheight']) && $cid == 690) $returnArr[$tid]['maxheight'] = $row->CharStateName;
                if(!isset($returnArr[$tid]['maxwidth']) && $cid == 738) $returnArr[$tid]['maxwidth'] = $row->CharStateName;

            }
        }
        $result->free();
        return $returnArr;
    }

    public function setSearchParams($json){
        $paramsArr = json_decode($json,true);
        if(is_array($paramsArr)){
            foreach($paramsArr as $str){
                $parts = explode("--",$str);
                $char = $parts[0];
                $cs = $parts[1];
                if(!$this->searchParamsArr[$char]) $this->searchParamsArr[$char] = array();
                if(!in_array($cs,$this->searchParamsArr[$char])) $this->searchParamsArr[$char][] = $cs;
            }
        }
    }

    public function setDisplay($dis){
        $this->display = $dis;
    }
}
?>