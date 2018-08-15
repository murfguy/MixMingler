var baseURL = window.location.protocol + "//" + window.location.host;
var baseActionUrl = baseURL+"/servlet/";

(function(){
	console.log('MixMingler Start!!');
	console.log("baseURL:" + baseURL);
	runNewsCollection();

	$('[data-toggle="tooltip"]').tooltip();

	// Convert follow/ignore type buttons based on display state
	console.log("classes: " + $("div.actionButtons.types").attr("class"));
	if ( $("div.actionButtons.types").hasClass("followed") ) {
		// Hide ignore, make "follow" button into "unfollow"
		$("#ignore").hide();

		$("#follow").removeClass('btn-primary');
		$("#follow").addClass('btn-danger');
		$("#follow").text('Unfollow');
		$("#follow").attr('title','Stop getting updates about this game.');


		$("#follow").attr('action','unfollowType');
		$("#follow").attr('id','unfollow');
	}

	if  ( $("div.actionButtons.types").hasClass("ignored") ) {
		console.log("you ignore this game");
		// Hide follow, make "ignore" button into "unignore"
		$("#follow").hide();

		$("#ignore").removeClass('btn-warning');
		$("#ignore").addClass('btn-danger');
		$("#ignore").text('Unignore');
		$("#ignore").attr('title','Have this game show up in lists again.');

		$("#follow").attr('action','unignoreType');
		$("#ignore").attr('id','unignore');
	}


	$("a.commToggle").click(function () {
		console.log("toggle community");
		category = $(this).attr('category');
		console.log(" --  "+category);

		if (category != "all") {
			$(".communityListing").hide();
			$("."+category).show();
		} else {
			$(".communityListing").show();
		}

		switch (category) {
			case "all":
				$("#communitiesList h2 span.mixBlue").text("All");
				$("#communityDescription").text("Well, here's all the communities, ranked by popularity. If you want to drill down further, just check the categories above.");
				break;
			case "style":
				$("#communitiesList h2 span.mixBlue").text("Style");
				$("#communityDescription").text("Some streamers are chill. Others are competitive. Others wear funny costumes. Regardless of the style you seek, you can find them all here.");
				break;
			case "content":
				$("#communitiesList h2 span.mixBlue").text("Content");
				$("#communityDescription").text("Want to find streamers by the kind of content they provide, or the genre of games they play? Well look no further.");
				break;
			case "platform":
				$("#communitiesList h2 span.mixBlue").text("Platform");
				$("#communityDescription").text("Pop on in, and find allies in your faction of the great Console Wars!");
				break;
			case "region":
				$("#communitiesList h2 span.mixBlue").text("Regional");
				$("#communityDescription").text("Find local hot streamers in your area!");
				break;
			case "game":
				$("#communitiesList h2 span.mixBlue").text("Game");
				$("#communityDescription").text("For some streamers, one game and one game alone is life. Want to find someone who lives and breathes your favorite game? Then you've come to the right spot!");
				break;
			case "streamers":
				$("#communitiesList h2 span.mixBlue").text("Streamer");
				$("#communityDescription").text("Have a certain streamer you love? Ones you adore and count yourself amongst the diehards? Then come and in be counted as a member of their community!");
				break;
			case "misc":
				$("#communitiesList h2 span.mixBlue").text("Misc.");
				$("#communityDescription").text("For everything else.");
				break;
		}
	});
	
	setNewsToggles();
	setViewToggleListeners();
	setFormListeners();

})();

function logout(tgtUser) {
	$.ajax({
		url: baseURL+"/auth/logout/",
		type: "POST",
		dataType: "json"
	})
		.done(function (json){
			console.log('logout - AJAX done');
			document.cookie = 'mixer_user=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			window.location.href = "/";
			if (json.success == true) {
				//console.log(json);
			}	

		}) 

		.fail(function (json){
			console.log('logout - AJAX failed');
		})

		.always(function (json){
			console.log('logout - AJAX always');
			console.log(json);
			//console.log(json.message);
		});
}

function hidePharError() {
	//console.log('hidePharError()');
	//$("p:contains('Message: Module')").parent().css("background-color", "#000");
}