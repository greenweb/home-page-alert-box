// home-page-alert-box.js
jQuery(document).ready(function($){

  var hpabBGcolorOptions = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){      
      $(tinymce.activeEditor.getBody()).css("background-color", $(this).val() );     
    },
    // a callback to fire when the input is emptied or an invalid color
    clear: function() {},
    // hide the color picker controls on load
    hide: true,
    // show a group of common colors beneath the square
    // or, supply an array of colors to customize further
    palettes: true
  };
  $('#hpab_bg_color').wpColorPicker(hpabBGcolorOptions);

  var hpabTextOptions = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){
      
      $(tinymce.activeEditor.getBody()).css("color", $(this).val() );
      
    },
    // a callback to fire when the input is emptied or an invalid color
    clear: function() {},
    // hide the color picker controls on load
    hide: true,
    // show a group of common colors beneath the square
    // or, supply an array of colors to customize further
    palettes: true
  };
  $('#hpab_text_color').wpColorPicker(hpabTextOptions);


  $( "button#save-hpab" ).click(function() {
    hpabSaveAjax();
  });

  // set the editor colors
   
  
   $('body#tinymce.hpab_content').ready(
      function(){
        $('body#tinymce.hpab_content').css("background-color", 'red' );
      }
      
    );


});

function hpabSaveAjax() {
    // ajax data
    var hpab_is_active  = jQuery('input[name="hpab_is_active"]:checked').val(); 
    var hpab_bg_color   = jQuery('#hpab_bg_color').val();
    var hpab_text_color = jQuery('#hpab_text_color').val();
    var hpab_body_val   = tinyMCE.activeEditor.getContent();
    
    var data = {
      'action'          : 'homepage_alert_box_save',
      'hpab_is_active'  : hpab_is_active,
      'hpab_bg_color'   : hpab_bg_color,
      'hpab_text_color' : hpab_text_color,
      'hpab_body_val'   : hpab_body_val,
    };

    jQuery.post(ajaxurl, data, function(response) {
      if (response==0 || response==null ) {
        alert('Error '+ response);
      }else{
        // do stuff
        alert(response);
      };
    }); // ajax
}

(function ($) {

/**
* @function
* @property {object} jQuery plugin which runs handler function once specified element is inserted into the DOM
* @param {function} handler A function to execute at the time when the element is inserted
* @param {bool} shouldRunHandlerOnce Optional: if true, handler is unbound after its first invocation
* @example $(selector).waitUntilExists(function);
*/

$.fn.waitUntilExists    = function (handler, shouldRunHandlerOnce, isChild) {
    var found       = 'found';
    var $this       = $(this.selector);
    var $elements   = $this.not(function () { return $(this).data(found); }).each(handler).data(found, true);

    if (!isChild)
    {
        (window.waitUntilExists_Intervals = window.waitUntilExists_Intervals || {})[this.selector] =
            window.setInterval(function () { $this.waitUntilExists(handler, shouldRunHandlerOnce, true); }, 500)
        ;
    }
    else if (shouldRunHandlerOnce && $elements.length)
    {
        window.clearInterval(window.waitUntilExists_Intervals[this.selector]);
    }

    return $this;
}

}(jQuery));