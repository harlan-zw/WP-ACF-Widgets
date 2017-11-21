<?php
namespace ACFWidgets;

use ACFWidgets\acf\WidgetACF;
use ACFWidgets\Helpers\WidgetHelper;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Loader {

    const PLUGIN_NAME = 'ACFWidgets';
    const VERSION = '1.0.0';

    public static $includes_path;
    public static $cpt_path;
    public static $acf_path;
    public static $tax_path;

    private $widgets;
    private $dynamic_fields;

    public function __set($name, $value) {
        $this->dynamic_fields[$name] = $value;
    }

    public function __get($name) {
        return $this->dynamic_fields[$name];
    }

	/**
	 * Make sure our empty() and isset() functions behave correctly
	 * @param $name
	 * @return bool
	 */
	public function __isset($name) {
		return isset($this->dynamic_fields[$name]);
	}

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     * @param $classes
     */
    public function __construct($classes) {
        self::$includes_path = __DIR__;
        self::$cpt_path = self::$includes_path . '/cpt/';
        self::$acf_path = self::$includes_path . '/acf/';
        self::$tax_path = self::$includes_path . '/tax/';

        foreach($classes as $class) {
            $this->$class = new $class;
        }
    }

    /**
     * Iterates through all files in the includes/acf folder and including them
     */
    private function load_acf() {
        // check ACF is loaded
        if (!function_exists('register_field_group')) {
            return;
        }
        if (!file_exists(self::$acf_path)) {
            return;
        }
        // iterate all files in acf
        foreach (scandir(self::$acf_path, SCANDIR_SORT_NONE) as $file) {
            $file = self::$acf_path . $file;
            if (is_file($file)) {
                include_once $file;
            }
        }
    }

    public function register_widgets() {
        // Check if we have the widgets folder
        if (!file_exists(get_stylesheet_directory() . '/templates/widgets/')) {
            return;
        }
        $this->widgets = apply_filters('acf-widgets/register', []);
        // Only if widgets have been registered
        if (empty($this->widgets)) {
            return;
        }
        // resolve the acf data for each widget registered
        $acf = collect($this->widgets)->mapWithKeys(function($widget) {
            return [$widget => WidgetHelper::to_acf($widget)];
        })->toArray();
        $acf = apply_filters('acf-widgets/register-acf', $acf);
        //register our widgets
        WidgetACF::register($acf);
        do_action('acf-widgets/ready');
    }

    public function run() {
        add_action('init', function() {
            // load in all our data
            $this->load_acf();
            // register our functions and hooks
            $this->{Front::class}->do_hooks();
            if (is_admin()) {
                $this->{Admin::class}->do_hooks();
            }
            $this->register_widgets();
        });
    }

}
