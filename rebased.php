<?php



declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) { exit ; }
if ( ! trait_exists( 'lib_rebase' ) ) {
    
    trait lib_rebase{
     
        use lib_string;
        use lib_cleans;
        use lib_valids;

    
        private $noticer = array() ;
        private $columns;
        private $bodycls;
        private $subtax_remove;
    
        
        
    /** NOTICE 
         * 
         *  @param $type    $string = error,warning,success, info, 
         *  @param $title   $string = notice title, 
         *  @param $content $string = notice messages, 
         *  @param $dismiss $bolean = true or false
         *  example: $this->new_notice('error', 'test', 'test', true );
        */
        public static function new_notice( $type, $title, $content, $dismiss ) {
            $type    = sanitize_key( $type );
            $title   = sanitize_text_field( $title );
            $content = sanitize_text_field( $content );
            array_push( $this->noticer, array( 'type' => $type, 'title' => $title, 'content' => $content, 'dismiss' =>  $dismiss ) );
            add_action( 'admin_notices', array( $this, 'render_notice' ) );
        }
    
        
        public function render_notice() {
            foreach( $this->noticer as $notice ) {
                $type    = $notice['type'];
                $title   = $notice['title'];
                $content = $notice['content'];
                $dismis  = ( $notice['dismiss'] === true ) ? 'notice-temporer' : 'is-dismissible';
                echo '<div class="notice notice-'. esc_attr( $type ) .' '. esc_attr( $dismis ) .'">';
                    echo '<figure class="avatar avatar-xl">';
                        echo '<img src="https://picturepan2.github.io/spectre/img/avatar-2.png" alt="...">';
                    echo '</figure>';
                    echo '<div class="message">';
                        echo '<h3>'. esc_html( $title ) .'</h3>';
                        echo '<p>'. esc_html( $content ) .'</p>';
                    echo '</div>';
                echo '</div>';
            }
        }
    

    
    /** COLUMNS
         * 
         *  @param $type    $string = post type plural / page screen id
         *  @param $cols    $array  = collumn will be remove
         *  @param $caps    $array  =  remove column for sepesific capability || all
         * 
         *  ex : remove fro spesific capability
         *  $this->filter_manage_column( 'posts', array( 'author' ), array( 'manage_options ) );
         * 
         *  ex : remove for all user
         *  $this->filter_manage_column( 'posts', array( 'author' ), array( 'all' ) );
         * 
        */
        public function filter_columns( $pages , $columns = array() ) {
            $columns =  $this->sanitize_arrays( $columns );
            $this->columns = $columns;
            $action_hook = 'manage_'. $pages .'_columns';
            add_action(  $action_hook , array( $this, 'collumn_rebase' ), 99);
        }
    
    
        public function collumn_rebase( $columns ) {
            
            $filtered = $this->columns;
            foreach( $filtered as $col ) {
                unset($columns[$col]);
            };
            unset( $filtered );
            return $columns;
        }


    /** BODY CLASS */

        public function add_body_class( $class ) {
            $this->bodycls = sanitize_html_class( $class );
            add_filter( 'admin_body_class', array( $this, 'admin_body_class') );
        }


        public function body_left_sidebar( string $page ) {

            if ( ! $this->is_uri_valid( $page ) ) {
                return;
            }

            $this->bodycls = sanitize_html_class( 'left-sidebar' );
            add_filter( 'admin_body_class', array( $this, 'admin_body_class') );
        }


        public function body_right_sidebar( string $page ) {

            if ( ! $this->is_uri_valid( $page ) ) {
                return;
            }

            $this->bodycls = sanitize_html_class( 'right-sidebar' );
            add_filter( 'admin_body_class', array( $this, 'admin_body_class') );
        }


        public function body_dual_sidebar( string $page ) {

            if ( ! $this->is_uri_valid( $page ) ) {
                return;
            }

            $this->bodycls = sanitize_html_class( 'dual-sidebar' );
            add_filter( 'admin_body_class', array( $this, 'admin_body_class') );
        }


        public function admin_body_class( $classes ) {
            $classes .= ' '. $this->bodycls;
            return $classes;
        }

        
    /** ADMIN MENU */

        public function remove_submenu_taxonony( string $parent ) {
            $this->subtax_remove = $parent;
            add_action( 'admin_menu', array( $this, 'submenu_taxonony_remover') );
        }

        public function submenu_taxonony_remover() {

            global $menu;
            global $submenu;
            $remove = $this->subtax_remove;

            foreach( $menu as $parent ) {
                if ( $parent[2] !== $remove ) {
                    continue;
                }
                $target = $parent[2];
                foreach( $submenu[$parent[2]] as  $taxomenu ) {

                    if( ! str_contains( $taxomenu[2], 'edit-tags.php?' ) ) {
                        continue;
                    }
                    remove_submenu_page(  $target,  $taxomenu[2] );
                };
            }
        }


        public function remove_submenu_addnew(){
            add_action( 'admin_menu', array( $this, 'submenu_addnew_remover') );
        }

        public function submenu_addnew_remover() {
            global $menu;
            global $submenu;

            foreach( $menu as $parent ) {
                if ( ! str_contains( $parent[2], 'edit.php' )  ) {
                    continue;
                }
                $target = $parent[2];
                foreach( $submenu[$parent[2]] as  $taxomenu ) {

                    if( ! str_contains( $taxomenu[2], 'post-new.php' ) ) {
                        continue;
                    }
                    remove_submenu_page(  $target,  $taxomenu[2] );
                };
            }
        }
    }
}
