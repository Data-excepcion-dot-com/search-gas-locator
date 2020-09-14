/**
* PHP Email Form Validation - v2.0
* URL: https://bootstrapmade.com/php-email-form/
* Author: BootstrapMade.com
*/
!(function($) {
  "use strict";

  $('form.php-email-form').submit(function(e) {
    e.preventDefault();
    
    var f = $(this).find('.form-group'),
      ferror = false,
      emailExp = /^[^\s()<>@,;:\/]+@\w[\w\.-]+\.[a-z]{2,}$/i;

    f.children('input').each(function() { // run all inputs
     
      var i = $(this); // current input
      var rule = i.attr('data-rule');

      if (rule !== undefined) {
        var ierror = false; // error flag for current input
        var pos = rule.indexOf(':', 0);
        if (pos >= 0) {
          var exp = rule.substr(pos + 1, rule.length);
          rule = rule.substr(0, pos);
        } else {
          rule = rule.substr(pos + 1, rule.length);
        }

        switch (rule) {
          case 'required':
            if (i.val() === '') {
              ferror = ierror = true;
            }
            break;

          case 'minlen':
            if (i.val().length < parseInt(exp)) {
              ferror = ierror = true;
            }
            break;

          case 'email':
            if (!emailExp.test(i.val())) {
              ferror = ierror = true;
            }
            break;

          case 'checked':
            if (! i.is(':checked')) {
              ferror = ierror = true;
            }
            break;

          case 'regexp':
            exp = new RegExp(exp);
            if (!exp.test(i.val())) {
              ferror = ierror = true;
            }
            break;
        }
        i.next('.validate').html((ierror ? (i.attr('data-msg') !== undefined ? i.attr('data-msg') : 'wrong Input') : '')).show('blind');
      }
    });
    f.children('select').each(function() { // run all selects

      var i = $(this); // current select
      var rule = i.attr('data-rule');

      if (rule !== undefined) {
        var ierror = false; // error flag for current select
        var pos = rule.indexOf(':', 0);
        if (pos >= 0) {
          var exp = rule.substr(pos + 1, rule.length);
          rule = rule.substr(0, pos);
        } else {
          rule = rule.substr(pos + 1, rule.length);
        }

        switch (rule) {
          case 'required':
            if (i.children("option:selected"). val() === 'none') {
              ferror = ierror = true;
            }
            break;
        }
        i.next('.validate').html((ierror ? (i.attr('data-msg') != undefined ? i.attr('data-msg') : 'wrong Input') : '')).show('blind');
      }
    });
    if (ferror) return false;

    var this_form = $(this);
    var action = $(this).attr('action');
    // Intercep Submit
    send_submit(this_form);
  });

  function send_submit(this_form) {
    // Get State's Number
    var state = $('#estado').children("option:selected").val();
    // Get Municipio's Number
    var mun = $('#municipio').children("option:selected").val();
    // Get Order's Flag
    var flag = $('#ordenar').is(":checked") ? 1 : 0;
    // Set URL
    var action = 'getGasolineData/'+state+'/'+mun+'/'+flag;
    // Launch Request
    $.ajax({
      type: "POST",
      url: "service/"+action,
      async: true,
      timeout: 1000
    }).done( function(response){
      // Get Total 
      var total = response.results.length;
      // Check Response
      if (response.success == true) {
        // Hide Loading
        this_form.find('.loading').slideUp();
        // Hide Error's Message
        this_form.find('.error-message').slideUp();
        // Show OK's Message
        this_form.find('.sent-message').slideDown().html('Se encontraron '+total+' gasolineras');
        // Set Points on Map
        interpreter(response.results);
      } 
      else {
        // Delete Markers on Map
        deleteMarkers();
        // Hide Previous Data
        $("#tblResults").find("tbody").html("");
        // Clear Caption
        $("#tblResults").find("caption").empty();
        // Hide Loading
        this_form.find('.loading').slideUp();
        // Hide OK's Message
        this_form.find('.sent-message').slideUp();
        // Show Message
        this_form.find('.error-message').slideDown().html('Sin resultados.');
      }

    }).fail( function(data){
      var error_msg = "Server Internal Error";
      this_form.find('.loading').slideUp();
      this_form.find('.sent-message').slideUp();
      this_form.find('.error-message').slideDown().html(error_msg);
    });

  }

})(jQuery);