	var pageID;

	var adjure = function(){

		pageID = $('.mainSection').parents().attr("id");

		if(pageID == 'classroom'){

			$('.classLinks').on("click", 'a', function(){
				adjureMarkClicked($(this));
				adjureShow($(this).attr("id"));
			});
		} else if(pageID == 'exhibition'){

			$('.classLinks').on("click", 'a', function(){
				adjureMarkClicked($(this));
				adjureShow($(this).attr("id"));
			});

		}


	}

	function adjureMarkClicked(obj) {

		$('.classLinks a').each( function(){

			if($(this).attr('id') == $(obj).attr("id")) {
				$(obj).addClass('current');


			}else if($(this).attr('class') == 'current') {
				$(this).removeClass('current');
			}


		});


	}

	function adjureShow(data){
		
		// the server request
		$.ajax({
 
		    // The URL for the request
		    url: "../adjure/classes.php",
		 
		    // The data to send (will be converted to a query string)
		    data: {
		          "keywordID": data,  
		 	},
		    // Whether this is a POST or GET request
		    type: "GET",
		 
		    // The type of data we expect back
		    dataType : "text",
		 
		    // Code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( text ) {

		    	$(".rightColumn").empty();

		    	$( ".rightColumn").html( text );
			    
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