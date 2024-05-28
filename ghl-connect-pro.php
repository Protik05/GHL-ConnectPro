<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.ibsofts.com
 * @since             1.0.0
 * @package           GHLCONNECTPRO
 *
 * @wordpress-plugin
 * Plugin Name:       GHL Connect for WooCommerce Pro
 * Plugin URI:        https://www.ibsofts.com/plugins/ghl-connect-pro
 * Description:       This plugin will connect the popular CRM goHighlevel(Go High Level) to the most popular content management software WordPress.
 * Version:           1.0.0
 * Author:            iB Softs
 * Author URI:        https://www.ibsofts.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ghl-connect-pro
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GHLCONNECTPRO_VERSION', '1.0.0' );
define( 'GHLCONNECTPRO_PLUGIN_BASENAME', plugin_basename( __DIR__ ) );
define( 'GHLCONNECTPRO_LOCATION_CONNECTED', false );
define( 'GHLCONNECTPRO_PATH', plugin_basename( __FILE__ ));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ghl-connect-pro-activator.php
 */
if ( ! function_exists( 'ghlconnectpro_activate' ) ) {
	function ghlconnectpro_activate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect-pro-activator.php';
		GHLCONNECTPRO_Activator::activate();
	}
	register_activation_hook( __FILE__, 'ghlconnectpro_activate' );
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ghl-connect-pro-deactivator.php
 */
if ( ! function_exists( 'ghlconnectpro_deactivate' ) ) {
	function ghlconnectpro_deactivate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect-pro-deactivator.php';
		GHLCONNECTPRO_Deactivator::deactivate();
	}
	register_deactivation_hook( __FILE__, 'ghlconnectpro_deactivate' );
}

/* Check If woocommerce Is Active */
function ghlconnectpro_woocommerce() {   
    
    if (!is_plugin_active('woocommerce/woocommerce.php')) {
        add_action('admin_notices', 'ghlconnectpro_woo_notice');
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }      
}
add_action('admin_init', 'ghlconnectpro_woocommerce');



/**
 * Display an error message when parent plugin is missing
 */
function ghlconnectpro_woo_notice()
{
?>
    <div class="notice notice-error">
        <p>
            <strong>Error:</strong>
            <em>GHL Connect for WooCommerce</em> plugin won't execute
            because the required Woocommerce plugin is not active. Install Woocommerce.
        </p>
    </div>
<?php
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ghl-connect-pro.php';
/**
 * Inclusion of ghl-connect-pro-definitions.php
 */
require_once plugin_dir_path( __FILE__ ) . 'ghl-connect-pro-definitions.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( ! function_exists( 'ghlconnectpro_run' ) ) {
	function ghlconnectpro_run() {

		$plugin = new GHLCONNECTPRO();
		$plugin->run();

	}
	ghlconnectpro_run();
}
