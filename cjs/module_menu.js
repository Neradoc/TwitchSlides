var listeOnOffs = [
	{"nom":"previsu", "block":"#slide_block"},
	{"nom":"livevisu", "block":"#livevisu"},
]
$(function() {
	listeOnOffs.forEach(function(menu) {
		var haveMenu = prefs.get("activer_"+menu['nom'],"0");
		var menuCheck = $("#menu ."+menu['nom']);
		var blocCible = $(menu['block']);
		if(haveMenu === "1") {
			menuCheck.prop("checked","1");
			blocCible.show();
			if(blocCible.data("onshow")) blocCible.data("onshow")();
		} else if (haveMenu === "0") {
			menuCheck.prop("checked","")
			if(blocCible.data("onhide")) blocCible.data("onhide")();
			blocCible.hide();
		} else {
			menuCheck.prop("checked","1");
			blocCible.show();
			if(blocCible.data("onshow")) blocCible.data("onshow")();
		}
		menuCheck.on("click",function() {
			if($(this).prop("checked")) {
				prefs.set("activer_"+menu['nom'],"1");
				blocCible.show();
				if(blocCible.data("onshow")) blocCible.data("onshow")();
			} else {
				prefs.set("activer_"+menu['nom'],"0");
				if(blocCible.data("onhide")) blocCible.data("onhide")();
				blocCible.hide();
			}
		});
	});
});
