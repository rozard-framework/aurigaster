<?php

declare(strict_types=1);
if ( ! defined('ABSPATH') || ! defined('WP_LIBRARY')  || ! defined( 'rozard' ) ){ exit; }
if ( ! function_exists ( 'rozard_header_hookers' )) {



/** HOOKERS SECTIONS */
 

    function rozard_header_hookers() {

        if ( get_current_screen()->is_block_editor()) {
            return false;
        }

        global $title;
        $header = 'global_header' ;
        $asside = str_keys($title). '_left' ;
        
        // query arguments callback
        do_action('render_query_argument');

        echo '<header>';
            do_action( $header );
        echo '</header>';
        echo '<asside class="left-sidebar">';
            do_action( $asside );
        echo '</asside>';
    }
    add_filter( 'all_admin_notices', 'rozard_header_hookers');


    function rozard_footer_hookers() {
        
        if ( get_current_screen()->is_block_editor()) {
            return false;
        }
        
        global $title;
        $credit = get_cores()->credit['statement']['credit'];
        $asside = str_keys($title) .'_right_sidebar' ;
        
        // remove wp version info
        remove_filter( 'update_footer', 'core_update_footer' ); 

        // right sidebar callback
        echo '<asside class="righ-sidebar">';
            do_action( $asside );
        echo '</asside>';


        // footer rendering
        echo '<footer id="builder-footer" class="container">';
            echo '<div class="columns" >';
                echo '<div class="credit column col-xs-12 col-sm-12 col-md-6 col-lg-6 col-6">';
                    do_action('footer_admin_right');
                    echo '<p class="small">'. $credit .'</p>';
                echo '</div>';
                echo '<div class="footnav  column col-xs-12 col-sm-12 col-md-6 col-lg-6 col-6">';
                    do_action('footer_admin_left');
                echo '</div>';
            echo '</div>';
        echo '</footer>';
    }
    add_filter( 'admin_footer_text', 'rozard_footer_hookers' );




/** REBRAND SECTIONS */

        
    function rozard_admin_favicon() {
        echo '<link rel="Shortcut Icon" type="image/x-icon" href="' . get_bloginfo('wpurl') . '/wp-content/favicon.ico" />';
    }
    add_action( 'admin_head', 'rozard_admin_favicon' );


    function rozard_editor_logo() {
    
        $brands = get_cores()->credit['company']['logo'];
        $screen = get_current_screen();

        if ( $screen->is_block_editor === false ) { 
            return; 
        }

        echo '
        <style>
            body.is-fullscreen-mode .edit-post-header a.components-button svg,
            body.is-fullscreen-mode .edit-site-navigation-toggle button.components-button svg{
                display: none;
            }
            
            body.is-fullscreen-mode .edit-post-header a.components-button:before,
            body.is-fullscreen-mode .edit-site-navigation-toggle button.components-button:before{
                background-image: url( '. esc_url( $brands ) .' );
                background-size: cover;
                box-shadow : 0 0 0 transparent;
            }

            .edit-post-fullscreen-mode-close.components-button{
                background-color: #fff;
            }
        </style>';
    }
    add_action( 'admin_enqueue_scripts', 'rozard_editor_logo' , 0);


    function rozard_login_logo() {
        $brands = get_cores()->credit['company']['logo'];
    
        echo '
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url('. esc_url( $brands ) .');
                width: 4em;
                background-size: contain;
                background-repeat: no-repeat;
                padding-bottom: 30px;
            }
        </style>';
    }
    add_action( 'login_enqueue_scripts', 'rozard_login_logo' );
    add_filter( 'login_headerurl', 'rozard_login_logo' , 10, 1 );



/** PROFILE SECTIONS */



    function rozard_custom_avatar_img( $avatar, $id_or_email, $size, $default, $alt ) {
        
        /*
        //If is email, try and find user ID
        if ( ! is_numeric( $id_or_email ) && is_email( $id_or_email ) ) {
            $user = get_user_by( 'email', $id_or_email ); 
            if ( $user ) { 
                $id_or_email = $user->ID;
            }
        }

        //if not user ID, return
        if ( ! is_numeric( $id_or_email ) ) {
            return;
        } */

        //Find URL of saved avatar in user meta
        $saved  = get_user_meta( $id_or_email, 'rozard_avatar', true );
        $males  = get_cores()->assets['images']['public']['link']. 'avatar-male.webp';
        $avatar = ( !empty( $saved ) || $saved =! null ) ? sanitize_url( $males ) : sanitize_url( $saved );

        //check if it is a URL and return saved image
        if ( filter_var( $avatar, FILTER_VALIDATE_URL ) ) {
            return sprintf( '<img src="%s" class="avatar avatar-64 photo" height="64" width="64" loading="lazy" alt="%s" />', esc_url( $avatar ), esc_attr( $alt ) );
        }
        return $avatar;
    }
    add_filter( 'get_avatar', 'rozard_custom_avatar_img' , 1, 5 );


    function rozard_custom_avatar_url( $url, $id_or_email, $args ) {
        /*
        //If is email, try and find user ID
        if ( ! is_numeric( $id_or_email ) && is_email( $id_or_email ) ) {
            $user  =  get_user_by( 'email', $id_or_email );
            if ( $user ) {
                $id_or_email = $user->ID;
            }
        }

        //if not user ID, return
        if ( ! is_numeric( $id_or_email ) ) {
            return;
        } */

        //Find URL of saved avatar in user meta
        $saved = get_user_meta( $id_or_email, 'rozard_avatar', true );
        $males = get_cores()->assets['images']['public']['link']. 'avatar-male.webp';
        $avatar = ( !empty( $saved ) || $saved =! null ) ? sanitize_url( $males ) : sanitize_url( $saved );
        return $avatar;
    }
    add_filter( 'get_avatar_url', 'rozard_custom_avatar_url' , 1, 3 );




/** FLUSHER SECTIONS */



    function rozard_render_flusher(  $subject  ) {
        // $subject = preg_replace( '#<div id="wpfooter" role="contentinfo".+?/div>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<div id="screen-options-link-wrap".+?/div>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<h1 class="wp-heading-inline">.+?Add New</a>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<h1 class="wp-heading-inline">.+?/h1>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<h1>.+?/h1>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<p>Here you can find information about updates,.+?/p>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<a class="welcome-panel-close".+?/a>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<span class="displaying-num">.+?/span>#s',  '' , $subject, 1 );
        // $subject = preg_replace( '#<h2 class="screen-reader-text">Posts .+?/h2>#s',  '' , $subject, 1 );
        return $subject;
    }


    function rozard_flusher_start() {
        ob_start( 'rozard_render_flusher' );
    }
    // add_action( 'all_admin_notices', 'rozard_flusher_start') ;


    function rozard_flusher_stop() {
        ob_end_flush();
    }
    // add_action( 'admin_footer',  'rozard_flusher_stop')  ;

}
