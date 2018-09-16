// classe de la checkbox et id du bloc controlé (à masquer/montrer)
var listeOnOffs = [
	{"nom":"previsu", "block":"#slide_block"},
	{"nom":"livevisu", "block":"#livevisu"},
	{"nom":"strawpolls", "block":"#strawpoll"},
	{"nom":"scoreboard", "block":"#scoreboard"},
];
$(function() {
	listeOnOffs.forEach(function(menu) {
		var haveMenu = prefs.get("activer_"+menu['nom'],"0");
		var menuCheck = $("#menu ."+menu['nom']);
		var blocCible = $(menu['block']);
		if(haveMenu === "1") {
			menuCheck.prop("checked","1");
			blocCible.show();
			if(blocCible.data("onshow")) blocCible.data("onshow")();
		} else {
			// désactivé par défaut ou "0"
			menuCheck.prop("checked","")
			if(blocCible.data("onhide")) blocCible.data("onhide")();
			blocCible.hide();
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
