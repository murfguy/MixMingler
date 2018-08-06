function setFormListeners() {

	console.log("setFormListeners()");

	// Set form validators
	$.validate({
		modules: 'security',
		onModulesLoaded: function () {
			console.log("Form Validation Modules loaded");
		}
	});
	
	setConfirmationActions();

	// Set validation listeners to prevent forms from sending without valid data
	setRequestCommunityValidationListeners();

	// Set the listeners for the follow/leave buttons
	setCommunityActionButtonListeners();

	// Set the listeners for the moderation buttons
	setCommunityModerationButtonListeners();

	// set listeners for apply role form on admin panel
	$("form#applyRole").submit(function (event) { applyUserRole(event, $(this))} );
	
	// set listeners for request community form
	$("form#requestCommunity").submit(function (event) { requestCommunity(event, $(this))} );
	
	// set listeners for community approval form on admin panel
	$("form.communityApproval").submit(function (event) { approveCommunity(event, $(this))} );

	// set listeners for founding community from community moderation pages
	$("form#foundCommunity").submit(function (event) { foundCommunity(event, $(this))} );
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

// ----------------------------------------------------------
// -- Community Request/Confirm/Foundation Form Controls ----
// ----------------------------------------------------------

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

function foundCommunity(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"foundCommunity/";

	submitButton = form.children("button.foundButton" );
	submitButton.attr('disabled', '');
	submitButton.text("Founding Community!");
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

				submitButton = form.children("button.foundButton" );
				parent = form.closest("div#foundCommunityNotice");
				submitButton.removeAttr('disabled');
				submitButton.remove("i");

				if (json.success) {
					displayAlert(parent, json.message, 'success');
					//$("form#requestCommunity").hide();
					//submitButton.text("Apply Role");
					//displayAlert($( "form#requestCommunity"), json.message, 'success');
					parent.hide();
				} else {
					submitButton.text("Try Again");
					displayAlert(form, json.message, 'danger');
					
				}

			}) 

			.fail(function (json){
				console.log('approveCommunity - AJAX failed');
				displayAlert(form, 'There was an issue communicating with the server.');

				submitButton = form.children("button.foundButton" );
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

// ----------------------------------------------------------
// -- Server Query Form Confirmation.Controls ---------------
// ----------------------------------------------------------

function setConfirmationActions () {
	console.log("setConfirmationActions()");

	$(".action").on('click', function () {
		console.log('action: ' + $(this).attr('action'));

		action = 'testServlet/';
		actionData = null;
		targetButton = $(this);

		/*if (!targetButton.hasClass('confirm')) {
			targetButton.html('<i class="fas fa-circle-notch fa-spin"></i>');
		}*/

		action = $(this).attr('action');

		switch ($(this).attr('action')) {
			case "joinCommunity":
				alertTitle = "Joining Community..."; 				
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "leaveCommunity":
				message = "You will no longer be a member of this community!";
				confirmText = "Leave";
				cancelText = "Stay";
				alertTitle = "Leaving Community..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "unpendCommunity":
				message = "Your request to join this community will be cancelled.";
				confirmText = "Cancel Join";
				cancelText = "Keep Waiting";
				alertTitle = "Removing Join Request..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "followCommunity":
				alertTitle = "Following Community..."; 				
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "unfollowCommunity":
				message = "You won't see this community in your preferred listings!";
				confirmText = "Unfollow";
				cancelText = "Keep Following";
				alertTitle = "Unfollowing Community..."; 				
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "setAsCore":
				alertTitle = "Setting as a Core Community..."; 				
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "removeAsCore":
				message = "This will no longer be one of your core communities!";
				confirmText = "Do it!";
				cancelText = "Keep as Core";
				alertTitle = "Removing as a Core Community..."; 				
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			/*case "unfollowType":
				message = "You won't see this stream type in your preferred listings!";
				confirmText = "Unfollow";
				cancelText = "Keep Following";
				alertTitle = "Unfollowing Stream Type..."; 
				successAlert = "You succesfully unfollowed {typeName}."; 
				break;

			case "ignoreType":
				message = "This will be hidden from the types listing page.";
				confirmText = "Ignore";
				cancelText = "Nevermind";
				alertTitle = "Ignoring Stream Type..."; 
				successAlert = "You succesfully ignored {typeName}."; 
				break;

			

			case "promoteMember":
				message = "This will make the target member a moderator of this community.";
				confirmText = "Make Mod";
				cancelText = "Keep as User";
				alertTitle = "Making a Moderator..."; 
				successAlert = "{username} was made a moderator for {communityName}.";
				break;

			case "demoteMember":
				message = "This will make the target member a standard member of this community.";
				confirmText = "Remove as Mod";
				cancelText = "Keep as Mod";
				alertTitle = "Removing a Moderator..."; 
				successAlert = "{username} is no longer a moderator of {communityName}.";
				break;*/

			default:
				message = "This action will change something.";
				confirmText = "Perform Action";
				cancelText = "Cancel";
				alertTitle = "Performing Action..."; 
				successAlert = "You succesfully completed the action."; 
				break;

		}

		if ($(this).hasClass('confirm')) {
			$.confirm({
				title: 'Are you sure?',
				content: message,
				theme: 'dark',
				buttons: {
					yes: {
						text: confirmText,
						btnClass: 'btn-danger',
						action: function () {
							$.alert({
								title: alertTitle,
								theme: 'dark',
								autoClose: 'ok|8000',
								content: function(){
									var self = this;
									
									return $.ajax({
										url: baseActionUrl + action,
										dataType: 'json',
										method: 'post',
										data: actionData
									}).done(function (response) {
										if (response.success) {
											self.setContentAppend('<div>'+response.message+'</div>');
											updateButtonView(targetButton, response);
										} else {
											self.setContentAppend('<div>There was an issue with completing your requested action! <br><b>Server Message:</b> '+response.message+'</div>');
										}
									}).fail(function(){
										self.setContentAppend('<div>There was a problem with communicating with the server.</div>');
									}).always(function(response){
										//self.setContentAppend('<div>Always!</div>');
										console.log(response)
									});
								}
							})
						}
					},
					no: {
						text: cancelText
					}
				}
			});
		} else {
			$.alert({
				title: alertTitle,
				theme: 'dark',
				autoClose: 'ok|8000',
				content: function(){
					var self = this;
					
					return $.ajax({
						url: baseActionUrl + action,
						dataType: 'json',
						method: 'post',
						data: actionData
					}).done(function (response) {
						if (response.success) {
							self.setContentAppend('<div>'+response.message+'</div>');
							updateButtonView(targetButton, response);
						} else {
							self.setContentAppend('<div>There was an issue with completing your requested action! <br><b>Server Message:</b> '+response.message+'</div>');
						}
					}).fail(function(){
						self.setContentAppend('<div>There was a problem with communicating with the server.</div>');
					}).always(function(response){
						//self.setContentAppend('<div>Always!</div>');
						console.log(response)
					});
				}
			})
		}
	})
}

function updateButtonView(tgt, serverData) {
	switch (serverData.completedAction) {
		case "join":
		case "addedToPending":
			// Becomes "leave state"
			tgt.removeClass('btn-primary btn-success');
			tgt.addClass('confirm');
			tgt.attr('action', 'leaveCommunity');
			
			if (serverData.approveMembers) {
				tgt.addClass('btn-info');
				tgt.attr('action', 'unpendCommunity');

				if (tgt.attr('btnType') == 'mini') {
					tgt.html('<i class="fas fa-circle-notch fa-spin"></i>');
				} else {
					tgt.html('<i class="fas fa-circle-notch fa-spin"></i> Pending');
				}
			} else {
				if (tgt.attr('btnType') == 'mini') {
					tgt.addClass('btn-success');
					tgt.html('<i class="fas fa-check"></i>');
				} else {
					tgt.addClass('btn-danger');
					tgt.html('Leave');
				}
			}
			break;

		case "leave":
		case "removedFromPending":
			tgt.removeClass('confirm btn-danger btn-success btn-info');
			tgt.attr('action', 'joinCommunity');

			if (serverData.communityStatus == 'closed') {
				// community is closed. Button should revert to non-interactive state.
				tgt.removeClass('action');
				tgt.attr('disabled', '');
				if (tgt.attr('btnType') == 'mini') {
					tgt.addClass('btn-danger');
					tgt.html('<i class="fas fa-minus-circle"></i>');
				} else {
					tgt.addClass('btn-secondary');
					tgt.html('Closed');
				}
			} else {
				// user succesfully left community. Revert to "join" status.
				if (tgt.attr('btnType') == 'mini') {
					tgt.addClass('btn-primary');
					tgt.html('<i class="fas fa-times"></i>');
				} else {
					tgt.addClass('btn-primary');
					tgt.html('Join');
				}

			}
			break;

		case "follow":
			tgt.removeClass('btn-primary btn-success');
			tgt.attr('action', 'unfollowCommunity');
			tgt.addClass('confirm');

			if (tgt.attr('btnType') == 'mini') {
				tgt.addClass('btn-success');
				tgt.html('<i class="fas fa-check"></i>');
			} else {
				tgt.addClass('btn-danger');
				tgt.html('Unfollow');
			}
			break;

		case "unfollow":
			tgt.removeClass('confirm btn-danger btn-success');
			tgt.attr('action', 'followCommunity');

			if (tgt.attr('btnType') == 'mini') {
				tgt.addClass('btn-primary');
				tgt.html('<i class="fas fa-times"></i>');
			} else {
				tgt.addClass('btn-primary');
				tgt.html('Follow');
			}
			break;

		case "setAsCore":
			tgt.removeClass('btn-primary');
			tgt.attr('action', 'removeAsCore');
			tgt.addClass('btn-success confirm');
			tgt.html('<i class="fas fa-check"></i>');
			break;

		case "removeAsCore":
			tgt.removeClass('confirm btn-success');
			tgt.attr('action', 'setAsCore');
			tgt.addClass('btn-primary');
			tgt.html('<i class="fas fa-thumbs-up"></i>');
			break;
	}
}


// ----------------------------------------------------------
// -- Possible Refactor/Deletion candidates - ---------------
// ----------------------------------------------------------


function setCommunityModerationButtonListeners() {
	console.log("setCommunityModerationButtonListeners()");
	$("button.modAction").click(function () {
		actionUrl = baseActionUrl + "changeMemberStatus/";
		
		submitButton = $(this);
		submitButton.attr('disabled', '');


		submitButton.removeClass('btn-success');
		submitButton.addClass('btn-dark');
		submitButton.html('<i class="fas fa-sync fa-spin"></i>');

		submitModerationAction(actionUrl,
			 $(this).attr('commId'), 
			 $(this).attr('memberId'), 
			 $(this).attr('memberName'), 
			 $(this).attr('btnAction')
			);
	});
}

function submitModerationAction(actionUrl, communityId, mixer_id, name_token, action) {
	console.log("submitModerationAction("+actionUrl+", "+communityId+", "+mixer_id+", "+action+")");

	$.ajax({
		url: actionUrl,
		type: "POST",
		dataType: "json",
		data: { 
			communityId: communityId,
			memberId: mixer_id,
			memberName: name_token,
			status: action
		}
	})
		.done(function (json){
			console.log('modAction - AJAX done');

			tgtRow = $("#"+json.memberStatus+"User-"+json.memberId).closest("tr");
			if (json.success) {
				console.log("remove row: " + "#"+json.memberStatus+"User-"+json.memberId);
				switch (json.memberStatus) {
					case "approve":
						tgtRow.html('<td colspan="4">'+json.memberName+' was approved.</td>');
						break;
					case "deny":
						tgtRow.html('<td colspan="4">'+json.memberName+' was denied.</td>');
						break;
				}
			}
			
		})

		.fail(function (json){
			console.log('modAction - AJAX failed');
			//displayAlert($("#userHeader"), "There was an issue communicating with the server. Reload and try again.", "danger", 0)
		})

		.always(function (json){
			console.log('modAction - AJAX always');
			console.log(json);
			//console.log(json.message);

		});
}

function setRequestCommunityValidationListeners() {
	console.log("setRequestCommunityValidationListeners()");

	$("#requestCommunity input#long_name").on("change paste keyup", function() {
		console.log( "#requestCommunity input#long_name on change paste keyup" );

		slug = $(this).val().toLowerCase();
		slug = slug.replace(/ /g, "-");
		slug = slug.replace(/[^0-9a-z_-]/gi, '');
		console.log('slug: '+slug)

		 $("#requestCommunity input#slug").val(slug)
	});


	$("input.long_name").on("change paste keyup", function() {
		target = "#slug-"+$(this).attr('id').replace('long_name-', '');

		slug = $(this).val().toLowerCase();
		slug = slug.replace(/ /g, "-");
		slug = slug.replace(/[^0-9a-z_-]/gi, '');
		console.log( "slug: "+slug );
		console.log( "target: "+target );
		console.log( "sibling: "+$(this).siblings("input.slug") );

		 $(target).val(slug)
	});

	$(".communityApproval input[name=status]").click(function(){
		console.log("selected status:"+ $(this).val())

		$(this).parent().removeClass('alert alert-success alert-danger alert-warning');
		if ($(this).val() == 'approved') {
			$(this).parent().addClass('alert alert-success');
		} else {
			$(this).parent().addClass('alert alert-danger');
		}
	}); 
}

function displayAlert(target, message, level, timeOnScreen) {
	if (level=='') { level = "warning"; }
	if (timeOnScreen=='') { timeOnScreen = 3000; }

	console.log("displayAlert("+target+", "+message+", "+level+")");
	alert = '<div id="alertMessage" class="alert alert-'+level+'">';
	alert += message;
	alert += '</div>';

	target.before(alert);
	if (timeOnScreen == 0) {
		$("#alertMessage").hide();
		$("#alertMessage").fadeIn(500).delay(timeOnScreen).fadeOut("slow", function(){
		  $(this).remove();
	});
	}
}



function setCommunityActionButtonListeners() {
	console.log("setCommunityActionButtonListeners()");
	$("button.commAction").click(function () {
		actionUrl = baseActionUrl + $(this).attr('id') +"Community/";


		submitButton = $(this);
		submitButton.attr('disabled', '');

		switch($(this).attr('id')) {
			case "join": 
				submitButton.text("Joining");
				$("button#follow").attr('disabled', '');
				$("button#follow").text("Following");
				$("button#follow").prepend('<i class="fas fa-sync fa-spin"></i> ');
				break;

			case "unpend": 
				submitButton.text("Unpending");
				break;

			case "leave": 
				submitButton.text("Leaving");
				$("button#moderateLink").attr('disabled', '');
				break;

			case "follow": 
				submitButton.text("Following");
				break;

			case "unfollow": 
				submitButton.text("Unfollowing");
				break;
		}

		submitButton.prepend('<i class="fas fa-sync fa-spin"></i> ');

		submitCommunityAction(actionUrl, $(this).attr('commId'));
	});
}
/*
function submitCommunityAction(actionUrl, communityId) {
	console.log("submitCommunityAction("+actionUrl+","+communityId+")");

	$.ajax({
		url: actionUrl,
		type: "POST",
		dataType: "json",
		data: { 
			communityId: communityId
		}
	})
		.done(function (json){
			console.log('commAction - AJAX done');

			if (json.success) {
				targetButton = $("button#"+json.completedAction)
				targetButton.removeAttr('disabled');
				targetButton.remove("i");

				switch(json.completedAction) {
					case "join": 						
						targetButton.removeClass('btn-primary');
						targetButton.addClass('btn-danger');
						targetButton.text('Leave');
						targetButton.attr('id','leave');
						targetButton.attr('title','Leave this community.');
						targetButton.attr('data-original-title','Leave this community.');

						if (!json.followsCommunity) {
							submitCommunityAction(baseActionUrl+"followCommunity/", json.communityID);
						}
						break;

					case "addedToPending":
						targetButton = $("button#join");
						targetButton.removeAttr('disabled');
						targetButton.removeClass('btn-primary');
						targetButton.addClass('btn-info');
						targetButton.text('Pending');
						targetButton.attr('id','unpend');
						targetButton.attr('title','Your membership is pending approval. Click to undo.');
						targetButton.attr('data-original-title','Your membership is pending approval. Click to undo.');

						if (!json.followsCommunity) {
							submitCommunityAction(baseActionUrl+"followCommunity/", json.communityID);
						}
						break;

					case "removedFromPending":
						targetButton = $("button#unpend");
						targetButton.removeAttr('disabled');
						targetButton.removeClass('btn-info');

						if (json.communityStatus == 'open') {
							targetButton.addClass('btn-primary');
							targetButton.text('Join');
							targetButton.attr('id','join');
							targetButton.attr('title','Become a member of this community so viewers can find you.');
							targetButton.attr('data-original-title','Become a member of this community so viewers can find you.');
						} else {
							targetButton.addClass('btn-secondary');
							targetButton.text('Closed');
							targetButton.attr('disabled', '');
							//targetButton.attr('id','join');
							targetButton.attr('title','Community is closed to new members.');
							targetButton.attr('data-original-title','Community is closed to new members.');
						}
						break;

					case "leave": 
						targetButton.removeClass('btn-danger');
							
						if (json.communityStatus == 'open') {
							targetButton.addClass('btn-primary');
							targetButton.text('Join');
							targetButton.attr('id','join');
							targetButton.attr('title','Become a member of this community so viewers can find you.');
							targetButton.attr('data-original-title','Become a member of this community so viewers can find you.');
						} else {
							targetButton.addClass('btn-secondary');
							targetButton.text('Closed');
							targetButton.attr('disabled', '');
							//targetButton.attr('id','join');
							targetButton.attr('title','Community is closed to new members.');
							targetButton.attr('data-original-title','Community is closed to new members.');
						}
						$("button#moderateLink").remove();
						break;

					case "follow": 						
						targetButton.removeClass('btn-primary');
						targetButton.addClass('btn-danger');
						targetButton.text('Unfollow');
						targetButton.attr('id','unfollow');
						targetButton.attr('title','Stop getting updates from this community on your profile.');
						targetButton.attr('data-original-title','Stop getting updates from this community on your profile.');
						break;
					case "unfollow": 
						targetButton.removeClass('btn-danger');
						targetButton.addClass('btn-primary');
						targetButton.text('Follow');
						targetButton.attr('id','follow');
						targetButton.attr('title','Track streamers in this community from your profile page.');
						targetButton.attr('data-original-title','Track streamers in this community from your profile page.');
						break;
				}
			}

			


		}) 

		.fail(function (json){
			console.log('commAction - AJAX failed');
			displayAlert($("#userHeader"), "There was an issue communicating with the server. Reload and try again.", "danger", 0)
		})

		.always(function (json){
			console.log('commAction - AJAX always');
			console.log(json);
			//console.log(json.message);

		});
}*/