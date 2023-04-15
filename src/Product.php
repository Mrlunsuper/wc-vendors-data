<?php
/**
 * Class WC_Vendors\Demo_Data\Product - Product.php
 */
namespace WC_Vendors\Demo_Data;

use Faker\Factory;
use WC_Product_Simple;
use WC_Product_Variable;
use WC_Vendors\Demo_Data\GenerateHistories;
/**
 * Class Product
 *
 * @package WC_Vendors_Data
 */
class Product {

	/**
	 * Product ID
	 *
	 * @var int $vendor_id  Vendor ID.
	 */
	private $vendor_id;

	/**
	 * Product ID
	 *
	 * @var int $id  Product ID.
	 */
	private $id;

	/**
	 * Constructor
	 *
	 * @param int $vendor_id  Vendor ID.
	 */
	public function __construct( $vendor_id, $product_id = null ) {
		if ( ! $vendor_id ) {
			return;
		}
		if ( $product_id ) {
			$this->id = $product_id;
		}
		$this->vendor_id = $vendor_id;
	}

	/**
	 * Geneate a product
	 */
	public function genrate_faker_product() {
		$faker = Factory::create();
		$faker->addProvider( new \Bezhanov\Faker\Provider\Commerce( $faker ) );
		$image_id = Attachment::generate_image();
		$product  = new WC_Product_Simple();
		$product->set_name( $faker->productName );
		$product->set_description( $faker->text( 1000 ) );
		$product->set_short_description( $faker->text );
		$product->set_regular_price( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( $faker->randomDigitNotNull() );
		$product->set_stock_status( 'instock' );
		$product->set_backorders( 'no' );
		$product->set_reviews_allowed( true );
		$product->set_sold_individually( false );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_featured( false );
		$product->set_price( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_sku( $faker->ean13 );
		$product->set_weight( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_length( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_width( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_height( $faker->randomFloat( 2, 1, 100 ) );
		$product->set_tax_status( 'taxable' );
		$product->set_tax_class( '' );
		$product->set_shipping_class_id( 0 );
		$product->set_purchase_note( '' );
		$product->set_attributes( Term::generate() );
		$product->set_default_attributes( array() );
		$product->set_menu_order( 0 );
		$product->set_virtual( false );
		$product->set_downloadable( false );
		$product->set_category_ids( array() );
		$product->set_tag_ids( array() );
		$product->set_gallery_image_ids( array() );
		$product->set_download_limit( -1 );
		$product->set_download_expiry( -1 );
		$product->set_downloads( array() );
		$product->set_parent_id( 0 );
		$product->set_reviews_allowed( true );
		$product->set_sold_individually( false );
		$product->set_image_id( $image_id );
		$product->save();
		$this->id = $product->get_id();
		$this->set_author( $this->vendor_id );
		GenerateHistories::store_object( $product->get_id(), 'product' );
	}

	/**
	 * Delete a product
	 */
	public function delete_product() {
		wp_delete_post( $this->id, true );
		GenerateHistories::clear_object( $this->id, 'product' );
	}

	/**
	 * Set author of the product
	 *
	 * @param int $user_id  User ID.
	 */
	public function set_author( $user_id ) {
		wp_update_post(
			array(
				'ID'          => $this->id,
				'post_author' => $user_id,
			)
		);
	}
}
