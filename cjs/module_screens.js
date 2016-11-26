// dimensions du cadre simulant l'écran
//var fw = 400;
//var fh = 225;
var fw = 1920;
var fh = 1080;
var scale = 5;
var movingImage = false;
var movingStart = [0,0];
var scalingImage = false;
var scalingStart = [0,0];
var scalingDirection = "";
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
//
function apply_screen_changes(screen) {
	var parent = screen.find(".pimage");
	var image = parent.find(".image");
	var top = image.offset().top-parent.offset().top;
	var left = image.offset().left-parent.offset().left;
	screen.find('input[name="image_top"]').val(Math.round(scale*top));
	screen.find('input[name="image_left"]').val(Math.round(scale*left));
	//screen.find('input[name="image_zoom"]').val(0);
}
//
$(function() {
	$(".assign").change(function() {
		var source = $(this).closest(".source");
		apply_screen_changes(source);
		$(this).closest("form").submit();
	});
	$(".changer").click(function() {
		var screen = $(this).closest(".screen");
		apply_screen_changes(screen);
	});
	$(".lien").click(function() {
		$(this).select();
	});
	$(".screen,.source").each(function() {
		var screen = $(this);
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
			apply_screen_changes(screen);
		});
	});
	//
	function screen_modified(screen) {
		apply_screen_changes(screen);
		screen.addClass("modified");
	}
	//
	$(".pos_btn.topleft").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"0px", right:"auto",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.topright").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"auto", right:"0px",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.bottomleft").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"0px", right:"auto",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.bottomright").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"auto", right:"0px",
		});
		screen_modified($(this).closest(".screen"));
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
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.centerright").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:"auto", right:"0px",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.centertop").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"0px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.centerbottom").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"auto", bottom:"0px",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.centercenter").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this).closest(".screen"));
		return false;
	});
	//
	function moveImage(img,deltaX,deltaY,shift) {
		if(typeof(shift) == "undefined") shift = false;
		var parent = img.parent();
		var imgPos = {
			top: img.offset().top  - parent.offset().top,
			left:img.offset().left - parent.offset().left,
		};
		var newPosX = Math.round(imgPos.left*scale+deltaX*scale);
		var newPosY = Math.round(imgPos.top *scale+deltaY*scale);
		if(shift) {
			// empêcher de sortir à gauche
			newPosX = Math.max(0, newPosX);
			newPosY = Math.max(0, newPosY);
			// empêcher de sortir à droite
			var fmw = Math.floor(fw-img.width());
			var fmh = Math.floor(fh-img.height());
			newPosX = Math.min(fmw, newPosX);
			newPosY = Math.min(fmh, newPosY);
		}
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
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.moveright").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,5,0);
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.movetop").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,0,-5);
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.movebottom").click(function() {
		var img = $(this).siblings(".image");
		moveImage(img,0,5);
		screen_modified($(this).closest(".screen"));
		return false;
	});
	//
	$(".pos_btn.zoomin").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.data("width")/img.width());
		zoom.val(zoom.val()*1.1);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.zoomout").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.width()/img.data("width"));
		zoom.val(zoom.val()*0.9);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this).closest(".screen"));
		return false;
	});
	$(".pos_btn.zoomzero").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		img.data("zoom",0);
		base_size(img);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this).closest(".screen"));
		return false;
	});
	//
	function scaleImage(img,deltaX,deltaY) {
		var parent = img.parent();
		var zoom = img.siblings(".zoom");
		// orienter les deltas selon la direction
		switch(scalingDirection) {
		case "topleft":
			deltaX = -deltaX;
			deltaY = -deltaY;
			break;
		case "topright":
			deltaY = -deltaY;
			break;
		case "bottomleft":
			deltaX = -deltaX;
			break;
		}
		// récupérer la largeur et la hauteur
		// changer de delta max en conservant les proportions
		var newWidth = img.width()+deltaX*scale;
		var newHeight = img.height()+deltaY*scale;
		if(Math.abs(deltaX/deltaY)>16/9) {
			newHeight = newWidth*img.height()/img.width();
			deltaY = (newHeight - img.height()) / scale;
		} else {
			newWidth = newHeight*img.width()/img.height();
			deltaX = (newWidth - img.width()) / scale;
		}
		zoom.val(img.width()/img.data("width"));
		// calculer la position pour passer en mode top/left
		var imgPos = {
			top: img.offset().top  - parent.offset().top,
			left:img.offset().left - parent.offset().left,
		};
		var posX = imgPos.left*scale;
		var posY = imgPos.top *scale;
		// calculer les nouvelles coordonnées selon la direction
		switch(scalingDirection) {
		case "topleft":
			posX -= deltaX*scale;
			posY -= deltaY*scale;
			break;
		case "topright":
			posY -= deltaY*scale;
			break;
		case "bottomleft":
			posX -= deltaX*scale;
			break;
		}
		//
		img.css({
			width: Math.round(newWidth)+"px",
			height:Math.round(newHeight)+"px",
			left:  Math.round(posX)+"px",
			top:   Math.round(posY)+"px",
			bottom:"auto",
			right:"auto",
		});
	}
	// déplacement manuel des images
	function movingImageMouseMove(evt) {
		if(movingImage != false) {
			var curPos = [evt.pageX,evt.pageY];
			var deltaX = curPos[0]-movingStart[0];
			var deltaY = curPos[1]-movingStart[1];
			moveImage(movingImage,deltaX,deltaY,evt.shiftKey);
			movingStart = curPos;
			return false;
		}
		if(scalingImage != false) {
			var curPos = [evt.pageX,evt.pageY];
			var deltaX = curPos[0]-scalingStart[0];
			var deltaY = curPos[1]-scalingStart[1];
			scaleImage(scalingImage,deltaX,deltaY);
			scalingStart = curPos;
			return false;
		}
	}
	//
	function noCursor() {
		$(".pimage, .pimage .image").removeClass("nwresize swresize neresize seresize");
	}
	function exitMove(evt) {
		movingImageMouseMove(evt);
		if(movingImage) {
			screen_modified(movingImage.closest(".screen,.source"));
		}
		if(scalingImage) {
			screen_modified(scalingImage.closest(".screen,.source"));
		}
		scalingImage = false;
		movingImage = false;
		noCursor();
	}
	function setScalingImage(image,evt) {
		movingImage = false;
		scalingImage = image;
		scalingStart = [evt.pageX,evt.pageY];
		//
		var parent = scalingImage.parent();
		var imgPos = {
			top: scalingImage.offset().top  - parent.offset().top,
			left:scalingImage.offset().left - parent.offset().left,
		};
		var ptTouch = {
			x: (evt.pageX - scalingImage.offset().left)
			/ scalingImage.width() * scale,
			y: (evt.pageY - scalingImage.offset().top)
			/ scalingImage.height() * scale,
		};
		noCursor();
		if(ptTouch.x<0.5 && ptTouch.y<0.5) {
			scalingDirection = "topleft";
			parent.addClass("nwresize");
			scalingImage.addClass("nwresize");
		} else if(ptTouch.x>=0.5 && ptTouch.y<0.5) {
			scalingDirection = "topright";
			parent.addClass("neresize");
			scalingImage.addClass("neresize");
		} else if(ptTouch.x<0.5 && ptTouch.y>=0.5) {
			scalingDirection = "bottomleft";
			parent.addClass("swresize");
			scalingImage.addClass("swresize");
		} else {
			scalingDirection = "bottomright";
			parent.addClass("seresize");
			scalingImage.addClass("seresize");
		}
	}
	$(".pimage .image").on("mouseover mousemove",function(evt) {
		if(movingImage == false && scalingImage == false) {
			image = $(this);
			if(evt.altKey) {
				setScalingImage(image,evt);
				scalingImage = false;
			} else {
				noCursor();
			}
		}
		return false;
	});
	$(".pimage").on("mouseover mousemove",function(evt) {
		if(movingImage == false && scalingImage == false) {
			noCursor();
		}
	});
	$(".pimage .image").mousedown(function(evt) {
		if(evt.altKey) {
			setScalingImage($(this),evt);
		} else {
			scalingImage = false;
			movingImage = $(this);
			movingStart = [evt.pageX,evt.pageY];
		}
		return false;
	});
	$(".pimage .image, .pimage").mousemove(movingImageMouseMove);
	$("body").on("mouseup",exitMove);
});
