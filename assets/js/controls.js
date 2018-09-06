var baseURL = window.location.protocol + "//" + window.location.host;
var baseActionUrl = baseURL+"/servlet/";

(function(){
	console.log('MixMingler Start!!');
	console.log("baseURL:" + baseURL);
	runNewsCollection();

	$('[data-toggle="tooltip"]').tooltip();

	// $(".userlist").tablesorter(); 
	addUserTableSorter();

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
	setSearchFilterFunctionality();
	if ($('form#filterStreamers').length > 0) {
		getStreamersData($('form#filterStreamers'));
	}
	

	$( ".typeInfo" ).hover(function() { 
		$(this).children('.btnGroupContainer').slideToggle(); });


	// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function() {scrollFunction()};

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
			location.reload();
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

function addUserTableSorter() {
	$.tablesorter.addParser({
		// set a unique id
		id: 'data',
		is: function(s, table, cell, $cell) {
			// return false so this parser is not auto detected
			return false;
		},
		format: function(s, table, cell, cellIndex) {
		  var $cell = $(cell);
			// I could have used $(cell).data(), then we get back an object which contains both
			// data-lastname & data-date; but I wanted to make this demo a bit more straight-forward
			// and easier to understand.

			// first column (zero-based index) has lastname data attribute
			if (cellIndex === 0) {
			// returns lastname data-attribute, or cell text (s) if it doesn't exist
			return $cell.attr('data-username') || s;

			// third column has date data attribute
			} else if (cellIndex === 1) {
			// return "mm-dd" that way we don't need to use "new Date()" to process it
			return $cell.attr('data-time') || s;
			

			// third column has date data attribute
			} else if (cellIndex === 2) {
			// return "mm-dd" that way we don't need to use "new Date()" to process it
			return $cell.attr('data-date') || s;

			// third column has date data attribute
			} else if (cellIndex === 3) {
			// return "mm-dd" that way we don't need to use "new Date()" to process it
			return $cell.attr('data-followers') || s;
			
			// third column has date data attribute
			} else if (cellIndex === 4) {
			// return "mm-dd" that way we don't need to use "new Date()" to process it
			return $cell.attr('data-views') || s;
			}

			// return cell text, just in case
			return s;
		},
		// flag for filter widget (true = ALWAYS search parsed values; false = search cell text)
		parsed: false,
		// set type, either numeric or text
		type: 'text'
	});

	jQuery.tablesorter.addParser({
	  id: "fancyNumber",
	  is: function(s) {
	    return /^[0-9]?[0-9,\.]*$/.test(s);
	  },
	  format: function(s) {
	    return jQuery.tablesorter.formatFloat( s.replace(/,/g,'') );
	  },
	  type: "numeric"
	});

	$('.userList').tablesorter({
		theme: 'blue',
		headers: {
			0 : { ignoreArticles : 'en' },
		 	1 : { sorter: 'data' },
		 	3 : { sorter: 'fancyNumber' },
		 	4 : { sorter: 'fancyNumber' },
		},
		widgets: ['zebra'],
		sortList: [[1,0]],
		 textExtraction: {
	      0: function(node, table, cellIndex) { return $(node).find("a").text(); }
	    }
	});
}



function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("topButton").style.display = "block";
    } else {
        document.getElementById("topButton").style.display = "none";
    }
}

function getElapsedTimeString(elapsedTime) {
		/*timecode =  Math.ceil(Date.parse(timestamp)/1000)+10800;
		now =  Math.ceil(Date.now()/1000);

		elapsedTime = Math.ceil( (now - timecode));*/

		// If under 10 seconds
		if (elapsedTime < 10) {
			return "Just now!";
		}

		// If under one minute
		if (elapsedTime < 60) {
			return elapsedTime+" seconds ago";
		}

		// If under one hour
		if (elapsedTime < (60 * 60)) {
			if (Math.ceil(elapsedTime/60) == 1) {
				return Math.ceil(elapsedTime/60)+" minute ago";
			} 
			return Math.ceil(elapsedTime/60)+" minutes ago";
		}

		// If under one day ago
		if (elapsedTime < (60 * 60 * 24)) {
			if (Math.ceil(elapsedTime/(60*60)) == 1) {
				return Math.ceil(elapsedTime/(60*60))+" hour ago";
			} 
			return Math.ceil(elapsedTime/(60*60))+" hours ago";
		}

		// If over 24 hours
		if (elapsedTime >= (60 * 60 * 24)) {
			if (Math.ceil(elapsedTime/(60*60)) == 1) {
				return Math.ceil(elapsedTime/(60*60*24))+" day ago";
			} 
			return Math.ceil(elapsedTime/(60*60*24))+" days ago";
		}
	}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}