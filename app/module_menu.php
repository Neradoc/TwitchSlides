<?php
function disp_menu($thisurl) {
	//global $Nscreens,$prefs,$url_miniature_stream;
	?><div id="menu">
		<a class="ici" href="gestion">Gestion</a>
		<a href="config">Config</a>
		<label><input class="previsu" type="checkbox" />Prévisu</label>
		<label><input class="livevisu" type="checkbox" />Live</label>
	</div><?
}
