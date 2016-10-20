<?php
// créer les dossiers
@mkdir("images");
@mkdir("sources");

$Nscreens = 1;

$image_format = "images/screen_%s.%s";
$image_glob = "images/screen_*.*";
$image_exts = ["png","jpg"];

$sources_glob = "sources/image_*";

$thisurl = 'http';
if(isset($_SERVER['HTTPS'])) $thisurl .= 's';
$thisurl .= '://';
$thisurl .= $_SERVER['HTTP_HOST'];
$thisurl .= $_SERVER['REQUEST_URI'];
