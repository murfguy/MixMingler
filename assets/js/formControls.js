function setFormListeners() {
	$.validate({
		modules: 'security',
		onModulesLoaded: function () {
			console.log("Form Validation Modules loaded");
		}
	});

	

	console.log("setFormListeners()");
	$("form#applyRole").submit(function (event) { applyUserRole(event, $(this))} );

	setRequestCommunityValidationListeners();
	$("form#requestCommunity").submit(function (event) { requestCommunity(event, $(this))} );
}

function applyUserRole(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"applyUserRole/";

	submitButton = $( "form#applyRole button.applyRole" );
	submitButton.attr('disabled', '');
	submitButton.text("Applying");
	submitButton.prepend('<i class="fas fa-sync fa-spin"></i> ');


	console.log("actionUrl: "+ actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json",
			data: form.serialize()
		})
			.done(function (json){
				console.log('applyUserRole - AJAX done');
				submitButton = $( "form#applyRole button.applyRole" );

				submitButton.removeAttr('disabled');
				submitButton.remove("i");

				if (json.success) {
					submitButton.text("Apply Role");
					displayAlert($( "form#applyRole"), json.message, 'success');
				} else {
					submitButton.text("Try Again");
					displayAlert($( "form#applyRole"), json.message);
				}

			}) 

			.fail(function (json){
				console.log('applyUserRole - AJAX failed');
			})

			.always(function (json){
				console.log('applyUserRole - AJAX always');
				console.log(json);
				//console.log(json.message);
			});
}

function requestCommunity(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"requestCommunity/";

	submitButton = $( "form#requestCommunity button.requestCommunity" );
	submitButton.attr('disabled', '');
	submitButton.text("Submitting Request");
	submitButton.prepend('<i class="fas fa-sync fa-spin"></i> ');

	console.log("actionUrl: "+ actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json",
			data: form.serialize()
		})
			.done(function (json){
				console.log('requestCommunity - AJAX done');
				

				submitButton = $( "form#requestCommunity button.requestCommunity" );
				submitButton.removeAttr('disabled');
				submitButton.remove("i");

				if (json.success) {
					$("form#requestCommunity").after('<div class="alert alert-success">Your commmunity request has been submitted. It will need to be approved by a site admin before it is available for you to make public.</div>');
					$("form#requestCommunity").hide();
					//submitButton.text("Apply Role");
					//displayAlert($( "form#requestCommunity"), json.message, 'success');
				} else {
					submitButton.text("Submit Request");

					json.messages.forEach(function (message) {
						displayAlert($( "form#requestCommunity"), message);
					})
				}

			}) 

			.fail(function (json){
				console.log('requestCommunity - AJAX failed');
				displayAlert($( "form#requestCommunity"), 'There was an issue communicating with the server.');
				submitButton = $( "form#requestCommunity button.requestCommunity" );
					submitButton.removeAttr('disabled');
					submitButton.remove("i");
					submitButton.text("Submit Request");
					
			})

			.always(function (json){
				console.log('requestCommunity - AJAX always');
				console.log(json);
				
				//
				//console.log(json.message);
			});
}

function setRequestCommunityValidationListeners() {
	console.log("setRequestCommunityValidationListeners()");

	/*$.formUtils.addValidator({
		name : 'name_taken',
		validatorFunction: function (value, $el, config, language, $form) {
			actionUrl = baseActionUrl+"checkCommunityName/"+$( "input#long_name" ).val();
	 		$.ajax({
				url: actionUrl,
				type: "POST",
				dataType: "json"
			})
				.done(function (json){
					console.log('checkCommunityName - AJAX done');
					//submitButton = $( "form#requestCommunity button.requestCommunity" );

					//submitButton.removeAttr('disabled');
					//submitButton.remove("i");

					if (json.success) {
						console.log("this name isn't in use");

						return true;
					} else {
						console.log("this name is in use");

						return false;
					}

				}) 

				.fail(function (json){
					console.log('checkCommunityName - AJAX failed');
					console.log(json);
					console.log(json.message);

					return false;
				})

				.always(function (json){
					//console.log('checkCommunityName - AJAX always');
					//console.log(json);
					//console.log(json.message);
					return false;
				});
		},
		errorMessage : 'That name is in use.',
		errorMessageKey: 'nameInUse'
	});*/

	$("input#long_name").on("change paste keyup", function() {
 		console.log( "Handler for .change() called." );

 		slug = $("input#long_name").val().toLowerCase();
 		slug = slug.replace(/ /g, "-");
 		slug = slug.replace(/[^0-9a-z_-]/gi, '');

 		$("input#slug").val(slug)
		
	});
	// check name for invalid characters
	// check URL for invalid characters
	// check URL against current database
	// check summary for invalid characters
	// check description for invalid characters	
}

function displayAlert(target, message, level = "warning") {
	console.log("displayAlert("+target+", "+message+", "+level+")");
	alert = '<div id="alertMessage" class="alert alert-'+level+'">';
	alert += message;
	alert += '</div>';


	target.prepend(alert);
	$("#alertMessage").hide();
	$("#alertMessage").fadeIn(500).delay(3000).fadeOut("slow", function(){
	  $(this).remove();
	});
}