<?php
/**
 * Plugin Name: On Sale Filter
 * Plugin URI: https://hagarhosny.com.co/woocommerce-on-sale-filter
 * Description: Adds a filter dropdown to filter WooCommerce products by sale status (on sale or not on sale)
 * Version: 1.0.0
 * Author: Hagar Hosny
 * Author URI: https://hagarhosny.com.co/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: on-sale-filter
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
function woo_sale_filter_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'woo_sale_filter_woocommerce_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        return false;
    }
    return true;
}

// Admin notice if WooCommerce is not active
function woo_sale_filter_woocommerce_notice() {
    ?>
    <div class="error">
        <p><?php esc_html_e('On Sale Filter requires WooCommerce to be installed and active.', 'on-sale-filter'); ?></p>
    </div>
    <?php
}

// Plugin activation hook
function woo_sale_filter_activation() {
    if (!woo_sale_filter_check_woocommerce()) {
        wp_die(esc_html__('This plugin requires WooCommerce to be installed and active.', 'on-sale-filter'));
    }
}
register_activation_hook(__FILE__, 'woo_sale_filter_activation');

// Check WooCommerce on plugins loaded
add_action('plugins_loaded', 'woo_sale_filter_check_woocommerce');

/**
 * Add sale filter dropdown to WooCommerce product filters
 */
function woo_sale_filter_dropdown($output) {
    $selected = filter_input(INPUT_GET, 'product_sale', FILTER_VALIDATE_INT);
    if ($selected === false || $selected === null) {
        $selected = 0;
    }
    
    $output .= '
        <select id="dropdown_product_sale" name="product_sale">
            <option value="">' . esc_html__('Filter by sale', 'on-sale-filter') . '</option>
            <option value="1" ' . selected($selected, 1, false) . '>' . esc_html__('On sale', 'on-sale-filter') . '</option>
            <option value="2" ' . selected($selected, 2, false) . '>' . esc_html__('Not on sale', 'on-sale-filter') . '</option>
        </select>
    ';
 
    return $output;
}
add_filter('woocommerce_product_filters', 'woo_sale_filter_dropdown');

/**
 * Modify the WHERE clause to filter products by sale status
 */
function woo_sale_filter_where_statement($where) {
    global $wpdb, $pagenow;
 
    // Get selected value.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading a URL parameter from WooCommerce's own admin list-table filter form, which does not include a nonce.
    $selected = filter_input(INPUT_GET, 'product_sale', FILTER_VALIDATE_INT);

    // Only trigger in admin on products page when filter is selected.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Same as above: no nonce on WooCommerce's built-in list-table filter form.
    $post_type = isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
    if (!is_admin() || $pagenow !== 'edit.php' || $post_type !== 'product' || !$selected) {
        return $where;
    }

    // Retrieve sale product IDs from cache or query the database.
    $cache_key  = 'woo_sale_filter_product_ids';
    $cache_group = 'woo_sale_filter';
    $pageposts  = wp_cache_get( $cache_key, $cache_group );

    if ( false === $pageposts ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- No WP/WooCommerce API covers this cross-table sale-price lookup; result is cached immediately below.
        $pageposts = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.ID, p.post_parent
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE pm.meta_key = %s
                AND pm.meta_value != %s
                AND pm.meta_value IS NOT NULL",
                '_sale_price',
                ''
            ),
            OBJECT
        );
        wp_cache_set( $cache_key, $pageposts, $cache_group, HOUR_IN_SECONDS );
    }
    
    if (empty($pageposts)) {
        // If no products on sale, handle accordingly
        if ($selected == 1) {
            // Show no products
            $where .= " AND 1=0 ";
        }
        return $where;
    }
    
    // Get parent product IDs for variations, otherwise use the product ID itself
    $productsIDs = array_map(function($n) {
        return $n->post_parent > 0 ? $n->post_parent : $n->ID;
    }, $pageposts);
    
    // Remove duplicates
    $productsIDs = array_unique($productsIDs);
    
    if ($selected == 1) {
        // Show only products on sale
        $where .= ' AND ' . $wpdb->posts . '.ID IN (' . implode(',', array_map('absint', $productsIDs)) . ') ';
    } elseif ($selected == 2) {
        // Show only products NOT on sale
        $where .= ' AND ' . $wpdb->posts . '.ID NOT IN (' . implode(',', array_map('absint', $productsIDs)) . ') ';
    }
    
    return $where;
}
add_filter('posts_where', 'woo_sale_filter_where_statement');

?>