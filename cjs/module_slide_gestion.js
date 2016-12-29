// scale est défini dans module_screens
function posScreen(screen) {
	var parent = screen.find(".pimage");
	var image = parent.find(".image");
	var top = image.offset().top-parent.offset().top;
	var left = image.offset().left-parent.offset().left;
	return [
		Math.round(scale*left),Math.round(scale*top),
		screen.find('input[name="image_zoom"]').val(),
	];
}
function update_the_slide() {
	var screens = [];
	$("#screens .screen").each(function() {
		if($(this).find('button[name="screen_switch"]').val() == 0) {
			var image = $(this).find('.image');
			var pos = posScreen($(this));
			var size = [image.data("width"),image.data("height")];
			var screenNum = $(this).find('input[name="screen_num"]').val();
			var screen = {
				"num":screenNum,
				"image":image.attr('src'),
				"pos":pos,
				"size":size,
				"on":true,
			}
			screens.push(screen);
		}
	});
	var liste_scores = "";
	$(".scoreboard_list .scoreboard_line").each(function(index) {
		var nom = $(this).find('input[name="scoreboard_nom"]').val();
		var score = $(this).find('input.score').val();
		var out = "<span>"+nom+" : "+score+"</span>";
		liste_scores += out;
	});
	var data = {
		"screens":screens,
		"scoreboard_on":$('button[name="scoreboard_switch"]').val()=="1" ?false:true,
		"scoreboard_index":$('button[name="scoreboard_index"]').val(),
		"liste_scores":liste_scores,
		"reload":false
	};
	update_slide(data);
}
function save_the_screens() {
	$('#black_block').show();
	var forms = [];
	// faire une série d'ajax avec les formulaires
	$("#screens .screen").each(function() {
		var form = $(this).find("form[name='screens']");
		forms.push(form);
	});
	var save_a_screen = function(index) {
		$.ajax({
			url:location.href,
			type:'POST',
			data: forms[index].serialize()+"&screen_changer=1",
			success: function(data,status) {
				if(index+1 < forms.length) {
					// sauver le screen suivant
					save_a_screen(index+1);
				} else {
					// puis recharger la page
					location.reload();
				}
			}
		});
	};
	save_a_screen(0);
	return false;
}
var timerMoveScore = false;
var timerUpdateSlide = false;
function stopSlideLoops() {
	if(timerMoveScore !== false) {
		clearInterval(timerMoveScore);
		timerMoveScore = false;
	}
	if(timerUpdateSlide !== false) {
		clearInterval(timerUpdateSlide);
		timerUpdateSlide = false;
	}
}
function startSlideLoops() {
	$("#screens .screen .pimage .image").on("load",function() {
		update_the_slide();
	});
	timerMoveScore = setInterval(movescores,50);
	timerUpdateSlide = setInterval(update_the_slide,500);
}
$(function() {
	setupSlides(true,false);
	startSlideLoops();
	$("#slide_block").data("onshow",startSlideLoops);
	$("#slide_block").data("onhide",stopSlideLoops);
	$(".changer_tous").click(save_the_screens);
});
