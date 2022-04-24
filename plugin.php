<?php
/**
 * Plugin Name: Charter Boat Booking Payments
 * Plugin URI: http://msp-media.org/projects/plugins/charter-boat-booking-payments
 * Description: Charter Boat Booking Payments integrates with WooCommerce enabling paid booking reservations and final payments.
 * Author: Meg Phillips
 * Author URI: http://msp-media.org/
 * Version: 0.0.1
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * WC requires at least: 3.8
 * WC tested up to: 4.6.1
 * Text Domain: charter-boat-bookings
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
require_once plugin_dir_path( __FILE__ ) . 'woo-webhooks.php';
require_once plugin_dir_path( __FILE__ ) . 'class-booking-orders.php';


/**
* =======================================
* ON PLUGIN ACTIVATION
* functions to be called on PLUGIN ACTIVATION - i.e. purge all custom data and tables
* =======================================
*/

add_action( 'plugins_loaded', __NAMESPACE__.'\\cb_check_for_woo' );
function cb_check_for_woo() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', __NAMESPACE__.'\\cbb_woocommerce_missing_notice' );
		return;
	}
/*
	if ( ! class_exists( 'Charter_Boat' ) ) {
		add_action( 'admin_notices', __NAMESPACE__.'\\charter_boat_bookings_missing_notice' );
		return;
	}
*/
}

function cbb_woocommerce_missing_notice() {
	/* translators: 1. URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Charter Boat Booking Payments requires WooCommerce to be installed and active. You can download %s here.', 'charter-boat-bookings' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

function charter_boat_bookings_missing_notice() {
	/* translators: 1. URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Charter Boat Booking Payments requires Charter Boat Bookings to be installed and active. You can download %s here.', 'charter-boat-bookings' ), '<a href="https://msp-media.org" target="_blank">Charter Boat Bookings</a>' ) . '</strong></p></div>';
}


?>