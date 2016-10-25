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
	/*top: 105px;*/
	top: 505px;
	left: 0px;
}
.pos_btn.centerright {
	/*top: 105px;*/
	top: 505px;
	right: 0px;
}
.pos_btn.centertop {
	top: 0px;
	/*left: 190px;*/
	left: 925px;
}
.pos_btn.centerbottom {
	bottom: 0px;
	/*left: 190px;*/
	left: 925px;
}
.pos_btn.centercenter {
	/*top: 105px;*/
	top: 505px;
	/*left: 190px;*/
	left: 925px;
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
.pos_btn {
	display: none;
}
.pimage:hover .pos_btn {
	display:block;
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

/* boutons / inputs */
.upload_fichier {
	font-size: 100%;
}
.upload_url {
	width: 280px;
}
.strawpoll_lien {
	width: 300px;
}
.strawpoll_frame {
	position: absolute;
	width:<?=$screen_width*2?>px;
	height:<?=$screen_height*1.8?>px;
	border:0;
	transform: scale(0.5,0.5);
	transform-origin: top left;
}
#upload .upload_btn {
	font-size: 100%;
	margin-left:310px;
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
	font-size: 120%;
	border-radius: 10px;
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

form {
	display: inline;
	padding: 0px;
	margin: 0px;
	background: transparent;
}

#scoreboard {
	font-size: 90%;
}
#scoreboard .new_scorecard {
	width: 230px;
}
#scoreboard .scoreboard_switch {
	padding:0px;
	margin:0px;
	border-radius: 10px;
	width: 5em;
	padding: 2px 4px;
	margin: 2px;
}
#scoreboard .scoreboard_switch_on {
	color:white;
	background: #080;
	text-align: left;
}
#scoreboard .scoreboard_switch_off {
	color:white;
	background: #800;
	text-align: right;
}

#scoreboard .scoreboard_list {
	overflow-x: auto;
	overflow-y: scroll;
	height: 220px;
}
#scoreboard .scorecard_line {
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
/*#scoreboard .score,
#scoreboard .ajout_score */
#scoreboard .score {
	text-align: right;
	font-size: 100%;
	width: 3.5em;
	border:none;
}
#scoreboard .ajout_score {
	text-align: center;
	width: 1.5em;
	padding: 2px 0px;
	border:none;
}
#scoreboard .nom {}

#scoreboard .effacer_score {
	float:right;
	margin: 2px 8px 0px;
}
#scoreboard .pair { background: #DFE; }
#scoreboard .impair { background: white; }
