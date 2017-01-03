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

function format_heure(time) {
	var myDate = new Date(time*1000);
	var heures = myDate.getHours();
	var minutes = myDate.getMinutes();
	if(minutes < 10) minutes = "0"+minutes;
	var secondes = myDate.getSeconds();
	if(secondes < 10) secondes = "0"+secondes;
	return heures + "h" + minutes;
}
