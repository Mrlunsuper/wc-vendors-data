<?php
/**
 * Class WC_Vendors\Demo_Data\Activator - Activator.php
 */
namespace WC_Vendors\Demo_Data;

/**
 * Class Activator
 *
 * @package WC_Vendors_Data
 */
class Activator {

	/**
	 * Activate the plugin
	 */
	public static function activate() {
		$required_plugins = self::check_environment();
		if ( ! empty( $required_plugins ) ) {
			deactivate_plugins( plugin_basename( WC_VENDORS_DATA_PLUGIN_FILE ) );
			return;
		}
	}

	/**
	 * Check environment
	 */
	public static function check_environment() {
		$required_plugins  = array(
			'woocommerce/woocommerce.php'     => 'WooCommerce',
			'wcvendors/class-wc-vendors.php' => 'WC Vendors Marketplace',
		);
		$activated_plugins = get_option( 'active_plugins' );

		foreach ( $required_plugins as $plugin => $name ) {
			if ( in_array( $plugin, $activated_plugins ) ) {
				unset( $required_plugins[ $plugin ] );
			}
		}
		return $required_plugins;
	}

	/**
	 * Admin notice for missing requires plugins.
	 */
	public static function admin_notice() {
		$required_plugins = self::check_environment();
		if ( ! empty( $required_plugins ) ) {
			foreach ( $required_plugins as $plugin => $name ) {
				echo sprintf('<div class="error"><p>%s</p></div>', sprintf( __( 'WC Vendors Data requires <strong>%s</strong> to be installed and activated.', 'wcv-data' ), $name ) ); //phpcs:ignore
			}
		}
	}

}
