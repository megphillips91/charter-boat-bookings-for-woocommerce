<?php
namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;
use Automattic\WooCommerce\Client;



/**
 * Charter Boat Bookings Orders
 */
class Charter_Boat_Booking_Orders {
    public $charters;
    public $charter_boat_product_ids;
    public $orders_by_charter;
    

    public function __construct(){
        $this->product_orders = array();
        $this->get_charter_boat_products();
        $this->get_booking_orders();
        $this->get_charters();
    }


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

    protected function get_booking_orders(){
        foreach($this->charter_boat_product_ids as $product_id){
            $this->get_orders_by_product($product_id);
        }
    }

    protected function get_orders_by_product($product_id){
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
        $orders = $woocommerce_rest->get('orders', $params);
        foreach($orders as $order){
            $this->orders_by_product[] = $order;
        }
    }

    protected function get_charters(){
        foreach($this->orders_by_product as $this_order){
            $order_id = $this_order->id;
            $charter_args = array();
            foreach($this_order->line_items as $item){
                $product_id = $item->product_id;
                $product = wc_get_product($product_id); //get product
                $variation_id = $item->variation_id;
                //booking fields
                $charter_args['booking_status'] = get_post_meta($item->variation_id, 'attribute_pa__cb_type', true);
                $date = get_post_meta($item->variation_id, 'attribute_pa__cb_date', true);
                $start_time = get_post_meta($item->variation_id, 'attribute_pa__cb_start_time', true);
                $charter_args['start_datetime'] = $date.' '.$start_time;
                $charter_args['duration'] = get_post_meta($item->variation_id, 'attribute_pa__cb_duration', true);
                $charter_args['start_location'] = get_post_meta($item->variation_id, 'attribute_pa__cb_location', true);
                $charter_args['end_location'] = get_post_meta($item->variation_id, 'attribute_pa__cb_location', true);
                $charter_args['tickets'] = $item->quantity;
                $charter_args['is_private'] = $product->get_sold_individually();
                $charter_args['customer_email'] = $this_order->billing->email;
                $charter_args['customer_name'] = $this_order->billing->first_name.' '.$this_order->billing->last_name;
                $charter_args['customer_phone'] = $this_order->billing->phone;
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


    

}