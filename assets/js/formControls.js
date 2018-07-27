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
	$("form.communityApproval").submit(function (event) { approveCommunity(event, $(this))} );
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

function approveCommunity(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"approveCommunity/";

	submitButton = form.children("button.setApproval" );
	submitButton.attr('disabled', '');
	submitButton.text("Submitting Approval");
	submitButton.prepend('<i class="fas fa-sync fa-spin"></i> ');

	console.log("actionUrl: "+ actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json",
			data: form.serialize()
		})
			.done(function (json){
				console.log('approveCommunity - AJAX done');

				submitButton = form.children("button.setApproval" );
				parent = form.closest("div.infoBox");
				submitButton.removeAttr('disabled');
				submitButton.remove("i");

				if (json.success) {
					//form.after('<div class="alert alert-success">The '+json.long_name+' community was succesfully '+json.status+'.</div>');

					displayAlert(parent, json.message, 'success');
					//$("form#requestCommunity").hide();
					//submitButton.text("Apply Role");
					//displayAlert($( "form#requestCommunity"), json.message, 'success');
					parent.hide();
				} else {
					submitButton.text("Submit Approval");
					displayAlert(form, json.message, 'danger');
					
				}

			}) 

			.fail(function (json){
				console.log('approveCommunity - AJAX failed');
				displayAlert(form, 'There was an issue communicating with the server.');

				submitButton = form.children("button.setApproval" );
					submitButton.removeAttr('disabled');
					submitButton.remove("i");
					submitButton.text("Submit Approval");
					
			})

			.always(function (json){
				console.log('approveCommunity - AJAX always');
				console.log(json);
				
				//
				//console.log(json.message);
			});
}

function setRequestCommunityValidationListeners() {
	console.log("setRequestCommunityValidationListeners()");

	$("input#long_name").on("change paste keyup", function() {
 		console.log( "Handler for .change() called." );

 		slug = $(this).val().toLowerCase();
 		slug = slug.replace(/ /g, "-");
 		slug = slug.replace(/[^0-9a-z_-]/gi, '');

 		 $(this).siblings("input#slug").val(slug)
	});


	$("input.long_name").on("change paste keyup", function() {
 		console.log( "Handler for .change() called." );

 		target = "#slug-"+$(this).attr('id').replace('long_name-', '');

 		slug = $(this).val().toLowerCase();
 		slug = slug.replace(/ /g, "-");
 		slug = slug.replace(/[^0-9a-z_-]/gi, '');
 		console.log( "slug: "+slug );
 		console.log( "target: "+target );
 		console.log( "sibling: "+$(this).siblings("input.slug") );

 		 $(target).val(slug)
	});

	$("input[name=status]").click(function(){
		console.log("selected status:"+ $(this).val())

	    $(this).parent().removeClass('alert alert-success alert-danger alert-warning');
	    if ($(this).val() == 'approved') {
	    	$(this).parent().addClass('alert alert-success');
	    } else {
	    	$(this).parent().addClass('alert alert-danger');
	    }
	}); 
}

function displayAlert(target, message, level = "warning") {
	console.log("displayAlert("+target+", "+message+", "+level+")");
	alert = '<div id="alertMessage" class="alert alert-'+level+'">';
	alert += message;
	alert += '</div>';


	target.before(alert);
	$("#alertMessage").hide();
	$("#alertMessage").fadeIn(500).delay(3000).fadeOut("slow", function(){
	  $(this).remove();
	});
}