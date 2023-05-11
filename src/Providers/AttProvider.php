<?php
/**
 * Class AttProvider - AttProvider.php
 */

namespace WC_Vendors\Demo_Data\Providers;

use Faker\Provider\Base;
use WC_Vendors;
use WC_Vendors\Demo_Data\GenerateHistories;

/**
 * Class AttProvider - generate terms
 *
 * @package WC_Vendors_Data
 */
class AttProvider extends Base {
	/**
	 * Default global attributes
	 *
	 * @var array $attributes Default global attributes.
	 */
	private $attributes = array(
		'color'    => 'Color',
		'size'     => 'Size',
		'brand'    => 'Brand',
		'material' => 'Material',
	);
	/**
	 * Default global attributes
	 *
	 * @var array $attribute_terms Default global attributes terms.
	 */
	private $attribute_terms = array(
		'color'    => array(
			'red',
			'blue',
			'green',
			'yellow',
			'black',
			'white',
			'orange',
			'purple',
			'pink',
			'brown',
			'grey',
			'silver',
			'gold',
		),
		'size'     => array(
			'small',
			'medium',
			'large',
			'x-large',
			'xx-large',
		),
		'brand'    => array(
			'Apple',
			'Samsung',
			'LG',
			'Sony',
			'Microsoft',
			'Google',
			'Amazon',
			'Dell',
			'HP',
			'Lenovo',
			'Asus',
			'Acer',
			'Toshiba',
			'Canon',
			'Nikon',
			'Sony',
			'Panasonic',
			'JVC',
			'Samsung',
			'LG',
			'Philips',
			'Bose',
			'JBL',
			'Harman Kardon',
			'Beats',
			'Bose',
			'JBL',
			'Harman Kardon',
			'Beats',
		),
		'material' => array(
			'cotton',
			'wool',
			'leather',
			'silk',
			'polyester',
			'nylon',
			'rayon',
			'spandex',
			'linen',
			'denim',
			'cashmere',
			'suede',
			'velvet',
			'lace',
			'fur',
			'plastic',
			'metal',
			'glass',
			'wood',
			'stone',
			'paper',
			'fabric',
			'rubber',
			'leather',
			'silk',
			'polyester',
			'nylon',
			'rayon',
			'spandex',
			'linen',
			'denim',
			'cashmere',
			'suede',
			'velvet',
			'lace',
			'fur',
			'plastic',
			'metal',
			'glass',
			'wood',
			'stone',
			'paper',
			'fabric',
			'rubber',
		),
	);

	/**
	 * Generate random product attribute
	 */
	public function productAttribute() {
		$random_attribute = array_rand( ( $this->attributes ), 1 );
		$random_terms     = array_rand( $this->attribute_terms[ $random_attribute ], 2 );
		$attribute        = array(
			'name'         => $this->attributes[ $random_attribute ],
			'slug'         => $random_attribute,
			'type'         => 'select',
			'order_by'     => 'menu_order',
			'has_archives' => false,
		);
		$att_name         = 'pa_' . $random_attribute;
		if ( ! taxonomy_exists( $att_name ) ) {
			$attribute_id = wc_create_attribute( $attribute );
			GenerateHistories::store_object( $attribute_id, 'pa_att' );
		}
		$term_arr = array();

		foreach ( $random_terms as $term ) {
			$term_arr[] = $this->attribute_terms[ $random_attribute ][ $term ];
			if ( ! term_exists( $this->attribute_terms[ $random_attribute ][ $term ], $att_name ) ) {
				$term_id = wp_insert_term(
					$this->attribute_terms[ $random_attribute ][ $term ],
					$att_name,
					array(
						'slug' => $this->attribute_terms[ $random_attribute ][ $term ],
					)
				);
				if ( ! is_wp_error( $term_id ) ) {
					GenerateHistories::store_object( $term_id['term_id'], $att_name );
				}
			}
		}
		return array(
			$att_name => array(
				'options' => $term_arr,
			),
		);
	}
}
