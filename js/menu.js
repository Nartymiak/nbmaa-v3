
  var mobile;
  var clicked = false;
  var mainNavClicked = [];
  var lastMainNavClicked;
  var lastMainNavHidden;

  var menu = function(){

    for(var i=0; i< $('.menuItem').length; i++){
      mainNavClicked[i] = false;
    }

    if($(window).width() < 700){
      mobile = true;
    }

    if( mobile != true){

      $(".menuItem").hover(

        function(){

          showSubNav($(this));

        },

        function(){
          
          hideSubNav($(this));
        }

      );

      $(".menuItem > a").on('click', function(){

        lastMainNavClicked = $(this).parent().index();

        $('.menuItem > a').each(function(){
          if($(this).parent().index() == lastMainNavClicked &&  $(this).next(".dropDown").css("display") == "none"){
            showSubNav($(this).parent());
          } else {
            hideSubNav($(this).parent());
          }

        });
       
      });

    } else {

      $("#menuButton").on('click', function(){

        if(clicked == false){
          showMobileMenu($(this));
          clicked = true;

        }else{
          hideMobileMenu($(this));
          clicked = false;
        }

      });

      $(".menuItem").on('click', 'a', function(){

        if(mainNavClicked[$(this).parent().index()] == false){
          $(this).next(".dropDown").css("display", "block");
          mainNavClicked[$(this).parent().index()] = true;

        } else {
          $(this).next(".dropDown").css("display", "none");
          mainNavClicked[$(this).parent().index()] = false;
        }

      });

    }

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

  var showSubNav = function(obj){
    $(obj).children(".dropDown").css("display", "block");
    $(obj).append("<div class=\"arrowUp\"></div>");
  }

  var hideSubNav = function(obj){
    $(obj).children(".dropDown").css("display", "none");
    $(obj).children(".arrowUp").remove();
  }

  var showMobileMenu = function(obj){
    $('#mainNav').show().css("height", window.innerHeight);
    $('#siteWrapper').css({
      'overflow': 'hidden',
      'height': '0'
    });
    $('.exhibitionLink .dropDown .left .image').after($(".exhibitionCaption"));
    $('#slider').css('display', 'none');
    $('#menuButton').empty();
    $('#menuButton').append( "CLOSE <i class=\"fa fa-times\"></i>" );
  }

  var hideMobileMenu = function(obj){
    $('#mainNav').hide().css("height","auto");
    $('#siteWrapper').css({
      'overflow': 'visible',
      'height': 'auto'
    });
    $(obj).removeAttr("style");
    $('#slider').removeAttr("style");
    $('#menuButton').empty();
    $('#menuButton').append( "MENU <i class=\"fa fa-bars\"></i>" );
  }

  var logo = function(){

    var percent = .23952095808;
    var outWidth = $(".wrapper").width();
    var width = outWidth * percent;

    var flag = false;

    $( window ).scroll(function() {

      if(flag == false && $( this ).scrollTop() > 100) {

        shrinkLogo(width);
        flag = true;

      } else if(flag == true && $( this ).scrollTop() <= 100) {
      
        expandLogo(width);
        flag = false;
    
      }
    
    });
  }

  var shrinkLogo = function(width) {

    if( mobile != true){
      $('#calendar .logo').animate({
        width : 91,
        left: width/4
      }, 300);

    } else {
      $('#calendar .logo').animate({
        width : 75,
        left: 0
      }, 300);

    }
  }

  var expandLogo = function(width) {

    $('#calendar .logo').animate({
      width : width,
      left: 0
    }, 300);

  }

var subNavShow = function(link) {

  var id = ".id" + link.attr('class').split(' ')[0];
  
  $('.rightColumn').children().css('display', 'none');
  
  $(id).css('display', 'block');

  $('html, body').animate({
      scrollTop: $('.rightColumn').offset().top
  }, 300);

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
