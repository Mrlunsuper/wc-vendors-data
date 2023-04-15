<?php
/**
 * Class WC_Vendors\Demo_Data\Term - Term.php
 */

namespace WC_Vendors\Demo_Data;

use Faker\Factory;
use WP_Error;
use \WC_Vendors\Demo_Data\Providers\AttProvider;

/**
 * Class Term - generate terms
 *
 * @package WC_Vendors_Data
 */
class Term {

	/**
	 * Generate terms
	 */
	public static function generate() {
		$faker = Factory::create();
		$faker->addProvider( new AttProvider( $faker ) );
		return $faker->productAttribute();
	}

}
