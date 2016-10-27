<?php
include("prefs.php");
$prefs = new PrefsManager();
$poll_on = $prefs->get("strawpoll_on",true);
$poll_page = $prefs->get("strawpoll","");
$poll_embed = $prefs->poll_embed();
// http://www.strawpoll.me/10987342
// http://www.strawpoll.me/4796816
if(isset($_POST['strawpoll'])) {
	print(json_encode(array(
		'page' => $poll_page,
		'embed' => $poll_embed,
		'on' => $poll_on,
	)));
	exit();
}
?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8" />
	<title></title>
	<style type="text/css" title="text/css">
	body {
		overflow: hidden;
	}
	#poll_page {
		display: none;
	}
	</style>
	<script type="text/javascript" src="cjs/jquery2.js"></script>
	<script type="text/javascript" language="javascript" charset="utf-8">
		var current_poll = "<?=$poll_page?>";
		function update_poll() {
			$.ajax({
				url:'strawpoll',
				type:'POST',
				data: { strawpoll: 1 },
				dataType: "json",
				success: function(data,status){
					if(data == "") {
						$("#poll_frame").hide();
					} else {
						if(data.page != current_poll) {
							current_poll = data.page;
							$("#poll_page").html(data.page);
							$("#poll_frame").attr("src",data.embed);
							var width = $(window).width();
							var height= $(window).height();
							$("#poll_frame").css({
								width: Math.floor(width)+"px",
								height: Math.floor(height)+"px",
							});
						}
						if(data.on) {
							$("#poll_frame").show();
						} else {
							$("#poll_frame").hide();
						}
					}
				}
			});
		}
		$(function() {
			var width = $(window).width();
			var height= $(window).height();
			$("#poll_frame").css({
				width: Math.floor(width)+"px",
				height: Math.floor(height)+"px",
			});
			setInterval(update_poll,1000);
		});
	</script>
</head>
<body>
<div id="contenu">
<div id="poll_frame_div">
	<div id="poll_page"><?=$poll_page?></div>
	<iframe  id="poll_frame" src="<?=$poll_embed?>" style="border:0;">Loading poll...</iframe>
</div>
</div>
</body>
</html>
