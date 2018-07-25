function setFormListeners() {
	console.log("setFormListeners()");
	$("form#applyRole").submit(function (event) { applyUserRole(event, $(this))} );
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