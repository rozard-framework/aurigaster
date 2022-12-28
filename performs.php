<?php


declare(strict_types=1);
if ( ! defined('ABSPATH') || ! defined('WP_LIBRARY')  || ! defined( 'rozard' ) ){ exit; }



/** DISABLE  EMOJIS */


    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );	
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ); 
   

    function rozard_disable_emojis_tinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        }
        return array();
    }
    add_filter( 'tiny_mce_plugins', 'rozard_disable_emojis_tinymce'  );


    function rozard_emojis_dns_prefetch( $urls, $relation_type ) {
        if ( 'dns-prefetch' == $relation_type ) {
            $emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
            foreach ( $urls as $key => $url ) {
                if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
                    unset( $urls[$key] );
                }
            }
        } return $urls;
    }
    add_filter( 'wp_resource_hints', 'rozard_emojis_dns_prefetch' , 10, 2 );


/** HEARTBEAT */

    function rozard_manage_heartbeat() {
        $screen = get_current_screen();
        if (  $screen->is_block_editor === false ) {
            wp_deregister_script('heartbeat');
        }
    }
    // add_action( 'admin_enqueue_scripts', 'rozard_manage_heartbeat' , 1 );