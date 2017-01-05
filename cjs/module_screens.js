// dimensions du cadre simulant l'écran
//var fw = 400;
//var fh = 225;
var time_origin_delta = 0;
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
function module_screens_init() {
	$(".screen .effacer_croix").off("click").click(function() {
		var parent = $(this).closest(".screen");
		var title = $(this).attr("title");
		var pop = $(this).closest("form").find(".effacer_croix_valide");
		console.log($(this));
		console.log(parent);
		pop.css({
			position: "absolute",
			borderColor: "red",
			top: ($(this).offset().top - parent.offset().top + $(this).height() - 2)+"px",
			left: ($(this).offset().left - parent.offset().left - 2)+"px",
			zIndex: "1000",
		});
 		var closePop = null;
 		closePop = function() {
 			$("body").off("mouseup",closePop)
 			$('.temp_pop').hide();
 		};
 		$("body").on("mouseup",closePop);
		pop.show();
		return false;
	});
	$(".screen .changer").off("click").click(function() {
		var screen = $(this).closest(".screen");
		apply_screen_changes(screen);
	});
	$(".source .assign").off("change").change(function() {
		var source = $(this).closest(".source");
		apply_screen_changes(source);
		$(this).closest("form").submit();
	});
	$(".lien").off("click").click(function() {
		$(this).select();
	});
	//
	function screen_modified(screen) {
		if(!screen.is(".module_screen_block")) {
			screen = screen.closest(".module_screen_block");
		}
		apply_screen_changes(screen);
		screen.addClass("modified");
		$("#slide_block").addClass("modified");
	}
	//
	$(".pos_btn.topleft").off("click").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"0px", right:"auto",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.topright").off("click").click(function() {
		$(this).siblings(".image").css({
			top:"0px", bottom:"auto",
			left:"auto", right:"0px",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.bottomleft").off("click").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"0px", right:"auto",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.bottomright").off("click").click(function() {
		$(this).siblings(".image").css({
			top:"auto", bottom:"0px",
			left:"auto", right:"0px",
		});
		screen_modified($(this));
		return false;
	});
	//
	$(".pos_btn.centerleft").off("click").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:"0px", right:"auto",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.centerright").off("click").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:"auto", right:"0px",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.centertop").off("click").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"0px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.centerbottom").off("click").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:"auto", bottom:"0px",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.centercenter").off("click").click(function() {
		var img = $(this).siblings(".image");
		var h=img.height(),w=img.width();
		img.css({
			top:Math.floor(fh/2-h/2)+"px", bottom:"auto",
			left:Math.floor(fw/2-w/2)+"px", right:"auto",
		});
		screen_modified($(this));
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
	$(".pos_btn.moveleft").off("click").click(function(evt) {
		var img = $(this).siblings(".image");
		if(evt.shiftKey) { moveImage(img,-1/scale,0); }
		else { moveImage(img,-5,0); }
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.moveright").off("click").click(function(evt) {
		var img = $(this).siblings(".image");
		if(evt.shiftKey) { moveImage(img,1/scale,0); }
		else { moveImage(img,5,0); }
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.movetop").off("click").click(function(evt) {
		var img = $(this).siblings(".image");
		if(evt.shiftKey) { moveImage(img,0,-1/scale); }
		else { moveImage(img,0,-5); }
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.movebottom").off("click").click(function(evt) {
		var img = $(this).siblings(".image");
		if(evt.shiftKey) { moveImage(img,0,1/scale); }
		else { moveImage(img,0,5); }
		screen_modified($(this));
		return false;
	});
	//
	$(".pos_btn.zoomin").off("click").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.data("width")/img.width());
		zoom.val(zoom.val()*1.1);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.zoomout").off("click").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		if(zoom.val() == 0) zoom.val(img.width()/img.data("width"));
		zoom.val(zoom.val()*0.9);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this));
		return false;
	});
	$(".pos_btn.zoomzero").off("click").click(function() {
		var img = $(this).siblings(".image");
		var zoom = $(this).siblings(".zoom");
		img.data("zoom",0);
		base_size(img);
		img.height(img.data("height")*zoom.val());
		img.width(img.data("width")*zoom.val());
		screen_modified($(this));
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
			screen_modified(movingImage);
		}
		if(scalingImage) {
			screen_modified(scalingImage);
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
	$(".pimage").off("mouseover mousemove")
	.on("mouseover mousemove",function(evt) {
		if(movingImage == false && scalingImage == false) {
			noCursor();
		}
	});
	$(".pimage .image").off("mouseover mousemove")
	.on("mouseover mousemove",function(evt) {
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
	$(".pimage .image, .pimage") // NOTE: pas de off ici
		.mousemove(movingImageMouseMove);
	$(".pimage .image").off("mousedown")
	.mousedown(function(evt) {
		if(evt.altKey) {
			setScalingImage($(this),evt);
		} else {
			scalingImage = false;
			movingImage = $(this);
			movingStart = [evt.pageX,evt.pageY];
		}
		return false;
	});
	$("body").off("mouseup",exitMove).on("mouseup",exitMove);
	// fin setup events
	$(".module_screen_block").each(function() {
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
	function update_timer() {
		var curTime = Math.round(Date.now()/1000 + time_origin_delta);
		$(".screen").each(function(i) {
			var time0 = parseInt($(this).find(".timestamp").val(),10);
			if(time0 == 0) {
				time = "";
			} else {
				time = Math.floor((curTime - time0) / 60);
			}
			var score = calculer_score(time);
			var heure = format_heure(time0,time);
			var focus = $(this).find(".screen_timer_text").is(":focus");
			var texteField = $(this).find(".screen_timer_text");
			if(!focus) {
				var title = "Image ajoutée à "+heure;
				if(texteField.data("title") != title) {
					texteField.data("title",title);
					try {
						texteField.tooltipster("content",title);
					} catch(err) {
						texteField.attr("title",title);
					}
				}
				if(texteField.val() != score) {
					texteField.val(score);
				}
			}
			if($(this).closest(".active").length>0) {
				if($(".score_value").val() != score) {
					$(".score_value").val(score);
				}
			}
		});
	}
	setTimeout(update_timer,100);
	setInterval(update_timer,1000);
}
$(function() {
	module_screens_init();
	var php_origin = $("#time_origin").val();
	if(php_origin != 0) {
		time_origin_delta = php_origin-Date.now()/1000;
	}
});
