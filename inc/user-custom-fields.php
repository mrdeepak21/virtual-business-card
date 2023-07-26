<?php
// Add custom fields to user profile
function add_sales_person_fields($user) {
    $avatar = get_the_author_meta('avatar', $user->ID);
    $avatar_url = $avatar ? wp_get_attachment_url($avatar) : 'https://www.gravatar.com/avatar/'.md5(get_the_author_meta('user_email', $user->ID));
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

    $selected_company = get_user_meta($user->ID, 'company', true);
    ?>
    <h3><?php _e('Sales Person Information', 'just-qr'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="mobile"><?php _e('Mobile', 'just-qr'); ?></label></th>
            <td><input type="tel" name="mobile" id="mobile" value="<?php echo esc_attr(get_the_author_meta('mobile', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="phone"><?php _e('Phone', 'just-qr'); ?></label></th>
            <td><input type="tel" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="fax"><?php _e('Fax', 'just-qr'); ?></label></th>
            <td><input type="tel" name="fax" id="fax" value="<?php echo esc_attr(get_the_author_meta('fax', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="address"><?php _e('Address', 'just-qr'); ?></label></th>
            <td><input type="text" name="address" id="address" value="<?php echo esc_attr(get_the_author_meta('address', $user->ID)); ?>" class="regular-text" /></td>
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
            <td><input type="text" name="designation" id="designation" value="<?php echo esc_attr(get_the_author_meta('designation', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="linked"><?php _e('Linkedin Profile', 'just-qr'); ?></label></th>
            <td><input type="url" name="linked_url" id="linked" value="<?php echo esc_attr(get_the_author_meta('linked_url', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="avatar"><?php _e('Avatar', 'just-qr'); ?></label></th>
            <td>
                <input type="hidden" name="avatar" id="avatar" value="<?php echo esc_attr($avatar); ?>" />
                <img src="<?php echo esc_url($avatar_url); ?>" width="100" height="100" alt="<?php _e('Avatar', 'just-qr'); ?>" />
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
                $('#upload-avatar-button').next('img').attr('src', attachment.url);
            });

            // Open the media uploader
            mediaUploader.open();
        });

        // Handle avatar removal
        $('#remove-avatar-button').on('click', function (e) {
            e.preventDefault();
            $('#avatar').val('');
            $('#upload-avatar-button').next('img').attr('src', '');
        });
    });
})(jQuery);
    </script>
    <?php
}
add_action('show_user_profile', 'add_sales_person_fields');
add_action('edit_user_profile', 'add_sales_person_fields');


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
    !metadata_exists( 'user', $user_id, 'custom_user_id')? update_user_meta($user_id, 'custom_user_id', genUserName()):'';
    // Update additional fields as needed
}
add_action('personal_options_update', 'save_sales_person_fields');
add_action('edit_user_profile_update', 'save_sales_person_fields');
