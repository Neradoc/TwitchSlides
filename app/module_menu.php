<?php
function disp_menu($thisurl) {
	?><div id="menu">
		<a class="ici" href="gestion">Gestion</a>
		<a href="config">Config</a>
		<label title="Prévisualise les positions des images avant de les valider"><input class="previsu" type="checkbox" />Prévisu</label>
		<label title="Visualise le slide live tel qu'il est affiché sur le stream"><input class="livevisu" type="checkbox" />Live</label>
	</div><?
}
