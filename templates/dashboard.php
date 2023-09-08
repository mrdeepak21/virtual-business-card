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
$image1 = $avatar ? wp_get_attachment_url($avatar) : plugin_dir_path(__FILE__ ).'../img/dummy.png';
$pngthumb = createpng($image1,'thumb_'.$user_id);
$company_id = esc_html( get_the_author_meta( 'company', $user_id) );
$image2 = get_the_post_thumbnail_url($company_id,'full');
$company_title = get_the_title($company_id);
$pnglogo = createpng($image2,'logo_'.$user_id);
$designation = esc_html( get_the_author_meta( 'designation', $user_id) );
$bg_r = 255;
$bg_g = 234;
$bg_b = 167;
$bg_color = 'rgb('.$bg_r.', '.$bg_g.', '.$bg_b.')';

require_once(plugin_dir_path( __FILE__ ) .'../vendor/autoload.php');

use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\Pass\Structure;
use Passbook\Type\Generic;

function createpng($src,$filename){
    // Path to the source image
    $sourceImagePath = $src;    
   // Check the file type
    $imageType = exif_imagetype($sourceImagePath);

 // Supported image types: IMAGETYPE_JPEG, IMAGETYPE_PNGetc.
    if ($imageType === IMAGETYPE_JPEG || $imageType === IMAGETYPE_PNG) {
    // Load the source image based on the detected file type
     switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourceImagePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourceImagePath);
            break;       
        default:
            $sourceImage = false;
            break;
     }
    
    if ($sourceImage) {
       // Get the dimensions of the source image
       $sourceWidth = imagesx($sourceImage);
       $sourceHeight = imagesy($sourceImage);

       // Create a blank PNG image with a white background
       $outputWidth = $sourceWidth;
       $outputHeight = $sourceHeight;

       $outputImage = imagecreatetruecolor($outputWidth, $outputHeight);
       $color = imagecolorallocate($outputImage, 255, 255, 255);
       imagefill($outputImage, 0, 0, $color);

       // Calculate the position to center the source image
       $x = ($outputWidth - $sourceWidth) / 2;
       $y = ($outputHeight - $sourceHeight) / 2;

       // Copy the source image to the center of the output image
       imagecopy($outputImage, $sourceImage, $x, $y, 0, 0, $sourceWidth, $sourceHeight);
    
        // Path where you want to save the PNG image
        $outputImagePath = plugin_dir_path( __FILE__ ).'../img/'.$filename.'.png';
    
        // Save the PNG image to the specified path
        imagepng($outputImage, $outputImagePath);
    
        // Free up memory by destroying the image resources
        imagedestroy($sourceImage);
        imagedestroy($outputImage);  
        return $outputImagePath;  
    }
    }
}

$outputDirectory = plugin_dir_path( __FILE__ ).'../apple-passes/';

if(!file_exists($outputDirectory.$data.'.pkpass')) {
if (!file_exists($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}

// Set these constants with your values
define('P12_FILE', plugin_dir_path( __FILE__ ) . '../assets/cer.p12');
define('P12_PASSWORD', 'virtual-passes-1010');
define('WWDR_FILE',  plugin_dir_path( __FILE__ ) . '../assets/AppleWWDRCA.pem');
define('PASS_TYPE_IDENTIFIER', 'pass.com.heigh10.digitalcards');
define('TEAM_IDENTIFIER', 'PH23YH2YN9');
define('ORGANIZATION_NAME', $company_title);
define('OUTPUT_PATH',  $outputDirectory);
define('ICON_FILE',  plugin_dir_path( __FILE__ ) .'../img/icon.png');
define('LOGO_FILE',  $pnglogo);
define('THUMB_FILE', $pngthumb);

// Create an event ticket
$pass = new Generic($username, $username);
$pass->setBackgroundColor($bg_color);
$pass->setLogoText($company_title);

// Create pass structure
$structure = new Structure();

// Add primary field
$primary = new Field('name', $username);
$primary->setLabel('Name');
$structure->addPrimaryField($primary);

// // Add secondary field
$secondary = new Field('membership', $designation);
$secondary->setLabel('Designation');
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
unlink($pngthumb);
unlink($pnglogo);
}

echo '<h1><a href="'.plugins_url('../apple-passes/',__FILE__).$data.'.pkpass " download="'.$data.'.pkpass"> Add to apple wallet</a></h1>';
?>
<body>
    
</body>
</html>