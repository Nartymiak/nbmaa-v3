  var menu = function(){

    $(".menuItem").hover(

      function(){

        $(this).children(".dropDown").css("display", "block");
        $(this).prepend("<div class=\"arrowUp\"></div>");

      },

      function(){
        $(this).children(".dropDown").css("display", "none");
        $(this).children(".arrowUp").remove();
      }

    );

  }

  var logo = function(){

    var flag = false;

    $( window ).scroll(function() {

      if(flag == false && $( this ).scrollTop() > 100) {

        shrinkLogo();
        flag = true;

      } else if(flag == true && $( this ).scrollTop() <= 100) {
      
        expandLogo();
        flag = false;
    
      }
    
    });
  }

  var shrinkLogo = function() {

    $('#calendar .logo').animate({
      width : 91,
      left: 91 
    }, 300);
  }

  var expandLogo = function() {

    $('#calendar .logo').animate({
      width : 240,
      left: 0
    }, 300);

  }