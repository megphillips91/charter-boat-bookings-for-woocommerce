# Charter Boat Booking Payments
Contributors: megphillips91

Author: Meg Phillips

Author URI: https://msp-media.org/

Donate link: https://msp-media.org/product/support-open-source/

Stable tag: 2.0.01

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Charter Boat Booking Payments is an integration that enables online payments for reservations and final payments. Check out Charter Boat Bookings - a boat booking system for captains - Sunset Sails, Daysails, Sportfishing, Inshore Fishing

## WooCommerce Integration
The goal is still paid bookings through WooCommerce, but less entangled. The idea is that the booking can be free, or require payments. That integration can be triggered through the Calendar Booking Block and the "add to cart" will just be a simple charter payment. 
- the integration of each date as a product variation was just overly complicated
- the concerns will be very separate and give captains more control over how and when to accept online payment reservations and final payments. 
- I think I can trigger woocommerce plugin install/activation wizard when the content creator chooses the calendar booking block so I'm going to experiment with all that before I even try to begin publishing this integration. 

With the new WooCommerce Store API and the **coming soon** new order object table I think its going to be far far less code to maintain and better overall for me to create something that I am much happier with that functions less 'buggy'. I don't really have much of a choice because my husband has shopped all the other booking systems and still wants this one even after I pleaded with him to check out other options. So, here we are. The whole thing sort of came to a suddenly critical problem when he had customers at the dock that he was not prepared for because of errors in my poorly maintained plugin. Uh Oh!

### WooCommerce Extension: Charter Boat Booking Payments
Depending on the user experience that eventually works itself into reality, I may be able to use an existing Woo extension but if not, I can pretty easily modify the Booking Blocks to "add to cart" in addition to "adding to bookings" and then I can consider what/if any integration between payment_status and booking_status needs to be considered. At the moment, most likely I figure just adding order_ids to the booking_meta table for every order associated with a particular booking. Although several of my charter boat captains are just wanting more like a customer statement view that shows bookings against payments and refunds more like a charter customer statement. 

## Other Charter Boat Bookings Plugins
When I originally wrote Charter Boat Bookings, I did not fully understand ReactJS. In fact, I was only just learning it. Now that I have a mnuch more mature understanding and a better appreciation for how much easier it is to maintain...I want to build a gallery of plugins which are all quite separate each other as to better enable myself to maintain all the parts / peices and retire the ones that can be replaced by other plugins maintained by other people (I hope at some point). So...it shall be a series of plugins including the following:

### Charter Boat Bookings: The Core API **Required
Each of the blocks and the admin experience will live in separate plugins. Even if it seems insane, I want to keep them all separate so that other people can toy around with the customer facing shopping experience and the WP Admin experience that they like the best. Maybe at some point, this Booking Rest API will be useful to some other bookings plugins and I can get a little help maintaining the back end code, combining efforts on the core 'bookings' plugin rest api. 

### Charter Boat Booking Blocks: Booking Calendars
The goal is to publish the booking calendar block and a "global" booking calendar block like the one that I offered in Charter Boat Bokings Pro. The global block will pull all the charters onto the same availability calendar. The Remote Booking Calendar block will offer affiliate bookings for boats living on different URLs. 

### Charter Boat Bookings: Admin Experience
And ReactJS WP-Admin experience or maybe a standable ReactJS PWA. I haven't decided yet on that one. Its honestly easier to do the standalone, and I think there is more long term potential if I do it that way because in theory, a SaaS Bookings Platform could be offered separately as its own thing not requiring an integrated WP Installation. And also, the components could perform double duty. 

## History
//for another time

## Separation of concerns
//for another time


