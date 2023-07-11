<?php
// Add custom fields to user profile
function add_sales_person_fields($user) {
    $avatar = get_the_author_meta('avatar', $user->ID);
    $avatar_url = $avatar ? wp_get_attachment_url($avatar) : 'https://www.gravatar.com/avatar/'.md5(get_the_author_meta('user_email', $user->ID));

    ?>
    <h3><?php _e('Sales Person Information', 'sterling'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="mobile"><?php _e('Mobile', 'sterling'); ?></label></th>
            <td><input type="tel" name="mobile" id="mobile" value="<?php echo esc_attr(get_the_author_meta('mobile', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="phone"><?php _e('Phone', 'sterling'); ?></label></th>
            <td><input type="tel" name="phone" id="phone" value="<?php echo esc_attr(get_the_author_meta('phone', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="fax"><?php _e('Fax', 'sterling'); ?></label></th>
            <td><input type="tel" name="fax" id="fax" value="<?php echo esc_attr(get_the_author_meta('fax', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="address"><?php _e('Address', 'sterling'); ?></label></th>
            <td><input type="text" name="address" id="address" value="<?php echo esc_attr(get_the_author_meta('address', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="designation"><?php _e('Designation', 'sterling'); ?></label></th>
            <td><input type="text" name="designation" id="designation" value="<?php echo esc_attr(get_the_author_meta('designation', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="linked"><?php _e('Linkedin Profile', 'sterling'); ?></label></th>
            <td><input type="url" name="linked_url" id="linked" value="<?php echo esc_attr(get_the_author_meta('linked_url', $user->ID)); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="avatar"><?php _e('Avatar', 'sterling'); ?></label></th>
            <td>
                <input type="hidden" name="avatar" id="avatar" value="<?php echo esc_attr($avatar); ?>" />
                <img src="<?php echo esc_url($avatar_url); ?>" width="100" height="100" alt="<?php _e('Avatar', 'sterling'); ?>" />
                <br />
                <input type="button" class="button" value="<?php _e('Upload Avatar', 'sterling'); ?>" id="upload-avatar-button" />
                <input type="button" class="button" value="<?php _e('Remove Avatar', 'sterling'); ?>" id="remove-avatar-button" />
                <br />
                <span class="description"><?php _e('Recommended size: 300x300 pixels', 'sterling'); ?></span>
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
                title: '<?php _e('Choose or Upload an Avatar', 'sterling'); ?>',
                button: {
                    text: '<?php _e('Select Avatar', 'sterling'); ?>'
                },
                multiple: false
            });

            // When a file is selected, grab the URL and set it as the value of the avatar field
            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                if (attachment.width !== 300 || attachment.height !== 300) {
                    alert('<?php _e('Please upload an image with dimensions 300x300 pixels.', 'sterling'); ?>');
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
    update_user_meta($user_id, 'designation', $_POST['designation']);
    update_user_meta($user_id, 'linked_url', $_POST['linked_url']);
    update_user_meta($user_id, 'avatar', $_POST['avatar']);
    // Update additional fields as needed
}
add_action('personal_options_update', 'save_sales_person_fields');
add_action('edit_user_profile_update', 'save_sales_person_fields');
