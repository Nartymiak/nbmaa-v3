//intialize vars
var submitting = false;
var checkFileResult = '';
var form_data;

//general pub
function initialize(){
    
    $('#submitCheck').on('click', function(e) {
        e.preventDefault();
        if(submitting === false){
            submitCheck();
        }
    });

    $('#formSubmit').on('click', function(e) {
        e.preventDefault();
        if(submitting === false){
            submitForm();
        }
    });

    // phone form group
    $('.changeType').on('click', function(){
        $(this).closest('.phone-input').find('.type-text').text($(this).text());
        $(this).closest('.phone-input').find('.type-input').val($(this).data('type-value'));
    });
}

function submitCheck(){

    var error = false;
    var debugString = "<h4>Oops, please go back and check your entry for:</h4>"; 
    var requiredError = false;
    var emailError = false;
    var fileError = false;

    // make sure its an email
    $('.validEmail').each(function(){
        if(!validateEmail($(this).val())){
            $(this).parent().addClass('has-error');
            error = true;
            emailError = true;
        } else {
            $(this).removeClass('has-error');
        }
    });

    // make sure all required fields are filled
    $('.required').each(function(){
        if(validateEmpty($(this))){
            $(this).parent().addClass('has-error');
            error = true;
            requiredError = true;
        } else {
            $(this).parent().removeClass('has-error');
        }
    });

    if($('input[type="file"]').val()) {

        form_data = new FormData($('#requestForDonations')[0]);
        file_data = $('#requestDonationFile').prop('files')[0];

        if(!checkFile(file_data)){
             form_data.append('file', file_data);

        } else {
            error=true;
            fileError=true;
        }
    } else {
        form_data = new FormData($('#requestForDonations')[0])
    }

    $('#debug').empty();
    if(error){
        
        if(requiredError){
            debugString += '<p class="alert alert-danger" role="alert">A required field was left blank</p>';
        }
        if(emailError){
            debugString += '<p class="alert alert-danger" role="alert">An email you entered was invalid</p>';
        }
        if(fileError){
            debugString += '<p class="alert alert-danger" role="alert">'+checkFileResult+'</p>';
        }

        $('#debug').html(debugString);
        $('html, body').animate({ scrollTop: $('#debug').offset().top-50 },400);

    // no errors
    }else{

        $('#formModal').modal('show');

    }
}

function submitForm(){

    submitting = true;
    $('#formModal').modal('hide');

    $( "#debug").html('<p style="padding: 15px;box-sizing: border-box;"" class="alert-warning">Please wait while your information is being saved.</p>');
    $('html, body').animate({ scrollTop: $('#debug').offset().top-50 },400);
    $.ajax({
        url: 'forms.php', // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false, 
        data: form_data,                
        type: 'POST',
        success: function(php_script_response){

            $("#debug").empty();
            $("#debug").html( php_script_response );
        },
        error: function( xhr, status, errorThrown ) {
            
            $("#debug").empty();
            $("#debug").html( "Sorry, there was a problem!" + " Error: " + errorThrown + " Status: " + status );    
        },
        complete: function( xhr, status ) {
            
            setTimeout(function(){
                submitting = false;
            }, 10000);
        }
    });
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}


function validateEmpty(obj){

    if(obj.is(':radio')){
        var radioName = obj.attr('name');
        if (!$('input[name='+radioName+']:checked').val()) {
           return true;
        }
        else {
          return false;
        }
    } else if(obj.val() == '' || obj.val() == null ){ 
        return true;
    } else { 
        return false;
    }
}

function checkFile(file){

    //console.log(file.name + ', ' + file.size + ', ' + file.type);

    var result = false;

    if(!file){
        checkFileResult = 'No file selected';
        return false;
    }else if(file.name.length < 1) {
        checkFileResult = 'Incorrect file name';
        result = true;
    }
    else if(file.size > 2000000) {
        checkFileResult = 'File size is too large. Try uploading a file smaller than 2MB';
        result = true;
    }
    else if(file.type != 'application/pdf') {
        checkFileResult = "Incorrect file type. Try saving the file as .pdf.";
        result = true;
    }
    return result;
}

//entries
function initializeEntries(){
    baseLogic();

    if(jwt!== null){
        view('loginComplete', 'header');
        view('menu', 'menu');
        view('docents', 'view');
    } else {
        view('login','view');
        loginLogic();
    }
}

function baseLogic(){

    $('#app').on('click', '.genLnk', function(e){
        e.preventDefault();
        var mod = $(this).attr('href');
        var loc = $(this).attr('data');
        view(mod,loc);
    });
}

function loginLogic(){

    $('#view').on('click', '#loginButton', function(e){
        var username = $("#loginID").val();
        var password = $("#loginPW").val();

        e.preventDefault();
        login(username, password);
    });

}