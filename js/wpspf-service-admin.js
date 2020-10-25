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
    $('#wpspf_add_new_form').on('click', wpspfCreateForm);
    $('#wpspf_cancel_new_form').on('click', function(evt){
        evt.preventDefault();
        $('#wpspf_new_form_wrap').hide();
        $('#wpspf_select_form_wrap, .wpspf_created_form_container, #wpspf_form_actions_wrap').show();
        $('.wpspf_btn_delete, .wpspf_btn_edit, .wpspf_btn_default').removeClass('disabled');
    });
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
    $('#wpspf_duplicate_form').on('click', wpspfCreateForm);
    $('#wpspf_cancel_duplicate_form').on('click', function(evt){
        evt.preventDefault();
    });
    $('#wpspf_form_actions').on('submit', function(){
        var formName = $('input[name="form_name"').val();
        if(confirm('Permanently delete the form ' + formName + '?')){
            return true;
        }
        return false;
    });
    function wpspfCreateForm(evt){
        evt.preventDefault();
        if(evt.target.id === 'wpspf_duplicate_form'){
            var formId = $('input[name="wpspf_parent_form"]').val();
            var formName = $('select[name="form_id"] option[value="' + formId + '"]').text();
            $('#wpspf_new_form_wrap h2').text('Duplicate "' + formName + '"');
            $('#save_form').val('Duplicate Form');
            $('#wpspf_form_name').val(formName + ' Copy');
            $('.wpspf_btn_delete, .wpspf_btn_edit, .wpspf_btn_default').addClass('disabled');
        }
        else {
            $('#wpspf_new_form_wrap h2').text('Add New Form');
            $('#save_form').val('Add Form');
            $('#wpspf_form_name').val('');
            $('#wpspf_form_name').prop('placeholder', 'Enter a form name');
            $('.wpspf_created_form_container').hide();            
        }
        $('#wpspf_new_form_wrap').show();
        $('#wpspf_select_form_wrap, #wpspf_form_actions_wrap').hide();
    }
    $( '.notice-dismiss' ).on( 'click', function() {
        $(this).parent('.notice.is-dismissible').remove();
    });
})(jQuery);
