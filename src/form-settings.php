<?php
$currentAction = (isset($_GET['action']) || isset($_POST['action'])) ? (isset($_GET['action'])) ? $_GET['action'] : $_POST['action'] : 'select';

$formId = (isset($_GET['form_id']) && intval($_GET['form_id']) > 0)  ? intval(trim($_GET['form_id'])) : false;
$formErrors = [];
$formName = '';
//Delete field
if($currentAction==='delete' && isset($_GET['page']) && isset($_GET['field'])){
    check_admin_referer('wpspf_delete_field', 'wpspf_nonce');
    if(current_user_can('manage_options')){
        $fieldId = intval(trim($_GET['field']));
        wpspf_delete_form_fields($fieldId);
    }
}
if($currentAction === 'delete-form'){
    if(wp_verify_nonce($_POST['wpspf_nonce'], 'wpspf_nonce_form_action')){
        if(current_user_can('manage_options')){
            $formId = (isset($_POST['form_id'])) ? sanitize_text_field($_POST['form_id']) : false;
            if($formId){
                wpspf_delete_form($formId);
                $formId = false;
            }
        }
    }    
}
if($currentAction==='add-form') {
    if(wp_verify_nonce($_POST['wpspf_nonce'], 'wpspf_nonce_form_action')){
        if(current_user_can('manage_options')){
            $parentFormId = (isset($_POST['wpspf_parent_form'])) ? sanitize_text_field($_POST['wpspf_parent_form']) : '';
            $name = (isset($_POST['wpspf_form_name'])) ? sanitize_text_field($_POST['wpspf_form_name']) : '';
            $formId = wpspf_add_form($name, $parentFormId);
        }
    }
}
$formFields = wpspf_get_form_fields($formId);	
$formsList = get_option('wpspf_forms_list');
?>
<div class="wrap">
<h1 class="wp-heading-inline"><?php echo esc_html_e( 'Payment Forms', 'wpspf_with_authorize.net' ); ?></h1>
    <div id="wpspf_select_form_wrap">
        <form name="wpspf_select_form" method="get" action="<?php echo get_admin_url() .'admin.php?page=wpspf-form-settings' ?>">
            <label>Select a form to edit:</label>
            <select name="form_id"><?php
    $idMatched = false;
    if(count($formsList)>0){
        foreach($formsList as $key => $value){
            if ($key == $formId) {
                $selected = ' selected';
                $idMatched = true;
                $formName = $value;
            }
            else{
                $selected = '';
            }
            echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
        }
    }
    if(!$idMatched){
        if(!empty($formId)){
            $formErrors['formnotfound'] = 'The form with ID ' . $formId . ' could not be found.';
        }
        if(empty($formFields)){
            if(count($formsList)>0){
                //set $formId to first item in the select list
                $formId = array_key_first($formsList);
                $formName = $formsList[$formId];
                $formFields = wpspf_get_form_fields($formId);	
            }
            else{
                echo '<option value="-1" disabled selected>No Forms Found</option>';
                //disable select button
            }
        }
        else{
            echo '<option value="' . $formId . '" selected>Form ' . $formId . '</option>';
        }
    }
    ?>
            </select>
<?php
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
                <input type="hidden" name="wpspf_parent_form" value="<?php echo (isset($_POST['form_id'])) ? $_POST['form_id'] : $formId; ?>">
                <span><a class="btn button" id="wpspf_cancel_new_form">Cancel</a></span>
                    <div class="wpspf-form-action"><input type="submit" name="add_form" id="save_form" class="button button-primary button-large" value="Add Form"></div>
            </form>
        </div>
    </div>
<hr class="wp-header-end">
<?php 
    if(count($formErrors)>0){
        $errorHtml = '<div class="notice notice-error is-dismissible wmea_admin">';
        foreach($formErrors as $value){
            $errorHtml .= '<p>' . $value . '</p>';
        }
        $errorHtml .= '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
        echo $errorHtml;
    }   
?>
<div id="wpspf_form_actions_wrap"><h2 class="wpspf_heading_inline"><?php echo $formName; ?></h2><?php if($formId) echo ' <span>Shortcode for this form: <strong>[wpspf-paymentform form_id='.$formId.']</strong></span>'; ?>
    <form id="wpspf_form_actions" method="post" action="<?php echo get_admin_url() .'admin.php?page=wpspf-form-settings' ?>">
        <?php echo wp_nonce_field('wpspf_nonce_form_action', 'wpspf_nonce'); ?>
        <input type="hidden" name="form_id" value="<?php echo $formId; ?>">
        <input type="hidden" name="form_name" value="<?php echo $formName; ?>">
        <input type="hidden" name="action" value="delete-form">
        <a class="btn button" id="wpspf_add_new_field" >Add New Field</a> <a class="btn button" id="wpspf_duplicate_form">Duplicate Form</a> <input type="submit" class="btn button" value="Delete Form">
    </form>
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
	$formHtml = '<div class="payment_box payment_method_authorizenet_lightweight">
        <form method="post" id="wpspf_form" name="payment" enctype="multipart/form-data">            
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
                $formHtml .= ' <a href="'.$deleteUrl.'&field='.$formField->id.'&form_id='.$formId.'&action=delete" data-label="'.$fieldAttributes->wpspf_input_field_label.'" class="btn button wpspf_btn_delete">Delete</a>';
            }else{
            	$formHtml .=' <a href="javascript:void();" title="This is not deleteable." class="btn button wpspf_btn_default">Default</a>';
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
        margin: 16px 0 0;
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
    #wpspf_form a.wpspf_btn_delete.button.disabled,
    #wpspf_form a.wpspf_btn_edit.button.disabled,
    #wpspf_form a.wpspf_btn_default.button.disabled {
        cursor: not-allowed;
    }
    .wpspf_heading_inline {
        display: inline-block;
        margin-right: 20px;
    }
</style>