function setNewsToggles() {
	$(".newsFeed").hide();

	$("a.newsToggle").on('click', function () {
		targetId = $(this).data("newstype") + "-"+ $(this).data("id");
		console.log("clicked: #" + targetId);
		$(".newsFeed").hide();
		$("#" + $(this).data("newstype") + "-"+ $(this).data("id")).show();

		getTopStreams($(this));
		//getRecentNews($(this));
		getNewsFeed($("#" + targetId +" > #news-"+$(this).data("id")))
	});
}


function getTopStreams(target) {
	group = target.data("newstype");
	
	urlAction = group.substr(0,1).toUpperCase()+group.substr(1);
	thisActionUrl = baseActionUrl+"getTopStreamsFor"+urlAction+"/"+target.data("id");

	listDiv ="div#"+group+"-"+target.data("id") + " > div#streams-"+target.data("id");

	console.log("getTopStreams("+thisActionUrl+")");

	$.ajax({
		url: thisActionUrl,
		type: "POST",
		dataType: "json"
	})
		.done(function (json){
			console.log('getTopStreams - AJAX done');
			console.log("listDiv: "+listDiv);

			streamCount = json.streams.length;

			$(listDiv).empty();

			if (streamCount > 0) {
				$(listDiv).append("<div class='streamerList row'></div>");
				
				for (i=0; i<streamCount; i++) {
					insertElement = "<div class=\"streamerListing mini\">";
					//echo "<img src=\"".$stream['user']['avatarUrl']."\" width=\"100\" class=\"avatar\" />";
					insertElement +=  "<a href=\"/user/"+json.streams[i].token+"\"><img class=\"live-thumb list\" src=\"https://thumbs.mixer.com/channel/"+json.streams[i].id+".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"175\"/></a>";
					insertElement += "<p class=\"streamerName\"><a href=\"/user/"+json.streams[i].token+"\">"+json.streams[i].token+"</a></p>";
					insertElement += "<p class=\"streamerStats\"><span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Views\"><i class=\"fas fa-eye\"></i> "+json.streams[i].viewersCurrent+"</span> | <span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Followers\"><i class=\"fas fa-user-circle\"></i> "+json.streams[i].numFollowers+"</p>";
					insertElement += "</span></div>";

					$(listDiv +" > .row").append(insertElement);
				}
			} else {
				$(listDiv).append("<div class=\"alert alert-dark\"><p>We didn't find any streams, sorry.</p></div>");
			}

		}) 

		.fail(function (json){
			console.log('getTopStreams - AJAX failed');
			$("div#type-"+json.typeID).append("<div class=\"alert alert-danger\"><p>There was an issue when attempting to contact the server. Please pick something else and try again.</p></div>");
		})

		.always(function (json){
			console.log('getTopStreams - AJAX always');
			console.log(json);
			//console.log(json.message);
		});
}

/*function getRecentNews(target) {
	group = target.data("newstype");
	
	urlAction = group.substr(0,1).toUpperCase()+group.substr(1);
	thisActionUrl = baseActionUrl+"getNewsFor"+urlAction+"/"+target.data("id");

	newsDiv ="div#"+group+"-"+target.data("id") + " > div#news-"+target.data("id");

	console.log("getRecentNews("+thisActionUrl+")");

	//thisActionUrl = baseActionUrl+"getNewsForType/"+target.data("typeid");
	//console.log("getNewsFeed("+target.data("typeid")+")")

		$.ajax({
				url: thisActionUrl,
				type: "POST",
				dataType: "json"
			})
				.done(function (json){
					console.log('getRecentNews - AJAX done');
					console.log("listDiv: "+listDiv);
					//console.log("json.typeID: "+json.typeID);

					newsDiv = $(newsDiv);
					newsDiv.empty();

					if(json.success) {
						//newsDiv.prepend("<>Collected news!</p>");
						newsCount = json.displayItems.length;

						for (i = 0; i<newsCount; i++) {
							newsDiv.append(json.displayItems[i]);
						}

					} else {
						newsDiv.prepend("<div class=\"alert alert-danger\"><p>There was an problem with collecting the news:<br>"+json.message+"</p></div>");
					}

				}) 

				.fail(function (json){
					console.log('getRecentNews - AJAX failed');
					newsDiv = $("div#news-"+json.id);
					newsDiv.prepend("<div class=\"alert alert-danger\"><p>There was a problem in contacting the server. Pick another game and try again.</p></div>");
				})

				.always(function (json){
					console.log('getRecentNews - AJAX always');
					console.log(json);
					//console.log(json.message);
				});
}*/

function runNewsCollection() {
	console.log("runNewsCollection()");
	getNewsFeed($('div#userNewsFeed'));
	getNewsFeed($('div#communityNewsFeed'));
}

function getNewsFeed(tgt) {
	console.log("getNewsFeed("+tgt.attr('id')+")");
	if (tgt.length > 0) {
		console.log("getNewsFeed("+tgt.attr('id')+")");
		//console.log(" -- feedtype: "+ tgt.data('feedtype'));
		switch (tgt.data('feedtype')) {
			case "user":
				feedParams = {
					mixerId: tgt.data('userid')}
				break;

			case "type":
				feedParams = {
					typeId: tgt.data('typeid')}
				break;

			case "community":
				feedParams = {
					communityId: tgt.data('communityid')}
				break;
		}
		feedParams['limit'] = 10;
		if (tgt.data('limit') > 0) { feedParams['limit'] = tgt.data('limit') }

		feedParams['']

		if (tgt.data('displaysize') == undefined) { displaySize = "med"; }
			else { displaySize = tgt.data('displaysize')}

		thisActionUrl = baseActionUrl+"getNewsFeed/";

		$.ajax({
			url: thisActionUrl,
			type: "POST",
			dataType: "json",
			data: {
				feedType: tgt.data('feedtype'),
				displaySize: displaySize,
				feedParams: feedParams
			}
		})
			.done(function (json){
				console.log('getNewsFeed - AJAX done');
				tgt.empty();
				if (json.success) {
					newsCount = json.displayItems.length;
					if (newsCount > 0) {

						for (i = 0; i<newsCount; i++) {
							tgt.append(json.displayItems[i]);
						}
					} else {
						tgt.append("<p>No news items for this user.");
					}
				}
			}) 

			.fail(function (json){
				console.log('getNewsFeed - AJAX failed');
				tgt.html("<div class=\"alert alert-danger\"><p>There was a problem in contacting the server. Pick another game and try again.</p></div>");
			})

			.always(function (json){
				console.log('getNewsFeed - AJAX always');
				console.log(json);
				//console.log(json.message);
			});
	}

}