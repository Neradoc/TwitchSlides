<?php
include_once("head.php");

if(isset($_FILES["upload_fichier"])||isset($_POST["upload_url"])) {
	if(isset($_FILES["upload_fichier"])) {
		$file = $_FILES["upload_fichier"];
		if(is_image($file['tmp_name'])) {
			$ext = pathinfo($file['name'],PATHINFO_EXTENSION);
			$filename = "sources/image_".uniqid().".".$ext;
			move_uploaded_file($file['tmp_name'],$filename);
		}
	}
	if(isset($_POST["upload_url"]) && $_POST["upload_url"] != "") {
		$tmp_file = tempnam("/tmp", "twitch_slides");
		if($tmp_file) {
			$data = file_get_contents($_POST["upload_url"]);
			file_put_contents($tmp_file,$data);
			switch(mime_content_type($tmp_file)) {
			case "image/jpeg":
				$ext = "jpg";
				break;
			case "image/png":
				$ext = "png";
				break;
			default:
				$ext = "";
			}
			if($ext) {
				mime_content_type($tmp_file);
				$filename = "sources/image_".uniqid().".".$ext;
				rename($tmp_file,$filename);
				chmod($filename,0777);
			} else {
				unlink($tmp_file);
			}
		}
	}
	exit_redirect();
}

function disp_upload($thisurl) {
	global $poll_page,$poll_embed;
	?>
	<div id="upload">
	<form action="<?=$thisurl?>" name="upload" method="POST" enctype="multipart/form-data">
		<b>Ajouter une image</b><br/>
		Locale&nbsp;: <input type="file" name="upload_fichier" class="upload_fichier"><br/>
		Par une URL&nbsp;: <input type="text" name="upload_url" class="upload_url"><br/>
		<button class="upload_btn">Envoyer</button>
	</form>
	</div>
	<?
}
