function prefs(key,value) {
	if(typeof(value) == "undefined") {
		if(typeof(localStorage) == "object") {
			return localStorage.getItem(key);
		}
	} else {
		if(typeof(localStorage) == "object") {
			localStorage.setItem(key,value);
		}
	}
}
$(function() {
	var previsu = prefs("activer_previsu");
	if(previsu === "1") {
		$("#menu .previsu").prop("checked","1");
		$("#previsu").show();
	} else if (previsu === "0") {
		$("#menu .previsu").prop("checked","")
		$("#previsu").hide();
	} else {
		$("#menu .previsu").prop("checked","1");
		$("#previsu").show();
	}
	$("#menu .previsu").on("click",function() {
		console.log($(this).prop("checked"));
		if($(this).prop("checked")) {
			prefs("activer_previsu","1");
			$("#previsu").show();
		} else {
			prefs("activer_previsu","0");
			$("#previsu").hide();
		}
	});
});
