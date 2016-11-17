$(document).ready(function() {

  $('#fast-choise li, #header-menu li').hover(function() {
    $(this).find('ul').slideDown('fast');
  }, function() {
    $(this).find('ul').slideUp('fast');
  });


  initScrollPane([
    // {pane: $('#scroller'), offset: 90},
    { pane: $('#scroller-ins'), offset: 91 }
  ]);

  $("a.fb").fancybox({
    'hideOnContentClick': false,
    'centerOnScroll': false,
    'overlayShow': false,
    'overlayShow': true,
    'overlayOpacity': 0.5,
    'overlayColor': '#000',
  });

  var images = $('div#h_pics div');
  var num_images = images.size() - 1;
  var prev_image = 1;
  var cur_image = 0;

  function changeImage() {
    prev_image = cur_image;
    cur_image = cur_image + 1;
    if (cur_image > num_images) { cur_image = 0 };
    $(images[cur_image]).fadeIn(1000);
    $(images[prev_image]).fadeOut(1000);
  }
  setInterval(changeImage, 5500);

  /*
  $(window).load(function() {
  	if ( $.cookie("town") ) {
  	} else {
  		//showpopup();
  	}
  });
  */
  $('a.select-town').click(function() {
    showpopup();
    return false;
  });

  function showpopup() {
    var popID = 'select-town'; //Get Popup Name
    var popWidth = 530;

    //Fade in the Popup and add close button
    $('#' + popID).fadeIn().css({ 'width': Number(popWidth) });

    //Define margin for center alignment (vertical + horizontal) - we add 80 to the height/width to accomodate for the padding + border width defined in the css
    var popMargTop = ($('#' + popID).height() + 80) / 2;
    var popMargLeft = ($('#' + popID).width() + 80) / 2;

    //Apply Margin to Popup
    $('#' + popID).css({
      'margin-top': -popMargTop,
      'margin-left': -popMargLeft
    });

    //Fade in Background
    $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
    $('#fade').css({ 'filter': 'alpha(opacity=40)' }).fadeIn(); //Fade in the fade layer 	
  }

  $('a.izh').live('click', function() {
    $('#fade , .popup').fadeOut(function() {
      $('#fade').remove();
      $.cookie("town", 1);
    });
    return false;
  });


  //Close Popups and Fade Layer
  $('a.close, #fade').live('click', function() { //When clicking on the close or fade layer...
    $('#fade , .popup').fadeOut(function() {
      $('#fade').remove();
    }); //fade them both out

    return false;
  });

  $('.fancy_order_form.fb').click(function() {
    ga('send', 'event', 'buttons', 'order', 'openform');
  });
  
  
  
});

function initScrollPane(scrollPanes) {
  $.each(scrollPanes, function() {
    var scrollPane = this.pane;

    // ��������� ������ ������
    var newWidth = 0;
    scrollPane.find('.scroll-pane > ul > li').each(function() {
      newWidth += this.scrollWidth;
    });
    scrollPane.find('.scroll-pane > ul').css('width', newWidth + 'px');

    var pageWidth = scrollPane.parent().width()
    scrollPane.find('.scroll-pane').jScrollHorizontalPane({
      minimumWidth: pageWidth,
      wheelSpeed: this.offset / (1 - pageWidth / newWidth)
    });

    if (scrollPane.find('.scroll-pane > ul').width() > scrollPane.width()) {
      // ��������� ����������� �������
      scrollPane.find('a.left-arrow').mousedown(function() {
        scrollPane.find('.jScrollArrowLeft').mousedown();
        return false;
      });
      scrollPane.find('a.right-arrow').mousedown(function() {
        scrollPane.find('.jScrollArrowRight').mousedown();
        return false;
      });
    } else {
      scrollPane.find('a.left-arrow, a.right-arrow').hide();
    }
  });

}

function initScrollPane2(scrollPanes) {
  $.each(scrollPanes, function() {
    var scrollPane = this.pane;

    // ��������� ������ ������
    var newWidth = 0;
    scrollPane.find('.scroll-pane > ul.main-ul > li').each(function() {
      newWidth += this.scrollWidth;
    });
    scrollPane.find('.scroll-pane > ul.main-ul').css('width', newWidth + 'px');

    var pageWidth = scrollPane.parent().width()
    scrollPane.find('.scroll-pane').jScrollHorizontalPane({
      minimumWidth: pageWidth,
      wheelSpeed: this.offset / (1 - pageWidth / newWidth)
    });

    if (scrollPane.find('.scroll-pane > ul.main-ul').width() > scrollPane.width()) {
      // ��������� ����������� �������
      scrollPane.find('a.left-arrow').mousedown(function() {
        scrollPane.find('.jScrollArrowLeft').mousedown();
        return false;
      });
      scrollPane.find('a.right-arrow').mousedown(function() {
        scrollPane.find('.jScrollArrowRight').mousedown();
        return false;
      });
    } else {
      scrollPane.find('a.left-arrow, a.right-arrow').hide();
    }
  });

}

function responseCaptcha() {
	$('#response').css("display", "");
	$('#responseErrors').css("display", "none");
}