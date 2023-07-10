<?php
// AJAX handler for generating QR code with logo
add_action('wp_ajax_custom_qr_code_logo', 'custom_qr_code_logo_ajax_handler');
add_action('wp_ajax_nopriv_custom_qr_code_logo', 'custom_qr_code_logo_ajax_handler');

function custom_qr_code_logo_ajax_handler() {
    // Verify the AJAX request
    check_ajax_referer('custom_qr_code_logo_ajax_nonce', 'security');

    // Retrieve the custom string from the AJAX request
    $string = site_url()."/".$_POST['string'];

    $tempDir = plugin_dir_path(__FILE__) . '../temp/'; // Temporary directory to store QR code image
    if (!is_dir($tempDir)) {
        mkdir($tempDir);
    }

    $filename = $tempDir . 'qr_code.png';

    QRcode::png($string, $filename, QR_ECLEVEL_H, 1000, 2); // Generate QR code image with a fixed size of 1000

    // Add the logo to the center of the QR code (optional)
    $logoPath = plugin_dir_path(__FILE__) .'../img/str-logo.png'; // Replace with the actual path to your logo image

    if (!empty($logoPath)) {
        // Add logo to the QR code
        $logo = imagecreatefromstring(file_get_contents($logoPath));
        $qrCode = imagecreatefrompng($filename);

        $qrWidth = imagesx($qrCode);
        $qrHeight = imagesy($qrCode);

        $logoWidth = 520;
        $logoHeight = 150;

        $logoX = ($qrWidth -  $logoWidth) / 2;
        $logoY = ($qrHeight -  $logoHeight ) / 2;

        // Merge the QR code and logo
        imagecopy($qrCode, $logo, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);

        // Save the final image
        imagepng($qrCode, $filename);

        // Free up memory
       imagedestroy($logo);
       imagedestroy($qrCode);
    }

    // Output the image as a data URI
    $image_src = 'data:image/png;base64,' . base64_encode(file_get_contents($filename));

    // Generate the HTML code for the QR code image
    $qr_code_html = '<img src="' . $image_src . '" alt="QR Code with Logo" width="200">';

    // Return the HTML code in the AJAX response
    wp_send_json_success($image_src);
}



// AJAX handler for showing scan Analytics
add_action('wp_ajax_scan_analytics', 'scan_analytics');
add_action('wp_ajax_nopriv_scan_analytics', 'scan_analytics');

function scan_analytics() {
    // Verify the AJAX request
    check_ajax_referer('scan_analytics_ajax_nonce', 'security');
if(!empty($_POST['string']))
{
    @ini_set('display_errors',1);
    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM ".TABLE_NAME." WHERE user_id=".$_POST['string']);

// Return the HTML code in the AJAX response
sizeof($result)>0 ? wp_send_json_success($result): wp_send_json_success('not scaned yet');
} else {
    wp_send_json_error('Error');
}
}