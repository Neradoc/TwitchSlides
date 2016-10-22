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
		if($file && file_exists($file)) {
			print(json_encode(array(
				'image' => $file,
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
	}
	#screen {
		position:relative;
		<?php if($align) print("text-align: ".$align.";\n") ?>
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
					$("#image").css({
						maxWidth: Math.floor(width)+"px",
						maxHeight: Math.floor(height)+"px",
					});
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
