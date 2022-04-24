<?php
namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;


/**
 * Charter Boat Bookings Orders
 */
class Charter_Boat_Booking_Orders {
    public $charter_boat_product_ids;


    protected function get_charter_boat_products(){
        global $woocommerce;
        $response = array();
        //get charter booking product ids.
        $args = array(
            'type' => 'charter_booking',
            'return'=>'ids',
            );
        $this->charter_boat_product_ids = \wc_get_products( $args );
    }

    

}