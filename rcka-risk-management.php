<?php
/*
   Plugin Name: RCKa Risk Manager
   Plugin URI: https://rcka.co.uk
   description: Manage project risks from a simple Wordpress plugin
   Version: 1.0.0
   Author: RCKa
   Author URI: https://rcka.co.uk
*/

// Create a new table

require_once('functions.php');

// Wordpress hooks

// This checks that the database table is up and running:

register_activation_hook( __FILE__, 'RiskManager_Table' );

// This enables the shortcode snippet that allows the 

add_shortcode('risk-manager-projectlist', 'RiskManager_PageSelect');

// This loads the RiskManager styles

function RiskManagerLoadCustomCSS() {
    wp_register_style('riskmanager-styles', plugins_url('styles.css',__FILE__ ));
    wp_enqueue_style('riskmanager-styles');
}

add_action( 'wp_enqueue_scripts',RiskManagerLoadCustomCSS);

// This loads the RiskManager scripts
function RiskManagerLoadCustomJS() {
	wp_register_script( 'riskmanager-popup', plugin_dir_url( __FILE__ ),'riskmanager-popup.js' );
	wp_enqueue_script('riskmanager-popup');
}
add_action('riskmanager-popup-load', 'RiskManagerLoadCustomJS');

