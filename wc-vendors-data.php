<?php
/**
 * Plugin Name: WC Vendors Data
 * Plugin URI: http://wcvendors.com
 * Description: WC Vendors Data
 * Version: 1.0.0
 * Author: WC Vendors
 * Author URI: http://wcvendors.com
 * Requires at least: 4.0
 * Tested up to: 4.0
 * Text Domain: wcv-data
 * Domain Path: /languages/
 *
 * @package WC Vendors Data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_VENDORS_DATA_VERSION', '1.0.0' );
define( 'WC_VENDORS_DATA_PLUGIN_FILE', __FILE__ );
define( 'WC_VENDORS_DATA_PLUGIN_DIR', untrailingslashit( dirname( WC_VENDORS_DATA_PLUGIN_FILE ) ) );
define( 'WC_VENDORS_DATA_TABLE_NAME', 'wcv_demo_histories' );

require WC_VENDORS_DATA_PLUGIN_DIR . '/vendor/autoload.php';

/**
 * Activate the plugin
 */
function wcv_data_activate() {
	register_activation_hook( __FILE__, array( 'WC_Vendors\Demo_Data\Activator', 'activate' ) );
	add_action( 'admin_notices', array( 'WC_Vendors\Demo_Data\Activator', 'admin_notice' ) );
	wcv_create_wcv_demo_histories_table();
}

/**
 * Create the wcv_demo_histories table.
 */
function wcv_create_wcv_demo_histories_table() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . WC_VENDORS_DATA_TABLE_NAME;
	$table_exists    = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );

	if ( $table_exists !== $table_name ) {
		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			object_id bigint(20) NOT NULL,
			object_type varchar(20) NOT NULL,
			date_created datetime NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

add_action( 'plugins_loaded', 'wcv_data_activate' );

new WC_Vendors\Demo_Data\WCVDemoData();
