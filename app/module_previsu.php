<?php
function disp_previsu($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?><div id="previsu">
		<h3><a href="slide" target="_blank">Prévisu</a></h3>
		<iframe class="pimage" src="slide?debug">...</iframe>
	</div><?
}
