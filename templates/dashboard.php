<?php
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
    require_once(plugin_dir_path( __FILE__ ) .'../vendor/autoload.php');

use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\Pass\Structure;
use Passbook\Type\Generic;

$user_id = intval($user[0]->data->ID);
$user_data = get_userdata($user_id);
$url = site_url()."/".$data;
$username = esc_html($user_data->first_name." ".$user_data->last_name);
$designation = esc_html( get_the_author_meta( 'designation', $user_id) );
$avatar = get_the_author_meta('avatar', $user_id);
$image1 = $avatar ? wp_get_attachment_url($avatar) : plugin_dir_path(__FILE__ ).'../img/dummy.png';
$pngthumb = createpng($image1,'thumb_'.$user_id);
$company_id = esc_html( get_the_author_meta( 'company', $user_id) );
$com_logo = get_the_post_thumbnail_url($company_id,'full');
$image2 = get_post_meta($company_id, 'white-logo',true);
$pnglogo = createpng($image2,'logo_'.$user_id);
$company_title = get_the_title($company_id);
$email = esc_html( get_the_author_meta( 'user_email', $user_id) );
$mobile = esc_html( get_the_author_meta( 'mobile', $user_id) );
$phone = esc_html( get_the_author_meta( 'phone', $user_id) );
$bg_r = 21;
$bg_g = 33;
$bg_b = 47; 
$bg_color = 'rgb('.$bg_r.', '.$bg_g.', '.$bg_b.')';


function createpng($src,$filename){
    $bg_r = 21;
    $bg_g = 33;
    $bg_b = 47; 
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
       $color = imagecolorallocate($outputImage, $bg_r, $bg_g, $bg_b);
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


$outputDirectory = wp_upload_dir()['basedir'].'/apple-passes';
$file_url = wp_upload_dir()['baseurl'].'/apple-passes/';

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
// $pass->setLogoText($company_title);
$pass->setLabelColor('#FFFFFF');
$pass->setForegroundColor('#FFFFFF');
// $pass->setStripColor('#FFFFFF');

// Create pass structure
$structure = new Structure();

// Add primary field
$primary = new Field('name', $username);
$primary->setLabel('Name');
$structure->addPrimaryField($primary);

// // Add secondary field
$secondary = new Field('email', $email);
$secondary->setLabel('Email');
$structure->addSecondaryField($secondary);

$call_data =  !empty($mobile)?$mobile:(!empty($phone)?$phone:"");
if(!empty($call_data)){
// // Add secondary field
$auxiliary = new Field('phone',$call_data);
$auxiliary->setLabel('Phone Number');
$structure->addAuxiliaryField($auxiliary);
}

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
$barcode = new Barcode(Barcode::TYPE_QR, $url);
$pass->setBarcode($barcode);

// Create pass factory instance
$factory = new PassFactory(PASS_TYPE_IDENTIFIER, TEAM_IDENTIFIER, ORGANIZATION_NAME, P12_FILE, P12_PASSWORD, WWDR_FILE);
$factory->setOutputPath(OUTPUT_PATH);
$factory->package($pass,$data);
}
if(file_exists($pngthumb)) {
    unlink($pngthumb);
}
if(file_exists($pnglogo)) {
    unlink($pnglogo);
}

// Enqueue QR and Chart.js library
wp_enqueue_script('jquery', home_url( '/javascript/jquery.js' ),false);
wp_enqueue_script('qrcode', plugin_dir_url(__FILE__).'../js/qrcode.min.js','1.0');
wp_enqueue_style( 'sales-person-style', plugins_url( 'style.css', __FILE__ ), false, '1.0', 'all' ); 
get_header();
?>

<div id="primary" class="content-area">
<main id="main" class="site-main" role="main">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <section>
            <div class="background" style="background-image:url('<?php echo plugin_dir_url( __FILE__ ); ?>../img/blue-background.webp')">
                        </div>
                <div class="top">
                    <div class="company-logo">
                        <img src="<?php echo get_the_post_thumbnail_url( $company_id,'full'); ?>" alt="<?php echo get_the_title( $company_id); ?>"">
                    </div>
                    <div class="entry">                       
                        <div class="profile-photo">
                            <img src="<?php echo esc_attr_e( $image1, 'heigh10' ) ?>" alt="" srcset=""
                                width="180px">
                        </div>
                        <h1 class="entry-title" data-id=" <?php echo $user_id; ?>">
                            <?php echo $username; ?>
                        </h1>
                        <h2 class="designation">
                            <?php echo $designation; ?>
                        </h2>
                    </div>

                    <div style="display:flex;align-items:center;justify-content: space-evenly">                    
                    <a href="#" id="download-qr" download="<?php echo $username."_qr"; ?>"><img src="<?php echo plugins_url('../img/download.svg',__FILE__)?>" alt=" Download QR" class="d-inline-block" style="width:150px"/> </a>                                              
                    <a href="<?php echo $file_url.$data?>.pkpass" download="<?php echo $username?>.pkpass"><img src="<?php echo plugins_url('../img/add-to-apple-wallet.svg',__FILE__)?>" alt="Add to Wallet" style="width:150px"/></a>                                                                  
                    </div>
                    <div id="qr" style="display:none"></div>
                </div>
            </section>
        </div>
    </article>
</main>
<footer>
    POWERED BY
    <a class="company-logo bottom" href="https://heigh10.com/" target='_blank'>
        <img src="<?php echo plugins_url( '../img/h-logo.webp', __FILE__ );?>" alt="Heigh10" srcset="">
    </a>
</footer>
</div>
<script>
  jQuery(window).on('load',show_qr());
  function show_qr(){
      var url= '<?php echo $url;?>', logo = '<?php echo $com_logo;?>';
        jQuery(document).ready(function($) { 
        let qrdim = 500;
        var qrcode = new QRCode(document.getElementById("qr"), {
            text: url,
            width: qrdim,
            height: qrdim,
            colorDark : "#6c5ce7",
            colorLight : "#ffeaa7",
            correctLevel : QRCode.CorrectLevel.H
        });


                // Load the logo image
            var logoImage = new Image();
            logoImage.src = logo // Replace with your logo image path

            logoImage.onload = function () {
            var canvas = document.querySelector('#qr canvas');
            var ctx = canvas.getContext('2d');
            var qrSize = canvas.width;

            var logoWidth = qrSize * 0.4; // Set the desired width of the logo
            var logoHeight = (logoWidth * logoImage.height) / logoImage.width; // Calculate height while maintaining aspect ratio

            var padding = 10;
            var logoX = (qrSize - logoWidth) / 2;
            var logoY = (qrSize - logoHeight) / 2;

            // Draw background rectangle with padding
            var bgX = logoX - padding;
            var bgY = logoY - padding;
            var bgWidth = logoWidth + 2 * padding;
            var bgHeight = logoHeight + 2 * padding;

            ctx.fillStyle = "#ffeaa7"; // Replace with your desired background color
            ctx.fillRect(bgX, bgY, bgWidth, bgHeight);

            // Draw logo image on top of the background rectangle
            ctx.drawImage(logoImage, logoX, logoY, logoWidth, logoHeight);
            jQuery("#download-qr").attr('href',canvas.toDataURL());
        };
        });
    }
    </script>
<script>document.title = '<?php echo bloginfo('name')." | ".$username; ?>';</script>

    <?php
get_footer();