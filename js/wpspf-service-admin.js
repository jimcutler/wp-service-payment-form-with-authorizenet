(function($){
    $('#doaction, #doaction2').on('click', function() {
        var action = ('doaction' === this.id) ? $('#bulk-action-selector-top').val() : $('bulk-action-selector-bottom').val();
        if( "-1" === action ){
            alert('Please select an action.');
            return false;
        }
        else if( 'delete' === action ) {
            return confirm('Permanently delete the selected items?');
        }
    });
    $('#wpspf_add_new_form').on('click', function(evt){
        evt.preventDefault();
        $('#wpspf_new_form_wrap').show();
        $('#wpspf_select_form_wrap').hide();
        $('.wpspf_created_form_container').hide();
        $('#wpspf_add_new_field').addClass('disabled');
    })
    $('#wpspf_cancel_new_form').on('click', function(evt){
        evt.preventDefault();
        $('#wpspf_new_form_wrap').hide();
        $('#wpspf_select_form_wrap').show();
        $('.wpspf_created_form_container').show();
        $('#wpspf_add_new_field').removeClass('disabled');
    })
    $('#wpspf_cancel_add_field').on('click', function(evt){
        evt.preventDefault();
        $('#wpspf_form_setting').hide();
        $('#wpspf_other_field_container').html('');
        $('#wpspf_save_new_field').hide();
        $('#wpspf_field_type').val('');
    })
    
})(jQuery);
