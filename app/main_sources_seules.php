<?php
include("head.php");
include_once("module_screens.php");

?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=400">
	<title>Sources: <?= $htmlTitleGestion ?></title>
	<link rel='stylesheet' href='cjs/gestion.css.php' type='text/css' />
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" src="cjs/jquery.elastic.js"></script>
	<script type="text/javascript" src="cjs/module_screens.js"></script>
</head>
<body>
<div id="menu"><a href="gestion">Gestion</a><a href="config">Config</a><a class="ici" href="sources">Sources</a></div>
<div id="contenu">

<?php disp_sources($thisurl); ?>

</div>
</body>
</html>
