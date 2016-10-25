$(function() {
	$(".new_scorecard").keyup(function() {
		var search = $(this).val();
		$(".scorecard_line").each(function() {
			var nom = $(this).find(".nom").html().toLowerCase();
			if(nom.match(search)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		if($(".scorecard_line:visible").size() == 0) {
			$(".scorecard_line").show();
		}
	});
});
