<?php
/**
 * Class WC_Vendors\Demo_Data\GenerateHistories - GenerateHistories.php
 */

namespace WC_Vendors\Demo_Data;

/**
 * Class GenerateHistories - Stores the history of the generated data
 */
class GenerateHistories {

	/**
	 * wpdb object
	 */
	private $wpdb;

	/**
	 * constructor
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;

	}

	/**
	 * Store the history of the generated data
	 *
	 * @param int    $id - The ID of the generated data.
	 * @param string $type - The type of the generated data.
	 */
	public static function store_object( $id, $type ) {
		$history = new self();
		$history->wpdb->insert(
			$history->wpdb->prefix . WC_VENDORS_DATA_TABLE_NAME,
			array(
				'object_id'   => $id,
				'object_type' => $type,
			)
		);
	}

	/**
	 * Clear the history of the generated data
	 *
	 * @param int    $id - The ID of the generated data.
	 * @param string $type - The type of the generated data.
	 */
	public static function clear_object( $id, $type ) {
		$history = new self();
		$row_id  = self::get_object_id( $id, $type );
		if ( $row_id ) {
			$history->wpdb->delete(
				$history->wpdb->prefix . WC_VENDORS_DATA_TABLE_NAME,
				array(
					'id' => $row_id,
				)
			);
		}
	}

	/**
	 * Retrieve objects from the history
	 *
	 * @param int    $id - Object ID.
	 * @param string $type - The type of the generated data.
	 */
	public static function get_object_id( $id, $type ) {
		$history = new self();
		$history->wpdb->get_results(
			$history->wpdb->prepare(
				"SELECT id FROM {$history->wpdb->prefix}" . WC_VENDORS_DATA_TABLE_NAME . ' WHERE object_id = %d AND object_type = %s',
				$id,
				$type
			)
		);
	}

	/**
	 * Retrive all row
	 */
	public static function get_all() {
		$history = new self();
		$data    = $history->wpdb->get_results( "SELECT * FROM {$history->wpdb->prefix}" . WC_VENDORS_DATA_TABLE_NAME );
		return $data;

	}

	/**
	 * Clear all the history
	 */
	public static function clear_all() {
		$history = new self();
		$history->wpdb->query( "TRUNCATE TABLE {$history->wpdb->prefix}" . WC_VENDORS_DATA_TABLE_NAME );
	}

}
