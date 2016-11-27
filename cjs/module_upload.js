/* Dropper des images dans la page */
$(function(){
	dropper = $("#upload");
	dropper.on('dragenter', function (e) 
	{
		e.stopPropagation();
		e.preventDefault();
		$(this).addClass("drop_enter");
	});
	dropper.on('dragexit', function (e) 
	{
		e.stopPropagation();
		e.preventDefault();
		$(this).removeClass("drop_enter drop_drop drop_wrong");
	});
	dropper.on('dragover', function (e) 
	{
		e.stopPropagation();
		e.preventDefault();
	});
	dropper.on('drop', function (e) 
	{
		$(this).addClass("drop_drop");
		e.preventDefault();
		var files = e.originalEvent.dataTransfer.files;
		//We need to send dropped files to Server
		handleFileUpload(files,dropper);
	});
	function finishWith(num,newBlock) {
		if(num == 0) {
			$("#black_block").hide();
			location.reload();
			return;
			if(newBlock) {
				$("#sources").replaceWith(newBlock);
			} else {
				location.reload();
			}
		}
	}
	function handleFileUpload(files,dropper) {
		dropper.removeClass("drop_enter drop_drop drop_wrong");
		// ne rien faire si pas de cible valide
		if(files.length == 0) return false;
		//
		$("#black_block").show();
		var form = new FormData();
		var numFiles = files.length;
		for(i=0; i<files.length; i++) {
			form.append("upload_fichier", files[i], files[i].name);
			//
			$.ajax({
				url: document.location.href,
				type: 'POST',
				data: form,
				processData: false,
				contentType: false,
				error: function() {
					numFiles -= 1;
					finishWith(numFiles,false);
				},
				complete: function(xhr,status) {
					numFiles -= 1;
					var newBlock = $(xhr.responseText).find('#sources');
					finishWith(numFiles,newBlock);
				}
			});
		}
	}
});
