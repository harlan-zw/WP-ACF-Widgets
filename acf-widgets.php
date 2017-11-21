<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.4mation.com.au/
 * @since             1.0.0
 * @package           ACFWidgets
 *
 * @wordpress-plugin
 * Plugin Name:       ACF Widgets - 4mation
 * Plugin URI:        https://www.4mation.com.au/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            4mation
 * Author URI:        https://www.4mation.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ACFWidgets
 * Domain Path:       /languages
 */

namespace ACFWidgets;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Register the autoloader
 */
spl_autoload_register(function($class_name) {
    // Setup the autoloading so we can namespace our classes
    $base_path = dirname(__FILE__);

    $class_name = ltrim($class_name, '\\');
    if (strpos($class_name, __NAMESPACE__) !== 0) {
        return;
    }
    //add in directory seperators
    $class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    // remove the namespace
    $class_name = str_replace(__NAMESPACE__ . DIRECTORY_SEPARATOR, '', $class_name);

    // camel case to
    $class_name = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $class_name));

    $path = $base_path . '/includes/' . $class_name . '.php';

    if (!file_exists($path)) {
        return;
    }

    require_once $path;
});
/**
 * Begins execution of the plugin.
 */
$plugin = new Loader([
    // classes to preload
    Admin::class,
    Front::class
]);

/**
 * Register activation hook
 */
register_activation_hook(__FILE__, function () {
    Activator::activate();
});
/**
 * Register deactivation hook
 */
register_deactivation_hook(__FILE__, function () {
    Deactivator::deactivate();
});
/**
 * Finally run our plugin
 */
$plugin->run();
