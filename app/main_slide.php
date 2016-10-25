<?php
include("head.php");
include("prefs.php");

$screen = 1;
$align = "";
if(isset($_REQUEST['screen'])) {
	$screen = intval($_REQUEST['screen']);
}
if(isset($_REQUEST['align'])) {
	switch($_REQUEST['align']) {
	case "left":
		$align = "left";
		break;
	case "right":
		$align = "right";
		break;
	case "center":
		$align = "center";
		break;
	}
}
if(isset($_REQUEST['get'])) {
	$prefs = new PrefsManager();
	$screen = false;
	if(isset($_REQUEST['screen'])) {
		$screen = @intval($_REQUEST['screen']);
	}
	if($screen !== false) {
		$file = $prefs->screenFile($screen);
		$pos = $prefs->screenPos($screen);
		$scores = $prefs->sortedScores();
		$scoreboard_on = $prefs->get("scoreboard_on",false);
		$reload = $prefs->get("reload_slide",false);
		if($reload) {
			$prefs->set("reload_slide",false);
			$prefs->save();
		}
		$liste_scores = "";
		foreach($scores as $score) {
			$liste_scores .= "<span>".ucfirst($score['nom'])
				." : ".$score['score']."</span>";
		}
		if($file && file_exists(SCREENS_DIR.$file)) {
			$sizes = getimagesize(SCREENS_DIR.$file);
			$width  = $sizes[0];
			$height = $sizes[1];
			print(json_encode(array(
				'image' => SCREENS_URL.$file,
				'pos' => $pos,
				'size' => array($width,$height),
				'liste_scores' => $liste_scores,
				'reload' => $reload,
				'scoreboard_on' => $scoreboard_on,
			)));
			exit();
		}
	}
	print(json_encode(false));
	exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title></title>
	<style type="text/css" title="text/css">
	body {
		padding:0px;
		margin:0px;
		overflow: hidden;
	}
	#screen {
		position:relative;
	}
	#screen img {
		position:absolute;
		bottom:0px;
		left:0px;
	}
	#scores {
		position:absolute;
		left:0px;
		bottom:16px;
		font-size: 24px;
		white-space: pre;
	}
	#scores span {
		position: relative;
		display: inline-block;
		padding: 8px 16px 12px;
		margin-right: 100px;
		border-radius: 24px;
		color: white;
		background: rgba(0,0,0,0.8);
	}
	#scores span:first-child img {
		position: absolute;
		height: 40px;
		left: 20px;
		top: -30px;
	}
	</style>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
		var current_image = "";
		function update_image() {
			$.ajax({
				url:'slide.php',
				type:'POST',
				data: {
					get:1,
					screen: "<?=$screen?>",
				},
				dataType: "json",
				success: function(data,status){
					if(data == false) {
						$("#image").hide();
						return;
					} 
					if(data['reload']) {
						location.reload();
					}
					//
					if(data['image'] != current_image) {
						current_image = data['image'];
						$("#image").attr("src",current_image);
					}
					//
					var width = $(window).width();
					var height= $(window).height();
					$("#screen").css({
						width: Math.floor(width)+"px",
						height: Math.floor(height)+"px",
					});
					//
					var left = Math.floor(data['pos'][0]/1920*width);
					var top = Math.floor(data['pos'][1]/1080*height);
					$("#image").css({
						left: left+"px",
						top: top+"px",
					});
					//
					var iw = data['size'][0];
					var ih = data['size'][1];
					var zoom = data['pos'][2];
					if(!(zoom>0)) { zoom = 1; }
					$("#image").css({
						width: Math.floor(iw*zoom)+"px",
						height: Math.floor(ih*zoom)+"px",
						maxWidth: "auto",
						maxHeight: "auto",
					});
					//
					$("#image").show();
					//
					if(data['scoreboard_on']) {
						$("#scores").show();
						var liste_scores = data['liste_scores'];
						if(liste_scores != $("#scores").html()) {
							$("#scores").html(liste_scores);
							$("#scores span:first-child").prepend('<img src="cjs/crown.png"/>');
						}
					} else {
						$("#scores").hide();
					}
				},
			});
		}
		var scorepos = -100000;
		var step = 5;
		var speed = 50;
		function movescores() {
			if($("#scores").is(":visible")) {
				scorepos = scorepos - step;
				var width = $("#scores").width();
				if(scorepos < -1*width) {
					scorepos = $(window).width();
				}
				$("#scores").css({
					left: scorepos+"px",
				});
			}
		}
		$(function() {
			setInterval(update_image,1000);
			setInterval(movescores,speed);
		});
	</script>
</head>
<body>
<div id="screen">
	<img id="image" src="cjs/vide.png" />
	<div id="scores">Les scores ne sont pas encore chargés</div>
</div>
</body>
</html>
