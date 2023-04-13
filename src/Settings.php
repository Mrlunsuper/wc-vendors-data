<?php
/**
 * Class WC_Vendors\Demo_Data\Settings - Settings.php
 */
namespace WC_Vendors\Demo_Data;

use \WC_Vendors\Demo_Data\Generator;
/**
 * Class Settings
 *
 * @package WC_Vendors_Data
 */
class Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ), 99 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page
	 */
	public function add_settings_page() {

		// Add submenu page below WC Vendors menu
		add_submenu_page(
			'wc-vendors',
			esc_html__( 'WC Vendors Demo Settings', 'wcv-data' ),
			esc_html__( 'Demo Settings', 'wcv-data' ),
			'manage_woocommerce',
			'wcv-data',
			array( $this, 'settings_page' ),
			10,
		);
	}
	/**
	 * Register settings
	 */
	public function register_settings() {
		// Register settings
		register_setting( 'wcv_data_settings', 'store_suffix' );
		register_setting( 'wcv_data_settings', 'vendor_number' );
		register_setting( 'wcv_data_settings', 'products_per_vendor' );

		// Add settings sections
		add_settings_section( 'wcv_data_general_section', esc_html__( 'General Settings', 'wcv-data' ), array( $this, 'general_section_callback' ), 'wcv_data_settings' );

		// Add settings fields
		add_settings_field( 'store_suffix', esc_html__( 'Store Suffix', 'wcv-data' ), array( $this, 'store_suffix_callback' ), 'wcv_data_settings', 'wcv_data_general_section' );
		add_settings_field( 'vendor_number', esc_html__( 'Vendor Number', 'wcv-data' ), array( $this, 'vendor_number_callback' ), 'wcv_data_settings', 'wcv_data_general_section' );
		add_settings_field( 'products_per_vendor', esc_html__( 'Products per Vendor', 'wcv-data' ), array( $this, 'products_per_vendor_callback' ), 'wcv_data_settings', 'wcv_data_general_section' );
	}

	/**
	 * Settings page
	 */
	public function settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WC Vendors Demo Settings', 'wcv-data' ); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'wcv_data_settings' );
					do_settings_sections( 'wcv_data_settings' );
					submit_button();
					do_action( 'wcv_data_settings_page_save' );
				?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wcv-data&generate_data=true' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Generate Demo Data', 'wcv-data' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wcv-data&delete_data=true' ) ); ?>" class="button submitdelete" style="color:red"><?php esc_html_e( 'Delete Demo Data', 'wcv-data' ); ?></a>
			</form>
		</div>
		<?php
	}

	/**
	 * General section callback
	 */
	public function general_section_callback() {
		if ( isset( $_GET['generate_data'] ) && 'true' === $_GET['generate_data'] ) {
			$generator = new Generator();
			$generator->generate();
		}

		if ( isset( $_GET['delete_data'] ) && 'true' === $_GET['delete_data'] ) {
			$generator = new Generator();
			$generator->delete();
		}
	}

	/**
	 * Store suffix callback
	 */
	public function store_suffix_callback() {
		$store_suffix = get_option( 'store_suffix', '' );
		?>
		<input type="text" name="store_suffix" value="<?php echo esc_attr( $store_suffix ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the store suffix for your vendors.', 'wcv-data' ); ?></p>
		<?php
	}

	/**
	 * Vendor number callback
	 */
	public function vendor_number_callback() {
		$vendor_number = get_option( 'vendor_number', 0 );
		?>
		<input type="number" name="vendor_number" value="<?php echo esc_attr( $vendor_number ); ?>" min="0" step="1" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the number of vendors allowed.', 'wcv-data' ); ?></p>
		<?php
	}

	/**
	 * Products per vendor callback
	 */
	public function products_per_vendor_callback() {
		$products_per_vendor = get_option( 'products_per_vendor', 0 );
		?>
		<input type="number" name="products_per_vendor" value="<?php echo esc_attr( $products_per_vendor ); ?>" min="0" step="1" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Enter the number of products allowed per vendor.', 'wcv-data' ); ?></p>
		<?php
	}
}
