<?php

    /**
     * Widgets class
     *
     * @package ccwp
     */

    namespace ccwp\core;

    class Widgets
    {
        /**
         * Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            add_action('widgets_init', array($this, 'initWidgets'));
        }

        /**
         * Register sidebar
         */
        public function initWidgets()
        {
            register_sidebar(array(
                'name'          => esc_html__('Sidebar', 'ccwp'),
                'id'            => 'ccwp-sidebar',
                'description'   => esc_html__('Default sidebar to add all your widgets.', 'ccwp'),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            ));
        }
    }

?>
