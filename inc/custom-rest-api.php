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
});