<?php
/*
Plugin Name: Auctions 2.0
Plugin URI: N/A
Description: New in the 2.0 update: better handling of captions, better picture management, table optimization, re-organization of backend file hierarchy, clarification of file types.
Author: Sean Carruthers (Elit Dev)
License: GPLv2 or later
*/

/*

Copyright (C) 2011 Sean Carruthers (Carruthers Coding)  (email : sean_carruthers@hotmail.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

global $wpdb;

$displayName = "Auctions 2.0";
$pluginName = "alhughes-revised"; // Folder name
$backend_url = "?page=ah-main";
$errors = FALSE;

if ( $errors ) {
   error_reporting( E_ALL );
   ini_set( 'display_errors', '1' );
   ini_set( 'error_log', 'errors.txt' );
   $wpdb->show_errors();
}

DEFINE( 'AH_PATH', 'wp-content/plugins/' . $pluginName . '/' );
DEFINE( 'AH_DATA', $wpdb->prefix . "_ah_" . "auctions" );
DEFINE( 'AH_PICS', $wpdb->prefix . "_ah_" . "pics" );
DEFINE( 'AH_ATTACHMENTS', $wpdb->prefix . "_ah_" . "attachments" );
DEFINE( 'AH_VERSION', '2.1');

// cc_main() handles everything, including displaying information, processing data, etc
function ah_main() {
	// Include necessary classes / functions

		// Classes
		require_once ABSPATH . AH_PATH . "classes/class.thumbnail.php";
		require_once ABSPATH . AH_PATH . "classes/class.auction.php";
		
		
		// Functions
		require_once ABSPATH . AH_PATH . "functions/customTinyMce.php";
		require_once ABSPATH . AH_PATH . "functions/links.php";
		require_once ABSPATH . AH_PATH . "functions/displayAuctions.php";
		require_once ABSPATH . AH_PATH . "functions/getAttachmentFileName.php";
		
		// Install / Upgrades for database
		require_once ABSPATH . AH_PATH . 'install.php';
		
		// Include the main php file
		require_once( ABSPATH . AH_PATH . 'main.php' );

}

// cc_create_menu() generates and displays the navigation menu
function ah_create_menu() {
	global $displayName;
	add_menu_page( $displayName, $displayName, 'read', 'ah-main', 'ah_main' );
}



// add_stylesheet() enqueues the plugin's stylesheet, so that the css is loaded
function add_ah_stylesheets() {
	global $pluginName;
	
	$stylesheets = array( 	"css/style.css",
   							"jqueryui/css/dark-hive/jquery-ui-1.8.16.custom.css",
   							"jqueryui/css/timepicker.css",
   							"uploadify/uploadify.css",
   							"lytebox/lytebox.css"
   						);
   
	$n = 0;
	foreach( $stylesheets as $stylesheet ) {
		$styleUrl = WP_PLUGIN_URL . '/' . $pluginName . '/' . $stylesheet;
		$styleFile = WP_PLUGIN_DIR . '/' . $pluginName . '/' . $stylesheet;

		if ( file_exists($styleFile) ) {
			wp_register_style( 'ahStyleSheets-' . $n, $styleUrl );
			wp_enqueue_style(  'ahStyleSheets-' . $n );
		}

		$n++;
   }
	
}

function add_ah_scripts() {
	global $pluginName;

	// Der-egister previous javascripts
	wp_deregister_script( 'jquery' );
	wp_deregister_script( 'jqueryui' );
	wp_deregister_script( 'jquerytime' );

	// Enqueue our own set of scripts for wordpress to include
	wp_enqueue_script( 'jquery', 		'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' );
	wp_enqueue_script( 'jquerytools', 	'http://cdn.jquerytools.org/1.2.6/jquery.tools.min.js' );
	wp_enqueue_script( 'jqueryui', 		WP_PLUGIN_URL . '/' . $pluginName . '/jqueryui/js/jquery-ui-1.8.16.custom.min.js' );
	wp_enqueue_script( 'jquerytime', 	WP_PLUGIN_URL . '/' . $pluginName . '/jqueryui/js/jquery-ui-timepicker-addon.js' );
	wp_enqueue_script( 'uploadifyswf',	WP_PLUGIN_URL . '/' . $pluginName . '/uploadify/swfobject.js' );
	wp_enqueue_script( 'uploadify',		WP_PLUGIN_URL . '/' . $pluginName . '/uploadify/jquery.uploadify.v2.1.4.min.js' );
	wp_enqueue_script( 'lytebox',		WP_PLUGIN_URL . '/' . $pluginName . '/lytebox/lytebox.js' );
	wp_enqueue_script( 'auction',		WP_PLUGIN_URL . '/' . $pluginName . '/js/auction.js' );

}

// Actions, Filters, Hooks

   // Menu
   add_action( 'admin_menu', 'ah_create_menu' );	# Create the administration menu
   
   // Stylesheets
   add_action('wp_print_styles', 'add_ah_stylesheets');	# Enqueue the plugin css file for frontend
   add_action('admin_print_styles', 'add_ah_stylesheets'); # Enqueue the plugin css file for backend
   
   // Scripts
   add_action('wp_enqueue_scripts', 'add_ah_scripts');     # Enqueue scripts on frontend
   add_action('admin_enqueue_scripts', 'add_ah_scripts');  # Enqueue scripts on backend
 
?>
