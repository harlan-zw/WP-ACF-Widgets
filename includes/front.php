<?php

namespace ACFWidgets;

use ACFWidgets\acf\WidgetACF;
use ACFWidgets\Helpers\WidgetHelper;
use ACFWidgets\model\Widget;

class Front {

	public $rendered = [];

    /**
     * Does our frontend hooks
     */
    public function do_hooks() {
        /**
         * This hook is added manually to the page content where we want our widgets to render
         */
        add_action('acf-widget/render', function ($options = []) {

        	$post_id = get_the_ID();
        	if (is_home()) {
		        $post_id = get_option('page_for_posts');
	        }

            $fields = get_field(WidgetACF::FIELD_ID, $post_id);

            if (empty($fields)) {
                return;
            }
            /**
             * Keep track of what index of the type we are rendering
             */
            $widgetTypes = [];
            /**
             * Each field is a different layout
             */
            foreach ($fields as $index => $field) {
                /**
                 * Only if they have the widget enabled
                 */
                if (!$field['enabled'] || !isset($field['acf_fc_layout'])) {
                    continue;
                }
                /**
                 * Render our widget!
                 */
                $widget_slug = $field['acf_fc_layout'];
				// only render widgets that have been specified
                if (!empty($options['include']) && !in_array($widget_slug, $options['include'])) {
                	continue;
				}
				if (!empty($options['exclude']) && in_array($widget_slug, $options['exclude'])) {
                	continue;
				}
				if (!isset($widgetTypes[$widget_slug])) {
                    $widgetTypes[$widget_slug] = 0;
                }
				WidgetHelper::render($widget_slug, $field,  ++$widgetTypes[$widget_slug]);
            }
        });

    }
}
