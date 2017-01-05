<?php
include_once("head.php");

$poll_page = $prefs->get("strawpoll","");
$poll_embed = $prefs->poll_embed();
$poll_on = $prefs->get("strawpoll_on",true);

if(isset($_POST["strawpoll_effacer"])) {
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

if(isset($_POST['strawpoll_switch'])) {
	$prefs->set("strawpoll_on",$_POST['strawpoll_switch']?true:false);
	$prefs->save();
	exit_redirect();
}

function disp_strawpoll($thisurl) {
	global $poll_page,$poll_embed,$poll_on;
	?>
	<div id="strawpoll" class="module_box">
		<h3><a href="<?=dirname($thisurl)?>/strawpoll" target="_BLANK">Strawpoll</a></h3>
		<form action="<?=$thisurl?>" name="strawpoll_onoff" method="POST">
		<?php
		if($poll_on) {
			?><button class="btn_switch btn_switch_on" name="strawpoll_switch" value="0" title="Activé, cliquer pour désactiver l'affichage des scores">ON</button><?
		} else {
			?><button class="btn_switch btn_switch_off" name="strawpoll_switch" value="1" title="Désactivé, cliquer pour activer l'affichage des scores">OFF</button><?
		}
		?>
		</form>
		<form action="<?=$thisurl?>" name="strawpoll" method="POST" enctype="multipart/form-data">
		<!-- <div>
		<button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/10987342")'>Test 1</button> <button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/3888622")'>Test 2</button> <button onclick='$(".strawpoll_lien").val("http://www.strawpoll.me/4796816")'>Test 3</button>
		</div> -->
		<input type="text" name="strawpoll_lien" class="strawpoll_lien" value="<?=$poll_page?>" placeholder="Lien (exemple: http://www.strawpoll.me/10987342 )"/>
		<input type="submit" name="strapoll_ok" value="ok" style="display:none;"/>
		<br/>
		<iframe class="strawpoll_frame" src="<?=$poll_embed?>">Loading poll...</iframe>
		<div class="btns">
			<button class="effacer" name="strawpoll_effacer" value="" title="Effacer le sondage">Effacer</button>
		</div>
		</form>
	</div>
	<?
}
