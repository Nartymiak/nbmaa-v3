
$( ".date" ).datepicker();

$(".requiredCheckBox").on("click", function(){
	if($(this).val()==""){
		$(this).val("clicked");
	} else {
		$(this).val("");
	}
});

$( "#submit" ).on("click", function(event){

	var missingRequire = false;
	var topInputRequired;
	var topInputRequiredFound = false;
				
	event.preventDefault();

	$(".required").each(function(){

		if($(this).val() == null || $(this).val() == ""){

			$(this).css("box-shadow", "0 0 5px rgba(255,0, 0, 1)");
			missingRequire = true;

			// get the first iput id for scroll
			if(topInputRequiredFound == false){
				topInputRequired = $(this);
				topInputRequiredFound = true;
			}
		
		} else {
			
			$(this).css("box-shadow", "none");
		}

	});

	// #taco is a hidden input field to catch bots
	if(missingRequire == false && ($("#taco").val() == null || $("#taco").val() == "" )) {
		submitForm($("form"));
	} else {
		$('html, body').animate({
			scrollTop: topInputRequired.offset().top - 200
		});
	}
});

function submitForm(form) {

	var data =[];
	var formType;

	// the server request
	$(form).find(':input').each(function(){

		if($(this).attr("type") == "radio" && $(this).is(':checked')){

			data.push("<strong>" + $(this).attr("name") + ":</strong> " + $(this).val());
		
		} else if($(this).attr("type") != "radio" ){

			data.push("<strong>" + $(this).attr("name") + ":</strong> " + $(this).val());
		
		}
	});

	formType = $('.formType').html();

	// the server request
	$.ajax({
		 
		// The URL for the request
		url: "http://www.nbmaa.org/adjure/TourFormRegistration.php",

		// Whether this is a POST or GET request
		type: "POST",
				 
		// The data to send
		data: {
			'data':data,
			'formType': formType
			},
		 
		// The type of data we expect back
		dataType : "text",
				 
		// Code to run if the request succeeds;
		// the response is passed to the function
		success: function( text ) {
			$(".bodyContent").empty();
			$( ".bodyContent").html(text);
					    
		},
				 
		// Code to run if the request fails; the raw request and
		// status codes are passed to the function
		error: function( xhr, status, errorThrown ) {
			alert( "Sorry, there was a problem!" );
				console.log( "Error: " + errorThrown );
				console.log( "Status: " + status );
				console.dir( xhr );
		},
				 
		// Code to run regardless of success or failure
		complete: function( xhr, status ) {
			//alert( "The request is complete!" );
		}

	});

}

function validateEmail(email) {
	var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	return re.test(email);
}

function validateZip(zip){
	var re = /(^\d{5}$)|(^\d{5}-\d{4}$)/;
	return re.test(zip);
}
