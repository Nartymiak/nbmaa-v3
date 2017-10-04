	// variables
	var memberNumber;
	var memberExp;
	var botCheck;
	var newEmails;
	var invalidEmail;
	var email;

	// run the script only if on members update email form page
	// change string if name of page chages
	if($('title').attr('id') =='Members Update Email Form'){


		$("#membersUpdateEmail").on("click", "#membersUpdateEmailSubmit", function(){
			
			memberNumber = $("#membersNumber").val();
			memberExp = $("#membersExp").val();
			botCheck = $("#botCheck").val();

			// test to see if fields have been inputed properly
			if(botCheck == "" && !(memberNumber == "" || memberExp == "")){
				checkAccount();
			} else {
				
				if(memberNumber == ""){
					$("#membersNumber").css("border", "1px solid red");
				} else {
					$("#membersNumber").css("border", "initial");
				}
				if(memberExp == ""){
					$("#membersExp").css("border", "1px solid red");
				} else {
					$("#membersExp").css("border", "initial");
				}

				$("#error").html("You need to fill out both fields in order to continue!");
				$("#formResponse").empty();
				$("#formSuccess").empty();
			}

		});


		$("#membersUpdateEmail").on("click", "#changeEmailSubmit", function(){

			// clear the string 
			newEmails ="";
			invalidEmail = 0;

			$(".invalidEmail").remove();
			
			$(".updateEmail").each(function(i){

				email = $(this).val();

				if(validateEmail(email) || email == "" || email == " "){
					// concatenate fields into a string
					newEmails = newEmails + $(this).attr("data") + ";" + email + ";";

				} else {
					$(this).after("<span class=\"invalidEmail\">invalid email address</span>");
					invalidEmail = 1;
				}
			
			});

			$("#formSuccess").empty();

			// if all emails are valid
			if(invalidEmail == 0){
				updateEmail();
			}

		});
	}

	// ajax call
	function checkAccount(){

		// the server request
		$.ajax({
 
		    // The URL for the request
		    url: "http://www.nbmaa.org/adjure/memberEmailForm.php",
		 
		    // The data to send (will be converted to a query string)
		    data: {
		         "number": memberNumber,
		         "expire": memberExp
		 	},
		    // Whether this is a POST or GET request
		    type: "POST",
		 
		    // The type of data we expect back
		    dataType : "text",
		 
		    // Code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( text ) {

		    	$("#membersNumber").css("border", "initial");

		    	$("#membersExp").css("border", "initial");

		    	$("#error").empty();

		    	$("#formResponse").html( text );
		    	
		    	$("#formSuccess").empty();
		    
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

		return true;
	}

	// ajax call
	function updateEmail(){

		// the server request
		$.ajax({
 
		    // The URL for the request
		    url: "http://www.nbmaa.org/adjure/memberEmailForm.php",
		 
		    // The data to send (will be converted to a query string)
		    data: {
		         "emails": newEmails
		 	},
		    // Whether this is a POST or GET request
		    type: "POST",
		 
		    // The type of data we expect back
		    dataType : "text",
		 
		    // Code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( text ) {

		    	$("#membersNumber").css("border", "initial");

		    	$("#membersExp").css("border", "initial");

		    	$("#error").empty();

		    	$("#formSuccess").html( text );
		    
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

		return true;
	}

	function validateEmail(email) {
    	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    	return re.test(email);
	}