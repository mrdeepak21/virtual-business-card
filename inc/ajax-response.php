<?php
//creating resized image
function recreateLogo($sourceImagePath, $targetImagePath, $targetWidth = 530, $targetHeight = 150, $padding = 0) {
    // Get the original image dimensions
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourceImagePath);

    // Create an image resource based on the source image type
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourceImagePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourceImagePath);
            break;
        case IMAGETYPE_WEBP:
            $sourceImage = imagecreatefromwebp($sourceImagePath);
            break;
        // Add support for other image types if needed
        default:
            return false;
    }

    // Calculate the new image dimensions while maintaining aspect ratio
    $aspectRatio = $sourceWidth / $sourceHeight;
    if ($sourceWidth <= $targetWidth && $sourceHeight <= $targetHeight) {
        $newWidth = $sourceWidth;
        $newHeight = $sourceHeight;
    } elseif ($targetWidth / $targetHeight > $aspectRatio) {
        $newWidth = $targetHeight * $aspectRatio;
        $newHeight = $targetHeight;
    } else {
        $newWidth = $targetWidth;
        $newHeight = $targetWidth / $aspectRatio;
    }

    // Create a blank image with the target dimensions and padding
    $canvasWidth = $targetWidth + 2 * $padding;
    $canvasHeight = $targetHeight + 2 * $padding;
    $canvasImage = imagecreatetruecolor($canvasWidth, $canvasHeight);
    $whiteColor = imagecolorallocate($canvasImage, 255, 255, 255);
    imagefill($canvasImage, 0, 0, $whiteColor);

    // Calculate the position to center the resized image on the canvas
    $xOffset = ($canvasWidth - $newWidth) / 2;
    $yOffset = ($canvasHeight - $newHeight) / 2;

    // Resize and copy the source image onto the canvas with padding
    imagecopyresampled(
        $canvasImage,
        $sourceImage,
        $xOffset + $padding,
        $yOffset + $padding,
        0,
        0,
        $newWidth,
        $newHeight,
        $sourceWidth,
        $sourceHeight
    );

    // Save the image with the padding as PNG format
    imagepng($canvasImage, $targetImagePath);

    // Free up memory by destroying the image resources
    imagedestroy($sourceImage);
    imagedestroy($canvasImage);
    return true;
}


// AJAX handler for generating QR code with logo
add_action('wp_ajax_custom_qr_code_logo', 'custom_qr_code_logo_ajax_handler');
add_action('wp_ajax_nopriv_custom_qr_code_logo', 'custom_qr_code_logo_ajax_handler');

function custom_qr_code_logo_ajax_handler() {
    // Verify the AJAX request
    check_ajax_referer('custom_qr_code_logo_ajax_nonce', 'security');

    // Retrieve the custom string from the AJAX request
    $data = sanitize_post($_POST['string']);
    $id = get_user_meta( $data,'custom_user_id', true );
    $company = get_user_meta( $data,'company', true );
    $logoPath = get_the_post_thumbnail_url($company,'full');
    $string = site_url()."/".$id;
    $tempDir = plugin_dir_path(__FILE__) . '../temp/'; // Temporary directory to store QR code image
    $qr_code = $tempDir . 'qr_code.png';
    $targetLogoPath = $tempDir.'logo.png';

    // if (!is_dir($tempDir)) {
    //     mkdir($tempDir);
    // }

    // QRcode::png($string, $qr_code, QR_ECLEVEL_H, 1000, 0); // Generate QR code image with a fixed size of 1000

    // create the logo from image file
    // if (!empty($logoPath)) {

    //     recreateLogo($logoPath, $targetLogoPath);

    //     $logo = imagecreatefrompng($targetLogoPath);

    //     $qrCode = imagecreatefrompng($qr_code);

    //     $qrWidth = imagesx($qrCode);
    //     $qrHeight = imagesy($qrCode);

    //     $logoWidth = getimagesize($targetLogoPath)[0];
    //     $logoHeight = getimagesize($targetLogoPath)[1];

    //     $logoX = ($qrWidth -  $logoWidth) / 2;
    //     $logoY = ($qrHeight -  $logoHeight ) / 2;

    //     // Merge the QR code and logo
    //     imagecopy($qrCode, $logo, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);

    //     // Save the final image
    //     imagepng($qrCode, $qr_code);

    //     // Free up memory
    //     imagedestroy($logo);
    //     imagedestroy($qrCode);
    //     unlink($targetLogoPath);
    // }

    // Output the image as a data URI
    // $image_src = 'data:image/png;base64,' . base64_encode(file_get_contents($qr_code));

    // Generate the HTML code for the QR code image
    // $qr_code_html = '<img src="' . $image_src . '" alt="QR Code with Logo" width="300">';
    
    unlink($qr_code);
    // Return the HTML code in the AJAX response
    // wp_send_json_success($image_src);
}

// AJAX handler for showing scan Analytics
add_action('wp_ajax_scan_analytics', 'scan_analytics');
add_action('wp_ajax_nopriv_scan_analytics', 'scan_analytics');

function scan_analytics() {
    // Verify the AJAX request
    check_ajax_referer('scan_analytics_ajax_nonce', 'security');
    if(!empty($_POST['string']))
    {
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM ".TABLE_NAME." WHERE user_id=".$_POST['string']);

    // Return the HTML code in the AJAX response
    sizeof($result)>0 ? wp_send_json_success($result): wp_send_json_success('not scaned yet');
    } else {
        wp_send_json_error('Error');
    }
}