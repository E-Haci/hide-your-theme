<?php
/**
 * Plugin Name:       Hide Your Theme Name
 * Plugin URI:        http://guaven.com/product_name_hider
 * Description:       WP detectors will not be able to detect which theme your website are using.
 * Version:           1.0.5
 * Author:            Guaven Labs
 * Author URI:        http://guaven.com/
 * Text Domain:       guaven_product_name_hider
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



//if (is_admin())
require_once(dirname(__FILE__)."/settings.php");
//else
require_once(dirname(__FILE__)."/functions.php");

guaven_pnh_load_defaults();



add_action('admin_menu', 'guaven_pnh_admin');
register_deactivation_hook( __FILE__, 'guaven_pnh_theme_switch' );