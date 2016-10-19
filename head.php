<?php
// créer les dossiers
mkdir("images");
mkdir("sources");

$Nscreens = 2;

$image_format = "images/screen_%02d.%s";
$image_glob = "images/screen_*.*";
$image_exts = ["png","jpg"];

$sources_glob = "sources/image_*";

