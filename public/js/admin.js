// jQuery plugin to prevent double submission of forms
jQuery.fn.preventDoubleSubmission = function() {
  var $form;
  $(this).bind('submit',function(e){
    $form = $(this);

    if ($form.data('submitted') === true) {
      // Previously submitted - don't submit again
      e.preventDefault();
    } else {
      // Mark it so that the next submit can be ignored
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return $form;
};

$(function()
{
    $('#form_sahaba_story').preventDoubleSubmission();

    // Input des tags
    $('.ui-widget-content').attr('value', '5 mots max (ex: mariage)');
    
    $('ul.tagit input[type="text"]').css('color','#888');
    $('ul.tagit input[type="text"]').focus(function() {
        $(this).val('');
        $(this).css('color','#333');
    });
    
    // Code pour l'autocomplete des sahabas
    $('#sahabas_values').hide();
    
    $("#tags").tagit({
            tagSource: "/administration/api/autocomplete",
            singleField: true,
            allowSpaces: true,
            singleFieldNode: $('#sahabas_values')
    });
})