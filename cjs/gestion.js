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
