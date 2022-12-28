<?php

/** ASSETS 
 *   
 *  https://www.purcellyoon.com/insights/articles/php-easily-combine-css-javascript-files
 *  https://manas.tungare.name/software/css-compression-in-php
 *  https://wphave.com/minify-compress-javascript-files-php/
 *
 * 
 */

// declare(strict_types=1);
if ( ! defined('ABSPATH') || ! defined('WP_LIBRARY')  || ! defined( 'rozard' ) ){ exit; }
if ( ! function_exists('rozard_script_master') ) {


/** REGISTER CLASS TO BODY */

    function rozard_master_class( $classes ) {

        $rozard_class = ( WP_DEBUG === true ) ? ' devel-rozard' : ' rozard';
        $classes .= $rozard_class;
        return $classes;
    }
    add_filter( 'admin_body_class', 'rozard_master_class' );



/** MAIN METHOD - EXPERIMENTAL*/


    function rozard_script_master( $hook_suffix ) {

        $vendor = get_cores()->assets['vendor']['admin']['link'];
        $admcss = get_cores()->assets['styles']['admin']['link'];
        $admjsx = get_cores()->assets['script']['admin']['link'];
      
        // preparation
        rozard_cores_optime( $hook_suffix );
        rozard_cores_assets( $vendor, $admcss, $admjsx );


        /** EXPERIMENTAL - MEMORY CONSUME IS TO HIGH ( NEED OPTIMIZE )  */

        /* 
        // compose assets
        $allow_css = array(
            'wp-admin/css', 
            'wp-admin/themes', 
            'wp-admin/extend',
            'thickbox.css', 
            'wp-content/extend', 
            'wp-content/plugins/', 
            'wp-content/themes/',
            'imgareaselect.css',
        );
        $allow_jsx = array(
            'wp-admin/extend/', 
            'wp-content/extend/', 
            'wp-content/plugins/', 
            'wp-content/themes', 
        );
        $css_file = ( is_admin() ) ? ABSPATH . 'wp-admin/css/wp-admins.css' : ABSPATH . 'wp-content/extend/script/themes.css' ;
        $jsx_file = ( is_admin() ) ? ABSPATH . 'wp-admin/js/wp-admins.js'  : ABSPATH . 'wp-content/extend/script/themes.js'  ;
        rozard_assets_composer( $hook_suffix, 'styles', $css_file, $allow_css,);
        rozard_assets_composer( $hook_suffix, 'script', $jsx_file, $allow_jsx );


        // render style
        $css_link = ( is_admin() ) ? admin_url('css/wp-admins.css') : content_url('extend/script/themes.css') ;
        wp_enqueue_style( 'wp-composer' , $css_link , array(), rozard_version , 'all' );


        // render script
        $jsx_link = ( is_admin() ) ? admin_url('js/wp-admins.js') : content_url('extend/script/themes.js') ;
        wp_enqueue_script( 'wp-composer' , $jsx_link , array(), rozard_version , true );

        unset( $hook_suffix );  */
    }
    add_action( 'admin_enqueue_scripts', 'rozard_script_master', 999);



/** REGISTER CORES ASSSETS */

    function rozard_cores_assets( string $vendor, string $admcss, string $admjsx ) {

        // REGISTER VENDOR CSS
        $vendor_css = array(
            'vendor-lineaws' => 'styles/line-awsome/css/line-awesome.css',
            'vendor-spectre' => 'styles/spectre/dist/spectre.css',
        );
        foreach( $vendor_css as $handle => $path ) {
            wp_enqueue_style(  $handle , $vendor . $path , array(), rozard_version, 'all' );
        }


        // REGISTER CORES CSS
        $rozard_css = array(
            'core-bricket-action'    => 'bricket-action.css',
            'core-bricket-button'    => 'bricket-button.css',
            'core-bricket-card'      => 'bricket-card.css',
            'core-bricket-field'     => 'bricket-field.css',
            'core-bricket-form'      => 'bricket-form.css',
            'core-bricket-head'      => 'bricket-head.css',
            'core-bricket-lottie'    => 'bricket-lottie.css',
            'core-bricket-menu'      => 'bricket-menu.css',
            'core-bricket-page'      => 'bricket-page.css',
            'core-bricket-tabs'      => 'bricket-tabs.css',
            'core-rebase-noticer'    => 'rebase-noticer.css',
            'core-widget-taxopost'   => 'widget-taxopost.css',
            'core-main'              => 'extend-main.css',
        );
        foreach( $rozard_css as $handle => $path ) {
            wp_enqueue_style(  $handle , $admcss . $path , array(), rozard_version, 'all' );
        }
    
    
        // REGISTER VENDOR JSX
        $vendor_jsx= array(
            'vendor-lotties' => 'script/lottie/lottie.js',
        );
        foreach( $vendor_jsx as $handle => $path ) {
            wp_enqueue_script(  $handle , $vendor . $path , array(), rozard_version, true );
        }

        
        // REGISTER CORES JSX
        $rozard_jsx= array(
            'core-main'          => 'main-extend.js',
            'core-rebase-notice' => 'rebase-notice.js',
        );
        foreach( $rozard_jsx as $handle => $path ) {
            wp_enqueue_script(  $handle , $admjsx . $path , array(), rozard_version, true );
        }
    }



/** OPTIMIZE CORES ASSSETS */

    function rozard_cores_optime( string $hook_suffix ) {
        // adjust core script;
        if ( $hook_suffix === 'index.php' ) {
            rozard_derender_script( 'site-health' );
            rozard_derender_script( 'plugin-install' );
            rozard_derender_script( 'updates' );
            rozard_derender_script( 'media-upload' );
        }

        if ( $hook_suffix === 'edit.php' ) {
            rozard_derender_script( 'inline-edit-post' );
        }

        rozard_derender_script( 'svg-painter' );
        rozard_derender_styles( 'svg-painter' );

        // clear variable
        unset( $hook_suffix );
    }



/** DERENDER ASSETS MODULE */

    function rozard_derender_styles( string $handle ) {
        wp_dequeue_style( $handle );
        wp_deregister_style( $handle );

        // clear variable
        unset( $handle );
    }

    function rozard_derender_script( string $handle ) {
        wp_dequeue_script( $handle);
        wp_deregister_script( $handle);

        // clear variable
        unset( $handle );
    }


/** COMPOSER ASSETS MODULE */
    function rozard_assets_composer( string $hook_suffix, string $types, string $target, array $filter = array(),  ) {
        
        global $wp_styles;
        global $wp_scripts;
        $assets = ( $types === 'styles') ? $wp_styles : $wp_scripts;
        $result = array();

        foreach( $assets->queue as $file ){
            $asset_path = uri_to_path( $assets->registered[$file]->src );
            if ( $asset_path !== false && is_array_contains( $asset_path, $filter ) === true ) {
                $result[] = $asset_path;
                if ( $types === 'styles' ) {
                    rozard_derender_styles( $assets->registered[$file]->handle );
                } 
                else {
                    rozard_derender_script( $assets->registered[$file]->handle );
                } 
            }
        };

        // combine assets
        rozard_assets_combiner( $hook_suffix, $types, $target, $result );

        // clear variable
        unset( $hook_suffix, $types, $target, $result );
    }

    function rozard_assets_combiner( string $hook_suffix, string $types, string $target, array $files = array() ) {
    
        $asset_key = 'cache_assets_'. $types .'_'. str_keys( $hook_suffix );
        $assetfile = wp_cache_get( $asset_key );

        // get combined assets from cache
        if ( $assetfile === true ) {
            file_put_contents( $target, $assetfile );
            dei( 'Assets "'. $target . '" on ' . $hook_suffix . ' cached' );
            return;
        }
        dei( 'Assets "'. $target . '" on ' . $hook_suffix . ' not cached' );


        // combine assets files
        $streamcss = fopen('php://memory','r+');
        foreach ( $files as $file ) {

            if ( file_exists( $file ) ) {

                $contents = file_get_contents( $file );
                if ( pathinfo( $file, PATHINFO_EXTENSION ) === 'css'  && strpos( $file, '.min.') === false )  {
                    // $contents = rozard_styles_minifier( $contents );
                } 
                else if ( pathinfo( $file, PATHINFO_EXTENSION ) === 'js' && strpos( $file, '.min.') === false )  {
                    // $contents = rozard_script_minifier( $contents );
                }
                else {
                    continue;
                }
                fwrite( $streamcss, $contents . PHP_EOL ); 
                unset( $contents);
            }
            else {
                continue;
            }
        }
        rewind($streamcss);
        file_put_contents( $target, $streamcss );
        fclose($streamcss); 


        // create cache
        $composed_file = file_get_contents( $target , true);
        wp_cache_set( $asset_key, $composed_file, '', 0 );

        // clear variable
        unset( $hook_suffix, $types, $target, $result, $composed_file );
    }

    function rozard_styles_minifier( string $string ) {

        if ( trim($string) === '' ) {
            return $string;
        } else {
            // Remove comments
            $string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
            // Remove space after colons
            $string = str_replace(': ', ':', $string);
            // Remove whitespace
            $string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $string);
            return $string;
        }
    }

    function rozard_script_minifier( string $string ) {

        if ( trim($string) === '' ) {
            return $string;
        } else {
            // Remove  tab
            $string = str_replace("\t", " ", $string);

            // Remove comments with "// "
            $string = preg_replace('/\n(\s+)?\/\/[^\n]*/', "", $string);	

            // Remove other comments
            $string = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $string);
            $string = preg_replace("/\/\*[^\/]*\*\//", "", $string);
            $string = preg_replace("/\/\*\*((\r\n|\n) \*[^\n]*)+(\r\n|\n) \*\//", "", $string);		

            // Remove a carriage return
            $string = str_replace("\r", "", $string);

            // Remove whitespaces
            $string = preg_replace("/\s+\n/", "\n", $string);	
            $string = preg_replace("/\n\s+/", "\n ", $string);
            $string = preg_replace("/ +/", " ", $string);

            // remove new line
            $string = trim(preg_replace('/\s+/', ' ', $string));

            return $string;
        }
    }
}