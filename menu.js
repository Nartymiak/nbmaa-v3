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

    $('.subNav a').each(function(){
        if($(this).html()==$("title").attr("id")){
          $(this).addClass("current");
        }
    })

    $('.subNav .display').on('click', 'a', function(){
      subNavShow($(this));
    });

    $('#socialSection').on('click', 'a', function(){
      showSocial($(this));
    });

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

var subNavShow = function(link) {

  var id = '#' + link.attr('class').split(' ')[0];

  $('.rightColumn').children().css('display', 'none');
  $(id).css('display', 'block');

  $('.display a').each(function(){
    
    if($(this).attr('class').split(' ')[1] == 'current'){
      $(this).removeClass('current');
    } 

  });

  link.addClass('current');

}

var showSocial = function(obj) {

  var id = '.' + obj.attr('id');
 
  if($(id).css('display') == 'none'){
    $(id).css('display', 'block');

  } else if ($(id).css('display') == 'block'){
    $(id).css('display', 'none');
  }

}
