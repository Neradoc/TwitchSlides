<?php
include_once("head.php");

$poll_page = $prefs->get("strawpoll","");
$poll_embed = $prefs->poll_embed();

if(isset($_POST["effacer_poll"])) {
	$prefs->set("strawpoll","");
	$prefs->save();
	exit_redirect();
}

if(isset($_POST["strawpoll_lien"])) {
	$poll = $_POST["strawpoll_lien"];
	$prefs->set("strawpoll",$poll);
	$prefs->save();
	exit_redirect();
}

function disp_strawpoll($thisurl) {
	global $poll_page,$poll_embed;
	?>
	<div id="strawpoll">
	<form action="<?=$thisurl?>" name="strawpoll" method="POST" enctype="multipart/form-data">
		<a href="<?=dirname($thisurl)?>/strawpoll.php" target="_BLANK">Strawpoll</a> <button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/10987342")'>Test 1</button> <button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/3888622")'>Test 2</button> <button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/4796816")'>Test 3</button>
		<br/>
		<input type="text" name="strawpoll_lien" class="strawpoll_lien" value="<?=$poll_page?>"/><br/>
		<iframe class="strawpoll_frame" src="<?=$poll_embed?>">Loading poll...</iframe>
		<div class="btns">
			<button class="effacer" name="effacer_poll" value="">Effacer</button>
		</div>
	</form>
	</div>
	<?
}
