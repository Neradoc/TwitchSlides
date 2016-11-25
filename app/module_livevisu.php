<?php
function disp_livevisu($thisurl) {
	global $Nscreens,$prefs,$url_miniature_stream;
	?><div id="livevisu">
		<h3><a href="slide" target="_blank">Sur le Live</a></h3>
		<iframe class="screensize inframe" data-src="slide?debug" src="slide?debug">...</iframe>
	</div><?
}
