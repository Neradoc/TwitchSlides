<?php
include("head.php");
$screen = false;
if(isset($_REQUEST['screen'])) {
	$screen = @intval($_REQUEST['screen']);
}

if($screen !== false) {
	foreach($image_exts as $ext) {
		$file = sprintf($image_format,$screen,$ext);
		if(file_exists($file)) {
			print($file);
			break;
		}
	}
}
