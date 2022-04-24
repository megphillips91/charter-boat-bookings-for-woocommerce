<?php
/**
 * Plugin Name: Charter Boat Bookings For WooCommerce
 * Plugin URI: http://msp-media.org/projects/plugins/charter-bookings
 * Description: Charter Boat Bookings For WooCommerce enables paid booking reservations and final payments.
 * Contributors: megphillips91
 * Author URI: http://msp-media.org/
 * Version: 1.7.1
 * License: GPL2+
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

 /*
 Charter Boat Bookings is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 Charter Boat Bookings is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with Charter Boat Bookings. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 */

namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
  * Include plugin files
  */

require_once plugin_dir_path( __FILE__ ) . 'rest-api.php';
require_once plugin_dir_path( __FILE__ ) . 'helper-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'woo-webhooks.php';




/**
* =======================================
* ON PLUGIN ACTIVATION
* functions to be called on PLUGIN ACTIVATION - i.e. purge all custom data and tables
* =======================================
*/

register_activation_hook( __FILE__, __NAMESPACE__ . '\\cb_check_for_woo' );
function cb_check_for_woo() {

	if ( ! $this->woocommerce_is_active() ) {

		deactivate_plugins( plugin_basename( __FILE__ ) );
		unset( $_GET['activate'] ); // Input var okay.
	}
}


?>