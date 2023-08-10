<?php

// 
add_action( 'rest_api_init', function (){
    register_rest_route('analytics','btnclick',[
        'methods'=>'POST',
        'callback'=> function ($data)
        {
            $ip_addr = '127.0.0.1';
            $user_id = intval(sanitize_text_field($data['uid']));
         if(!empty($user_id)){
            global $wpdb;
            
            $res = $wpdb->query("UPDATE ".TABLE_NAME." SET btn_click=btn_click+1 WHERE `user_id`={$user_id} AND `client_ip`= {$ip_addr}");
            echo $res;
         }
        }
    ]);

    register_rest_route('validate','customUserId',[
        'methods'=>'GET',
        'callback'=> function ($data)
        {
            $id = $data['data'];
            $user =get_users(
                array(
                 'meta_key' => 'custom_user_id',
                 'meta_value' => $id,
                 'number' => 1
                )
               );
               !$user ?  wp_send_json_success(true):  wp_send_json_error(false);
            // print_r($id);
        }
    ]);
});