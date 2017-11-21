<?php
namespace ACFWidgets\Helpers;

use ACFWidgets\acf\WidgetACF;
use ACFWidgets\Loader;
use ACFWidgets\model\Widget;
use Illuminate\Support\Str;
use Location\Location;

class WidgetHelper {
    /**
     * Our custom post type slug
     * @var string
     */
    const SLUG = Loader::PLUGIN_NAME;
    /**
     * The post types which support our widgets
     */
    const DEFAULT_SUPPORTED_POST_TYPES = [
        'page',
        'post'
    ];

    public $field;

    public static function to_acf($name) {
    	$file = get_stylesheet_directory_uri() . self::get_widgets_dir() . "$name/acf-fields.php";
		$acf = [];
    	if (file_exists($file)) {
			$acf = include $file;
		}
        $label = str_replace('-', ' ', $name);
        $label = Str::title($label);
        return [
            'label' => $label,
            'name' => $name,
            'display' => 'row',
            'min' => '',
            'max' => '',
            'sub_fields' => $acf
        ];
    }


    /**
     * Checks if a post type should be rendering the widgets
     * @param $post_type
     * @return bool
     */
    public static function is_post_type_supported($post_type) {
    	return in_array($post_type, self::get_supported_post_types());
    }

	/**
	 * Gets the directory that the widgets are within
	 * @return mixed|void
	 */
    public static function get_widgets_dir() {
	    return apply_filters('acf-widgets/widgets-dir', '/templates/widgets/');
    }

	/**
	 * Gets a list of supported post types
	 * @return array
	 */
    public static function get_supported_post_types() {
	    return apply_filters('acf-widgets/supported-post-types', self::DEFAULT_SUPPORTED_POST_TYPES);
    }

    public function get_slug() {
        return $this->field;
    }

    /**
     * Renders a specific widget
     * @param $field
     * @param $data
     * @param $field_type_index int This is used in the widget-template - Do not remove
     */
    public static function render($field, $data, $field_type_index) {
        do_action('acf-widgets/before-' . $field);

        $vars = [];
        // load in our acf fields into scope of the partial files
        foreach($data as $k => $v) {
            //only for our layout
            $vars[$k] = $v;
        }

        // declare dynamically to avoid unused variable
        ${'markup'} = false;
        try {
            ${'markup'} = \App\template(self::get_widgets_dir() . "$field/markup", $vars);
        } catch (\Exception $e) {
            echo "<p>Widget $field is missing markup!</p>";
            return;
        }

        //pass the widgets content to our template
        include \dirname(__DIR__) . '/partials/widget-template.php';

        do_action('acf-widgets/after-' . $field);
    }
    /**
     * Gets all widgets for the post id that are enabled and valid.
     * @param $post_id
     * @return Widget[]
     */
    public static function get_widgets_for_post($post_id) {
        return collect(get_field(WidgetACF::FIELD_ID, $post_id))->filter(function ($field) {
            return (isset($field['enabled']) && $field['enabled']) && isset($field['acf_fc_layout']);
        })->map(function($field, $i) {
            return new Widget($field, $i);
        })->toArray();
    }

}
