<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/taxa/index.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/TaxonProfileManager.php');
Header("Content-Type: text/html; charset=".$CHARSET);

$taxonValue = array_key_exists("taxon",$_REQUEST)?$_REQUEST["taxon"]:""; 
$taxAuthId = array_key_exists("taxauthid",$_REQUEST)?$_REQUEST["taxauthid"]:1; 
$clValue = array_key_exists("cl",$_REQUEST)?$_REQUEST["cl"]:0;
$projValue = array_key_exists("proj",$_REQUEST)?$_REQUEST["proj"]:0;
$lang = array_key_exists("lang",$_REQUEST)?$_REQUEST["lang"]:$DEFAULT_LANG;
$descrDisplayLevel = array_key_exists("displaylevel",$_REQUEST)?$_REQUEST["displaylevel"]:"";

//if(!$projValue && !$clValue) $projValue = $defaultProjId;

$taxonManager = new TaxonProfileManager();
if($taxAuthId || $taxAuthId === "0") $taxonManager->setTaxAuthId($taxAuthId);
if($clValue) $taxonManager->setClName($clValue);
if($projValue) $taxonManager->setProj($projValue);
if($lang) $taxonManager->setLanguage($lang);
if($taxonValue) {
	$taxonManager->setTaxon($taxonValue);
	$taxonManager->setAttributes();
}
$ambiguous = $taxonManager->getAmbSyn();
$acceptedName = $taxonManager->getAcceptance();
$synonymArr = $taxonManager->getSynonymArr();
$spDisplay = $taxonManager->getDisplayName();
$taxonRank = $taxonManager->getRankId();
$links = $taxonManager->getTaxaLinks();
$vernaculars =@implode(", ",$taxonManager->getVernaculars());
$vernStr = $taxonManager->getVernacularStr();
$synStr = $taxonManager->getSynonymStr();
if($links){
	foreach($links as $linkKey => $linkUrl){
		if($linkUrl['title'] == 'REDIRECT'){
			$locUrl = str_replace('--SCINAME--',rawurlencode($taxonManager->getSciName()),$linkUrl['url']);
			header('Location: '.$locUrl);
			exit;
		}
	}
}

$styleClass = '';
if($taxonRank > 180) $styleClass = 'species';
elseif($taxonRank == 180) $styleClass = 'genus';
else $styleClass = 'higher';

$displayLocality = 0;
$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN || array_key_exists("TaxonProfile",$USER_RIGHTS)){
		$isEditor = true;
	}
	if($IS_ADMIN || array_key_exists("CollAdmin",$USER_RIGHTS) || array_key_exists("RareSppAdmin",$USER_RIGHTS) || array_key_exists("RareSppReadAll",$userRights)){
		$displayLocality = 1;
	}
}
if($taxonManager->getSecurityStatus() == 0){
	$displayLocality = 1;
}
$taxonManager->setDisplayLocality($displayLocality);
$descr = Array();

include('includes/config/taxaProfileElementsGarden.php');

//is page a garden page?
$isGardenProfile = $OSUManager ? $OSUManager->isGardenProfile() : false;
if(!$isGardenProfile) {
    header("Location:index.php?taxon=" . $taxonManager->getTid());
    exit();
}
$garden_image = $OSUManager->mainGardenImage();
$attribs = $OSUManager->getAllAttribs();
$collections = $OSUManager->gardenCollections();

?>

<html>
<head>
	<title><?php echo $DEFAULT_TITLE." - ".$spDisplay; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<link href="../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../css/speciesprofilebase.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../css/speciesprofile.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="../css/jquery-ui.css" type="text/css" rel="Stylesheet" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery-ui.js"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
	<script type="text/javascript">
		var currentLevel = <?php echo ($descrDisplayLevel?$descrDisplayLevel:"1"); ?>;
		var levelArr = new Array(<?php echo ($descr?"'".implode("','",array_keys($descr))."'":""); ?>);
		var tid = <?php echo $taxonManager->getTid(); ?>
	</script>
	<script src="../js/symb/taxa.index.js?ver=20170310" type="text/javascript"></script>
	<script src="../js/symb/taxa.editor.js?ver=20140619" type="text/javascript"></script>
    <?php
    if(isset($CSSARR)){
        foreach($CSSARR as $cssVal){
            echo '<link href="includes/config/'.$cssVal.'?ver=150106" type="text/css" rel="stylesheet" id="editorCssLink" />';
        }
    }
    if(isset($JSARR)){
        foreach($JSARR as $jsVal){
            echo '<script src="includes/config/'.$jsVal.'?ver=150106" type="text/javascript"></script>';
        }
    }
    ?>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/header.php');


?>
<script type="text/javascript" src="../js/tooltipster.bundle.min.js"></script>
<link rel="stylesheet" type="text/css" href="../css/tooltipster.bundle.min.css" />
<link href="../css/tooltipster-sideTip-light.min.css" type="text/css" rel="stylesheet" />
<div class="garden-content-wrapper">
    <div class="garden-page-header">
        <a href="printable-garden-facts.php?taxon=<?php echo $taxonManager->getTid(); ?>" class="btn light-purple-btn pull-right" target="_blank">Printable Fact Sheet</a>
    </div>
    <div class="garden-left">
        <h1 class="section"><strong><em><?php echo $spDisplay; ?></em></strong><br>
            <?php echo $vernStr; ?></h1>
        <p class="section"><?php echo $attribs['type'] ?></p>
        <p class="section">Grows up to <?php echo $attribs['max_height'] ?>'h x <?php echo $attribs['max_width'] ?>'w</p>
        <p class="section garden-icons">Cultivation tolerances<br>
            <?php echo $attribs['moisture'] ?>
            <?php echo $attribs['sunlight'] ?>
        </p>
        <?php if(isset($attribs['wildlife_string']) && $attribs['wildlife_string'] != ''){ ?>
            <p class="section garden-icons">Wildlife support<br><?php echo $attribs['wildlife'] ?></p>
        <?php } ?>
        <p class="section">Ease of growth: <?php echo $attribs['ease_of_growth'] ?></p>
        <p class="section garden-icons">Commercial availability: <?php echo $attribs['commercial_availability'] ?></p>
        <div class="garden-desc">
            <?php echo $garden_content; ?>
        </div>
    </div>
    <div class="garden-image">
        <img src="<?php echo $garden_image['url'] ?>" title="<?php echo $spDisplay; ?> image" alt="<?php echo $spDisplay; ?> image" />
        <div class="photographer"><?php echo $garden_image['photographer'] ?>&nbsp;&nbsp;<?php //echo $garden_image['image_type'] ?></div>
    </div>
    <?php echo $OSUobsImgDiv; ?>
    <div class="garden-facts">
        <h3>Plant Facts</h3>
        <p>
            <?php if( $attribs['type'] != '' ){ ?>
                <strong>Plant type: </strong><?php echo strtolower($attribs['type']) ?><br>
            <?php } ?>
            <strong>Size at maturity: </strong>
            <?php $height_string = ''; $width_string = ''; $joiner = ''; ?>
            <?php if(!isset($attribs['min_height']) || $attribs['min_height'] == 0 || $attribs['min_height'] == $attribs['max_height']) {
                if(isset($attribs['max_height']) && $attribs['max_height'] !=''){
                    $height_string .= $attribs['max_height'] . '&apos; high';
                }
            }else{
                $height_string .=  $attribs['min_height'];
                if(isset($attribs['max_height']) && $attribs['max_height'] !=''){
                    $height_string .=  '-' . $attribs['max_height'];
                }
                $height_string .=  '&apos; high';
            } ?>
            <?php if(!isset($attribs['min_width']) || $attribs['min_width'] == 0 || $attribs['min_width'] == $attribs['max_width']) {
                if(isset($attribs['max_width']) && $attribs['max_width'] !=''){
                    $width_string .= $attribs['max_width'] . '&apos; wide';
                }
            }else{
                $width_string .=  $attribs['min_width'];
                if(isset($attribs['max_width']) && $attribs['max_width'] !=''){
                    $width_string .=  '-' . $attribs['max_width'];
                }
                $width_string .=  '&apos; wide';
            }
            if($width_string !='' && $height_string !=''){ $joiner = ' x ';}
            $height_width_string = $height_string . $joiner . $width_string;
            ?>

            <?php echo $height_width_string; ?><br>
            <?php if( $attribs['flower_color'] != '' ){ ?>
                <strong>Flower color: </strong><?php echo $attribs['flower_color'] ?><br>
            <?php } ?>
            <?php if( $attribs['bloom_months'] != '' ){ ?>
                <strong>Bloom time: </strong><?php echo $attribs['bloom_months'] ?><br>
            <?php } ?>
            <?php if( $attribs['sunlight_string'] != '' ){ ?>
                <strong>Light: </strong><?php echo $attribs['sunlight_string'] ?><br>
            <?php } ?>
            <?php if( $attribs['moisture_string'] != '' || $attribs['watering_string'] != '' ){ ?>
                <strong>Moisture: </strong><?php echo $attribs['moisture_string'] ?>
                <?php if( $attribs['watering_string'] != '' ){ ?>
                    <?php echo $attribs['moisture_string'] != '' ? ';' : '' ?> <?php echo $attribs['watering_string'] ?> summer water
                <?php } ?>
                <br>
            <?php } ?>
            <?php if(isset($attribs['wildlife_string']) && $attribs['wildlife_string'] != ''){ ?>
                <strong>Wildlife support: </strong><?php echo $attribs['wildlife_string'] ?><br>
            <?php } ?>
        </p>
        <h3>Growth and Maintenance</h3>
        <p>
            <?php if( $attribs['ease_of_growth'] != '' ){ ?>
                <strong>Ease of cultivation: </strong><?php echo $attribs['ease_of_growth'] ?><br>
            <?php } ?>
            <?php if( $attribs['spreads_vigorously'] != '' ){ ?>
                <strong>Spreads vigorously: </strong><?php echo $attribs['spreads_vigorously'] ?><br>
            <?php } ?>
            <?php if( $attribs['landscape_uses'] != '' ){ ?>
                <strong>Landscape uses: </strong><?php echo $attribs['landscape_uses'] ?><br>
            <?php } ?>
            <?php if( $attribs['other_cultivation_factors'] != '' ){ ?>
                <strong>Other cultivation factors: </strong><?php echo $attribs['other_cultivation_factors'] ?><br>
            <?php } ?>
            <?php if( $attribs['plant_behavior'] != '' ){ ?>
                <strong>Plant behavior: </strong><?php echo $attribs['plant_behavior'] ?><br>
            <?php } ?>
            <?php if( $attribs['propogation'] != '' ){ ?>
                <strong>Propagation: </strong><?php echo $attribs['propogation'] ?><br>
            <?php } ?>
        </p>
        <div class="garden-commercial">
            <h3>Commercial Availability</h3>
            <p>We are looking forward to presenting the businesses that sell this plant.  Contact us if you are interested in helping to develop this resource!</p>
        </div>
        <p>
            <a href="index.php?taxon=<?php echo $taxonManager->getTid(); ?>" class="btn light-purple-btn">Core profile page</a></p>
    </div>
    <div class="garden-collections">
        <h4>Plant collections containing <?php echo $vernaculars; ?></h4>
        <?php if(is_array($collections)) { ?>
        <div class="home-boxes">
            <?php foreach ($collections as $collection) { ?>
                <a href="<?php echo $clientRoot; ?>/checklists/checklist.php?cl=<?php echo $collection['clid'] ?>&pid=3" class="home-box image-box">
                    <img src="<?php echo $collection['iconurl']; ?>" alt="<?php echo $collection['name'] ?>">
                    <h3><?php echo $collection['name'] ?></h3>
                    <div class="box-overlay">
                        <div class="centered"><?php echo $collection['title'] ?></div>
                    </div>
                </a>
            <?php }?>
        </div>
        <?php } //end if collections ?>
    </div>
</div>
<div class="metro-wrapper">
    <div class="inner-content">
        <hr />
        <div class="metro-col1"> </div>
        <div class="metro-col2">
            <div class="col-content">
                <p>Metro is a primary contributor to OregonFlora's Gardening with Native Plants and supports efforts to protect clean
                    air, water and habitat in greater Portland.</p>
            </div>
        </div>
    </div>
</div>

<?php
include($SERVER_ROOT.'/footer.php');
?>
<script>
    $('.tooltip').tooltipster({
        theme: 'tooltipster-light'
    });
</script>
</body>
</html>