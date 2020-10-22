<?php
$currentAction = (isset($_GET['action']) || isset($_POST['action'])) ? (isset($_GET['action'])) ? $_GET['action'] : $_POST['action'] : 'select';

$formId = (isset($_GET['form_id']) && intval($_GET['form_id']) > 0)  ? intval(trim($_GET['form_id'])) : null;

//Delete field
if($currentAction==='delete' && isset($_GET['page']) && isset($_GET['field'])){
    check_admin_referer('wpspf_delete_field', 'wpspf_nonce');
    if(current_user_can('manage_options')){
        $fieldId = intval(trim($_GET['field']));
        wpspf_delete_form_fields($fieldId);
    }
}
if($currentAction==='add-form') {
    if(wp_verify_nonce($_POST['wpspf_nonce'], 'wpspf_nonce_form_action')){
        if(current_user_can('manage_options')){
            $name = (isset($_POST['wpspf_form_name'])) ? sanitize_text_field($_POST['wpspf_form_name']) : '';
            $formId = wpspf_add_form($name);
        }
    }
}
$formsList = get_option('wpspf_forms_list');
?>
<div class="wrap">
<h1 class="wp-heading-inline"><?php echo esc_html_e( 'Form Settings', 'wpspf_with_authorize.net' ); ?></h1><a href="javascript:void(0);" id="wpspf_add_new_field" class="page-title-action">Add New Field</a>
    <div id="wpspf_select_form_wrap">
        <form name="wpspf_select_form" method="get" action="<?php echo get_admin_url() .'admin.php?page=wpspf-form-settings' ?>">
            <label>Select a form to edit:</label>
            <select name="form_id"><?php
    $idMatched = false;
    foreach($formsList as $key => $value){
        if ($key == $formId) {
            $selected = ' SELECTED';
            $idMatched = true;
        }
        else{
            $selected = '';
        }
        echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
    }
    ?>
            </select>
<?php
    if(!$idMatched){
        //set $formId to first item in the select list
        $formId = array_key_first($formsList);
    }
?>
            <input type="hidden" name="action" value="select">
            <input type="hidden" name="page" value="wpspf-form-settings">
            <input type="submit" value="Select" class="btn button">
            or <a href="" id="wpspf_add_new_form">add a new form</a>.
        </form>
    </div>
    <div id="wpspf_new_form_wrap" class="hidden">
        <h2>Add New Form</h2>
        <div id="wpspf_new_form">
            <form name="wpspf_new_form" method="post" action="<?php echo get_admin_url() .'admin.php?page=wpspf-form-settings' ?>"><label>Form Name</label>
                <input type="text" name="wpspf_form_name" id="wpspf_form_name" class="regular-text">
                <p class="description">Once the form is created you'll be able to update the default field attributes and add additional fields.</p>
                <?php echo wp_nonce_field('wpspf_nonce_form_action', 'wpspf_nonce'); ?>
                <input type="hidden" name="action" value="add-form">
                <span><a class="btn button" id="wpspf_cancel_new_form">Cancel</a></span>
                    <div class="wpspf-form-action"><input type="submit" name="add_form" id="save_menu_header" class="button button-primary button-large" value="Add Form"></div>
            </form>
        </div>
    </div>
<?php
//Edit field
if($currentAction==='edit' && isset($_GET['page']) && isset($_GET['field']) && $_GET['page']=='wpspf-form-settings'){
	$fieldId = intval(trim($_GET['field']));
	$editFields = wpspf_get_form_fields($formId, $fieldId);
	
	if(!empty($editFields)){
		foreach($editFields as $editField){
			$editFieldAttributes = json_decode($editField->field_other_attributes);
			//echo '<pre>';print_r($editFieldAttributes);echo '</pre>';
?>
<hr class="wp-header-end">
<div class="wpspf_form_setting" id="wpspf_form_setting">
	<form name="wpspf_form_setting_form" id="wpspf_form_setting_form" method="post" enctype="multipart/form-data">
		<div class="row">
			<input type="hidden" name="field_id" value="<?php echo $fieldId; ?>">
			<input type="hidden" name="form_id" value="<?php echo $formId; ?>">
			<div class="col">Selected Field Type</div>
			<div class="col">
				<select name="wpspf_field_type" class="field" id="wpspf_field_type" required>
					<option value="<?php echo $editFieldAttributes->wpspf_field_type; ?>"><?php echo $editFieldAttributes->wpspf_field_type; ?></option>
				</select>
			</div>
		</div>
	<div class="wpspf_other_field_container" id="wpspf_other_field_container">
		<?php wpspf_get_edit_field_attributes($formId, $fieldId); ?>
	</div>
	<br style="clear: both;">
	<input type="submit" name="wpspf_update_field" value="Update Field" class="btn button" id="wpspf_update_field">
	<a href="<?php echo admin_url(); ?>admin.php?page=wpspf-form-settings&form_id=<?php echo $formId ?>" class="btn button">Cancel</a>
	<div id="wpspf_response"></div>	
	</form>
</div>
<?php }}}else{ ?>
<hr class="wp-header-end">
<div class="wpspf_form_setting" id="wpspf_form_setting" style="display: none;">
	<form name="wpspf_form_setting_form" id="wpspf_form_setting_form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="form_id" value="<?php echo $formId; ?>">
		<div class="row">
			<div class="col">Select Field Type</div>
			<div class="col">
				<select name="wpspf_field_type" class="field" id="wpspf_field_type" required="required">
					<option value="">Select Field Type</option>
					<option value="text">Text</option>
					<option value="textarea">Textarea</option>
					<option value="select">Select</option>
					<option value="email">Email</option>
					<option value="number">Number</option>
					<option value="password">Password</option>
					<option value="date">Date</option>
					<option value="checkbox">Checkbox</option>
					<option value="radio">Radio</option>
					<option value="hidden">Hidden</option>
				</select>
			</div>
		</div>
	<div class="wpspf_other_field_container" id="wpspf_other_field_container"></div>
	<br style="clear: both;">
	<input type="submit" name="wpspf_save_new_field" value="Save New Field" class="btn button" id="wpspf_save_new_field" style="display: none;">
	<a href="<?php echo admin_url(); ?>admin.php?page=wpspf-form-settings" id="wpspf_cancel_add_field" class="btn button">Cancel</a>
	<div id="wpspf_response"></div>	
	</form>
</div>
<?php } ?>
<div class="wpspf_created_form_container">
	<?php	
	$formFields = wpspf_get_form_fields($formId);	
	$formHtml = '<div class="payment_box payment_method_authorizenet_lightweight">
        <form method="post" id="wpspf_form" name="payment" action="" enctype="multipart/form-data">            
        <table id="wc-authorizenet_lightweight-cc-form" class="wp-list-table widefat fixed striped wc-credit-card-form wc-payment-form"><thead><tr><th>Field Label</th><th>Field Name</th><th>Field View</th><th class="align-center">Position</th><th>Action</th></tr></thead><tbody>';
	if(!empty($formFields) && count($formFields) > 0){
		foreach($formFields as $formField){
			$fieldAttributes = json_decode($formField->field_other_attributes);			
			$case = $fieldAttributes->wpspf_field_type;			
			$formHtml .='<tr><th>'.$fieldAttributes->wpspf_input_field_label;
            if($fieldAttributes->wpspf_input_field_is_required==='true'){
            	$formHtml .='<span class="required">*</span>';
            }                
            $formHtml .='</th><td>'.$fieldAttributes->wpspf_input_field_name.'</td><td>';
			switch ($case) {
				case 'checkbox':	                
	                $options = explode("|", $fieldAttributes->wpspf_input_field_options);
	                if(!empty($options)){
	                	foreach ($options as $option) {
	                		$option = trim($option);
	                   		$formHtml .='<input type="checkbox" name="'.$fieldAttributes->wpspf_input_field_name.'" class="'.$fieldAttributes->wpspf_input_field_class.'" value="'.$option.'"><span class="wpspf_checkbox">'.$option.' </span>';
	                	}
	                } 
	                
					break;
				case 'radio':	                
	                $options = explode("|", $fieldAttributes->wpspf_input_field_options);
	                if(!empty($options)){
	                	foreach ($options as $option) {
	                		$option = trim($option);
	                   		$formHtml .='<input type="radio" name="'.$fieldAttributes->wpspf_input_field_name.'" class="'.$fieldAttributes->wpspf_input_field_class.'" value="'.$option.'"><span class="wpspf_radio">'.$option.' </span>';
	                	}
	                }					
					break;
				case 'file':
	                $formHtml .='<input type="'.$case.'" name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">';
					break;
				case 'textarea':
	                $formHtml .='<textarea name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" placeholder="'.$fieldAttributes->wpspf_input_field_placeholder.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">'.$fieldAttributes->wpspf_input_field_default_value.'</textarea>';
					break;
				case 'select':
	                $formHtml .='<select name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">';
	                $options = explode("|", $fieldAttributes->wpspf_input_field_options);
	                   $formHtml .='<option value="">Select</option>';
	                if(!empty($options)){
	                	foreach ($options as $option) {
	                		$option = trim($option);
	                   		$formHtml .='<option value="'.$option.'">'.$option.'</option>';
	                	}
	                } 
	                $formHtml .='</select>';					
					break;
				case 'password':
	                $formHtml .='<input type="'.$case.'" name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" placeholder="'.$fieldAttributes->wpspf_input_field_placeholder.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">';
					break;
				case 'date':
	                $formHtml .='<input type="'.$case.'" name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" placeholder="'.$fieldAttributes->wpspf_input_field_placeholder.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">';
					break;
				default:
	                $formHtml .='<input type="'.$case.'" name="'.$fieldAttributes->wpspf_input_field_name.'" id="'.$fieldAttributes->wpspf_input_field_id.'" class="'.$fieldAttributes->wpspf_input_field_class.'" placeholder="'.$fieldAttributes->wpspf_input_field_placeholder.'" value="'.$fieldAttributes->wpspf_input_field_default_value.'" required="'.$fieldAttributes->wpspf_input_field_is_required.'">';					
					break;
			}
			$formHtml .='</td><td class="align-center">'.$formField->field_position.'</td><td>';
            $formHtml .='<a href="?page=wpspf-form-settings&action=edit&form_id='.$formId.'&field='.$formField->id.'" class="btn button wpspf_btn_edit">Edit</a>';
            
            $deleteUrl = wp_nonce_url(admin_url('admin.php?page=wpspf-form-settings'), 'wpspf_delete_field', 'wpspf_nonce');
            
            if(!in_array($fieldAttributes->wpspf_input_field_name,wpspf_getDefaultFormFieldsList())){
                $formHtml .= ' <a href="'.$deleteUrl.'&field='.$formField->id.'&form_id='.$formId.'&action=delete" onclick="return confirmDelete(\''.$fieldAttributes->wpspf_input_field_label.'\');" class="btn button wpspf_btn_delete">Delete</a>';
            	//$formHtml .=' <a href="?page=wpspf-form-settings&action=delete&field='.$formField->id.'&wpspf_nonce='.wp_create_nonce( 'wpspf_nonce_field_action' ).'" onclick="return confirmDelete();" class="btn button wpspf_btn_delete">Delete</a>';
            }else{
            	$formHtml .=' <a href="javascript:void();" title="This is not deleteable." class="btn button">Default</a>';
            }
            $formHtml .='</td></tr>';
		}
	}
	echo $formHtml .= '</tbody><tfoot><tr><th>Field Label</th><th>Field Name</th><th>Field View</th><th class="align-center">Position</th><th>Action</th></tr></tfoot></table></form></div>';
	?>

</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var admin_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';		
		jQuery('#wpspf_add_new_field').on('click',function(evt){
            evt.preventDefault();
            if(jQuery(this).hasClass('disabled')){
                return;
            }
			jQuery('#wpspf_form_setting').toggle();
		});

		jQuery('#wpspf_field_type').on('change',function(){
			var fieldType = jQuery(this).val();
			jQuery.ajax({
	            url : admin_ajax_url,
	            type : 'post',
	            data : {
	                action : 'wpspf_get_form_field',
	                field_type : fieldType
	            },
	            success : function( response ) {
	            	jQuery('#wpspf_other_field_container').html(response);
	            	jQuery('#wpspf_save_new_field').show();
	            }
	        });
		});

		jQuery('#wpspf_form_setting_form').on('submit',function(event){
	        event.preventDefault();
	        var formData = jQuery(this).serializeArray();
	        jQuery.ajax( 
	            {
	                url : admin_ajax_url,
		            type : 'post',
		            data : {
		                action : 'wpspf_save_form_field',
                        wpspf_nonce : '<?php echo wp_create_nonce( 'wpspf_nonce_field_action' ) ?>',
		                field_detail : formData
		            },
		            success : function( response ) {
		            	var res = JSON.parse(response);
		            	if(res.status=='success'){
		            		jQuery('#wpspf_response').html(res.msg);
		            		setTimeout(function(){
		            			var url = '<?php echo admin_url(); ?>'+'admin.php?page=wpspf-form-settings&form_id=' + jQuery('input[name="form_id"]').val();
		            			window.location.href=url;
		            		},1000);		            		
		            	}else{
		            		jQuery('#wpspf_response').html(res.msg);
		            	}
		            	
		            }
	            });
	    });
	});
	function confirmDelete(fieldLabel){
		return confirm('Permanently delete the field "' + fieldLabel + '"?');
	}
</script>
<style type="text/css">
	#wpspf_form_setting .row{
		    width: 225px;
		    float: left;
		    padding: 10px 0px;
	}
	#wpspf_form_setting .field {
	    width: 221px;
	}
	#wc-authorizenet_lightweight-cc-form th {
	    text-align: left;
	}
	.align-center{  text-align: center !important; }
	#wpspf_success_msg{
		border: 1px solid;
	    padding: 5px;
	    color: #048004;
	}
	#wpspf_error_msg{
		border: 1px solid;
	    padding: 5px;
	    color: #d8190b;
	}
	.wpspf_created_form_container{ padding-top: 10px; }
	.row .col {
	    padding: 2px;
	    font-size: 14px;
	    font-weight: 400;
	}
	form#wpspf_form input[type=text], form#wpspf_form input[type=email], form#wpspf_form input[type=number], form#wpspf_form select, form#wpspf_form textarea, form#wpspf_form input[type=password], form#wpspf_form input[type=date] {
	    width: 100%;
	}
	span.wpspf_checkbox, span.wpspf_radio{
	    padding: 2px;
	}
	a.btn.button.wpspf_btn_delete {
	    color: #d40e0e;
	    border-color: #d40e0e;
	}
	.wpspf_onlyreable {
	    box-shadow: 0 0 0 transparent;
	    border-radius: 4px;
	    border: 1px solid #7e8993;
	    color: #32373c;
	    padding: 5px;
	}
    #wpspf_new_form_wrap p.submit{
        margin: 0;
    }
    #wpspf_new_form, #wpspf_select_form_wrap {
        padding: 10px;
        overflow: hidden;
        background: #fbfbfb;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    #wpspf_select_form_wrap{
        margin: 16px 0;
    }
    #wpspf_select_form_wrap label {
        display: inline-block;
        margin-right: 3px;
        vertical-align: middle;
    }
    .wpspf-form-action {
        text-align: right;
        float: right;
    }
    .wrap .page-title-action.disabled{
        color: gray;
        border-color: gray;
        cursor: not-allowed;
    }
</style>