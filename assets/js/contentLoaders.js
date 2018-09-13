// fetches a collection of top stream thumbnails
function fetchTopStreams(tgt) {
	console.log("fetchTopStreams("+tgt.attr('id')+")");
	thisActionUrl = baseURL+"/servlet/getTopStreamsFromMixer/";

	$.ajax({
		url: thisActionUrl,
		type: "POST",
		dataType: "json",
		data: {
			type: tgt.data('grouptype'),
			id:  tgt.data('groupid')
		}
	})
		.always(function (json){
			console.log('fetchTopStreams - AJAX always');
			console.log(json);
			//console.log(json.message);
		})
		.done(function (json){
			console.log('fetchTopStreams - AJAX done');
			streamCount = json.streams.length;

			tgt.empty();

			if (streamCount > 0) {
				tgt.append("<div class='streamerList row'></div>");
				
				for (i=0; i<streamCount; i++) {
					insertElement = "<div class=\"streamerListing mini\">";
					//echo "<img src=\"".$stream['user']['avatarUrl']."\" width=\"100\" class=\"avatar\" />";
					insertElement +=  "<a href=\"/user/"+json.streams[i].token+"\"><img class=\"live-thumb list\" src=\"https://thumbs.mixer.com/channel/"+json.streams[i].id+".small.jpg\" onerror=\"this.src='/assets/graphics/blankThumb.jpg'\" width=\"175\"/></a>";
					insertElement += "<p class=\"streamerName\"><a href=\"/user/"+json.streams[i].token+"\">"+json.streams[i].token+"</a></p>";
					insertElement += "<p class=\"streamerStats\"><span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Views\"><i class=\"fas fa-eye\"></i> "+json.streams[i].viewersCurrent+"</span> | <span class=\"onlineStat\" data-placement=\"bottom\"data-toggle=\"tooltip\" title=\"Current Followers\"><i class=\"fas fa-user-circle\"></i> "+json.streams[i].numFollowers+"</p>";
					insertElement += "</span></div>";

					$(tgt).children(".row").append(insertElement);
				}
			} else {
				tgt.append("<div class=\"alert alert-dark\"><p>We didn't find any streams.</p></div>");
			}
		}) 

		.fail(function (json){
			console.log('fetchTopStreams - AJAX failed');
			//tgt.html("<div class=\"alert alert-danger\"><p>There was a problem in contacting the server. Pick another game and try again.</p></div>");
		});
}



// fetches streamer data and fills a user data table
function fetchStreamersList(tgt) {
	console.log("fetchStreamersList("+tgt.attr('id')+")");
	tgt = $("#"+tgt.attr('id')); 
	thisActionUrl = baseURL+"/search/getStreamersByGroup/";

	//console.log("type: "+tgt.data('grouptype'))
	//console.log("id: "+tgt.data('groupid'))

	$.ajax({
		url: thisActionUrl,
		type: "POST",
		dataType: "json",
		data: tgt.data()
	})
		.always(function (json){
			console.log('fetchStreamersList - AJAX always');
			console.log(json);
			//console.log(json.message);
		})
		.done(function (json){
			console.log('fetchStreamersList - AJAX done');

			if (json.success) {
				tgt.children("tbody").empty();

				//resultCount = json.results.length;

				json.results.forEach(function(item){
					isOffline = false;

					if (item.LastSeenOnline_Elapsed > (60*10)) {
						isOffline = true; 
						row = "<tr class=\"offlineStream\">";
					} else {
						row = "<tr class=\"onlineStream\">";
					}

				 	row += "<td data-username=\""+item.Username+"\"><img src=\""+item.AvatarURL+"\" onerror=\"this.src='https://mixer.com/_latest/assets/images/main/avatars/default.png';\" class=\"avatar thin-border\" width=\"25px\" /> <a href=\"/user/"+item.Username+'">'+item.Username+"</a></td>";

				 	
					 		if (item.LastStreamStart != "0000-00-00 00:00:00") {
					 			row += "<td>"+item.StreamCount+"</td>";
					 		} else {
					 			row += "<td>-</td>";
					 		}



				 		if (item.NumFollowers < 25) {
				 			row += '<td class="never">Not tracked</td>'
				 		} else {

					 		if (item.LastTypeTime_Elapsed != undefined) {
					 			if (!isOffline && item.LastTypeID == tgt.data('groupid')) {
					 				row += '<td class="online">Online: '+getElapsedTimeString(item.LastTypeTime_Elapsed)+'</td>';
					 			} else {
					 				row += '<td>'+getElapsedTimeString(item.LastTypeTime_Elapsed)+'</td>';
					 			}
					 		} else {
					 			if (item.LastStreamStart != "0000-00-00 00:00:00") {
						 			if (isOffline) {
										row += '<td class="offline">Offline since '+getElapsedTimeString(item.LastSeenOnline_Elapsed)+'</td>';
						 			} else {
										row += '<td class="online">Online since '+getElapsedTimeString(item.LastStreamStart_Elapsed)+'</td>';
						 			}
						 		} else {
						 			row += '<td class="never">Never Seen</td>'; 
						 		}
						 	
					 		}
				 		}
						row += '<td><a href="/type/'+item.LastTypeID+'/'+getUrlSlug(item.LastType)+'">'+item.LastType+'</a></td>';
						row += '<td>'+addCommas(item.NumFollowers)+'</td>';
						row += '<td>'+addCommas(item.ViewersTotal)+'</td>';

					row += "</tr>"

					tgt.children("tbody").append(row);
				});
			}
		}) 

		.fail(function (json){
			console.log('fetchStreamersList - AJAX failed');
			//tgt.html("<div class=\"alert alert-danger\"><p>There was a problem in contacting the server. Pick another game and try again.</p></div>");
		});

}

// fetches news and populates display
function fetchNewsFeed(tgt) {
	if (tgt.length > 0) {
		console.log("fetchNewsFeed("+tgt.attr('id')+")");

		feedParams = { 
			groupid: tgt.data('groupid'),
			limit: 10 }

		if (tgt.data('limit') > 0) { 
			if (tgt.data('limit') <= 100) { feedParams['limit'] = tgt.data('limit'); } else { feedParams['limit'] = 100 }
		}


		if (tgt.data('displaysize') == undefined) { displaySize = "med"; }
			else { displaySize = tgt.data('displaysize')}

		thisActionUrl = baseActionUrl+"getNewsFeed/";

		$.ajax({
			url: thisActionUrl,
			type: "POST",
			dataType: "json",
			data: {
				groupType: tgt.data('grouptype'),
				displaySize: displaySize,
				feedParams: feedParams
			}
		})
			.done(function (json){
				console.log('fetchNewsFeed - AJAX done');
				tgt.empty();
				if (json.success) {
					newsCount = json.displayItems.length;
					if (newsCount > 0) {

						for (i = 0; i<newsCount; i++) {
							tgt.append(json.displayItems[i]);
						}
					} else {
						tgt.append("<p>No news items were found.");
					}
				}
			}) 

			.fail(function (json){
				console.log('fetchNewsFeed - AJAX failed');
				tgt.html("<div class=\"alert alert-danger\"><p>There was a problem in contacting the MixMingler server.</p></div>");
			})

			.always(function (json){
				console.log('fetchNewsFeed - AJAX always');
				console.log(json);
				//console.log(json.message);
			});
	}
}