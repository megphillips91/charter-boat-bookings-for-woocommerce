<?php
/**
 * Create wp rest api namespace and endpoint to post new csv files
 */
namespace Charter_Boat_Bookings;
use \Datetime;
use \DateTimeZone;
use \DateInterval;

$charterboat_rest = new Charter_Boat_Woo_API();

 /**
  * MEGTODO: it is screwey that the insert charter booking class is in the basic bookings plugin and not an exension inside of the woo_supporting plugin
  * so we need to figure that out in my mind Where should each peice live and what shoud that extension relationship be
  * should extensions have access to the namespace nad classes of the parent extension?
  * 
  */

 /**
  * MEGTODO: what happens when there is a new order in woocommerce?
  * what happens when a payment completes?
  * what happens when the sync completes?
  * what happens when a stripe payment fails?
  * what happens when WC Payments payment fails?
  */

class Charter_Boat_Woo_API {
    private $timezone;

    public function __construct(){
        $this->timezone = \get_option('timezone_string');
        add_action( 'rest_api_init',array( $this, 'register_routes' ) );
    }

    public function register_routes(){
       /*
        register_rest_route( 'charter-boat-bookings/v3', 'insert-bookings-from-woo-orders/', array(
            'methods' => 'POST',
            'callback' =>array($this, 'insert_bookings_from_all_orders'),
            'permission_callback' => '__return_true'
            ) );
        */
            
    }

    public function insert_bookings_from_all_orders(){
        if( !current_user_can('manage_woocommerce') || !$this->user_is_charter_admin() ){
            return new \WP_Error( 'no_permission', 'Invalid user', array( 'status' => 404 ) );
        } else {
            $new_bookings = array();
            $charter_bookings = new Charter_Boat_Booking_Orders();
            $charter_bookings->get_charter_booking_orders();
            $charter_bookings->get_charters_from_all_orders();
            foreach($charter_bookings->charters as $booking_data){
                $new_bookings[] = new Insert_Booking_From_Woo_Order($booking_data, '2022');
            }
            return $new_bookings;
        }
    }

    /**
     * checks user permissions
     */
    protected function user_has_permission(){
        if( get_user_meta( get_current_user_id(), 'cb_charter_affiliate', true) === '' && !current_user_can('edit_others_posts') && get_user_meta( get_current_user_id(), 'charter_admin', true) === ''){
            return false;
        } else {
            return true;
        }
    }

    /**
     * checks if user is a charter admin
     */
    protected function user_is_charter_admin(){
        if( get_user_meta( get_current_user_id(), 'cb_charter_admin', true) === '' && !current_user_can('edit_others_posts') && get_user_meta( get_current_user_id(), 'charter_admin', true) === ''){
            return false;
        } else {
            return true;
        }
    }

    public function z_dep_get_bookings_from_orders(){
        $user = wp_get_current_user();
        if(!$user->has_cap('manage_woocommerce')){
            return $user;
         } else {
            global $woocommerce;
            $response = array();
            //get charter booking product ids.
            $args = array(
                'type' => 'charter_booking',
                'return'=>'ids',
                );
            $product_ids = \wc_get_products( $args );
            //arrays for dumping into
            $charter_gallery = [];
            $products = array();
            foreach($product_ids as $product_id){
                $product = wc_get_product($product_id);
                $products[] = $product->get_id();
            }
            $orders = array();
            $charter_details = array();
            foreach($products as $product_id){
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
                $product_orders = $woocommerce_rest->get('orders', $params);
                foreach($product_orders as $this_order){
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
                        $factory = new CB_Booking_Factory($type, $variation_id, $order_id);
                        //$factory->make_booking($type, $variation_id, $orderid = NULL);
                        $factory->make_booking($order_array['type'], $order_array['variation_id'], $order_array['order_id']);
                        $order_array['factory'] = $factory;
                        $order_array['booking'] = $factory->booking;
                    }
                    $charter_details[] = $order_array;
                }
            }
            $response['charter_details'] = $charter_details;
            return $response;
        }
    }





} // end class


?>