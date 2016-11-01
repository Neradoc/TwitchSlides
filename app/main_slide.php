<?php
include("head.php");

$debug = isset($_REQUEST['debug']);

// screens
$screensNumbers = [];
if(isset($_REQUEST['screen'])) {
	$screensNumbers =
		array_filter(
			array_map(
				function($x) { return intval($x); },
				preg_split('/,/', $_REQUEST['screen'], -1, PREG_SPLIT_NO_EMPTY)
			),
		function($n) use ($Nscreens) {
			return $n>0 && $n<=intval($Nscreens);
		}
	);
} else {
	for($i=1; $i<=$Nscreens; $i++) {
		$screensNumbers[] = $i;
	}
}
// ajax
if(isset($_REQUEST['get'])) {
	$data = array("screens" => array());
	foreach($screensNumbers as $screen) {
		$file = $prefs->screenFile($screen);
		$pos = $prefs->screenPos($screen);
		$on = $prefs->screenOn($screen);
		if($file && file_exists(SCREENS_DIR.$file)) {
			$sizes = getimagesize(SCREENS_DIR.$file);
			$width  = $sizes[0];
			$height = $sizes[1];
			$data['screens'][] = array(
				'num' => $screen,
				'image' => SCREENS_URL.$file,
				'pos' => $pos,
				'size' => array($width,$height),
				'on' => $on?true:false,
			);
		}
	}
	// scores
	$scores = $prefs->sortedScores();
	$scoreboard_on = $prefs->get("scoreboard_on",false);
	$data['scoreboard_on'] = $scoreboard_on;
	$nscore = 0;
	$liste_scores = "";
	foreach($scores as $score) {
		$liste_scores .= "<span>".ucfirst($score['nom'])." : ".$score['score'];
		if($nscore == 0) { $liste_scores .= '<img class="crown" src="cjs/img/crown.png"/>'; }
		#if($nscore == 1) { $liste_scores .= '<img class="crown" src="cjs/img/tiare.png"/>'; }
		$liste_scores .= "</span>";
		$nscore += 1;
	}
	$data['liste_scores'] = $liste_scores;
	// reload
	$reload = $prefs->get("reload_slide",false);
	$data['reload'] = $reload;
	if($reload) {
		$prefs->set("reload_slide",false);
		$prefs->save();
	}
	print(json_encode($data));
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
	#screen .image {
		position:absolute;
		bottom:0px;
		left:0px;
	}
	#scores {
		position:absolute;
		left:0px;
		bottom:8px;
		font-size: 24px;
		white-space: pre;
	}
	#scores span {
		position: relative;
		display: inline-block;
		padding: 16px;
		margin-right: 100px;
		border-radius: 48px;
		color: white;
		background: rgba(0,0,0,1);
	}
	#scores span .crown {
		position: absolute;
		height: 40px;
		left: 20px;
		top: -30px;
	}
	</style>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
		var current_image = {};
		var liste_scores = "";
		var ref_width = 1920;
		var ref_height = 1080;
		var height_0 = 0;
		function update_image() {
			$.ajax({
				url:'slide.php',
				type:'POST',
				data: {
					get:1,
					screen: "<?=join($screensNumbers,',')?>",
				},
				dataType: "json",
				error: function(a,b,c) {
					console.log("ERROR");
					console.log(a,b,c);
				},
				success: function(data,status){
					if(data == false) {
						$(".image").hide();
						return;
					} 
					if(data['reload']) {
						location.reload();
					}
					//
					var width = $(window).width();
					var height= $(window).height();
					$("#screen").css({
						width: Math.floor(width)+"px",
						height: Math.floor(height)+"px",
					});
					//
					if(data['scoreboard_on']) {
						$("#scores").show();
						if(liste_scores != data['liste_scores']) {
							liste_scores = data['liste_scores'];
							$("#scores").html(liste_scores);
						}
						if(height != height_0) { // height != ref_height
							height_0 = height;
							$("#scores span").css({
								fontSize: Math.max(8,Math.floor(24*height/ref_height))+"px",
								padding: Math.max(4,Math.floor(16*height/ref_height))+"px",
								marginRight: Math.max(10,Math.floor(100*height/ref_height))+"px",
							});
							$("#scores span .crown").css({
								height: Math.max(4,Math.floor(40*height/ref_height))+"px",
								left: Math.max(4,Math.floor(20*height/ref_height))+"px",
								top: -1*Math.max(6,Math.floor(30*height/ref_height))+"px",
							});
						}
					} else {
						$("#scores:visible").hide();
					}
					//
					$(".image:visible").hide();
					for(var num in data['screens']) {
						var screen = data['screens'][num];
						var image = $(".image"+screen['num']);
						if(!(num in current_image) || screen['image'] != current_image[num]) {
							current_image[num] = screen['image'];
							image.attr("src",current_image[num]);
						}
						//
						if( screen['image'] == ""
							|| ("on" in screen
								&& screen['on'] == false)
						) {
							continue;
						}
						//
						var left = screen['pos'][0]*width/ref_width;
						var top = screen['pos'][1]*height/ref_height;
						var iw = screen['size'][0]*width/ref_width;
						var ih = screen['size'][1]*height/ref_height;
						var zoom = screen['pos'][2];
						if(!(zoom>0)) { zoom = 1; }
						image.css({
							left: Math.floor(left)+"px",
							top: Math.floor(top)+"px",
							width: Math.floor(iw*zoom)+"px",
							height: Math.floor(ih*zoom)+"px",
						});
						//
						image.not(":visible").show();
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
	<?php if($debug): ?>
	<img src="<?=$url_miniature_stream?>" style="display:absolute; left:0px; top:0px; width:100%; height:100%;"/>
	<?php endif; ?>
	<?php for($i=0; $i<max($Nscreens,8); $i++) {
		print('<img class="image image'.($i+1).'" src="cjs/img/vide.png" />'."\n");
	}
	?>
	<div id="scores">Les scores ne sont pas encore chargés</div>
</div>
</body>
</html>
