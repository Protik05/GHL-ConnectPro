<?php
 if ( ! defined( 'ABSPATH' ) ) exit;
function ghlconnectpro_connect_to_ghl_based_on_order( $order_id, $old_status, $new_status ){

    $order = wc_get_order($order_id);//fetch the order id.

    $ghlconnectpro_order_status = get_option('ghlconnectpro_order_status', 'wc-processing');
    $set_new_status = str_ireplace( "wc-", "", $ghlconnectpro_order_status );

    if ( $set_new_status != $new_status ) {
        return;
    }
    //fetch the location 
    $locationId = get_option( 'ghlconnectpro_locationId' );
    
    //make a contact data and send it to the connected location.
    $contact_data = [
        "locationId"    => $locationId,
        "firstName"     => $order->get_billing_first_name(),
        "lastName"      => $order->get_billing_last_name(),
        "email"         => $order->get_billing_email(),
        "phone"         => $order->get_billing_phone()      
    ];
    $invoice_data=get_option('ghlconnectpro_invoice_check');
    
    //do here to send invoice data.
    if($invoice_data==='yes'){
        create_ghlpro_invoices($order);
    }
    $contactId = ghlconnectpro_get_location_contact_id($contact_data);

    $product_ids = array(); // Initialize an array to store product IDs

    // Get and store product IDs from order items
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_ids[] = $item->get_product_id();
    }

    // Fetch meta data for all product IDs in one query
    $products_meta = array();
    foreach ( $product_ids as $product_id ) {
        $products_meta[ $product_id ] = array(
            'tags' => get_post_meta( $product_id, 'ghlconnectpro_location_tags', true ),
            'workflow' => get_post_meta( $product_id, 'ghlconnectpro_location_workflow', true ),
        );
    }

    // Process order items
    foreach ( $order->get_items() as $item_id => $item ) {
        $product_id = $item->get_product_id();
        $product_meta = $products_meta[ $product_id ];

        $tags = array( 'tags' => $product_meta['tags'] );
        ghlconnectpro_location_add_contact_tags( $contactId, $tags );
    


        if ( ! empty( $product_meta['workflow'] ) ) {
            foreach ( $product_meta['workflow'] as $workflow_id ) {
                ghlconnectpro_location_add_contact_to_workflow( $contactId, $workflow_id );
            }
        }
    }

}
add_action( 'woocommerce_order_status_changed', 'ghlconnectpro_connect_to_ghl_based_on_order', 10, 3 );