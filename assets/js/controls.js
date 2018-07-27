var baseURL = window.location.protocol + "//" + window.location.host;
var baseActionUrl = baseURL+"/servlet/";

(function(){
	console.log('MixMingler Start!!');
	console.log("baseURL:" + baseURL);


	$('[data-toggle="tooltip"]').tooltip();

	hidePharError();
	
	$("#protoLogin").submit(function (event) {
		console.log("submitting!");
		event.preventDefault();

		mixer_name = $("#inputMixerName").val();
		if (mixer_name != "") {
			login(mixer_name);
		}
	})

	$("button.commAction").click(function () {
		actionUrl = baseActionUrl;
		switch($(this).attr('id')) {
			case "join": 
				actionUrl += "joinCommunity/";

				$(this).removeClass('btn-primary');
				$(this).addClass('btn-danger');
				$(this).text('Leave');
				$(this).attr('id','leave');
				$(this).attr('title','Leave this community.');

				break;
			case "leave": 
				actionUrl += "leaveCommunity/";

				$(this).removeClass('btn-danger');
				$(this).addClass('btn-primary');
				$(this).text('Join');
				$(this).attr('id','join');
				$(this).attr('title','Become a member of this community so viewers can find you.');
				break;
			case "follow": 
				actionUrl += "followCommunity/";

				$(this).removeClass('btn-primary');
				$(this).addClass('btn-danger');
				$(this).text('Unfollow');
				$(this).attr('id','unfollow');
				$(this).attr('title','Stop getting updates from this community on your profile.');
				break;
			case "unfollow": 
				actionUrl += "unfollowCommunity/";

				$(this).removeClass('btn-danger');
				$(this).addClass('btn-primary');
				$(this).text('Follow');
				$(this).attr('id','follow');
				$(this).attr('title','Track streamers in this community from your profile page.');
				break;
		}
		console.log($(this).attr('commId'));
		actionUrl += $(this).attr('commId');

		console.log(actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json"
		})
			.done(function (json){
				console.log('commAction - AJAX done');
			}) 

			.fail(function (json){
				console.log('commAction - AJAX failed');
			})

			.always(function (json){
				console.log('commAction - AJAX always');
				console.log(json);
				//console.log(json.message);
			});


	});

	console.log("classes: " + $("div.actionButtons.types").attr("class"));
	if ( $("div.actionButtons.types").hasClass("followed") ) {
		// Hide ignore, make "follow" button into "unfollow"
		$("#ignore").hide();

		$("#follow").removeClass('btn-primary');
		$("#follow").addClass('btn-danger');
		$("#follow").text('Unfollow');
		$("#follow").attr('title','Stop getting updates about this game.');


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


		$("#ignore").attr('id','unignore');
	}


	$("button.typeAction").click(function () {

		actionUrl = baseActionUrl;
		console.log("button.typeAction:" + $(this).attr('typeId'));
		switch($(this).attr('id')) {
			case "follow": 
				actionUrl += "followType/";

				$(this).removeClass('btn-primary');
				$(this).addClass('btn-danger');
				$(this).text('Unfollow');
				$(this).attr('id','unfollow');
				$(this).attr('title','Stop getting updates about this game.');
				// show ignore button

				$("#ignore").hide();
				break;

			case "unfollow": 
				actionUrl += "unfollowType/";

				$(this).removeClass('btn-danger');
				$(this).addClass('btn-primary');
				$(this).text('Follow');
				$(this).attr('id','follow');
				$(this).attr('title','Get updates about this game.');
				// hide ignore button
				$("#ignore").show();
				break;

			case "ignore": 
				actionUrl += "ignoreType/";

				$(this).removeClass('btn-warning');
				$(this).addClass('btn-danger');
				$(this).text('Unignore');
				$(this).attr('id','unignore');
				$(this).attr('title','Have this game show up in lists again.');
				// hide follow button
				$("#follow").hide();
				break;

			case "unignore": 
				actionUrl += "unignoreType/";

				$(this).removeClass('btn-danger');
				$(this).addClass('btn-warning');
				$(this).text('Ignore');
				$(this).attr('id','ignore');
				$(this).attr('title','Hide this game in lists.');
				// show ignore button
				$("#follow").show();
				break;
		};

		console.log($(this).attr('typeId'));
		actionUrl += $(this).attr('typeId');

		console.log("actionUrl: "+ actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json"
		})
			.done(function (json){
				console.log('typeAction - AJAX done');
			}) 

			.fail(function (json){
				console.log('typeAction - AJAX failed');
			})

			.always(function (json){
				console.log('typeAction - AJAX always');
				console.log(json);
				//console.log(json.message);
			});
	});

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

	console.log("HIDE!");
	$("a.typeToggle").click(function () {
		console.log("toggle type view");
		category = $(this).attr('category');
		console.log(" --  "+category);

		switch (category) {
			case "followed":
				$("div#followed").show();
				$("div#allActive").hide();
				break;

			case "active":
				$("div#followed").hide();
				$("div#allActive").show();
				break;
			}
	});


	$("div.inactiveView").hide();
	console.log("HIDE!");
	$("a.viewToggle.accountTypes").click(function () {
		console.log("toggle account type management view");
		category = $(this).attr('category');
		console.log(" --  "+category);

		switch (category) {
			case "followed":
				$("div#followed").show();
				$("div#ignored").hide();
				break;

			case "ignored":
				$("div#followed").hide();
				$("div#ignored").show();
				break;
			}
	});


	var coreLimit = 4;
	$('input.coreCommunities').on('change', function(evt) {
		console.log();

		thisActionUrl = baseActionUrl;
		if($("input[name='core']:checked").length > coreLimit) {
			this.checked = false;
			alert("You cannot select more than "+coreLimit+" Core Communities!");
		} else {
			commId = $(this).attr("commId");
			if ($(this).is(':checked')) {
				thisActionUrl += "setCoreCommunity/";
				//console.log("this has been checked");
			} else {
				thisActionUrl += "unsetCoreCommunity/";
				//console.log("this has been unchecked");
			}
			thisActionUrl += commId+"/";

			console.log(thisActionUrl);

			$.ajax({
				url: thisActionUrl,
				type: "POST",
				dataType: "json"
			})
				.done(function (json){
					console.log('commAction - AJAX done');
				}) 

				.fail(function (json){
					console.log('commAction - AJAX failed');
				})

				.always(function (json){
					console.log('commAction - AJAX always');
					console.log(json);
					//console.log(json.message);
				});
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
	console.log('hidePharError()');
	$("p:contains('Message: Module')").parent().css("background-color", "#000");
}