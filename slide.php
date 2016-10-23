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
		if($file && file_exists($file)) {
			$sizes = getimagesize($file);
			$width  = $sizes[0];
			$height = $sizes[1];
			print(json_encode(array(
				'image' => $file,
				'pos' => $pos,
				'size' => array($width,$height),
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
		overflow:hidden;
	}
	#screen {
		position:relative;
	}
	#screen img {
		position:absolute;
		bottom:0px;
		left:0px;
	}
	</style>
	<script type="text/javascript" src="jquery2.js"></script>
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
				},
			});
		}
		$(function() {
			setInterval(update_image,1000);
		});
	</script>
</head>
<body>
<div id="screen">
	<img id="image" src="vide.png" />
	<!-- <div id="scores"></div> -->
</div>
</body>
</html>
