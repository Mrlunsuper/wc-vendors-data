<?php
/**
 * Class WC_Vendors\Demo_Data\Vendor - Vendor.php
 */
namespace WC_Vendors\Demo_Data;

use WP_User;
use Faker\Factory;
use Generator;
use WC_Vendors\Demo_Data\GenerateHistories;
/**
 * Class Vendor
 *
 * @package WC_Vendors_Data
 */
class Vendor {

	/**
	 * Vendor ID
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Vendor props
	 *
	 * @var array
	 */
	private $props = array(
		'first_name'       => 'firs_name',
		'last_name'        => 'last_name',
		'phone'            => '_wcv_store_phone',
		'address'          => '_wcv_store_address1',
		'city'             => '_wcv_store_city',
		'state'            => '_wcv_store_state',
		'country'          => '_wcv_store_country',
		'shop_name'        => 'pv_shop_name',
		'shop_description' => 'pv_shop_description',
		'shop_slug'        => 'pv_shop_slug',
		'email'            => 'user_email',

	);

	/**
	 * Vendor data
	 *
	 * @var array
	 */
	private $data;



	/**
	 * Constructor
	 *
	 * @param int $user_id User ID.
	 */
	public function __construct( $user_id = false ) {
		if ( $user_id ) {
			$this->id   = $user_id;
			$this->data = $this->get_vendor_data();
		} else {
			$this->id   = 0;
			$this->data = $this->generate_faker_data();
		}

	}

	/**
	 * Generate faker data
	 */
	public function generate_faker_data() {
		$faker = Factory::create();

		$faker_data = array(
			'first_name'       => $faker->firstName(),
			'last_name'        => $faker->lastName(),
			'phone'            => $faker->phoneNumber(),
			'email'            => $faker->email(),
			'address'          => $faker->address(),
			'city'             => $faker->city(),
			'state'            => $faker->state(),
			'country'          => $faker->country(),
			'shop_name'        => $faker->company(),
			'shop_description' => $faker->text(),
			'shop_slug'        => sanitize_title( $faker->company() ),
		);

		return $faker_data;
	}

	/**
	 * Get vendor ID
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get vendor data
	 */
	public function get_vendor_data() {
		$vendor_data = array();

		foreach ( $this->props as $key => $meta_key ) {
			$vendor_data[ $key ] = get_user_meta( $this->id, $meta_key, true );
		}

		return $vendor_data;
	}

	/**
	 * Create a new vendor
	 */
	public function create_vendor() {
		$vendor_data = $this->data;

		// Check if vendor exists.
		if ( $this->vendor_exists( $vendor_data['email'] ) ) {
			return;
		}

		// Create vendor.
		$vendor_id = wp_insert_user(
			array(
				'user_login' => $vendor_data['email'],
				'user_email' => $vendor_data['email'],
				'first_name' => $vendor_data['first_name'],
				'last_name'  => $vendor_data['last_name'],
				'role'       => 'vendor',
				'user_pass'  => 123456,
			)
		);

		if ( ! is_wp_error( $vendor_id ) ) {
			$this->id = $vendor_id;
			$this->save();
			return $vendor_id;
		}
		return false;
	}

	/**
	 * Check vendor exists
	 *
	 * @param string $email Vendor email.
	 */
	public function vendor_exists( $email ) {
		$existing_vendor = get_user_by( 'email', $email );

		if ( $existing_vendor ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set props
	 *
	 * @param array $meta_data Meta data.
	 */
	public function set_props( $meta_data ) {
		foreach ( $meta_data as $key => $value ) {
			$this->data[ $key ] = $value;
		}
	}

	/**
	 * Save vendor data
	 */
	public function save() {
		// Update vendor meta data.
		$this->update_vendor_meta( $this->data );
		GenerateHistories::store_object( $this->id, 'vendor' );
	}

	/**
	 * Delete vendor
	 */
	public function delete() {
		// Delete vendor.
		wp_delete_user( $this->id, 1 );
		GenerateHistories::clear_object( $this->id, 'vendor' );
	}

	/**
	 * Update vendor meta data
	 *
	 * @param array $meta_data Meta data.
	 */
	public function update_vendor_meta( $meta_data ) {
		foreach ( $meta_data as $key => $value ) {
			update_user_meta( $this->id, $this->props[ $key ], $value );
		}
	}

	/**
	 * Track vendor
	 */
	public function track_vendor() {
		$vendor = new WP_User( $this->id );
		$vendor->add_role( 'vendor' );
	}
}
