/* -- This is the set of function that help to control user view controls -- */
function setViewToggleListeners() {
	console.log("setViewToggleListeners()");

	$("a.viewToggle").click(function () {
		console.log("toggle view");
		category = $(this).attr('category');
		console.log(" --  "+category);

		$("div.mainView").removeClass("inactiveView");
		$("div.mainView").removeClass("activeView");
		$("div.mainView").hide();

		$('div#'+category).addClass("activeView");
		$('div#'+category).show();
	});


	$("button.displayToggle").click(function () {
		// Target is the element we are going to display
		target = $('div#'+$(this).attr('target'));
		console.log("button.displayToggle: "+$(this).attr('target'));

		// Level : where we are focusing the toggle so we don't impact all buttons
			// Panel: A top level display
			// Window: A sub-level display
	
		$(this).siblings().removeAttr('disabled');
		$(this).attr('disabled', '');

		target.siblings().hide();
		target.show();


	});


}