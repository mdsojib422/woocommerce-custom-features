<?php 

/* YHS Shipping Class For Composite Product for single attribute */

//add_action( 'woocommerce_before_calculate_totals', 'set_flagpoles_shipping_class_based_on_size', 10, 1 );

function set_flagpoles_shipping_class_based_on_size( $cart ) {
    if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
        return;
    }

    // Loop through cart items
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['composite_data'] ) && is_array( $cart_item['composite_data'] ) && 20777 == $cart_item['product_id'] ) {
            foreach ( $cart_item['composite_data'] as $component_id => $component_data ) {
                if ( isset( $component_data['attributes']['attribute_pa_size'] ) ) {
                    $selected_size = $component_data['attributes']['attribute_pa_size'];
                    add_shipping_class_by_size( $selected_size, $cart_item );
                }
            }
        }
    }
}

// Helper function to get the shipping class ID by name
function get_shipping_class_id_by_name( $shipping_class_name ) {
    $shipping_class = get_term_by( 'slug', sanitize_title( $shipping_class_name ), 'product_shipping_class' );

    if ( $shipping_class ) {
        error_log( "Shipping Class Name: " . $shipping_class_name );
        error_log( "Shipping Class ID: " . $shipping_class->term_id );
        return $shipping_class->term_id;
    } else {
        error_log( "Shipping Class Not Found: " . $shipping_class_name );
        return 0; // Return 0 if no matching shipping class is found
    }
}

function add_shipping_class_by_size( $selected_size, &$cart_item ) {
    $shipping_class_name = '';

    switch ( $selected_size ) {
    case '15-2':
        $shipping_class_name = 'flagpoles-15';
        break;
    case '20-2':
        $shipping_class_name = 'flagpoles-20';
        break;
    case '20-lt':
        $shipping_class_name = 'flagpoles-20lt';
        break;
    case '25':
        $shipping_class_name = 'flagpoles-25';
        break;
    default:
        error_log( "No matching size found for: " . $selected_size );
        break;
    }

    if ( $shipping_class_name ) {
        $shipping_class_id = get_shipping_class_id_by_name( $shipping_class_name );
        if ( $shipping_class_id ) {
            $cart_item['data']->set_shipping_class_id( $shipping_class_id );
        }
    }
}