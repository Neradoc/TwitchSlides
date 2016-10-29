$(function() {
	$('.twitter_exemple_message').click(function() {
		var message = $(this).data("message");
		$('.twitter_message').val(message);
	});
	$('.twitter_message').elastic();
	// afficher
	$('.screen .btns .twitter').click(function() {
		var img = $(this).closest('.screen').find('.image').attr("src");
		if(img && !$(this).is(".disabled")) {
			$('.twitter_impetrant img').attr("src",img);
			$('.twitter_screen').val($(this).val());
			$('#twitter_window').show();
			$('#black_block').show();
		}
		return false;
	});
	// fermer
	$('#black_block, #twitter_window .twitter_fermer').click(function() {
		$('.twitter_screen').val(0);
		$('#twitter_window').hide();
		$('#black_block').hide();
		return false;
	});
	//valider
	$('#twitter_window .twitter_envoyer').click(function() {
		var message = $('.twitter_message').val();
		var screen = $('.twitter_screen').val();
		if(message.length > 140) {
			$('.twitter_error').html("Vous avez dépassé les 140 caractères");
			return false;
		}
		if(message.length == 0) {
			$('.twitter_error').html("Ben alors, pas de message ?");
			return false;
		}
		$('#twitter_window').hide();
		$('#black_block').hide();
	});
});
