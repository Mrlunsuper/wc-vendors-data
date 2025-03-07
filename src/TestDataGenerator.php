<?php

/**
 * Test Data Generator for WC Vendors
 *
 * Generates test data including vendors, products, and orders
 */
class WCV_Test_Data_Generator {
    private $faker;
    private $generated_vendors  = array();
    private $generated_products = array();
    private $generated_orders   = array();

    public function __construct() {
        // Ensure Faker is installed via composer
        if ( ! class_exists( 'Faker\Factory' ) ) {
            throw new Exception( 'Please install Faker: composer require fakerphp/faker' );
        }
        $this->faker = Faker\Factory::create();
    }

    /**
     * Generate test data with specified counts
     */
    public function generate_all( $vendor_count = 100, $products_per_vendor = 10, $orders_per_vendor = 250 ) {
        $this->generate_vendors( $vendor_count );
        $this->generate_products( $products_per_vendor );
        $this->generate_orders( $orders_per_vendor );
    }

    /**
     * Generate vendor accounts
     */
    private function generate_vendors( $count ) {
        for ( $i = 0; $i < $count; $i++ ) {
            $vendor_data = array(
                'user_login'   => $this->faker->userName,
                'user_email'   => $this->faker->email,
                'user_pass'    => wp_generate_password(),
                'first_name'   => $this->faker->firstName,
                'last_name'    => $this->faker->lastName,
                'display_name' => $this->faker->company,
                'role'         => 'vendor',
            );

            $vendor_id = wp_insert_user( $vendor_data );

            if ( ! is_wp_error( $vendor_id ) ) {
                // Add vendor meta
                update_user_meta( $vendor_id, 'pv_shop_name', $vendor_data['display_name'] );
                update_user_meta( $vendor_id, 'pv_shop_slug', sanitize_title( $vendor_data['display_name'] ) );
                update_user_meta( $vendor_id, 'pv_seller_info', $this->faker->paragraph );
                update_user_meta( $vendor_id, 'pv_shop_description', $this->faker->paragraphs( 2, true ) );

                $this->generated_vendors[] = $vendor_id;
            }
        }
    }

    /**
     * Generate products for each vendor
     */
    private function generate_products( $products_per_vendor ) {
        foreach ( $this->generated_vendors as $vendor_id ) {
            for ( $i = 0; $i < $products_per_vendor; $i++ ) {
                $product = array(
                    'post_title'   => $this->faker->productName,
                    'post_content' => $this->faker->paragraphs( 3, true ),
                    'post_status'  => 'publish',
                    'post_type'    => 'product',
                    'post_author'  => $vendor_id,
                );

                $product_id = wp_insert_post( $product );

                if ( ! is_wp_error( $product_id ) ) {
                    // Set product data
                    wp_set_object_terms( $product_id, 'simple', 'product_type' );
                    update_post_meta( $product_id, '_regular_price', $this->faker->randomFloat( 2, 10, 1000 ) );
                    update_post_meta( $product_id, '_price', get_post_meta( $product_id, '_regular_price', true ) );
                    update_post_meta( $product_id, '_stock_status', 'instock' );
                    update_post_meta( $product_id, '_manage_stock', 'yes' );
                    update_post_meta( $product_id, '_stock', $this->faker->numberBetween( 5, 100 ) );

                    $this->generated_products[ $vendor_id ][] = $product_id;
                }
            }
        }
    }

    /**
     * Generate orders for each vendor
     */
    private function generate_orders( $orders_per_vendor ) {
        foreach ( $this->generated_vendors as $vendor_id ) {
            for ( $i = 0; $i < $orders_per_vendor; $i++ ) {
                // Create a customer for this order
                $customer_data = array(
                    'user_login' => $this->faker->userName,
                    'user_email' => $this->faker->email,
                    'user_pass'  => wp_generate_password(),
                    'role'       => 'customer',
                );

                $customer_id = wp_insert_user( $customer_data );

                if ( is_wp_error( $customer_id ) ) {
                    continue;
                }

                // Create the order
                $order = wc_create_order(
                    array(
                        'customer_id' => $customer_id,
                        'status'      => 'completed',
                    )
                );

                if ( is_wp_error( $order ) ) {
                    continue;
                }

                // Add 1-3 products from this vendor to the order
                $num_products    = rand( 1, 3 );
                $vendor_products = $this->generated_products[ $vendor_id ];

                for ( $j = 0; $j < $num_products; $j++ ) {
                    $product_id = $vendor_products[ array_rand( $vendor_products ) ];
                    $quantity   = rand( 1, 5 );

                    $order->add_product( wc_get_product( $product_id ), $quantity );
                }

                // Add order meta
                $order->set_address(
                    array(
                        'first_name' => $this->faker->firstName,
                        'last_name'  => $this->faker->lastName,
                        'address_1'  => $this->faker->streetAddress,
                        'city'       => $this->faker->city,
                        'state'      => $this->faker->state,
                        'postcode'   => $this->faker->postcode,
                        'country'    => $this->faker->countryCode,
                    ),
                    'billing'
                );

                $order->calculate_totals();
                $order->save();

                $this->generated_orders[ $vendor_id ][] = $order->get_id();
            }
        }
    }

    /**
     * Get generated data counts
     */
    public function get_stats() {
        return array(
            'vendors'  => count( $this->generated_vendors ),
            'products' => array_sum( array_map( 'count', $this->generated_products ) ),
            'orders'   => array_sum( array_map( 'count', $this->generated_orders ) ),
        );
    }

    /**
     * Clean up all generated test data
     *
     * @return array Array containing counts of deleted items
     */
    public function cleanup() {
        $deleted = array(
            'orders'   => 0,
            'products' => 0,
            'vendors'  => 0,
        );

        // Delete orders first
        foreach ( $this->generated_orders as $vendor_orders ) {
            foreach ( $vendor_orders as $order_id ) {
                $order = wc_get_order( $order_id );
                if ( $order ) {
                    // Delete the customer associated with the order
                    wp_delete_user( $order->get_customer_id() );
                    // Force delete the order
                    $order->delete( true );
                    ++$deleted['orders'];
                }
            }
        }

        // Delete products
        foreach ( $this->generated_products as $vendor_products ) {
            foreach ( $vendor_products as $product_id ) {
                if ( wp_delete_post( $product_id, true ) ) {
                    ++$deleted['products'];
                }
            }
        }

        // Delete vendors
        foreach ( $this->generated_vendors as $vendor_id ) {
            if ( wp_delete_user( $vendor_id ) ) {
                ++$deleted['vendors'];
            }
        }

        // Clear the stored IDs
        $this->generated_orders   = array();
        $this->generated_products = array();
        $this->generated_vendors  = array();

        return $deleted;
    }
}
