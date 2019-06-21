<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php
// TOOD: Remove, dev only
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!-- Compat stuff -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
<link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' type='text/css' media='all' />
<script src="<?php echo $clientRoot; ?>/js/jquery-ui-1.12.1/external/jquery/jquery.js"></script>
<script src="<?php echo $clientRoot; ?>/js/jquery-ui-1.12.1/jquery-ui.js"></script>
<script src="<?php echo $clientRoot; ?>/js/superfish.min.js"></script>
<script src="<?php echo $clientRoot; ?>/js/menu.js"></script>
<link href="<?php echo $clientRoot; ?>/css/component.css" type="text/css" rel="stylesheet" />

<!-- Bootstrap Deps -->
<script
  src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
  integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
  crossorigin="anonymous">
</script>
<script
  src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
  integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
  crossorigin="anonymous">
</script>

<!-- Bootstrap -->
<link
  rel="stylesheet"
  href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
  integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
  crossorigin="anonymous">
<script
  src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
  integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
  crossorigin="anonymous">
</script>

<style>
  #navbar-container {
    position: relative;
    padding: 0;
    margin: 0;
  }

  #navbar-background {
    position: absolute;
    width: 100%;
    height: 75%;
    z-index: -1;

    background-image: url(<?php echo $clientRoot; ?>/images/layout/whois-bg.jpg);
    background-size: 100% auto;
  }

  .dropdown:hover .dropdown-menu {
    display: block;
  }

  .nav-link.dropdown-toggle {
    text-transform: uppercase;
    color: white !important;
  }

  /* Get rid of caret */
  .nav-link.dropdown-toggle::after {
    display: none;
  }
</style>

<!-- Navbar -->
<div id="navbar-container">
  <div id="navbar-background" class="shadow"></div>
  <nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="<?php echo $clientRoot; ?>/index.php">
      <img src="<?php echo $clientRoot; ?>/images/layout/new-logo.png" alt="Oregon Flora">
    </a>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav mr-auto ml-2">
        <!-- Explore our Site -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="explore">
            Explore Our Site
          </a>
          <div class="dropdown-menu" aria-labelledby="explore">
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/spatial/index.php">Mapping</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/checklists/dynamicmap.php?interface=key">Interactive Key</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/projects/index.php">Plant Inventories</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/collections/harvestparams.php?db[]=5,8,10,7,238,239,240,241">OSU Herbarium</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/garden/index.php">Gardening with Natives</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/imagelib/search.php">Image Search</a>
            <a class="dropdown-item" href="<?php echo $clientRoot; ?>/taxa/admin/taxonomydisplay.php">Taxonomic Tree</a>
          </div>
        </li>


      </ul>
    </div>
  </nav>
</div>

<div id="site-content">
