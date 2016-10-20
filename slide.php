<?php
$screen = 1;
$width = 0;
$height = 0;
$align = "";
if(isset($_REQUEST['screen'])) {
	$screen = intval($_REQUEST['screen']);
}
if(isset($_REQUEST['width'])) {
	$width = intval($_REQUEST['width']);
}
if(isset($_REQUEST['height'])) {
	$height = intval($_REQUEST['height']);
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title></title>
	<style type="text/css" title="text/css">
	#screen {
		position:relative;
		<?php if($align) print("text-align: ".$align.";\n") ?>
		<?php if($width>0) print("width: ".$width."px;\n") ?>
		<?php if($height>0) print("height: ".$height."px;\n") ?>
	}
	#screen img {
		<?php if($width>0 || $height>0):?>
		position:absolute;
		bottom:0px;
		left:0px;
		<?php endif; ?>
		<?php if($width>0) print("max-width: ".$width."px;\n") ?>
		<?php if($height>0) print("max-height: ".$height."px;\n") ?>
	}
	</style>
	<script type="text/javascript" src="jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
		var current_image = "";
		function update_image() {
			$.ajax({
				url:'getslide.php',
				type:'POST',
				data: {
					screen: "<?=$screen?>"
				},
				success: function(data,status){
					if(data == "") {
						data = "vide.png";
						//$("#screen img").hide();
					}
					if(data != current_image) {
						current_image = data;
						//var blop = (new Date()).getTime();
						//data = data + "?blop=" + blop;
						$("#screen img").attr("src",data);
					}
					$("#screen img").show();
				}
			});
		}
		$(function() {
			setInterval(update_image,1000);
		});
	</script>
</head>
<body>
<div id="screen"><img src="vide.png" /></div>
</body>
</html>
