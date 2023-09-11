<?php
// Register Custom Post Type
function create_user_company_post_type() {
    $labels = array(
        'name'                  => _x('Companies', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Company', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Companies', 'text_domain'),
        'name_admin_bar'        => __('Company', 'text_domain'),
        'archives'              => __('Company Archives', 'text_domain'),
        'attributes'            => __('Company Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Company:', 'text_domain'),
        'all_items'             => __('All Companies', 'text_domain'),
        'add_new_item'          => __('Add New Company', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Company', 'text_domain'),
        'edit_item'             => __('Edit Company', 'text_domain'),
        'update_item'           => __('Update Company', 'text_domain'),
        'view_item'             => __('View Company', 'text_domain'),
        'view_items'            => __('View Companies', 'text_domain'),
        'search_items'          => __('Search Company', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into Company', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this Company', 'text_domain'),
        'items_list'            => __('Companies list', 'text_domain'),
        'items_list_navigation' => __('Companies list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter Companies list', 'text_domain'),
    );
    
        $args = array(
            'label'                 => __('Company', 'just-qr'),
            'description'           => __('Custom post type for user companies', 'just-qr'),
            'labels'                => $labels,
            'supports'              => array('title', 'thumbnail','custom-fields'),
            'taxonomies'            => array(),
            'hierarchical'          => false,            
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 26,
            'menu_icon'             => 'dashicons-building',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'publicly_queryable'    => false,           
            'map_meta_cap'          => true, // Map meta capabilities (required for block editor)
            'public'             => false,
            'rewrite'            => true, // Disable slug
            'exclude_from_search' => true, // Disable search indexing
            'show_in_rest'       => false, // Disable REST API
            'capability_type'    => 'post',
            'capabilities'       => array(
                'create_posts' => 'edit_user_companies', // Allow users to add new 'user-company' posts
            ),
    );
    
    register_post_type('user-company', $args);
    
    // Allow users to add/edit their own 'user-company' posts
    $roles = array('administrator', 'author'); // Adjust the roles as needed
    foreach ($roles as $role) {
        $role = get_role($role);
        if ($role) {
            $role->add_cap('edit_user_companies');
        }
    }
}
add_action('init', 'create_user_company_post_type', 0);


// Add thumbnail column to 'user-company' admin page  **************************************************
function custom_user_company_columns($columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'thumbnail' => __('Main Logo'),
        'logo' => __('White Logo'),
        'date' => __('Date'),
    );
    return $new_columns;
}
add_filter('manage_user-company_posts_columns', 'custom_user_company_columns');

// Display the company thumbnail in the custom column
function custom_user_company_column($column, $post_id) {
    if ($column === 'thumbnail') {
        if (has_post_thumbnail($post_id)) {
            echo get_the_post_thumbnail($post_id, [200,'auto']);
        } else {
            echo 'N/A';
        }
    }
    if ($column === 'logo') { // Check if we are in the 'logo' column
        // Get the custom field value for the logo (assuming 'logo' is a custom field name)
        $logo_url =get_post_meta($post_id, 'white-logo',true);
        
        if (!empty($logo_url)) {
            echo '<img src="' . esc_url($logo_url) . '" style="max-width: 100px; height: auto;background-color:#000;padding:.3em;border-radius:4px" alt="Logo" />';
        } else {
            echo 'N/A';
        }
    }
}
add_action('manage_user-company_posts_custom_column', 'custom_user_company_column', 10, 2);
