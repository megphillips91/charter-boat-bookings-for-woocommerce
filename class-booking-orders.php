<?php
namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;
use Automattic\WooCommerce\Client;

/**
 * So I wrote this considering many orders, but in the normal course of business, we need to consider one order on a webhook
 * We can poll orders but it makes more sense to have one order at a time...
 */

/**
 * Charter Boat Bookings Orders
 */
class Charter_Boat_Booking_Orders {
    public $charters;
    public $charter_boat_product_ids;
    private $orders_by_charter;
    

    public function __construct(){
        $this->product_orders = array();
        $this->get_charter_boat_products();
    }

    protected function get_charters_from_order($this_order){
            $order_id = $this_order->id;
            $charter_args = array();
            foreach($this_order->line_items as $item){
                $product_id = $item->product_id;
                $product = wc_get_product($product_id); //get product
                $variation_id = $item->variation_id;
                $meta_data = $item->meta_data;
                //work with the dates 
                $datetime_meta_string = trim($this->filter_item_meta($meta_data, 'Date').' '.$this->filter_item_meta($meta_data, 'Start Time'));
                $datetime_meta_string = trim($this->clean_date_for_parse($datetime_meta_string));
                //booking fields
                $charter_args['booking_status'] = get_post_meta($item->variation_id, 'attribute_pa__cb_type', true).' '. $this_order->status;
                if( date_parse($datetime_meta_string)['year'] ){
                    $start_datetime = new DateTime($datetime_meta_string, new DateTimeZone(get_option('timezone_string')));
                    $charter_args['start_datetime'] = $start_datetime->format('Y-m-d H:i:s');
                } else {
                    $charter_args['start_datetime'] = NULL;
                }
                $charter_args['duration'] = $this->filter_item_meta($meta_data, 'Duration');
                $charter_args['start_location'] = $this->filter_item_meta($meta_data, 'Location');
                $charter_args['end_location'] = $this->filter_item_meta($meta_data, 'Location');
                $charter_args['tickets'] = $item->quantity;
                $charter_args['is_private'] = $product->get_sold_individually();
                $charter_args['customer_email'] = $this_order->billing->email;
                $charter_args['customer_name'] = $this_order->billing->first_name.' '.$this_order->billing->last_name;
                $charter_args['customer_phone'] = $this_order->billing->phone;
                $charter_args['ota_id'] = $order_id;
                //booking meta to be saved
                $charter_meta = array();
                $charter_meta['product_id'] = $product_id;
                $charter_meta['variation_id'] = $item->variation_id;
                $charter_meta['order_id'] = $order_id;
                $charter_meta['order_status'] = $this_order->status;
                $charter_meta['order_date'] = $this_order->date_created;
                $charter_meta['order_total'] = $this_order->total;
                $charter_meta['booking_total'] = $item->total;
                $charter_meta['order_discounts'] = $this_order->discount_total;
                $charter_args['booking_meta'] = $charter_meta;
            }
            $this->charters[] = $charter_args;
    }


    /**
     * ============= polling functions ================
     * these functions are for patching from the old table to the new table directly from every previous order of a charter boat booking
     * useful for running cleanup or patches
     */

    //get all products which are charter boat bookings
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

    //loop through all the charter boat products and get orders for those products
    public function get_charter_booking_orders(){
        foreach($this->charter_boat_product_ids as $product_id){
            $this->query_orders_by_product($product_id);
        }
    }
    
    //loop through the returned orders and get the charteres from the order. Put the charters into the charter array
    public function get_charters_from_all_orders(){
        foreach($this->orders_by_product as $this_order){
            $this->get_charters_from_order($this_order);
        }
    }

    //WC Rest API call to get orders by product
    protected function query_orders_by_product($product_id){
        $woocommerce_rest = new Client(
            site_url(),
            WP_WC_PUBLIC,
            WP_WC_PRIVATE,
            [
            'version' => 'wc/v3',
            ]
        );
        $params = array(
            'product'=>$product_id,
        );  
        //MEGTODO: need to loop do while for all pages so do while the link rel="next"
        $orders = $woocommerce_rest->get('orders', $params);
        foreach($orders as $order){
            $this->orders_by_product[] = $order;
        }
        //end do while here
    }

    /**
     * Item meta comes back as an array of objects
     * 
     * @param array meta_array is the line_item->meta_data array of objects
     * @param string meta_key is the key of the meta you need
     */
    private function filter_item_meta($meta_array, $meta_key){
        foreach($meta_array as $meta_object){ //these will be objects
            if($meta_object->key === $meta_key){
                return $meta_object->value;
            }
        }
    }

    /**
     * clean date for parse
     */
    private function clean_date_for_parse($datetime_string){
        $err = array( 'EDT', 'EST', 'Mon,', 'Tue,', 'Wed,', 'Thu,', 'Fri,', 'Sat,', 'Sun,' );
        foreach($err as $string){
            $datetime_string = str_replace($string, '', $datetime_string);
        }
        return $datetime_string;
    }

    /**
     * Get time from attribute. Add the colon between hours:mins
     */
    private function attribute_to_time($start_time_attribute){
        $chunks = str_split($start_time_attribute, 2);
        return $chunks[0].':'.$chunks[1];
    }

    /**
    * Return Hours and Minutes from Duration
    *
    * basically provides the needed information for PHP DateInterval
    *
    * @param  string $str_duration in hours float
    * @return array of integers
    */
    private function duration_float_to_time($str_duration){
       $duration = (float)$str_duration;
       $duration_hours = floor($duration);
       $duration_minutes = ($duration-$duration_hours)*60;
       return array('H'=>$duration_hours, 'M'=>$duration_minutes);
   }

} // end class