function setFormListeners() {

	console.log("setFormListeners()");

	// Set form validators
	$.validate({
		modules: 'security, file',
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

	// Set the listeners for user setting adjustments
	setSettingsAdjustmentListeners();

	// set listeners for apply role form on admin panel
	$("form#applyRole").submit(function (event) { applyUserRole(event, $(this))} );
	
	// set listeners for request community form
	$("form#requestCommunity").submit(function (event) { requestCommunity(event, $(this))} );
	
	// set listeners for community approval form on admin panel
	$("form.communityApproval").submit(function (event) { processCommunity(event, $(this))} );

	// set listeners for founding community from community moderation pages
	$("form#foundCommunity").submit(function (event) { foundCommunity(event, $(this))} );

	// set listeners for founding community from community moderation pages
	$("form#editCommunity").submit(function (event) { editCommunity(event, $(this))} );


	$("form#setNewAdmin").submit(function (event) { setNewAdmin(event, $(this))} );
}

function applyUserRole(e, form) {
	e.preventDefault();

	actionData = form.serialize();
	action = "applyUserRole";

	$.confirm({
		title: 'Are you sure?',
		content: "This user's role will be changed accordingly.",
		theme: 'dark',
		buttons: {
			yes: {
				text: "Change Role",
				btnClass: 'btn-danger',
				action: function () {
					$.alert({
						title: "Changing User's Site Role...",
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
									//updateButtonView(targetButton, response);
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
				text: "Nevermind"
			}
		}
	});
}

// ----------------------------------------------------------
// -- Community Request/Confirm/Foundation Form Controls ----
// ----------------------------------------------------------

function requestCommunity(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"requestCommunity/";

	//submitButton = $( "form#requestCommunity button.requestCommunity" );
	//submitButton.attr('disabled', '');
	//submitButton.text("Submitting Request");
	//submitButton.prepend('<i class="fas fa-sync fa-spin"></i> ');


	$.alert({
		title: "Submitting Request...",
		theme: 'dark',
		autoClose: 'ok|8000',
		content: function(){
			var self = this;
			
			return $.ajax({
				url: actionUrl,
				dataType: 'json',
				method: 'post',
				data: form.serialize()
			}).done(function (response) {
				if (response.success) {
					self.setContentAppend('<div>'+response.message+'</div>');
					//updateButtonView(targetButton, response);

					if (response.success) {
						//parent = form.closest("div.infoBox").children('.infoInterior').html(response.message);
						$("form#requestCommunity").after('<div class="alert alert-success">Your commmunity request has been submitted. You will need to wait for it to be processed by a site admin before you can proceed or request another community.</div>');
						$("form#requestCommunity").hide();
					}
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

function findFormDataItem(target, data) {
	for( var i = 0, len = data.length; i < len; i++ ) {
	    if( data[i]['name'] === target ) {
	        return data[i]['value'];
	    }
	}
} 

function processCommunity(e, form) {
	e.preventDefault();

	actionData = form.serialize();
	action = "processCommunity";

	console.log("processCommunity");

	data = form.serializeArray();
	var communityName = findFormDataItem('name', data);
	var status = findFormDataItem('status', data);

	if (status == 'approved') {
		processingTitle = 'Approving Community...';
		buttonClass = 'btn-success';
		buttonText = "Approve";
	} else {
		processingTitle = 'Rejecting Community...';
		buttonClass = 'btn-danger';
		buttonText = "Deny";
	}

	$.confirm({
		title: 'Are you sure?',
		content: "By submitting this you will have "+status+" the <i>"+communityName+"</i> community.",
		theme: 'dark',
		buttons: {
			yes: {
				text: buttonText,
				btnClass: buttonClass,
				action: function () {
					$.alert({
						title: processingTitle,
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
									//updateButtonView(targetButton, response);

									if (response.success) {
										parent = form.closest("div.infoBox").children('.infoInterior').html(response.message);
										$("tr#notice-"+response.originalSlug).html('<td colspan="8">'+response.Name+" was approved.</td>")
									}
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
				text: "Nevermind"
			}
		}
	});
}

function editCommunity(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"editCommunity/";


	processingTitle = 'Editing Community...';
	buttonClass = 'btn-warning';
	buttonText = "Save Edits";
	//formData = new FormData(form);
	var formData = new FormData($("#editCommunity")[0])
	// Attach file
	var file_data = $('#coverart').prop('files')[0];
	console.log(file_data);
	formData.append('file', file_data); 

	$.confirm({
		title: 'Are you sure?',
		content: "All provided data will be applied to your community.",
		theme: 'dark',
		buttons: {
			yes: {
				text: buttonText,
				btnClass: buttonClass,
				action: function () {
					$.alert({
						title: processingTitle,
						theme: 'dark',
						autoClose: 'ok|8000',
						content: function(){
							var self = this;
							
							return $.ajax({
								url: actionUrl,
								dataType: 'json',
								method: 'POST',
								data: formData,
								processData:false,
								contentType:false,
							}).done(function (response) {
								if (response.success) {
									self.setContentAppend('<div>'+response.message+'</div>');
									//updateButtonView(targetButton, response);

									if (response.success) {
										parent = form.closest("div.infoBox").children('.infoInterior').html(response.message);
										$("tr#notice-"+response.originalSlug).html('<td colspan="8">'+response.Name+" was approved.</td>")
									}
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
				text: "Nevermind"
			}
		}
	});
}

function setNewAdmin(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"transferCommunityOwnership/";


	processingTitle = 'Starting Transfer...';
	buttonClass = 'btn-warning';
	buttonText = "Request Transfer";

	$.confirm({
		title: 'Are you sure?',
		content: "All provided data will be applied to your community.",
		theme: 'dark',
		buttons: {
			yes: {
				text: buttonText,
				btnClass: buttonClass,
				action: function () {
					$.alert({
						title: processingTitle,
						theme: 'dark',
						autoClose: 'ok|8000',
						content: function(){
							var self = this;
							
							return $.ajax({
								url: actionUrl,
								dataType: 'json',
								method: 'POST',
								data: form.serialize()
							}).done(function (response) {
								if (response.success) {
									self.setContentAppend('<div>'+response.message+'</div>');
									//updateButtonView(targetButton, response);

									if (response.success) {
										$("#transferForm").empty();
										$("#transferForm").html("<p>You have a pending transfer in progress. We are waiting for "+response.newAdmin.Username+" to approve the transfer.</p>")
										//parent = form.closest("div.infoBox").children('.infoInterior').html(response.message);
										//$("tr#notice-"+response.originalSlug).html('<td colspan="8">'+response.Name+" was approved.</td>")
									}
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
				text: "Nevermind"
			}
		}
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

					setTimeout(function () {
				       window.location.reload(false); 
				    }, 3000); 
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
// -- Server Query Form Confirmation Controls ---------------
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
			// ==============================================================
			// --- Community Related Actions --------------------------------
			// ==============================================================
			case "deleteCommunity":
				message = "This will permanently delete this community! All members will be removed. It cannot be recovered. BE 100% CERTAIN BEFORE YOU CLICK THE DELETE BUTTON!";
				alertTitle = "Deleting Community...";
				confirmText = "DELETE (Cannot be undone)";
				cancelText = "Nevermind";
				actionData = {
					communityId: $(this).attr('communityId')
				}
				break;

			case "approveCommunity":
				action = "processCommunity/";
				message = "This will approve this community with no adjustments. Once confirmed: Name, URL and Category cannot be edited. Please be 100% certain before quick approving.";
				alertTitle = "Approving Community...";
				confirmText = "Approve";
				cancelText = "Nevermind";
				actionData = {
					status: 'approved',
					isQuickApprove: true,
					userId: $(this).attr('communityId'),
					communityId: $(this).attr('communityId')
				}
				break;

			case "joinCommunity":
				action = "changeCommunityStatus/";
				alertTitle = "Joining Community..."; 				
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "leaveCommunity":
				action = "changeCommunityStatus/";
				message = "You will no longer be a member of this community!";
				confirmText = "Leave";
				cancelText = "Stay";
				alertTitle = "Leaving Community..."; 
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "unpendCommunity":
				action = "changeCommunityStatus/";
				message = "Your request to join this community will be cancelled.";
				confirmText = "Cancel Join";
				cancelText = "Keep Waiting";
				alertTitle = "Removing Join Request..."; 
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "followCommunity":
				action = "changeCommunityStatus/";
				alertTitle = "Following Community..."; 				
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "unfollowCommunity":
				action = "changeCommunityStatus/";
				message = "You won't see this community in your preferred listings!";
				confirmText = "Unfollow";
				cancelText = "Keep Following";
				alertTitle = "Unfollowing Community..."; 				
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "setAsCore":
				action = "changeCommunityStatus/";
				alertTitle = "Setting as a Core Community..."; 				
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "removeAsCore":
				action = "changeCommunityStatus/";
				message = "This will no longer be one of your core communities!";
				confirmText = "Do it!";
				cancelText = "Keep as Core";
				alertTitle = "Removing as a Core Community...";
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "acceptTransfer":
				action = "processTransfer/";
				message = "You will become the owner of this community.";
				confirmText = "Become Owner";
				cancelText = "Nevermind";
				alertTitle = "Transfering Ownership...";
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			case "rejectTransfer":
				action = "processTransfer/";
				message = "The current admin will remain as owner.";
				confirmText = "Deny Request";
				cancelText = "Nevermind";
				alertTitle = "Rejecting Request...";
				actionData = {
					action: $(this).attr('action'),
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId')
				}
				break;

			// ==============================================================
			// --- Type Related Actions -------------------------------------
			// ==============================================================

			case "followType":
				action = 'changeTypeStatus/';
				alertTitle = "Following Stream Type..."; 
				actionData = {
					action: 'followType',
					typeId: $(this).attr('typeId')
				}
				break;

			case "unfollowType":
				action = 'changeTypeStatus/';
				message = "This will no longer show up as a preferred type.";
				confirmText = "Unfollow Type";
				cancelText = "Keep Following";
				alertTitle = "Following Stream Type..."; 
				actionData = {
					action: 'unfollowType',
					typeId: $(this).attr('typeId')
				}
				break;

			case "ignoreType":
				action = 'changeTypeStatus/';
				message = "This will be hidden from any large lists of stream types.";
				confirmText = "Ignore it!";
				cancelText = "Nevermind";
				alertTitle = "Ignoring Stream Type..."; 
				actionData = {
					action: 'ignoreType',
					typeId: $(this).attr('typeId')
				}
				break;

			case "unignoreType":
				action = 'changeTypeStatus/';
				alertTitle = "Unignoring Stream Type..."; 
				actionData = {
					action: 'unignoreType',
					typeId: $(this).attr('typeId')
				}
				break;

			
			// ==============================================================
			// --- Community Moderation Actions -----------------------------
			// ==============================================================

			case "promoteMember":
				action = 'changeMemberStatus/';
				message = "This will make the target member a moderator of this community.";
				confirmText = "Make Mod";
				cancelText = "Keep as User";
				alertTitle = "Making a Moderator..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;

			case "demoteMember":
				action = 'changeMemberStatus/';
				message = "This will make the target member a standard member of this community.";
				confirmText = "Remove as Mod";
				cancelText = "Keep as Mod";
				alertTitle = "Removing a Moderator..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;

			case "approveMember":
				action = 'changeMemberStatus/';
				alertTitle = "Approving Member..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;


			case "denyMember":
				action = 'changeMemberStatus/';
				message = "This will remove this user's request to join.";
				confirmText = "Deny";
				cancelText = "Nevermind";
				alertTitle = "Denying Request..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;


			case "kickMember":
				action = 'changeMemberStatus/';
				message = "This will remove this user from the community.";
				confirmText = "Kick";
				cancelText = "Nevermind";
				alertTitle = "Denying Request..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;


			case "banMember":
				action = 'changeMemberStatus/';
				message = "This will ban this user from being able to join the community.";
				confirmText = "Ban Hammer";
				cancelText = "Nevermind";
				alertTitle = "Banning Member..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;

			case "unbanMember":
				action = 'changeMemberStatus/';
				message = "This will restore a user's ability to join this community.";
				confirmText = "Restore User";
				cancelText = "Nevermind";
				alertTitle = "Unbanning Member..."; 
				actionData = {
					communityId: $(this).attr('communityId'),
					userId: $(this).attr('userId'),
					status: $(this).attr('action')
				}
				break;

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
				animateFromElement: false,
				scrollToPreviousElement: false,
				title: 'Are you sure?',
				content: message,
				theme: 'dark',
				buttons: {
					yes: {
						text: confirmText,
						btnClass: 'btn-danger',
						action: function () {
							$.alert({
								animateFromElement: false,
								scrollToPreviousElement: false,
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
			alertTime = "5000";
			if ($(this).hasClass('no-alert')) {
				alertTime = "250"; }
			//console.log("alertTime: "+alertTime)
			//console.log("$(this).hasClass('no-alert': "+$(this).hasClass('no-alert'));

				$.alert({
					animateFromElement: false,
					scrollToPreviousElement: false,
					title: alertTitle,
					theme: 'dark',
					autoClose: 'ok|'+alertTime,
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
		case "deleteCommunity":
			var pathname = window.location.pathname;
			var modPage = "/community/"+serverData.slug+"/mod";
			if (pathname == modPage) {
				// redirect
				window.location.replace('/');
			}
			break;

		case "quickApproveCommunity":
			tgt.closest("tr").html('<td colspan="8">'+serverData.message+'</td>');
			$("div#process-"+serverData.originalSlug).children('.infoInterior').html(serverData.message);
			break;

		case "joinCommunity":
		case "addedToPending":
			// Becomes "leave state"
			tgt.removeClass('btn-primary btn-success');
			tgt.addClass('confirm');
			tgt.attr('action', 'leaveCommunity');
			
			if (serverData.isApprovalRequired) {
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

					$('span#memberCount').html(parseInt($('span#memberCount').html(), 10)+1)
				}

				

				coreTgt = $("button[communityid='"+serverData.communityID+"'][action='setAsCore']");
				coreTgt.removeClass('confirm btn-success btn-danger');
				//coreTgt.attr('action', 'setAsCore');
				coreTgt.addClass('btn-primary');
				coreTgt.removeAttr('disabled');
				coreTgt.html('<i class="fas fa-thumbs-up"></i>');
			}

			if (serverData.alsoFollowed) {
				followTgt = $("button[communityid='"+serverData.communityID+"'][action='followCommunity']");
				
				followTgt.removeClass('btn-primary btn-success');
				followTgt.attr('action', 'unfollowCommunity');
				followTgt.addClass('confirm');

				if (followTgt.attr('btnType') == 'mini') {
					followTgt.addClass('btn-success');
					followTgt.html('<i class="fas fa-check"></i>');
				} else {
					followTgt.addClass('btn-danger');
					followTgt.html('Unfollow');
				}

				$('span#followCount').html(parseInt($('span#followCount').html(), 10)+1)
			}
			break;

		case "leaveCommunity":
		case "removedFromPending":
			tgt.removeClass('confirm btn-danger btn-success btn-info');
			tgt.attr('action', 'joinCommunity');

			if (serverData.Status == 'closed') {
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
				if (serverData.isApprovalRequired) {
					if (tgt.attr('btnType') == 'mini') {
						tgt.addClass('btn-info');
						tgt.html('<i class="fas fa-question-circle"></i>');
					} else {
						tgt.addClass('btn-info');
						tgt.html('Ask to Join');
					}
				} else {
					if (tgt.attr('btnType') == 'mini') {
						tgt.addClass('btn-primary');
						tgt.html('<i class="fas fa-times"></i>');
					} else {
						tgt.addClass('btn-primary');
						tgt.html('Join');
					}
				}


				coreTgt = $("button[communityid='"+serverData.communityID+"'][action='setAsCore'],[action='removeAsCore']");
				coreTgt.removeClass('confirm btn-success btn-danger btn-primary');
				coreTgt.attr('action', 'setAsCore');
				coreTgt.attr('disabled', '');
				coreTgt.addClass('btn-danger');
				coreTgt.html('<i class="fas fa-minus-circle"></i>');

				if (serverData.completedAction == "leaveCommunity") {
					$('span#memberCount').html(parseInt($('span#memberCount').html(), 10)-1);}
			}
			break;

		case "followCommunity":
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

			$('span#followCount').html(parseInt($('span#followCount').html(), 10)+1)
			break;

		case "unfollowCommunity":
			tgt.removeClass('confirm btn-danger btn-success');
			tgt.attr('action', 'followCommunity');

			if (tgt.attr('btnType') == 'mini') {
				tgt.addClass('btn-primary');
				tgt.html('<i class="fas fa-times"></i>');
			} else {
				tgt.addClass('btn-primary');
				tgt.html('Follow');
			}

				$('span#followCount').html(parseInt($('span#followCount').html(), 10)-1)
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

		case "followType":
			tgt.removeClass('confirm btn-danger');
			tgt.addClass('btn-danger confirm');
			tgt.attr('action', 'unfollowType');
			
			if (tgt.attr('btnType') == 'mini') {
				tgt.html('<i class="fas fa-thumbs-down"></i>');
				tgt.siblings('button').hide();
				tgt.closest('.typeInfo').addClass("followed");
				tgt.removeAttr("data-original-title");
				tgt.attr("title", "Unfollow");
				tgt.removeClass('confirm');
			} else {
				tgt.html('Unfollow');

				tgt.closest('tr').children('td.followState').text('Followed');

				tgtIgnore = $("button[typeid='"+serverData.typeID+"'][action='ignoreType']");
				tgtIgnore.hide();
			}
			
			break;

		case "unfollowType":
			tgt.removeClass('confirm btn-danger');
			tgt.attr('action', 'followType');
			tgt.addClass('btn-primary');

			if (tgt.attr('btnType') == 'mini') {
				tgt.html('<i class="fas fa-thumbs-up"></i>');
				tgt.siblings('button').show();
				tgt.closest('.typeInfo').removeClass("followed");
				tgt.removeAttr("data-original-title");
				tgt.attr("title", "Follow");
				tgt.removeClass('confirm');

			} else {
				tgt.html('Follow');
				tgt.closest('tr').children('td.followState').text('n/a');

				tgtIgnore = $("button[typeid='"+serverData.typeID+"'][action='ignoreType']");
				tgtIgnore.show();
			}
			
			break;

		case "ignoreType":
			tgt.removeClass('confirm btn-danger btn-warning');
			tgt.attr('action', 'unignoreType');
			tgt.addClass('btn-danger');

			if (tgt.attr('btnType') == 'mini') {
				tgt.html('<i class="fas fa-window-close"></i>');
				tgt.siblings('button').hide();
				tgt.closest('.typeInfo').addClass("ignored");
				tgt.removeAttr("data-original-title");
				tgt.attr("title", "Unignore");
				tgt.removeClass('confirm');
			} else {
				tgt.html('Unignore');
				tgt.closest('tr').children('td.followState').text('Ignored');

				tgtFollow = $("button[typeId='"+serverData.typeID+"'][action='followType']");
				tgtFollow.hide();
			}

			

			break;

		case "unignoreType":
			tgt.removeClass('confirm btn-danger btn-warning');
			tgt.attr('action', 'ignoreType');
			tgt.addClass('btn-warning confirm');
		
			if (tgt.attr('btnType') == 'mini') {
				tgt.html('<i class="fas fa-ban"></i>');
				tgt.siblings('button').show();
				tgt.closest('.typeInfo').removeClass("ignored");
				tgt.removeAttr("data-original-title");
				tgt.attr("title", "Ignore");
				tgt.removeClass('confirm');
			} else {
				tgt.html('Ignore');
				tgt.closest('tr').children('td.followState').text('n/a');

				tgtFollow = $("button[typeId='"+serverData.typeID+"'][action='followType']");
				tgtFollow.show();;
			}

			
			break;


		case "approveMember":
			tgt.closest('tr').html('<td>'+serverData.memberName+'\'s membership was approved.</td>');
			break;
		case "denyMember":
			tgt.closest('tr').html('<td>'+serverData.memberName+' was denied membership.</td>');
			break;

		case "kickMember":
			tgt.closest('tr').html('<td>'+serverData.memberName+' was kicked from the community.</td>');
			break;

		case "promoteMember":
			tgt = $("button[userId='"+serverData.memberId+"'][action='promoteMember']")

			tgt.removeClass('confirm btn-success');
			tgt.addClass('btn-secondary confirm');
			tgt.html('<i class="fas fa-user"></i>');
			tgt.attr('action', 'demoteMember');
			
			disableModerationButton($("button[userId='"+serverData.memberId+"'][action='banMember']"));
			disableModerationButton($("button[userId='"+serverData.memberId+"'][action='kickMember']"));

			/*$("button[userId='"+serverData.memberId+"'][action='banMember']").removeClass('action confirm btn-danger');
			$("button[userId='"+serverData.memberId+"'][action='banMember']").attr('disabled', '');
			$("button[userId='"+serverData.memberId+"'][action='banMember']").addClass('btn-secondary')
			$("button[userId='"+serverData.memberId+"'][action='banMember']").html('<i class="fas fa-minus-circle"></i>')

			$("button[userId='"+serverData.memberId+"'][action='kickMember']").removeClass('action confirm btn-danger');
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").attr('disabled', '');
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").addClass('btn-secondary')
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").html('<i class="fas fa-minus-circle"></i>')*/
			break;

		case "demoteMember":
			tgt.removeClass('confirm btn-secondary');
			tgt.attr('action', 'promoteMember');
			tgt.addClass('btn-success confirm');
			tgt.html('<i class="fas fa-chess-knight"></i>');

			$("button[userId='"+serverData.memberId+"'][action='banMember']").removeClass('btn-secondary');
			$("button[userId='"+serverData.memberId+"'][action='banMember']").removeAttr('disabled');
			$("button[userId='"+serverData.memberId+"'][action='banMember']").addClass('btn-danger action confirm')
			$("button[userId='"+serverData.memberId+"'][action='banMember']").html('<i class="fas fa-ban"></i>')

			$("button[userId='"+serverData.memberId+"'][action='kickMember']").removeClass('btn-secondary');
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").removeAttr('disabled');
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").addClass('btn-danger action confirm')
			$("button[userId='"+serverData.memberId+"'][action='kickMember']").html('<i class="fas fa-trash"></i>')

			break;

		case "banMember":
			tgt.closest('tr').html('<td>'+serverData.memberName+' was banned from the community.</td>');
			break;
		case "unbanMember":
			tgt.closest('tr').html('<td>'+serverData.memberName+' was unbanned from the community.</td>');
			break;

		case "acceptTransfer":
			$("#processTransfer").empty();
			$("#processTransfer").removeClass('alert-warning');

			$("#processTransfer").addClass('alert-success');
			$("#processTransfer").html("<p>Transfer was approved! You are now the admin, this page will reload shortly.");
			setTimeout(function () {
		       window.location.reload(false); 
		    }, 5000); 
			break;

		case "rejectTransfer":
			$("#processTransfer").empty();
			$("#processTransfer").removeClass('alert-warning');
			$("#processTransfer").addClass('alert-success');
			$("#processTransfer").html("<p>Transfer was succesfully denied.</p>");
			break;
	}
}

function disableModerationButton(tgt) {
	tgt.removeClass('action confirm btn-danger btn-success btn-primary btn-dark');
	tgt.attr('disabled', '');
	tgt.addClass('btn-secondary')
	tgt.html('<i class="fas fa-minus-circle"></i>')
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

	$("#requestCommunity input#name").on("change paste keyup", function() {
		console.log( "#requestCommunity input#name on change paste keyup" );

		slug = $(this).val().toLowerCase();
		slug = slug.replace(/ /g, "-");
		slug = slug.replace(/[^0-9a-z_-]/gi, '');
		console.log('slug: '+slug)

		 $("#requestCommunity input#slug").val(slug)
	});


	$("input.name").on("change paste keyup", function() {
		console.log( "input.name on change paste keyup" );
		target = "#slug-"+$(this).attr('id').replace('name-', '');

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

function setSettingsAdjustmentListeners() {
	console.log("setSettingsAdjustmentListeners()");
	actionUrl = baseActionUrl + "applyUserSettings/";
	$('.changeSettings').change(function () {
		newSettings = {};
		keys = [];
		vals = [];

		if ($(this).hasClass('communications')) {
			
			$('input:checkbox.communications').each(function () {
				//var sThisVal = (this.checked ? $(this).val() : "");
				val = 0;
				if ($(this).is(':checked')) {
					val = 1;
				}
				console.log($(this).attr('name')+": "+val);
				keys.push($(this).attr('name'));
				vals.push(val);
			});

			for (i=0; i<keys.length; i++) {
				newSettings[keys[i]] = vals[i];
			}

			//console.log(newSettings);

			submitUserSettings('communications', newSettings);
		}
	})
}

function submitUserSettings(settingsGroup, newSettings) {
	console.log("submitUserSettings("+settingsGroup+", "+newSettings+")")

	console.log(newSettings);
	settings = {};
	settings['group'] = settingsGroup;
	settings['settings'] = newSettings;

	console.log(settings);

	$.ajax({
		url: actionUrl,
		type: "POST",
		dataType: "json",
		data: settings
	})
		.done(function (json){
			console.log('applyUserSettings - AJAX done');			
		})

		.fail(function (json){
			console.log('applyUserSettings - AJAX failed');
			//displayAlert($("#userHeader"), "There was an issue communicating with the server. Reload and try again.", "danger", 0)
		})

		.always(function (json){
			console.log('applyUserSettings - AJAX always');
			console.log(json);
			//console.log(json.message);

		});
}