<?php

declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_LIBRARY' )  || ! defined( 'rozard' ) ){ exit; }
if ( ! defined( 'rozard_daemon' ) ) { define( 'rozard_daemon', __DIR__ . '/' ) ; }


require_once rozard_daemon . 'modprobe.php';
require_once rozard_daemon . 'performs.php';
require_once rozard_daemon . 'scripter.php';