<?php
header("Content-type: text/css");
$screen_width = 480;
$screen_height = 270;
$scale = 0.25;
$screen_width = 384;
$screen_height = 216;
$scale = 0.20;
?>

body { padding: 0px; margin:0px; }
p { padding: 2px; margin:0px; }
#black_block {
	background-image: url("damier-dark.png");
	position: absolute;
	left: 4px;
	right: 4px;
	top: 4px;
	bottom: 4px;
	opacity: 0.8;
}

.screen,
.source,
#strawpoll,
#scoreboard {
	position: relative;
	float:left;
	padding: 8px;
	margin: 8px;
	border-width: 2px;
	border-style: solid;
	border-radius: 8px;
	width: <?=$screen_width?>px;
}
.screen {
	border-color:#080;
	height: <?=$screen_height + 65?>px;
}
.source {
	border-color:#008;
	height: <?=$screen_height + 37?>px;
}
#strawpoll {
	border-color:#080;
	height: <?=$screen_height + 65?>px;
}
#scoreboard {
	border-color: #008080;
	height: <?=$screen_height + 65?>px;
}
#upload {
	position: relative;
	padding: 8px;
	margin: 8px;
	clear:both;
	border-width: 2px 0px 2px;
	border-style: solid;
	border-color:#FF0;
}

h3 {
	margin: 5px auto 5px;
	padding: 0px;
	text-align: center;
}

.pimage {
	position: absolute;
	overflow:hidden;
	/* 16/9 */
	width: 1920px;
	height: 1080px;
	transform: scale(<?=$scale?>);
	transform-origin: top left;
	padding: 0px;
	margin: 0px;
	background-image: url("damier.png");
}
.pimage .back_screen {
	position:absolute;
	width: 1920px;
	height: 1080px;
	top: 0px;
	left: 0px;
	opacity: 0.5;
}

/* boutons de positions */
.pos_btn {
	position: absolute;
	padding: 0px;
	font-size: 300%;
	width: 70px;
	height: 70px;
	text-align: center;
	background: white;
	border: none;
	border-radius: 0px;
	display: none;
}
.pimage:hover .pos_btn {
	display:block;
}
.pos_btn.topleft {
	top: 0px;
	left: 0px;
}
.pos_btn.topright {
	top: 0px;
	right: 0px;
}
.pos_btn.bottomleft {
	bottom: 0px;
	left: 0px;
}
.pos_btn.bottomright {
	bottom: 0px;
	right: 0px;
}
.pos_btn.centerleft {
	top: 505px;
	left: 0px;
}
.pos_btn.centerright {
	top: 505px;
	right: 0px;
}
.pos_btn.centertop {
	top: 0px;
	left: 925px;
}
.pos_btn.centerbottom {
	bottom: 0px;
	left: 925px;
}
.pos_btn.centercenter {
	top: 505px;
	left: 925px;
}
.pos_btn.moveleft {
	top: 420px;
	left: 0px;
}
.pos_btn.moveright {
	top: 420px;
	right: 0px;
}
.pos_btn.movetop {
	top: 0px;
	left: 840px;
}
.pos_btn.movebottom {
	bottom: 0px;
	left: 840px;
}
.pos_btn.zoomout {
	bottom: 0px;
	left: 1020px;
}
.pos_btn.zoomzero {
	bottom: 0px;
	left: 1100px;
}
.pos_btn.zoomin {
	bottom: 0px;
	left: 1180px;
}
@media only screen and (max-device-width: 480px) {
	.pos_btn {
		width: 140px;
		height: 140px;
	}
	.pos_btn.zoomout {
		left: 1080px;
	}
	.pos_btn.zoomzero {
		left: 1240px;
	}
	.pos_btn.zoomin {
		left: 1400px;
	}
	.pos_btn.moveleft {
		top: 350px;
	}
	.pos_btn.moveright {
		top: 350px;
	}
	.pos_btn.movetop {
		left: 770px;
	}
	.pos_btn.movebottom {
		left: 770px;
	}
}

/* position changée mais pas validée */
.screen.modified {
	background-color: #FFFFB0;
}
.screen.modified .changer {
	color: white;
	background: #444488;
}

/* images */
.screen .image,
.source .image {
	/*max-width: 400px;*/
	/*max-height: 225px;*/
	/* max-width: 1920px; */
	/* max-height: 1080px; */
}

#sources .pagination_sources {
	padding: 0px 8px 0px;
	clear:both;
	border-style: solid;
	border-color: #88F;
	border-width: 2px 0px 2px;
	text-align: center;
}
#sources .bouton_pagination {
	display: inline-block;
	padding: 4px 0px;
	border-style: solid;
	border-color: #88F;
	border-width: 0px 2px;
	border-radius: 16px;
	text-decoration: none;
	color: #44F;
	background: white;
	min-width: 2em;
	min-height: 20px;
	text-align: center;
}
#sources .bouton_pagination.useless {
	color: #BBB;
	border-color: #BBB;
}
#sources .bouton_pagination.current {
	color: white;
	background: #BBB;
	border-color: #BBB;
}
#sources a.bouton_pagination:hover {
	color: white;
	background: #88F;
}
#sources .pagination_star {
	width: 16px;
	height: 16px;
}

.source .sources_star {
	position: absolute;
	bottom: 8px;
	right: 8px;
	padding: 0px;
	margin: 0px;
	border: none;
	background: transparent;
}

/* boutons / inputs */
#upload .upload_fichier {
	font-size: 100%;
}
#upload .upload_url {
	width: <?=$screen_width - 120?>px;
}
#upload .upload_btn {
	font-size: 100%;
	margin-left:<?=$screen_width - 85?>px;
}
.strawpoll_lien {
	width: <?=$screen_width-12?>px;
}
.strawpoll_frame {
	position: absolute;
	width:<?=floor($screen_width * 1.99)?>px;
	height:<?=floor($screen_height * 1.8)?>px;
	border:0;
	transform: scale(0.5,0.5);
	transform-origin: top left;
}
.source .assign option:first-child {
	color: #888;
}
.btns {
	position:absolute;
	bottom:8px;
}
.btns button,
.btns select {
	font-size: 100%;
	border-radius: 10px;
	padding: 2px 8px;
	background: white;
	border-color: #88F;
	cursor:pointer;
}
.btns button:hover {
	background:#000!important;
	color:white!important;
}
.btns .twitter {
	border-color: green;
}
.btns .effacer {
	border-color: red;
}
.btns button.effacer:hover {
	background:#800!important;
	color:white!important;
}
.btns button.disabled:hover,
.btns button.disabled {
	background: white !important;
	border-color: #BBB !important;
	color: #BBB !important;
}

.btn_switch {
	position: absolute;
	top: 8px;
	right: 8px;
	padding:0px;
	margin:0px;
	border-radius: 10px;
	width: 5em;
	padding: 2px 4px;
}
.btn_switch_on {
	color:white;
	background: #080;
	text-align: left;
}
.btn_switch_off {
	color:white;
	background: #800;
	text-align: right;
}

form {
	display: inline;
	padding: 0px;
	margin: 0px;
	background: transparent;
}

#scoreboard {
	font-size: 90%;
}
#scoreboard .scoreboard_new {
	position: absolute;
	bottom: 8px;
	width: <?=$screen_width?>px;
}
#scoreboard .scoreboard_new_nom {
	width: 225px;
}
#scoreboard .scoreboard_new_score {
	width: 60px;
	text-align: right;
}
#scoreboard .scoreboard_new_btn {
	position: absolute;
	right: 0px;
}
#scoreboard .scoreboard_list {
	overflow-x: auto;
	overflow-y: auto;
	height: 220px;
}
#scoreboard .scoreboard_line {
	clear: both;
	padding: 2px;
}
#scoreboard button.rond {
	width:21px;
	height:21px;
	padding: 0px;
	vertical-align:bottom;
	background:transparent;
	border-radius: 10px;
	border: 2px solid transparent;
}
#scoreboard button:hover {
	border: 2px solid black;
}
#scoreboard .down_score:hover { border-color: #BB9105; }
#scoreboard .up_score:hover { border-color: #0C5B0B; }
#scoreboard .valider_score:hover { border-color: #117E9C; }
#scoreboard .effacer_score:hover { border-color: #8C0002; }

#scoreboard .nom {
	padding: 2px 4px 2px;
}
#scoreboard .score {
	text-align: right;
	font-size: 100%;
	width: 3.5em;
	border:none;
}
#scoreboard .modif_score {
	text-align: center;
	width: 1.5em;
	padding: 2px 0px;
	border:none;
}
#scoreboard .nom {}

#scoreboard .effacer_score {
	float:right;
}
#scoreboard .pair { background: #DFE; }
#scoreboard .impair { background: white; }
