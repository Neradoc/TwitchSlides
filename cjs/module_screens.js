// dimensions du cadre simulant l'écran
//var fw = 400;
//var fh = 225;
var fw = 1920;
var fh = 1080;
var scale = 5;
var movingImage = false;
var movingStart = [0,0];
function base_size(img) {
	var iw = img.data("width");
	var ih = img.data("height");
	var zoom = img.data("zoom");
	// calculer la taille d'affichage
	var h,w;
	w = iw;
	h = ih;
	// zoom est défini pour screen, sinon on fait un fit
	// zoom n'est pas défini pour source au début -> fit
	if(!(zoom>0)) {
		// faire tenir dans l'écran en gardant les proportions (fit)
		if(iw>fw || ih>fh) {
			if(iw/ih>16/9) {
				w = fw;
				h = ih * fw/iw;
			} else {
				w = iw * fh/ih;
				h = fh;
			}
		} else {
			w = iw;
			h = ih;
		}
		// définir le zoom par rapport à la taille originale
		zoom = w / iw;
	} else {
		// zoom défini -> ben l'utiliser quoi
		w = Math.round(w*zoom);
		h = Math.round(h*zoom);
	}
	// initiliaser le champ zoom (vide au début)
	img.siblings('input[name="image_zoom"]').val(zoom);
	// dimensionner
	img.css({
		width: w+"px",
		height: h+"px",
	});
}
$(function() {
	$(".assign").change(function() {
		var source = $(this).closest(".source");
		var parent = source.find(".pimage");
		var image = parent.find(".image");
		var top = image.offset().top-parent.offset().top;
		var left = image.offset().left-parent.offset().left;
		source.find('input[name="image_top"]').val(Math.round(scale*top));
		source.find('input[name="image_left"]').val(Math.round(scale*left));
		//source.find('input[name="image_zoom"]').val(0);
		$(this).closest("form").submit();
	});
	$(".changer").click(function() {
		var screen = $(this).closest(".screen");
		var parent = screen.find(".pimage");
		var image = parent.find(".image");
		var top = image.offset().top-parent.offset().top;
		var left = image.offset().left-parent.offset().left;
		screen.find('input[name="image_top"]').val(Math.round(scale*top));
		screen.find('input[name="image_left"]').val(Math.round(scale*left));
		//screen.find('input[name="image_zoom"]').val(0);
	});
	$(".lien").click(function() {
		$(this).select();
	});
	$(".screen,.source").each(function() {
		var img = $(this).find(".pimage .image");
		img.on("load",function() {
			var itop = img.data("top");
			var ileft = img.data("left");
			if(!itop) itop = 0;
			if(!ileft) ileft = 0;
			img.css({
				position:"absolute",
				left: (ileft?ileft:0)+"px",
				top: (itop?itop:0)+"px",
			});
			base_size(img);
		});
	});
	//
	$(".pos_btn.topleft").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"0px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.topright").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"auto", right:"0px",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.bottomleft").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"0px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.bottomright").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"auto", right:"0px",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	//
	$(".pos_btn.centerleft").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:"0px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.centerright").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:"auto", right:"0px",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.centertop").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"0px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.centerbottom").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"auto", bottom:"0px",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.centercenter").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	//
	function moveImage(img,deltaX,deltaY) {
		var parent = img.parent();
		var imgPos = {
			top: img.offset().top  - parent.offset().top,
			left:img.offset().left - parent.offset().left,
		};
		var newPosX = Math.round(imgPos.left*scale+deltaX*scale);
		var newPosY = Math.round(imgPos.top *scale+deltaY*scale);
		// empêcher de sortir à gauche
		newPosX = Math.max(0, newPosX);
		newPosY = Math.max(0, newPosY);
		// empêcher de sortir à droite
		var fmw = Math.floor(fw-img.width());
		var fmh = Math.floor(fh-img.height());
		newPosX = Math.min(fmw, newPosX);
		newPosY = Math.min(fmh, newPosY);
		img.css({
			left: newPosX+"px",
			top:  newPosY+"px",
			bottom:"auto",
			right:"auto",
		});
	}
	$(".pos_btn.moveleft").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,-5,0);
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.moveright").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,5,0);
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.movetop").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,0,-5);
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.movebottom").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,0,5);
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	//
	$(".pos_btn.zoomin").click(function() {
		var img = $(this).siblings(".image");
		zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.data("width")/img.width());
		zoom.val(zoom.val()*1.1);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.zoomout").click(function() {
		var img = $(this).siblings(".image");
		zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.width()/img.data("width"));
		zoom.val(zoom.val()*0.9);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	$(".pos_btn.zoomzero").click(function() {
		var img = $(this).siblings(".image");
		zoom = $(this).siblings(".zoom");
		img.data("zoom",0);
		base_size(img);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		$(this).closest(".screen").addClass("modified");
		return false;
	});
	// déplacement manuel des images
	function movingImageMouseMove(evt) {
		if(movingImage != false) {
			var curPos = [evt.pageX,evt.pageY];
			var deltaX = curPos[0]-movingStart[0];
			var deltaY = curPos[1]-movingStart[1];
			moveImage(movingImage,deltaX,deltaY);
			movingStart = curPos;
			return false;
		}
	}
	function exitMove(evt) {
		if(movingImage) {
			movingImageMouseMove(evt);
			movingImage.closest(".screen").addClass("modified");
		}
		movingImage = false;
	}
	$(".pimage .image").mousedown(function(evt) {
		movingImage = $(this);
		movingStart = [evt.pageX,evt.pageY];
		return false;
	});
	$(".pimage .image, .pimage").mousemove(movingImageMouseMove);
	$("body").on("mouseup",exitMove);
});
