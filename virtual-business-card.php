<?php

/*
 * @package           virtual-business-card
 * @author            Deepak Kumar
 * @copyright         2023 Heigh10
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:      Business Card QR Generator (Heigh10)
 * Description:      Generates Dynamic QR and front end for user
 * Version:          1.0
 * Requires at least: 5.2
 * Requires PHP:      7.3
 * Author:            Deepak Kumar
 * Author URI:        https://linkedin.com/in/deepak01
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sterling
 * Domain Path:       /languages
 */

// Hook into the plugin activation
global $wpdb;
define('TABLE_NAME',$wpdb->prefix . 'virtual_card_scan_analytics');

register_activation_hook( __FILE__, function (){
    global $wpdb;
// $table_name = $wpdb->prefix . 'virtual_card_scan_analytics';

   // Replace 'my_table' with your desired table name
    $charset_collate = $wpdb->get_charset_collate();

     //Check to see if the table exists already, if not, then create it
  if($wpdb->get_var( "show tables like ".TABLE_NAME) != TABLE_NAME ) 
  {
        $sql = "CREATE TABLE ".TABLE_NAME." (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id VARCHAR(255) NOT NULL,
            scan int(20),
            client_ip VARCHAR(50) NOT NULL,
            PRIMARY KEY id (id)
        ) $charset_collate;";
 
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option( 'test_db_version', $test_db_version );
  }

} );

// require_once('inc/create-table.php');

require_once('inc/basic.php');

require_once('inc/user-custom-fields.php');

require_once('inc/ajax-response.php');

require_once('inc/admin-menu-page.php');

require_once('inc/register-template.php');
