prefs = (function() {
	return {
		"get" : function(key, defaut) {
			if(typeof(localStorage) == "object") {
				if(typeof(localStorage.getItem(key)) != "undefined") {
					return localStorage.getItem(key);
				}
			}
			return defaut;
		},
		"set" : function(key, value) {
			if(typeof(localStorage) == "object") {
				localStorage.setItem(key,value);
			}
		}
	}
})();

function format_heure(timestamp,timer) {
	var myDate = new Date(timestamp*1000);
	var heures = myDate.getHours();
	var minutes = myDate.getMinutes();
	if(minutes < 10) minutes = "0"+minutes;
	var secondes = myDate.getSeconds();
	if(secondes < 10) secondes = "0"+secondes;
	return heures + "h" + minutes + " (" + timer + " min)";
}

function calculer_score(time) {
	var calc_score = $("#calc_score").data("calcul");
	try {
		if(typeof(calc_score) == "undefined" || calc_score.trim() == "") {
			return time;
		}
		var score = parseInt(eval(calc_score),10);
		return score;
	} catch(err) {
		return time;
	}
}

var tooltipOptions = {
	animationDuration: 0,
	delay: [500,0],
	distance: 0,
}
$(function() {
	$(".tooltiper, button, input, select").tooltipster(tooltipOptions);
});
