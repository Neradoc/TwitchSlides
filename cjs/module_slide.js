var screenNumbers = "";
var resizeTo = window;
var current_image = {};
var liste_scores = "";
var ref_width = 1920;
var ref_height = 1080;
var height_0 = 0;
function update_slide(data,status){
	if(data == false) {
		$("#slide_block .image").hide();
		return;
	} 
	if(data['reload']) {
		location.reload();
	}
	//
	var width = ref_width;
	var height= ref_height;
	if(resizeTo) {
		width = $(resizeTo).width();
		height= $(resizeTo).height();
		$("#slide_block").css({
			width: Math.floor(width)+"px",
			height: Math.floor(height)+"px",
		});
	}
	//
	if(data['scoreboard_on']) {
		$("#slide_scores").show();
		if(liste_scores != data['liste_scores']) {
			liste_scores = data['liste_scores'];
			$("#slide_scores").html(liste_scores);
			$("#slide_scores span:first-child").append("<img class=\"crown\" src=\"cjs/img/crown.png\"/>");
		}
		if(height != height_0) { // height != ref_height
			height_0 = height;
			$("#slide_scores span").css({
				fontSize: Math.max(8,Math.floor(24*height/ref_height))+"px",
				marginRight: Math.max(10,Math.floor(100*height/ref_height))+"px",
			});
			$("#slide_scores span .crown").css({
				height: Math.max(4,Math.floor(40*height/ref_height))+"px",
				left: Math.max(4,Math.floor(20*height/ref_height))+"px",
				top: -1*Math.max(6,Math.floor(30*height/ref_height))+"px",
			});
		}
		if(data['scoreboard_index'] !== false) {
			$("#slide_scores").css("z-index",data['scoreboard_index']*10+5);
		}
	} else {
		$("#slide_scores:visible").hide();
	}
	//
	$("#slide_block .image:visible").hide();
	for(var num in data['screens']) {
		var screen = data['screens'][num];
		var image = $("#slide_block .image"+screen['num']);
		if(!(num in current_image) || screen['image'] != current_image[num]) {
			current_image[num] = screen['image'];
			image.attr("src",current_image[num]);
		}
		//
		if( screen['image'] == ""
			|| ("on" in screen
				&& screen['on'] == false)
		) {
			continue;
		}
		//
		var left = screen['pos'][0]*width/ref_width;
		var top = screen['pos'][1]*height/ref_height;
		var iw = screen['size'][0]*width/ref_width;
		var ih = screen['size'][1]*height/ref_height;
		var zoom = screen['pos'][2];
		if(!(zoom>0)) { zoom = 1; }
		var css = {
			left: Math.floor(left)+"px",
			top: Math.floor(top)+"px",
			width: Math.floor(iw*zoom)+"px",
			height: Math.floor(ih*zoom)+"px",
		};
		image.css(css);
		//
		image.not(":visible").show();
	}
}
function do_auto_update() {
	$.ajax({
		url:'slide.php',
		type:'POST',
		data: {
			get:1,
			screen: screenNumbers,
		},
		dataType: "json",
		error: function(a,b,c) {
			console.log("ERROR");
			console.log(a,b,c);
		},
		success: update_slide
	});
}
var scorepos = -100000;
var step = 5;
function movescores() {
	if($("#slide_scores").is(":visible")) {
		scorepos = scorepos - step;
		var width = $("#slide_scores").width();
		if(scorepos < -1*width) {
			scorepos = $(window).width();
		}
		$("#slide_scores").css({
			left: scorepos+"px",
		});
	}
}
function setupSlides(withBackground,resizeToIN,screenNumbersIN) {
	if(withBackground) {
		$("#slide_block .slide_background").show();
	} else {
		$("#slide_block .slide_background").hide();
	}
	if(typeof(screenNumbersIN) != "undefined") {
		screenNumbers = screenNumbersIN;
	}
	if(typeof(resizeToIN) != "undefined") {
		resizeTo = resizeToIN;
	}
}
function startFullSlide() {
	var speed = 50;
	setTimeout(do_auto_update,100);
	setInterval(do_auto_update,1000);
	setInterval(movescores,speed);
}
