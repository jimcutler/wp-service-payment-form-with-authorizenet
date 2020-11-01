<?php
function wpspf_paymentform($atts = []){
    $formId = false;
    if( isset( $atts['form_id'] ) && !empty( $atts['form_id'] ) ){
        $formId = sanitize_text_field($atts['form_id']);
    }
    else {
        $formId = wpspf_get_min_form_id();
    }
    if(!wpspf_form_exists($formId)){
        return '<div class="error">Payment form could not be loaded.</div>';
    }
    $formSettings = wpspf_get_form_settings($formId);
    $publickey = get_option( 'wpspf_sitekey' );
    $admin_ajax_url = admin_url('admin-ajax.php');
    $formName = wpspf_get_form_name($formId);
    $formName = strtolower(str_replace(' ', '_', trim($formName)));
    $heading = (isset($formSettings['title']) && !empty(trim($formSettings['title']))) ? '<h1>'.esc_attr($formSettings['title']).'</h1>' : '';
    $formHtml = '<div class="payment_box payment_method_authorizenet_lightweight">'.$heading.'<div class="wpspf_form_container" id="wpspf_form_container">
        <form method="post" id="wpspf_form" onsubmit="return wpspfCheckGrecaptcha();" name="'.$formName.'" enctype="multipart/form-data" action="">
        <table id="wc-authorizenet_lightweight-cc-form" class="wc-credit-card-form wc-payment-form">';
    $formFields = wpspf_get_form_fields($formId);
        //override the form fields
        $formFields = apply_filters( 'wpspf_frontend_form_fields', $formFields);
    if(!empty($formFields) && count($formFields)>0){
        foreach($formFields as $formField){
            $fieldAttributes = json_decode($formField->field_other_attributes);
            $formHtml .= wpspf_get_dynamic_form_field_view($fieldAttributes);
        }
        //adding payment gateway fields if enabled
        $formHtml .= wpspf_get_paymentgateway_field_view($formSettings);
    }
    $formHtml .='</table><input type="hidden" name="form_id" value="'.$formId.'"></form></div><div id="wpspf_response"></div></div>';
    $formHtml .= "<script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit'
        async defer></script><script> var sitekey = '".$publickey."'; var admin_ajax_url = '".$admin_ajax_url."'; </script>";
    return $formHtml;
}
add_shortcode('wpspf-paymentform','wpspf_paymentform');
?>