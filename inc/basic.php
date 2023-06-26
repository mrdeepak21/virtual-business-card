<?php

//phpqrcode lib
require_once plugin_dir_path(__FILE__) . '../inc/phpqrcode/qrlib.php';

// Add custom user type
add_action('init', function () {
    add_role(
        'sales_person',
        'Sales Person',
        array(
            'read' => true,
            // Add additional capabilities as needed
        )
    );
});

// Default user role when adding new user
add_filter('pre_option_default_role', function($default_role){
    return 'sales_person'; 
    return $default_role; //
});

##################################
//untick the send the new user and email 
##################################
add_action( 'user_new_form', function () { 
    echo '<scr'.'ipt>jQuery(document).ready(function($) { 
        $("#send_user_notification").removeAttr("checked"); 
    } ); </scr'.'ipt>';
} );
 


##################################
//remove messy profile section items
##################################
if( is_admin() ){
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
    add_action( 'personal_options', 'prefix_hide_personal_options' );
}
 
function prefix_hide_personal_options() {
  ?>
    <script type="text/javascript">
        jQuery( document ).ready(function( $ ){
            $( '#your-profile .form-table:first, #your-profile h3:first, .yoast, .user-description-wrap, .user-profile-picture, h2, .user-pinterest-wrap, .user-myspace-wrap, .user-soundcloud-wrap, .user-tumblr-wrap, .user-wikipedia-wrap' ).remove();
        } );
    </script>
  <?php
}

 // Enqueue necessary media scripts
 add_action('admin_enqueue_scripts', function ($hook) {
    if ('user-edit.php' !== $hook) {
        return;
    }
    wp_enqueue_media();
});

//Accept Query Var
add_filter( 'query_vars', function ( $vars ){
    $vars[] = "id";
    return $vars;
  });