<?php
// Create admin menu page
function sales_person_menu_page() {
    add_menu_page(
        'Sales Persons',
        'Sales Persons',
        'manage_options',
        'sales_person',
        'display_sales_persons',
        'dashicons-businessman',
        25
    );
}
add_action('admin_menu', 'sales_person_menu_page');

// Display sales persons in admin menu page
function display_sales_persons() {
    // Retrieve sales persons' data and display in a table
    $users = get_users(array('role' => 'sales_person','order'=>'DESC'));
    ?>
    <div class="wrap">
        <h1 style="display:inline-block;"><?php _e('Sales Persons', 'sterling'); ?></h1>
        <a href="<?php echo admin_url('user-new.php?role=sales_person'); ?>" target="_blank" id="add-new-sales-person" class="page-title-action" style="display:inline-block;"><?php _e('Add New Sales Person', 'sterling'); ?></a>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Profile Photo', 'sterling'); ?></th>
                    <th><?php _e('Name', 'sterling'); ?></th>
                    <th><?php _e('Email', 'sterling'); ?></th>
                    <th><?php _e('Phone', 'sterling'); ?></th>
                    <th><?php _e('Designation', 'sterling'); ?></th>
                    <th><?php _e('Address', 'sterling'); ?></th>
                    <th><?php _e('Linkedin Profile', 'sterling'); ?></th>
                    <th><?php _e('Website', 'sterling'); ?></th>
                    <th><?php _e('Analytics', 'sterling'); ?></th>
                    <!-- Add more columns as needed -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : 
                    $avatar = get_the_author_meta('avatar', $user->ID);
                    $avatar_url = $avatar ? wp_get_attachment_url($avatar) : 'https://www.gravatar.com/avatar/'.md5(get_the_author_meta('user_email', $user->ID));
                    $name =  esc_html(get_the_author_meta('first_name', $user->ID)." ".get_the_author_meta('last_name', $user->ID));
                    ?>
                    <tr>                      
                        <td><img src="<?php echo esc_url($avatar_url); ?>" width="50" height="50"><br><button class="button" onclick="show_qr(<?php echo $user->ID.',\''.$name; ?>')">Show QR</button></td>                       
                        <td><a href="<?php echo get_permalink( get_page_by_path( 'qr' ) ).'&id='.$user->ID; ?>" target="_blank"><?php echo $name; ?></a></td>
                        <td><?php echo esc_html(get_the_author_meta('user_email', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('phone', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('designation', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('address', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('linked_url', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('user_url', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('scan', $user->ID)); ?></td>
                        <!-- Display additional columns as needed -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div id="popup_modal" style="display: none;">
        <h2 id="title">QR code  for <i><span id="u_name"></span></i></h2>
        <div id="qr"></div><caption>Scan the code</caption><br><br><br>
        <div><a href="" id="download" class="button" download="QR-Code">Download QR Code</a>        
        <button id="my-close-btn" class="button">Close</button></div>
    </div>

    <script>
    jQuery(document).ready(function($) {        
        // Close the popup modal
        $('#my-close-btn').click(function() {
            $('#popup_modal').slideUp();
        });
    }); 
    function show_qr(id,name){
         // Open the popup modal
         jQuery(document).ready(function($) {
        jQuery('#popup_modal').slideDown();
        jQuery('#popup_modal #qr').html("<h1>Generating...!</h1>");
        jQuery('#popup_modal #u_name').html(name);
    // AJAX request
    $.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'custom_qr_code_logo',
            security: '<?php echo  wp_create_nonce('custom_qr_code_logo_ajax_nonce'); ?>',
            string: id, // Replace with the actual custom string
        },
        success: function(response) {
            if (response.success) {
                // Display the QR code image in a container
                $('#popup_modal #qr').html('<img src="'+response.data+'" alt="" srcset="" width="300">');
                $('#download').attr('href',response.data);
                
            } else {
                // Handle error
                $('#popup_modal #text').html(response.data);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            $('#popup_modal #text').html(xhr+"<br>"+status+"<br>"+error);
            console.log(xhr);
        }
    });
});

    }
    </script>
    <style>
        #popup_modal{
    position: fixed;
    z-index: 10;
    background-color: #fff;
    min-width: 200px;
    min-height:100px;
    width: 420px;
    height: 460px;
    border-radius: 4px;
    padding: 10px;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    display: none;
    box-shadow: 0 0 50px 2px #ddd;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
        }
        #qr{
            height: 300px;
            width: 300px;
        }
        </style>
    <?php
}