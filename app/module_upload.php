<?php
include_once("head.php");

// retourne le nom du fichier dans lequel mettre une nouvelle image source
// retourne false si le fichier n'est pas une image d'un format supporté
function source_file($file) {
	if(!file_exists($file)) return false;
	switch(mime_content_type($file)) {
	case "image/jpeg":
		$ext = "jpg";
		break;
	case "image/png":
		$ext = "png";
		break;
	case "image/gif":
		$ext = "gif";
		break;
	default:
		$ext = "";
	}
	if($ext) {
		return SOURCES_DIR."image_".uniqid().".".$ext;
	} else {
		return false;
	}
}

if(isset($_FILES["upload_fichier"])||isset($_POST["upload_url"])) {
	if(isset($_FILES["upload_fichier"])) {
		$file = $_FILES["upload_fichier"];
		$filename = source_file($file['tmp_name']);
		if($filename) {
			move_uploaded_file($file['tmp_name'],$filename);
		}
	}
	if(isset($_POST["upload_url"]) && $_POST["upload_url"] != "") {
		$tmp_file = tempnam("/tmp", "twitch_slides");
		if($tmp_file) {
			$data = file_get_contents($_POST["upload_url"]);
			file_put_contents($tmp_file,$data);
			$filename = source_file($tmp_file);
			if($filename) {
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
		<b>Ajouter une image</b> <i>(jpg,png,gif)</i><br/>
		Locale&nbsp;: <input type="file" name="upload_fichier" class="upload_fichier"><br/>
		Par une URL&nbsp;: <input type="text" name="upload_url" class="upload_url" placeholder="Lien direct d'une image"><br/>
		<button class="upload_btn">Envoyer</button>
	</form>
	</div>
	<?
}
