<?php
namespace ACFWidgets;

use ACFWidgets\Helpers\WidgetHelper;

class Admin {

    const META_BOXES_TO_HIDE = [
        'editor'
    ];

    /**
     * Run hooks related to admin functionality
     */
    public function do_hooks() {
        /**
         * Remove post type support to hide functionality that isn't used
         */
        add_action( 'add_meta_boxes', function() {

            $post_type = get_post_type();

            if (!WidgetHelper::is_post_type_supported($post_type)) {
                return;
            }

            collect(self::META_BOXES_TO_HIDE)->each(function($box) use($post_type) {
                remove_post_type_support( $post_type, $box );
            });

        }, PHP_INT_MAX );

        /**
         * Moves the yoast seo box to the bottom of the page
         */
        add_filter( 'wpseo_metabox_prio', function() {
            return 'low';
        });
    }

}
