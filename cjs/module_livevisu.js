$(function() {
	var blocCible = $("#livevisu");
	function showLiveVisu() {
		blocCible.find(".inframe").attr("src", blocCible.find(".inframe").data("src"));
	}
	function hideLiveVisu() {
		blocCible.find(".inframe").attr("src","");
	}
	$("#livevisu").data("onshow",showLiveVisu);
	$("#livevisu").data("onhide",hideLiveVisu);
});
