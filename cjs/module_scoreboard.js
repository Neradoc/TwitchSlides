$(function() {
	$(".scoreboard_new_nom").keyup(function() {
		var search = $(this).val().toLowerCase();
		$(".scoreboard_line").each(function() {
			var nom = $(this).find(".nom").html().toLowerCase();
			if(nom.match(search)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		if($(".scoreboard_line:visible").size() == 0) {
			$(".scoreboard_line").show();
		}
	});
	$(".scoreboard_index").change(function() {
		$(this).closest("form").submit();
	});
});
