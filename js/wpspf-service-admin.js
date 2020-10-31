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
    $('#wpspf_add_new_form').on('click', wpspfCreateEditForm);
    $('#wpspf_cancel_new_edit_form').on('click', function(evt){
        evt.preventDefault();
        $('#wpspf_form_settings_wrap').hide();
        $('#wpspf_select_form_wrap, .wpspf_created_form_container, #wpspf_form_actions_wrap').show();
        $('.wpspf_btn_delete, .wpspf_btn_edit, .wpspf_btn_default').removeClass('disabled');
    });
    $('#wpspf_edit_form_settings').on('click', wpspfCreateEditForm);
    $('.wpspf_btn_delete').on('click', function(){
        if($(this).hasClass('disabled')){
            return false;
        }
        var fieldLabel = $(this).data('label');
        return confirm('Permanently delete the field "' + fieldLabel + '"?');            
    });
    $('.wpspf_btn_edit, .wpspf_btn_default').on('click', function(){
        if($(this).hasClass('disabled')){
            return false;
        }        
    })
    $('#wpspf_add_new_field').on('click',function(evt){
        evt.preventDefault();
        if($(this).hasClass('disabled')){
            return;
        }
        $('#wpspf_form_setting').toggle();
    });
    $('#wpspf_cancel_add_field').on('click', function(evt){
        evt.preventDefault();
        if($('#wpspf_other_field_container').html() !== ''){
            if(!confirm('Disard new field settings?')){
                return;
            }
        }
        $('#wpspf_form_setting, #wpspf_save_new_field').hide();
        $('#wpspf_other_field_container').html('');
        $('#wpspf_field_type').val('');
    });
    $('#wpspf_duplicate_form').on('click', wpspfCreateEditForm);
    $('#wpspf_cancel_duplicate_form').on('click', function(evt){
        evt.preventDefault();
    });
    $('#wpspf_form_actions').on('submit', function(){
        var formName = $('#wpspf_form_actions input[name="form_name"').val();
        if(confirm('Permanently delete the form ' + formName + '?')){
            return true;
        }
        return false;
    });
    function wpspfCreateEditForm(evt){
        evt.preventDefault();
        var action = evt.target.id;
        resetFormSettings(action);
        var formName = $('#wpspf_form_actions input[name="form_name"]').val();
        if(action === 'wpspf_duplicate_form'){
            $('#wpspf_form_settings_wrap h2').text('Duplicate "' + formName + '"');
            $('#save_form').val('Duplicate Form');
            $('#wpspf_form_settings input[name="name"]').val(formName + ' Copy');
            $('.wpspf_btn_delete, .wpspf_btn_edit, .wpspf_btn_default').addClass('disabled');
            $('#wpspf_formcreate_instruction').show();
            $('#wpspf_form_settings input[name="form_id"').val('');
        }
        else if(action === 'wpspf_edit_form_settings'){
            $('#wpspf_form_settings_wrap h2').text(formName + ' Settings');
            $('#save_form').val('Update');
            $('#wpspf_form_settings input[name="name"]').val(formName);
            $('.wpspf_btn_delete, .wpspf_btn_edit, .wpspf_btn_default').addClass('disabled');            
            $('#wpspf_formcreate_instruction').hide();
            $('#wpspf_form_settings input[name="parent_form_id"').val('');
        }
        else {
            $('#wpspf_form_settings_wrap h2').text('Add New Form');
            $('#save_form').val('Add Form');
            $('#wpspf_form_settings input[name="name"]').val('');
            $('.wpspf_created_form_container').hide();            
            $('#wpspf_formcreate_instruction').show();
        }
        $('#wpspf_form_settings_wrap').show();
        $('#wpspf_select_form_wrap, #wpspf_form_actions_wrap').hide();
    }
    $('.notice-dismiss').on('click', function() {
        $(this).parent('.notice.is-dismissible').remove();
    });
    $('#wpspf_email_receipt').on('click', function(){
        var checked = $(this).prop('checked');
        $('#wpspf_form_settings textarea[name="email_receipt_heading"').prop('disabled', !checked);
        $('#wpspf_form_settings textarea[name="email_receipt_footer"').prop('disabled', !checked);

    });
    function resetFormSettings(action){
        if(action === 'wpspf_edit_form_settings' || action === 'wpspf_duplicate_form' && typeof(wpspfFormSettings !== 'undefined')){
            $('#wpspf_form_settings input[name="name"').val(wpspfFormSettings['name']);
            $('#wpspf_form_settings input[name="title"').val(wpspfFormSettings['title']);
            $('#wpspf_form_settings input[name="submit_button_label"').val(wpspfFormSettings['submit_button_label']);
            $('#wpspf_form_settings input[name="success_message"').val(wpspfFormSettings['success_message']);
            $('#wpspf_form_settings textarea[name="email_receipt_heading"').val(wpspfFormSettings['email_receipt_heading']);
            $('#wpspf_form_settings textarea[name="email_receipt_footer"').val(wpspfFormSettings['email_receipt_footer']);
            $('#wpspf_form_settings input[name="email_customer_receipt"').prop('checked', (wpspfFormSettings['email_customer_receipt'] === 1) ? true: false);
            $('#wpspf_form_settings input[name="generate_customer_id"').prop('checked', (wpspfFormSettings['generate_customer_id'] === 1) ? true: false);
            $('#wpspf_form_settings input[name="form_id"').val(wpspfFormSettings['id']);
            $('#wpspf_form_settings input[name="parent_form_id"').val(wpspfFormSettings['id']);
        }
        else{
            $('#wpspf_form_settings input[name="name"').val('');
            $('#wpspf_form_settings input[name="title"').val('');
            $('#wpspf_form_settings input[name="submit_button_label"').val('');
            $('#wpspf_form_settings textarea[name="email_receipt_heading"').val('');
            $('#wpspf_form_settings textarea[name="email_receipt_footer"').val('');
            $('#wpspf_form_settings input[name="success_message"').val('');
            $('#wpspf_form_settings input[name="email_customer_receipt"').prop('checked', true);
            $('#wpspf_form_settings input[name="generate_customer_id"').prop('checked', true);
            $('#wpspf_form_settings input[name="form_id"').val('');
            $('#wpspf_form_settings input[name="parent_form_id"').val('');
        }
    }
})(jQuery);
