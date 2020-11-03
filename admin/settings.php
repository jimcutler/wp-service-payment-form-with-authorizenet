<?php

if(isset($_POST['submit']) && wp_verify_nonce($_REQUEST['wpspf_nonce'], 'wpspf_nonce_action')){
        $wpspfnet_enable        = (!empty($_POST['wpspfnet_enable'])) ? intval($_POST['wpspfnet_enable']) : 0;
        $wpspfnet_enable_check  = (!empty($_POST['wpspfnet_enable_check'])) ? intval($_POST['wpspfnet_enable_check']) : 0;
        $wpspf_apiloginid       = sanitize_text_field($_POST['wpspf_apiloginid']);
        $wpspf_transactionkey   = sanitize_text_field($_POST['wpspf_transactionkey']);
        $wpspf_transactionmode  = (!empty($_POST['wpspf_transactionmode'])) ? intval($_POST['wpspf_transactionmode']) : 0;
        $wpspf_removedata  = (!empty($_POST['wpspf_removedata'])) ? intval($_POST['wpspf_removedata']) : 0;
        
        $wpspf_sitekey       = trim($_POST['wpspf_sitekey']);
        $wpspf_secretekey    = trim($_POST['wpspf_secretekey']);
                
        update_option( 'wpspf_sitekey', $wpspf_sitekey, $autoload );
        update_option( 'wpspf_secretekey', $wpspf_secretekey, $autoload );
        update_option( 'wpspfnet_enable', $wpspfnet_enable, $autoload );
        update_option( 'wpspfnet_enable_check', $wpspfnet_enable_check, $autoload );
        update_option( 'wpspf_transactionmode', $wpspf_transactionmode, $autoload );
        update_option( 'wpspf_removedata', $wpspf_removedata, $autoload );
        update_option( 'wpspf_apiloginid', $wpspf_apiloginid, $autoload );
        update_option( 'wpspf_transactionkey', $wpspf_transactionkey, $autoload );
    }
?>
<div class="wrap">
<h3><?php echo esc_html_e( 'WP Service Payment Form With Authorize.net Plugin For Wordpress', 'wpspf_with_authorize.net' ); ?></h3>
<p><?php echo esc_html_e( 'Please use "[wpspf-paymentform]" shortcode for payment form.', 'wpspf_with_authorize.net' ); ?></p>
<form method="post" action="">
    <table class="form-table">        
        <!-- <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Enable/Disable', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="checkbox" name="wpspfnet_enable_servicetype" value="1" <?php if ( trim(get_option( 'wpspfnet_enable_servicetype' ))==1 ){ echo 'checked'; } ?> /><?php esc_html_e( 'Check to show service type on front end', 'wpspf_with_authorize.net' ); ?></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Service type', 'wpspf_with_authorize.net' ); ?></th>
        <td><textarea name="wpspf_servicetype" style="width:100%;" required="required" placeholder="seperate service type by | e.g. type one | type two | type three"><?php echo esc_html_e(get_option( 'wpspf_servicetype' )); ?></textarea></td>
        </tr> -->
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Enable/Disable', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="checkbox" name="wpspfnet_enable" value="1" <?php if ( trim(get_option( 'wpspfnet_enable' ))==1 ){ echo 'checked'; } ?> /><?php echo esc_html_e( 'Enable Authorize.Net', 'wpspf_with_authorize.net' ); ?></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Check Processing', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="checkbox" name="wpspfnet_enable_check" value="1" <?php if ( trim(get_option( 'wpspfnet_enable_check' ))==1 ){ echo 'checked'; } ?> /><?php echo esc_html_e( 'Enable Check Processing', 'wpspf_with_authorize.net' ); ?></td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'API Login ID', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="text" name="wpspf_apiloginid" value="<?php echo esc_attr( get_option('wpspf_apiloginid') ); ?>" required="required" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Transaction Key', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="text" name="wpspf_transactionkey" value="<?php echo esc_attr( get_option('wpspf_transactionkey') ); ?>" required="required" /></td>
        </tr>   
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Transaction Mode', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="checkbox" name="wpspf_transactionmode" value="1" <?php if ( trim(get_option( 'wpspf_transactionmode' ))==1 ){ echo 'checked'; } ?> /><?php echo esc_html_e( 'Enable Authorize.Net sandbox (Live Mode if Unchecked)', 'wpspf_with_authorize.net' ); ?>
        <?php wp_nonce_field('wpspf_nonce_action', 'wpspf_nonce'); ?>   
        </td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Remove Data on Uninstall', 'wpspf_with_authorize.net' ); ?></th>
        <td><input type="checkbox" name="wpspf_removedata" value="1" <?php if ( trim(get_option( 'wpspf_removedata' ))==1 ){ echo 'checked'; } ?> /><?php echo esc_html_e( 'Completely remove all Service Payment data when the plugin is uninstalled', 'wpspf_with_authorize.net' ); ?>
        <?php wp_nonce_field('wpspf_nonce_action', 'wpspf_nonce'); ?>   
        </td>
        </tr>

        <tr valign="top">
        <th scope="row" colspan="2"><h1><?php echo esc_html_e( 'Google reCAPTCHA Details'); ?></h1></th>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Site key'); ?></th>
        <td><input type="text" style="width:100%;" name="wpspf_sitekey" value="<?php echo get_option( 'wpspf_sitekey' ); ?>" required="required" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html_e( 'Secret key'); ?></th>
        <td><input type="text" style="width:100%;" name="wpspf_secretekey" value="<?php echo get_option( 'wpspf_secretekey' ); ?>" required="required" /></td>
        </tr>
        
    </table>
    
    <p class="submit"><input name="submit" id="submit" class="button button-primary" value="Save Form Settings" type="submit"></p>

</form>
</div>