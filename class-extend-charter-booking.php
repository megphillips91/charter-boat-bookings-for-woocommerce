<?php
namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;
use Automattic\WooCommerce\Client;

class Insert_Booking_From_Woo_Order extends Charter_Booking {
    public $year;
    public $order_booking_data;
    public $order_booking_meta;


    public function __construct($booking_data, $year = NULL){
        parent::__construct();

        $this->order_booking_meta = $booking_data['booking_meta']; //set meta data
        unset($booking_data['booking_meta']); //pull meta data out of booking data
        $this->order_booking_data = $booking_data; //set booking data
        $this->save_booking($this->order_booking_data); //save booking
        $this->save_order_meta(); //save meta
        $this->sync_update_time(); // save last update time in options
      }
    
    protected function save_order_meta(){
        foreach($this->order_booking_meta as $key=>$value){
            $this->add_booking_meta($this->id, $key, $value);
        }
    }

    protected function sync_update_time(){
        update_option('cb_woo_last_sync', wp_date("Y-m-d H:i:s"));
    }

}

?>