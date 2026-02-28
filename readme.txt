=== On Sale Filter ===
Contributors: hagarhosny
Donate link: https://hagarhosny.com.co/
Tags: woocommerce, sale, filter, products, admin
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a filter dropdown to the WooCommerce admin products list to quickly filter products by sale status.

== Description ==

On Sale Filter adds a simple and effective dropdown filter to your WooCommerce admin products list page. Store managers can instantly filter the product list to show only products that are currently on sale, or only products that are not on sale — saving time when managing large catalogues.

**Features:**

* Dropdown filter on the WooCommerce admin products list
* Filter to show only products **on sale**
* Filter to show only products **not on sale**
* Handles product variations correctly
* Translation-ready (`.pot` file included)
* Lightweight — no settings page, no database tables

**Requirements:**

* WordPress 5.0 or higher
* WooCommerce 3.0 or higher
* PHP 7.2 or higher

== Installation ==

1. Upload the `woocommerce-on-sale-filter` folder to the `/wp-content/plugins/` directory, or install it directly from the WordPress plugin repository.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **WooCommerce > Products** in your admin dashboard.
4. Use the **Filter by sale** dropdown in the product filters bar and click **Filter**.

== Frequently Asked Questions ==

= Does this plugin work with variable products? =

Yes. The filter correctly identifies variable products by checking their variations for a sale price and maps them back to the parent product.

= Will the plugin activate if WooCommerce is not installed? =

No. The plugin checks for an active WooCommerce installation on activation and on every page load. If WooCommerce is not active, the plugin deactivates itself and shows an admin notice.

= Is the plugin translation-ready? =

Yes. All user-facing strings are internationalised using the `on-sale-filter` text domain. A `.pot` template file is located in the `/languages/` directory.

= Does this plugin add any database tables or store any data? =

No. The plugin does not create database tables or store any data. It simply filters the existing WooCommerce product query.

== Screenshots ==

1. The "Filter by sale" dropdown displayed in the WooCommerce admin products list filters bar.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
