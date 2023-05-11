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
	 * WPDB object
	 *
	 * @var object $wpdb wpdb object.
	 */
	private $wpdb;

	/**
	 * Table name
	 *
	 * @var string $table_name  Table name.
	 */
	private static $table_name;

	/**
	 * Constructor
	 */
	private function __construct() {
		global $wpdb;
		$this->wpdb       = $wpdb;
		self::$table_name = $wpdb->prefix . WC_VENDORS_DATA_TABLE_NAME;
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
			self::$table_name,
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
				self::$table_name,
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
		$sql     = $history->wpdb->prepare(
			'SELECT id FROM %s WHERE object_id = %d AND object_type = %s',
			self::$table_name,
			$id,
			$type
		);
		$history->wpdb->get_results( $sql ); // WPCS: unprepared SQL ok.
	}

	/**
	 * Retrive all row
	 */
	public static function get_all() {
		$history = new self();
		$sql     = $history->wpdb->prepare( 'SELECT * FROM %s', self::$table_name );
		$data    = $history->wpdb->get_results( $sql ); // wpcs: unprepared SQL ok.
		return $data;

	}

	/**
	 * Clear all the history
	 */
	public static function clear_all() {
		$history = new self();
		$sql     = $history->wpdb->prepare( 'TRUNCATE TABLE %s', self::$table_name );
		$history->wpdb->query( $sql ); // wpcs: unprepared SQL ok.
	}

}
