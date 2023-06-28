<?php
// Create custom page template for displaying sales person profile
function sales_person_profile_template($template) {
    if ( is_page( 'qr' ) ) {
    $template = plugin_dir_path(__FILE__) . '../templates/sales-person-profile.php';
    return $template;
    }
}
add_filter('page_template', 'sales_person_profile_template');

// Template Style and Script
  add_action( 'wp_enqueue_scripts', function () {
    if ( is_page( 'qr' ) ) {
        wp_enqueue_style( 'sales-person-style', plugins_url( '../templates/style.css', __FILE__ ), false, '1.0', 'all' ); 
        wp_enqueue_script('sales-person-script', plugin_dir_url(__FILE__) . '../templates/script.js',true);
    }
  });

  //Shortcode for profile
add_shortcode('custom_profile', function () {
    ob_start();
    get_template_part('profile-template');
    return ob_get_clean();
});
