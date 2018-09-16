<?php
function disp_menu($thisurl) {
	?><div id="menu">
		<a class="ici" href="gestion">Gestion</a>
		<a href="config">Config</a>
		<a href="sources">Sources</a>
		<label title="Prévisualise les positions des images avant de les valider"><input class="previsu" type="checkbox" />Prévisu</label>
		<label title="Visualise le slide live tel qu'il est affiché sur le stream"><input class="livevisu" type="checkbox" />Live</label>
		<label title="TBD"><input class="strawpolls" type="checkbox" />Strawpoll</label>
		<label title="TBD"><input class="scoreboard" type="checkbox" />Scores</label>
	</div><?
}
