<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php echo " Dashboard - User".bloginfo( 'name' ); ?></title>
</head>
<?php

$ip_addr = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');

$data = trim(sanitize_text_field(get_query_var( 'dash_param' )));
$user =get_users(
    array(
     'meta_key' => 'custom_user_id',
     'meta_value' => $data,
     'number' => 1
    )
   );
   if(!$user) {global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 );
    exit('User Not Found!');}
    $user_id = intval($user[0]->data->ID);
$user_data = get_userdata($user_id);

$username = esc_html($user_data->first_name." ".$user_data->last_name);
$avatar = get_the_author_meta('avatar', $user_id);
$image = $avatar ? wp_get_original_image_path($avatar) : plugin_dir_path(__FILE__ ).'../img/dummy.png';
$url = esc_html( get_the_author_meta( 'user_url', $user_id) );
$designation = esc_html( get_the_author_meta( 'designation', $user_id) );

require_once(plugin_dir_path( __FILE__ ) .'../vendor/autoload.php');

use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\Pass\Structure;
use Passbook\Type\Generic;

$outputDirectory = plugin_dir_path( __FILE__ ).'../apple-passes/';
if(!file_exists($outputDirectory.$data.'.pkpass')) {
    echo '1';
if (!file_exists($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}

// Set these constants with your values
define('P12_FILE', plugin_dir_path( __FILE__ ) . '../assets/cer.p12');
define('P12_PASSWORD', 'virtual-passes-1010');
define('WWDR_FILE',  plugin_dir_path( __FILE__ ) . '../assets/AppleWWDRCA.pem');
define('PASS_TYPE_IDENTIFIER', 'pass.com.heigh10.digitalcards');
define('TEAM_IDENTIFIER', 'PH23YH2YN9');
define('ORGANIZATION_NAME', 'Heigh10');
define('OUTPUT_PATH',  $outputDirectory);
define('ICON_FILE',  plugin_dir_path( __FILE__ ) .'../img/icon.png');
define('LOGO_FILE',  plugin_dir_path( __FILE__ ) .'../img/logo.png');
define('THUMB_FILE',  $image);

// Create an event ticket
$pass = new Generic($username, $username);
$pass->setBackgroundColor('rgb(255, 234, 167)');
$pass->setLogoText('Heigh10');

// Create pass structure
$structure = new Structure();

// Add primary field
$primary = new Field('name', $username);
$primary->setLabel('Name');
$structure->addPrimaryField($primary);

// // Add secondary field
$secondary = new Field('membership', $designation);
$secondary->setLabel($designation);
$structure->addSecondaryField($secondary);

// Add icon image
$icon = new Image(ICON_FILE, 'icon');
$pass->addImage($icon);
//add logo image
$logo = new Image( LOGO_FILE, 'logo' );
$pass->addImage( $logo );
//add thumb image
$thumb = new Image( THUMB_FILE, 'thumbnail' );
$pass->addImage( $thumb );

// Set pass structure
$pass->setStructure($structure);

// Add barcode
$barcode = new Barcode(Barcode::TYPE_QR, site_url()."/".$data);
$pass->setBarcode($barcode);

// Create pass factory instance
$factory = new PassFactory(PASS_TYPE_IDENTIFIER, TEAM_IDENTIFIER, ORGANIZATION_NAME, P12_FILE, P12_PASSWORD, WWDR_FILE);
$factory->setOutputPath(OUTPUT_PATH);
$factory->package($pass,$data);
}

echo '<h1><a href="'.plugins_url('../apple-passes/',__FILE__).$data.'.pkpass " download="'.$data.'.pkpass">Download</a></h1>';
?>
<body>
    
</body>
</html>