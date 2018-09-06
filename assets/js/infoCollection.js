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

function getStreamersData(form, e) {
	if (e != undefined) {
		e.preventDefault
	}

	if (form == undefined) {
		filterData = null;
	} else {
		filterData = form.serialize();
	}

	$("button.filterStreamers").html("<i class=\"fas fa-spinner fa-pulse\"></i> Fetching Streamers");
	$("button.filterStreamers").removeClass("btn-primary");
	$("button.filterStreamers").addClass("btn-dark");
	$("button.filterStreamers").attr("disabled", "");

	thisActionUrl = baseURL+"/search/getStreamers/";

	$.ajax({
		url: thisActionUrl,
		type: "POST",
		dataType: "json",
		data: filterData
		
	})
		.always(function (json){
			console.log('getStreamersData - AJAX always');
			console.log(json);
			
			$("button.filterStreamers").html("Get Streamers");
			$("button.filterStreamers").removeClass("btn-dark");
			$("button.filterStreamers").addClass("btn-primary");
			$("button.filterStreamers").removeAttr("disabled");
			//console.log(json.message);
		})
		.done(function (json){
			console.log('getStreamersData - AJAX done');

			if (json.success) {
				console.log(json.message);
				$("#streamerSearchList tbody").empty();

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

				 		if (item.NumFollowers < 25) {
				 			row += '<td class="never">Not tracked</td>'
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
						row += '<td><a href="/type/'+item.LastTypeID+'/'+getUrlSlug(item.LastType)+'">'+item.LastType+'</a></td>';
						row += '<td>'+addCommas(item.NumFollowers)+'</td>';
						row += '<td>'+addCommas(item.ViewersTotal)+'</td>';

					row += "</tr>"

					$("#streamerSearchList tbody").append(row);
				});
			}
		}) 

		.fail(function (json){
			console.log('getStreamersData - AJAX failed');
			//tgt.html("<div class=\"alert alert-danger\"><p>There was a problem in contacting the server. Pick another game and try again.</p></div>");
		});

}

function setSearchFilterFunctionality() {
	$("form#filterStreamers").on('submit', function (e) { 
		e.preventDefault();

		getStreamersData($("form#filterStreamers"), event)} );

	$( function() {
	    $( "#follower-range" ).slider({
	      range: true,
	      min: 1,
	      max: 10,
	      values: [ 1, 10 ],
	      slide: function( event, ui ) {
	        $( "#followers" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
	      }
	    });
	    $( "#followers" ).val( "$" + $( "#follower-range" ).slider( "values", 0 ) +
	      " - $" + $( "#follower-range" ).slider( "values", 1 ) );
  } );
}

function getUrlSlug(str) {
	fixed = str.replace(/[^a-zA-Z0-9\-\s]/g, '');
	fixed = fixed.replace(/[\-\s]/g, '_');
	return fixed;
}