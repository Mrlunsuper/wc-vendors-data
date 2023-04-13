<?php
/**
 * Class WC_Vendors\Demo_Data\Generator - Generator.php
 */

namespace WC_Vendors\Demo_Data;

use \WC_Vendors\Demo_Data\Vendor;
use \WC_Vendors\Demo_Data\Product;

/**
 * Class Generator
 *
 * @package WC_Vendors_Data
 */
class Generator {

	/**
	 * Number of vendors to generate
	 *
	 * @var int
	 */
	private $vendors_count = 0;

	/**
	 * Number of products to generate
	 *
	 * @var int
	 */
	private $products_count = 0;

	/**
	 * Number of orders to generate
	 *
	 * @var int
	 */
	private $orders_count = 0;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->vendors_count  = get_option( 'vendor_number', 0 );
		$this->products_count = get_option( 'products_per_vendor', 0 );
	}

		/**
		 * Generate data
		 */
	public function generate() {
		$this->generate_vendors_and_products();
		wp_safe_redirect( admin_url( 'admin.php?page=wcv-data' ) );
	}

		/**
		 * Generate vendors
		 */
	private function generate_vendors_and_products() {
		for ( $i = 0; $i < $this->vendors_count; $i++ ) {
			$vendor = new Vendor();
			$vendor_id = $vendor->create_vendor();
			if ( ! $vendor_id ) {
				continue;
			}
			for ( $j = 0; $j < $this->products_count; $j++ ) {
				$product = new Product( $vendor_id );
				$product->genrate_faker_product();
			}
		}
	}

	/**
	 * Delete vendors and products.
	 */
	public function delete() {
		$data = GenerateHistories::get_all();
		foreach ( $data as $item ) {
			switch ( $item->object_type ) {
				case 'vendor':
					wp_delete_user( $item->object_id );
					break;
				case 'product':
					wp_delete_post( $item->object_id );
					break;
				case 'attachment':
					wp_delete_attachment( $item->object_id );
					break;
			}
		}
		GenerateHistories::clear_all();
		wp_safe_redirect( admin_url( 'admin.php?page=wcv-data' ) );
	}
}
