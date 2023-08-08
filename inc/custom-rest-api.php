<?php

// 
add_action( 'rest_api_init', function (){
    register_rest_route('analytics','btnclick',[
        'methods'=>'POST',
        'callback'=> function ($data)
        {
            $ip_addr = getenv('HTTP_CLIENT_IP')?:
            getenv('HTTP_X_FORWARDED_FOR')?:
            getenv('HTTP_X_FORWARDED')?:
            getenv('HTTP_FORWARDED_FOR')?:
            getenv('HTTP_FORWARDED')?:
            getenv('REMOTE_ADDR');
            $user_id = intval(sanitize_text_field($data['uid']));
         if(!empty($user_id)){
            global $wpdb;
            $query = sprintf("UPDATE %s SET `btn_click`=1 WHERE `client_ip` = %s AND `user_id`= %d",TABLE_NAME,$ip_addr,$user_id);
            $res = $wpdb->query($query);
            // var_dump($wpdb->last_query);
         }
        }
    ]);
});