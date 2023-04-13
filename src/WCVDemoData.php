<?php
/**
 * Class WC_Vendors\Demo_Data\WCVDemoData - WCVDemoData.php - Main class
 */

namespace WC_Vendors\Demo_Data;

use WC_Vendors\Demo_Data\Settings;

/**
 * Class WCVDemoData
 *
 * @package WC_Vendors_Data
 */
class WCVDemoData {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init
	 */
	private function init() {
		new Settings();
	}
}
