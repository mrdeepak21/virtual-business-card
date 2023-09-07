<?php
// Load the user data template
add_action( 'template_redirect', function () {
  global $wp_query;
  // Check if the user endpoint is requested
  if ( isset( $wp_query->query_vars['user'] ) ) {
      // Load the template file
      include( plugin_dir_path( __FILE__ ) . '../templates/sales-person-profile.php' );
      exit();
  }
} );



// Load the admin dash template
add_action( 'template_redirect', function () {
  global $wp_query;
  // Check if the user endpoint is requested
  if ( isset( $wp_query->query_vars['dash_param'] ) ) {
      // Load the template file
      include( plugin_dir_path( __FILE__ ) . '../templates/dashboard.php' );
      exit();
  }
} );