<?php

//phpqrcode lib
// require_once plugin_dir_path(__FILE__) . '../inc/phpqrcode/qrlib.php';

// Enqueue QR and Chart.js library
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script('qrcode', plugin_dir_url(__FILE__).'../js/qrcode.min.js','1.0');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');
});

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

// Register the user data endpoint
add_action( 'init', function () {
    add_rewrite_endpoint( 'user', EP_ROOT );
    add_rewrite_rule('^dashboard/([a-zA-Z\d-]+)/?', 'index.php?pagename=dashboard&dash_param=$matches[1]', 'top');
    add_rewrite_rule('^([a-zA-Z\d-]+)/?', 'index.php?user=$matches[1]', 'top');
});


//Accept Query Var
add_filter( 'query_vars', function ( $vars ){
    $vars[] = "user";
    return $vars;
  });
//Accept Query Var
add_filter( 'query_vars', function ( $vars ){
    $vars[] = "dash_param";
    return $vars;
  });

  function genUserName() {
    $len = 4;
    $string = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyz', ceil($len/strlen($x)) )),1,$len);
if(!get_users(array(
      'meta_key' => 'custom_user_id',
      'meta_value' => $string,
      'number' => 1
     )
    )
   ){ return $string;} else {
    genUserName();
   } 
  }

  function check_custom_userId($string) {
    if(!get_users(array(
        'meta_key' => 'custom_user_id',
        'meta_value' => $string,
        'number' => 1
       )
      )
     ) { return $string;} 
     else {
    //    show_errors();
        return '';
     }
  }

//   function show_errors()
//   {
//     global $errors;
//     $errors = new WP_Error();
//         $errors->add('user_login_error',__('This URL string already exists, please try again'));
//         return $errors;
//   }