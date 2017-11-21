<?php
namespace ACFWidgets\acf;

use ACFWidgets\Helpers\WidgetHelper;

class WidgetACF {

    const FIELD_ID = 'acf_widgets';
    const FIELD_TITLE = 'Widgets';
    const FIELD_KEY = 'field_345356547645';

    public static function register($layouts) {
        $field_group = self::generate_field_group($layouts);

        // inject default acf values in
        $field_group = self::add_default_widget_acf($field_group);

        register_field_group($field_group);
    }

    private static function generate_field_group($layouts) {
        /**
         * Generate the locations based on supported post types
         */
        $locations = [];
        foreach(WidgetHelper::get_supported_post_types() as $post_type) {
            $locations[] = [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => $post_type,
                ]
            ];
        }

        return [
            'id' => self::FIELD_ID,
            'title' => self::FIELD_TITLE,
            'fields' => [
                [
                    'key' => self::FIELD_KEY,
                    'label' => self::FIELD_TITLE,
                    'name' => self::FIELD_ID,
                    'type' => 'flexible_content',
                    'layouts' => $layouts, /* these are our widget implementations */
                    'button_label' => 'Add Widget',
                    'min' => '',
                    'max' => '',
                ],
            ],
            'location' => $locations,
            'options' => [
                'position' => 'normal',
                'layout' => 'no_box',
                'hide_on_screen' => [],
            ],
            'menu_order' => 0,
        ];
    }


    private static function add_default_widget_acf(&$field_group) {
        //load in default acf
        $default_acf = include __DIR__ . '/_default.php';

        foreach($field_group['fields'][0]['layouts'] as $i => $layout) {
            // since each widget is different, we need a different id for each default field
            $new_acf = $default_acf;
            foreach($new_acf as $k => $acf ){
                $id = 'field_' . $k . $i;
                $new_acf[$k]['key'] = $id;
            }
            $field_group['fields'][0]['layouts'][$i]['sub_fields'] = array_merge($new_acf, $layout['sub_fields']);
        }

        return $field_group;
    }
}
