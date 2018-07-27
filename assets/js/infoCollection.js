function setNewsToggles() {
	$(".newsFeed").hide();
	$("a.newsToggle").on('click', function () {
		console.log("clicked: #" + $(this).data("newstype") + "-"+ $(this).data("typeid"));
		$(".newsFeed").hide();
		$("#" + $(this).data("newstype") + "-"+ $(this).data("typeid")).show();

		getTopStreams($(this));
		getTypeNewsFeed($(this));

	});
}


function getTopStreams(target) {
	thisActionUrl = baseActionUrl+"getTopStreamsForType/"+target.data("typeid");

		$.ajax({
				url: thisActionUrl,
				type: "POST",
				dataType: "json"
			})
				.done(function (json){
					console.log('getTopStreams - AJAX done');
					console.log("json.typeID: "+json.typeID);

					streamCount = json.streams.length;
					$("div#type-"+json.typeID).empty();
					$("div#type-"+json.typeID).append("<h4>Current Top Streams</h4>");

					if (streamCount > 0) {
						$("div#type-"+json.typeID).append("<div class='streamerList row'></div>");

						
						for (i=0; i<streamCount; i++) {
							insertElement = "<div class=\"streamerListing mini\">";
							//echo "<img src=\"".$stream['user']['avatarUrl']."\" width=\"100\" class=\"avatar\" />";
							insertElement +=  "<a href=\"/user/"+json.streams[i].token+"\"><img class=\"live-thumb list\" src=\"https://thumbs.mixer.com/channel/"+json.streams[i].id+".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"175\"/></a>";
							insertElement += "<p class=\"streamerName\"><a href=\"/user/"+json.streams[i].token+"\">"+json.streams[i].token+"</a></p>";
							insertElement += "<p class=\"streamerStats\"><span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Views\"><i class=\"fas fa-eye\"></i> "+json.streams[i].viewersCurrent+"</span> | <span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Followers\"><i class=\"fas fa-user-circle\"></i> "+json.streams[i].numFollowers+"</p>";
							insertElement += "</span></div>";

							$("div#type-"+json.typeID+" > .row").append(insertElement);
						}
					} else {
						$("div#type-"+json.typeID).append("<p>No one is streaming this game right now.</p>");
					}

				}) 

				.fail(function (json){
					console.log('getTopStreams - AJAX failed');
				})

				.always(function (json){
					console.log('getTopStreams - AJAX always');
					console.log(json);
					//console.log(json.message);
				});
}

function getTypeNewsFeed(target) {
	thisActionUrl = baseActionUrl+"getNewsForType/"+target.data("typeid");
	console.log("getTypeNewsFeed("+target.data("typeid")+")")
		$.ajax({
				url: thisActionUrl,
				type: "POST",
				dataType: "json"
			})
				.done(function (json){
					console.log('getNewsFeed - AJAX done');
					console.log("json.typeID: "+json.typeID);

					newsDiv = $("div#news-"+json.typeID);
					newsDiv.empty();

					if(json.success) {
						//newsDiv.prepend("<>Collected news!</p>");
						newsCount = json.newsFeed.length;

						for (i = 0; i<newsCount; i++) {
							newsDiv.append(json.newsFeed[i]);
						}

					} else {
						newsDiv.prepend("<p>There was an problem with collecting the news:<br>"+json.message+"</p>");
					}

					/*if (streamCount > 0) {
						$("div#type-"+json.typeID).append("<div class='streamerList row'></div>");

						
						for (i=0; i<streamCount; i++) {
							insertElement = "<div class=\"streamerListing mini\">";
							//echo "<img src=\"".$stream['user']['avatarUrl']."\" width=\"100\" class=\"avatar\" />";
							insertElement +=  "<a href=\"/user/"+json.streams[i].token+"\"><img class=\"live-thumb list\" src=\"https://thumbs.mixer.com/channel/"+json.streams[i].id+".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"175\"/></a>";
							insertElement += "<p class=\"streamerName\"><a href=\"/user/"+json.streams[i].token+"\">"+json.streams[i].token+"</a></p>";
							insertElement += "<p class=\"streamerStats\"><span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Views\"><i class=\"fas fa-eye\"></i> "+json.streams[i].viewersCurrent+"</span> | <span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Followers\"><i class=\"fas fa-user-circle\"></i> "+json.streams[i].numFollowers+"</p>";
							insertElement += "</span></div>";

							$("div#type-"+json.typeID+" > .row").append(insertElement);
						}
					} else {
						$("div#type-"+json.typeID).append("<p>No one is streaming this game right now.</p>");
					}*/

				}) 

				.fail(function (json){
					console.log('getNewsFeed - AJAX failed');
				})

				.always(function (json){
					console.log('getNewsFeed - AJAX always');
					console.log(json);
					//console.log(json.message);
				});
}