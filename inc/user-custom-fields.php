<?php
//load admin media_files 
function load_media_files() {
    wp_enqueue_media();
}

add_action( 'admin_enqueue_scripts', 'load_media_files' );

// Add custom fields to user profile
function add_sales_person_fields($user) {
    $avatar = !empty($user->ID)?get_the_author_meta('avatar', $user->ID):'';
    $avatar_url = !empty($avatar) ? wp_get_attachment_url($avatar) :  plugin_dir_url(__FILE__ ).'../img/dummy.webp';
    $args = array(
        'post_type'      => 'user-company',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $companies[] = [get_the_ID(),get_the_title()];           
        }
        wp_reset_postdata();
    }

    $selected_company = !empty($user->ID)?get_user_meta($user->ID, 'company', true):'';
    $phone = !empty($user->ID)?esc_attr(get_the_author_meta('phone', $user->ID)):'';
    $mobile = !empty($user->ID)?esc_attr(get_the_author_meta('mobile', $user->ID)):'';
    $fax = !empty($user->ID)?esc_attr(get_the_author_meta('fax', $user->ID)):'';
    $address = !empty($user->ID)?esc_attr(get_the_author_meta('address', $user->ID)):'';
    $designation = !empty($user->ID)?esc_attr(get_the_author_meta('designation', $user->ID)):'';
    $linkedIn = !empty($user->ID)?esc_attr(get_the_author_meta('linked_url', $user->ID)):'';
    $custom_user_id = !empty($user->ID) && metadata_exists( 'user', $user->ID, 'custom_user_id')?esc_attr(get_the_author_meta('custom_user_id', $user->ID)):false;

    ?>
    <h3><?php _e('Sales Person Information', 'just-qr'); ?></h3>
    <table class="form-table">
       <?php echo ! boolval($custom_user_id) ? '<tr>
            <th><label for="custom_id">Enter URL string</label></th>
            <td><input type="text" name="custom_user_id" id="custom_id" value="'. $custom_user_id .'" class="regular-text" maxlength="15" placeholder="Leave empty to generate automatically (max: 15 char)"/><small id="custom_id_error"></small></td>
        </tr>': '' ?>
        <tr>
            <th><label for="mobile"><?php _e('Mobile', 'just-qr'); ?></label></th>
            <td><input type="tel" name="mobile" id="mobile" value="<?php echo $mobile; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="phone"><?php _e('Phone', 'just-qr'); ?></label></th>
            <td><input type="tel" name="phone" id="phone" value="<?php echo $phone; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="fax"><?php _e('Fax', 'just-qr'); ?></label></th>
            <td><input type="tel" name="fax" id="fax" value="<?php echo $fax; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="address"><?php _e('Address', 'just-qr'); ?></label></th>
            <td><input type="text" name="address" id="address" value="<?php echo $address; ?>" class="regular-text" /></td>
        </tr>
        <tr>         
            <th><label for="company"><?php _e('Company', 'just-qr'); ?></label></th>
            <td><select name="company" id="custom_company">
                    <option value=""><?php _e('Select a Company', 'just-qr'); ?></option>
                    <?php foreach ($companies as $company) :                     
                        ?>
                        <option value="<?php echo esc_attr($company[0]); ?>" <?php selected($selected_company, $company[0]); ?>>
                            <?php echo esc_attr($company[1]); ?>
                        </option>
                    <?php endforeach; ?>
                </select></td>
        </tr>
        <tr>
            <th><label for="designation"><?php _e('Designation', 'just-qr'); ?></label></th>
            <td><input type="text" name="designation" id="designation" value="<?php echo $designation; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="linked"><?php _e('Linkedin Profile', 'just-qr'); ?></label></th>
            <td><input type="url" name="linked_url" id="linked" value="<?php echo $linkedIn; ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="avatar"><?php _e('Avatar', 'just-qr'); ?></label></th>
            <td>
                <input type="hidden" name="avatar" id="avatar" value="<?php echo esc_attr($avatar); ?>" />
                <img src="<?php echo esc_url($avatar_url); ?>" width="100" height="100" alt="<?php _e('Avatar', 'just-qr'); ?>" class="avatar" />
                <br />
                <input type="button" class="button" value="<?php _e('Upload Avatar', 'just-qr'); ?>" id="upload-avatar-button" />
                <input type="button" class="button" value="<?php _e('Remove Avatar', 'just-qr'); ?>" id="remove-avatar-button" />
                <br />
                <span class="description"><?php _e('Recommended size: 300x300 pixels', 'just-qr'); ?></span>
            </td>
        </tr>
        <!-- Add more fields as needed -->
    </table>
    <script>
       (function ($) {
    $(document).ready(function () {
        var mediaUploader;

        // Handle avatar upload
        $('#upload-avatar-button').on('click', function (e) {
            e.preventDefault();

            // If the media uploader object already exists, open it
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Create the media uploader
            mediaUploader = wp.media({
                title: '<?php _e('Choose or Upload an Avatar', 'just-qr'); ?>',
                button: {
                    text: '<?php _e('Select Avatar', 'just-qr'); ?>'
                },
                multiple: false
            });

            // When a file is selected, grab the URL and set it as the value of the avatar field
            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                if (attachment.width !== 300 || attachment.height !== 300) {
                    alert('<?php _e('Please upload an image with dimensions 300x300 pixels.', 'just-qr'); ?>');
                    return;
                }

                $('#avatar').val(attachment.id);
                $('img.avatar').attr('src', attachment.url);
            });

            // Open the media uploader
            mediaUploader.open();
        });

        // Handle avatar removal
        $('#remove-avatar-button').on('click', function (e) {
            e.preventDefault();
            $('#avatar').val('');
            $('img.avatar').attr('src', '<?php echo plugin_dir_url(__FILE__ ).'../img/dummy.webp'; ?>');
        });

        //Validate Custom string url
        var re = /^[a-zA-Z0-9+=\-/@#_]+$/g;
        $('#custom_id').keyup(function(e){
            $('#custom_id_error').html('').removeAttr('class');
         if($(this).val()!='') {
        if(re.test($(this).val()))   { 
            $.get('<?php echo site_url();?>/wp-json/validate/customUserId',{"data":$(this).val()},function(data){
                if(data.success) {
                    $('#custom_id_error').html('Available').removeClass('notice-error').addClass('notice-success');
                    $('#createusersub').removeAttr('disabled');
                } else {
                    $('#createusersub').attr('disabled','disabled');
                    $('#custom_id_error').html('This URL is not allowed').removeClass('notice-success').addClass('notice-error');
                }
            });} else {
                $('#createusersub').attr('disabled','disabled');
                    $('#custom_id_error').html('This URL is not allowed').removeClass('notice-success').addClass('notice-error');
            }
        } else{         
            $('#createusersub').removeAttr('disabled');             
            }
        });
    });
})(jQuery);
    </script>
    <style>
        .notice-error{
            color: red;
        }
        .notice-success{
            color: green;
        }
    </style>
    <?php
}
add_action('show_user_profile', 'add_sales_person_fields');
add_action('edit_user_profile', 'add_sales_person_fields');
add_action('user_new_form', 'add_sales_person_fields');


// Save custom fields data
function save_sales_person_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    // Save custom fields data
    update_user_meta($user_id, 'mobile', $_POST['mobile']);
    update_user_meta($user_id, 'phone', $_POST['phone']);
    update_user_meta($user_id, 'fax', $_POST['fax']);
    update_user_meta($user_id, 'address', $_POST['address']);
    update_user_meta($user_id, 'company', $_POST['company']);
    update_user_meta($user_id, 'designation', $_POST['designation']);
    update_user_meta($user_id, 'linked_url', $_POST['linked_url']);
    update_user_meta($user_id, 'avatar', $_POST['avatar']);
    !metadata_exists( 'user', $user_id, 'custom_user_id')? update_user_meta($user_id, 'custom_user_id', empty($_POST['custom_user_id'])?genUserName():$_POST['custom_user_id']):'';
    // Update additional fields as needed
}
add_action('personal_options_update', 'save_sales_person_fields');
add_action('edit_user_profile_update', 'save_sales_person_fields');
add_action('user_register', 'save_sales_person_fields');
