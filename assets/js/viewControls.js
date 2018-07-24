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
}