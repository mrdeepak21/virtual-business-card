<?php
/*
Template Name: Profile Template
*/

get_header();
$user_id = trim(get_query_var( 'id' ));
$ip_addr = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');
update_user_meta($user_id, 'scan',intval(get_the_author_meta('scan', $user_id))+1);
$user_data = get_userdata($user_id);
global $wpdb;
$result = $wpdb->query("UPDATE ".TABLE_NAME." SET scan=scan+1 WHERE `client_ip` = '".$ip_addr."' AND `user_id`=".$user_id);
//If nothing found to update, it will try and create the record.
if ($result === FALSE || $result < 1) {
    $wpdb->insert(TABLE_NAME, array(
        "user_id" => $user_id,
        "scan" => 1,
        "client_ip" => $ip_addr,
    ));
}

$user_name = esc_html($user_data->first_name." ".$user_data->last_name);
$designation = esc_html( get_the_author_meta( 'designation', $user_id) );
$phone = esc_html( get_the_author_meta( 'phone', $user_id) );
$email = esc_html( get_the_author_meta( 'user_email', $user_id) );
$linkedin = esc_html( get_the_author_meta( 'linked_url', $user_id) );
$url = esc_html( get_the_author_meta( 'user_url', $user_id) );
$address = esc_html( get_the_author_meta( 'address', $user_id) );
$avatar = get_the_author_meta('avatar', $user_id);
$avatar_url = $avatar ? wp_get_attachment_url($avatar) : 'https://www.gravatar.com/avatar/'.md5($email);

?>

<div id="primary" class="content-area">
<main id="main" class="site-main" role="main">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <section>
            <div class="background" style="background-image:url('<?php echo plugin_dir_url( __FILE__ ); ?>../img/blue-background.webp')">
                        </div>
                <div class="top">
                    <div class="company-logo">
                        <img src="<?php echo plugins_url('../img/h-logo.webp', __FILE__ );?>" alt="" srcset="">
                    </div>
                    <div class="entry">                       
                        <div class="profile-photo">
                            <img src="<?php echo esc_attr_e( $avatar_url, 'sterling' ) ?>" alt="" srcset=""
                                width="200" height="200">
                        </div>
                        <h1 class="entry-title">
                            <?php echo $user_name; ?>
                        </h1>
                        <h2 class="designation">
                            <?php echo $designation; ?>
                        </h2>
                    </div>

                    <div class="contact-details">                    
                            <a href="tel:+1<?php echo $phone; ?>">
                                <img src="<?php echo plugins_url('../img/icons/phone-call-orange.png', __FILE__ );?>" class="icon" />
                                Call
                            </a>                                              
                            <a
                                href="mailto:<?php echo $email; ?>">
                                <img src="<?php echo plugins_url('../img/icons/envelope-orange.png', __FILE__ );?>" class="icon" />
                                Email
                            </a>                                              
                            <a href="<?php echo $linkedin; ?>" class="linkedin">
                                <img src="<?php echo plugins_url('../img/icons/linkedin.png', __FILE__ );?>" class="icon" />
                                Profile
                            </a>                      
                    </div>
                </div>
                <div class="content">
                    <?php if(!empty($phone)){ ?>
                    <a href="tel: <?php echo $phone; ?>" class="phone">
                        <img src="<?php echo plugins_url( '../img/icons/phone-call-black.png',__FILE__ );?>" class="icon" />
                        <div>
                          <span class="phone-data"> +1 <?php echo $phone; ?></span>
                            <p>Mobile phone</p>
                        </div>
                    </a>
                    <?php }   if(!empty($email)){  ?>
                    <a href="mailto:<?php echo $email; ?>"
                        class="mail">
                        <img src="<?php echo plugins_url('../img/icons/envelope-black.png', __FILE__ );?>" class="icon" />
                        <div >
                           <span class="email" style="display:none;"><?php echo $email;?> </span><?php echo strlen($email)>35? explode('@',$email)[0]."<br />@".explode('@',$email)[1]:$email; ?>
                            <p> Email address</p>
                        </div>

                    </a>
                    <?php }  if(!empty($url)){  ?>
                    <a href="<?php echo $url; ?>">
                        <img src="<?php echo plugins_url('../img/icons/globe-black.png', __FILE__ );?>" class="icon" />
                        <div >
                           <span class="website"> <?php echo explode('//',$url)[1]; ?></span>
                            <p>Website</p>
                        </div>
                    </a>
                    <?php }  if(!empty($address)){  ?>
                    <a href="https://maps.google.com/?q=<?php echo $address; ?>">
                        <img src="<?php echo plugins_url('../img/icons/pin-black.png', __FILE__ );?>" class="icon" />
                        <div>
                        <span class="address" style="display:none;"><?php echo $address;?> </span>
                           <?php echo str_contains($address,',')? explode(',',$address)[0].",<br />".explode(',',$address)[1].", ".explode(',',$address)[2]: $address; ?>
                            <p>Address</p>
                        </div>
                    </a>
                    <?php } ?>
                </div>
            </section>
        </div>
    </article>
    <a href="javascript:createVCard();" class="add-to-contact" id="download-vcf"><img src="<?php echo plugins_url('../img/icons/download-white.png', __FILE__ );?>" class="icon" /></a>
</main>
<footer>
    POWERED BY
    <a class="company-logo bottom" href="https://heigh10.com/" target='_blank'>
        <img src="<?php echo plugins_url( '../img/h-logo.webp', __FILE__ );?>" alt="Heigh10" srcset="">
    </a>
</footer>
</div>
<?php get_footer(); ?>