/* -- This is the set of function that help to control user view controls -- */
function setInputListeners() {
	
}

function setInfoToggleListeners() {
	console.log("setInfoToggleListeners()");

	$("a.infoToggle").click(function () {
		console.log("toggle info view");
		category = $(this).attr('category');
		console.log(" --  "+category);


		$("div.mainView").removeClass("inactiveView");
		$("div.mainView").removeClass("activeView");
		$("div.mainView").hide();

		$('div#'+category).addClass("activeView");
		$('div#'+category).show();
	});
}