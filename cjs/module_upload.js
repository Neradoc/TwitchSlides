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
	function finishWith(numFile,fileCalls,newBlock) {
		if(numFile+1 < fileCalls.length) {
			fileCalls[numFile+1]();
		} else {
			$("#black_block").hide();
			if(newBlock) {
				$("#sources").replaceWith(newBlock);
				module_screens_init();
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
		var numFiles = files.length;
		var fileCalls = [];
		for(i=0; i<files.length; i++) {
			fileCalls[i] = function() {
				var numFile = i;
				var fileForm = new FormData();
				fileForm.append("upload_fichier", files[i], files[i].name);
				return function() {
					$.ajax({
						url: document.location.href,
						type: 'POST',
						data: fileForm,
						processData: false,
						contentType: false,
						error: function() {
							finishWith(numFiles,fileCalls,false);
						},
						complete: function(xhr,status) {
							var newBlock = $(xhr.responseText).find('#sources');
							finishWith(numFile,fileCalls,newBlock);
						}
					});
				};
			}();// fileCalls[i]
		}
		fileCalls[0]();
	}
});
