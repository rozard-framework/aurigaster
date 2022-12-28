<?php

declare(strict_types=1);
if ( ! defined('ABSPATH') || ! defined('WP_LIBRARY')  || ! defined( 'rozard' ) ){ exit; }


/** API AND AJAX */
    add_filter('xmlrpc_enabled', '__return_false');


/** META AND INFORMATION */
    remove_action('wp_head', 'wp_generator' );