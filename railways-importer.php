<?php
/*
Plugin Name: Railways Importer
Version: 0.1-alpha
Description: `wp railways import <taxonomy>`
Plugin URI: PLUGIN SITE HERE
Text Domain: railways-importer
Domain Path: /languages
*/

define( 'RAILWAYS_CSV_DIR', dirname( __FILE__ ) . '/data' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

// Enables wp-cli command
if ( class_exists( 'WP_CLI_Command' ) ) {
	WP_CLI::add_command( 'railways', 'Railways\Cli');
}
