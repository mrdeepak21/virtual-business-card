<?php
/*
Template Name: Profile Template
*/

get_header();

$user_id = get_query_var( 'id' );
update_user_meta($user_id, 'scan',intval(get_the_author_meta('scan', $user_id))+1);
$user_data = get_userdata($user_id);
$avatar = get_the_author_meta('avatar', $user_id);
$avatar_url = $avatar ? wp_get_attachment_url($avatar) : 'https://www.gravatar.com/avatar/'.md5(get_the_author_meta('user_email', $user->ID));
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
                        <img src="<?php echo plugins_url('../img/str-logo.png', __FILE__ );?>" alt="" srcset="">
                    </div>
                    <div class="entry">                       
                        <div class="profile-photo">
                            <img src="<?php echo esc_attr_e( $avatar_url, 'sterling' ) ?>" alt="" srcset=""
                                width="200" height="200">
                        </div>
                        <h1 class="entry-title">
                            <?php echo esc_html($user_data->first_name." ".$user_data->last_name); ?>
                        </h1>
                        <h2 class="designation">
                            <?php echo esc_html( get_the_author_meta( 'designation', $user_id) ); ?>
                        </h2>
                    </div>

                    <div class="contact-details">
                        <div class="call-me">
                            <a href="tel:+1<?php echo esc_html( get_the_author_meta( 'phone', $user_id) ); ?>">
                                <img src="<?php echo plugins_url('../img/icons/phone-call-orange.png', __FILE__ );?>" class="icon" />
                                Call
                            </a>
                        </div>
                        <div>
                            <a
                                href="mailto:<?php echo esc_html( get_the_author_meta( 'user_email', $user_id) ); ?>">
                                <img src="<?php echo plugins_url('../img/icons/envelope-orange.png', __FILE__ );?>" class="icon" />
                                Email
                            </a>
                        </div>
                        <div >
                            <a href="<?php echo esc_html( get_the_author_meta( 'linked_url', $user_id) ); ?>" class="linkedin">
                                <img src="<?php echo plugins_url('../img/icons/linkedin.png', __FILE__ );?>" class="icon" />
                                Profile
                            </a>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <a href="tel: <?php echo esc_html( get_the_author_meta( 'phone', $user_id) ); ?>" class="phone">
                        <img src="<?php echo plugins_url( '../img/icons/phone-call-black.png',__FILE__ );?>" class="icon" />
                        <div>
                          <span class="phone-data"> +1 <?php echo esc_html( get_the_author_meta( 'phone', $user_id) ); ?></span>
                            <p>Mobile phone</p>
                        </div>
                    </a>
                    <a href="mailto:<?php echo esc_html( get_the_author_meta( 'user_email', $user_id) ); ?>"
                        class="mail">
                        <img src="<?php echo plugins_url('../img/icons/envelope-black.png', __FILE__ );?>" class="icon" />
                        <div >
                           <span class="email" style="display:none;"><?php echo esc_html(get_the_author_meta( 'user_email', $user_id) );?> </span><?php echo strlen(esc_html( get_the_author_meta( 'user_email', $user_id)))>35? explode('@',esc_html(get_the_author_meta( 'user_email', $user_id) ))[0]."<br />@".explode('@',esc_html(get_the_author_meta( 'user_email', $user_id) ))[1]: esc_html(get_the_author_meta( 'user_email', $user_id) ); ?>
                            <p> Email address</p>
                        </div>

                    </a>
                    <a href="<?php echo esc_html( get_the_author_meta( 'user_url', $user_id) ); ?>">
                        <img src="<?php echo plugins_url('../img/icons/globe-black.png', __FILE__ );?>" class="icon" />
                        <div >
                           <span class="website"> <?php echo explode('//',esc_html( get_the_author_meta( 'user_url', $user_id) ))[1]; ?></span>
                            <p>Website</p>
                        </div>
                    </a>
                    <a href="https://maps.google.com/?q=<?php echo esc_html( get_the_author_meta( 'address', $user_id) ); ?>">
                        <img src="<?php echo plugins_url('../img/icons/pin-black.png', __FILE__ );?>" class="icon" />
                        <div>
                        <span class="address" style="display:none;"><?php echo esc_html(get_the_author_meta( 'address', $user_id));?> </span>
                           <?php echo str_contains(esc_html(get_the_author_meta( 'address', $user_id)),',')? explode(',',esc_html(get_the_author_meta( 'address', $user_id)))[0].",<br />".explode(',',esc_html(get_the_author_meta( 'address', $user_id)))[1].", ".explode(',',esc_html(get_the_author_meta( 'address', $user_id)))[2]: esc_html(get_the_author_meta( 'address', $user_id)); ?>
                            <p>Address</p>
                        </div>
                    </a>
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