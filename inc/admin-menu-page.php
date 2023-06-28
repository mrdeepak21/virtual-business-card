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
                    <th><?php _e('Total Page Views', 'sterling'); ?></th>
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
                        <td><img src="<?php echo esc_url($avatar_url); ?>" width="50" height="50"><br><button class="button open-popup" onclick="show_qr(<?php echo $user->ID.',\''.$name; ?>')">Show QR</button></td>                       
                        <td><a href="<?php echo get_permalink( get_page_by_path( 'qr' ) ).'&id='.$user->ID; ?>" target="_blank"><?php echo $name; ?></a></td>
                        <td><?php echo esc_html(get_the_author_meta('user_email', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('phone', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('designation', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('address', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('linked_url', $user->ID)); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('user_url', $user->ID)); ?></td>
                        <td><a class="open-popup" onclick="display_analytics(<?php echo $user->ID; ?>);"><?php echo esc_html(get_the_author_meta('scan', $user->ID)); ?></a></td>
                        <!-- Display additional columns as needed -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="popup_modal" style="display: none;">
        <div class="html">
            Loading...
        </div>
        <button class="close-popup button">	&times;</button>
    </div>

    <script>
    jQuery(document).ready(function($) {        
        // Open the popup modal
        $('.open-popup').click(function() {
            $('.popup_modal').slideDown();
        });
        // Close the popup modal
        $('.close-popup').click(function() {
            $('.popup_modal').slideUp();
        });
    }); 

    function show_qr(id,name){
        
        jQuery(document).ready(function($) {
        jQuery('.popup_modal .html').html(`<h2>QR Code for <i>${name}</i></h2><div id='qr'><h1>Generating...!</h1></div><a href="${void(0)}" id="download" class="button" download="QR-Code">Download QR Code</a>`); 
        
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
                $('.popup_modal #qr').html('<img src="'+response.data+'" alt="" srcset="" width="300">');
                $('#download').attr('href',response.data);
                
            } else {
                // Handle error
                $('.popup_modal #qr').html(response.data);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            $('.popup_modal #qr').html(xhr+"<br>"+status+"<br>"+error);
            console.log(xhr);
        }
    });
});

    }

    function display_analytics(user_id) {
        jQuery(document).ready(function($) {
        jQuery('.popup_modal .html').html(`<h2>Loading...</h2>`);
        // AJAX request
    $.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'POST',
        data: {
            action: 'scan_analytics',
            security: '<?php echo  wp_create_nonce('scan_analytics_ajax_nonce'); ?>',
            string: user_id, // Replace with the actual custom string
        },
        success: function(response) {
            if (response.success && typeof response.data =='object') {
                rows = '';
                response.data.forEach(element => {
                    rows+=`<tr>
                <td>${element.client_ip}</td>                
                <td>${element.scan}</td>  
                </tr>`; 
                });
                // Display the QR code image in a container
                $('.popup_modal .html').html(`
                <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                <th>Unique Visitors</th>
                <th>Page Views</th>
                </tr>
                </thead>
                <tbody>
                ${rows}     
                </tbody>        
                <table>
                `);

                }            
             else {
                // Handle error
                $('.popup_modal .html').html(`<h2>${response.data}</h2>`);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            $('.popup_modal .html').html(xhr+"<br>"+status+"<br>"+error);
            console.log(xhr);
        }
    });
    });
}
    </script>
    <style>
        .popup_modal{
    position: fixed;
    z-index: 10;
    background-color: #fff;
    min-width: 200px;
    min-height:100px;
    width: 360px;
    height: fit-content;
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

        .popup_model .html{
            display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
        }

        #qr{
            height: 300px;
            width: 300px;
        }
        .close-popup{
            position: absolute;
            right:0;
            top:0;
        }
        .open-popup{
            cursor: pointer;
        }
        </style>
    <?php
}