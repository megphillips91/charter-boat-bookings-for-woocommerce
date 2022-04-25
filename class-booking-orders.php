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
            foreach($this_order->line_items as $item){
                $product_id = $item->product_id;
                $variation_id = $item->variation_id;
                $order_array['order_id'] = $order_id;
                $order_array['order_status'] = $this_order->status;
                $type = get_post_meta($item->variation_id, 'attribute_pa__cb_type', true);
                $order_array['start_time'] = get_post_meta($item->variation_id, 'attribute_pa__cb_start_time', true);
                $order_array['duration'] = get_post_meta($item->variation_id, 'attribute_pa__cb_duration', true);
                $order_array['date'] = get_post_meta($item->variation_id, 'attribute_pa__cb_date', true);
                $order_array['product_id'] = $product_id;
                $order_array['variation_id'] = $variation_id;
                $order_array['type'] = $type;
            }
            $this->charters[] = $order_array;
        }
    }

    

}