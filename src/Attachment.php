<?php
/**
 * Attachment class
 */

namespace WC_Vendors\Demo_Data;

use \Jdenticon\Identicon;
/**
 * Class Attachment - handles attachment and upload images via url
 *
 * @package WC_Vendors\Demo_Data
 */
class Attachment {

	/**
	 * Check if image exists in uploads folder
	 *
	 * @param string $url - image url.
	 */
	public static function image_exists( $url ) {
		global $wpdb;

		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url ) );

		return $attachment;
	}

	/**
	 * Generate image via Jdenticon and wp_upload_bits
	 */
	public static function generate_image() {
		$faker = \Faker\Factory::create();
		$name  = $faker->word;
		$icon  = new Identicon();
		$icon->setValue( $name );
		$icon->setSize( 600 );
		$icon->setStyle(
			array(
				'backgroundColor'     => '#0f0f0f',
				'colorLightness'      => array( 0.00, 0.80 ),
				'grayscaleLightness'  => array( 0.00, 0.96 ),
				'colorSaturation'     => 0.50,
				'grayscaleSaturation' => 0.00,
			)
		);
		$image = $icon->getImageData( 'jpeg' );

		// Save image to uploads folder.
		$upload_dir = wp_upload_dir();
		$filename   = $name . '.jpg';
		$filepath   = $upload_dir['path'] . '/' . $filename;
		$upload     = wp_upload_bits( $filename, null, $image );

		// Check if image was uploaded.
		if ( ! $upload['error'] ) {
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);
			$attach_id   = wp_insert_attachment( $attachment, $filepath );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
			wp_update_attachment_metadata( $attach_id, $attach_data );
			GenerateHistories::store_object( $attach_id, 'attachment' );
			return $attach_id;
		}

		return 0;
	}

}




