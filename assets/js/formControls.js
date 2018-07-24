function setFormListeners() {
	console.log("setFormListeners()");
	$("form#applyRole").submit(function (event) { applyUserRole(event, $(this))} );
}

function applyUserRole(e, form) {
	e.preventDefault();
	actionUrl = baseActionUrl+"applyUserRole/";

	console.log("actionUrl: "+ actionUrl);
		$.ajax({
			url: actionUrl,
			type: "POST",
			dataType: "json",
			data: form.serialize()
		})
			.done(function (json){
				console.log('applyUserRole - AJAX done');
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