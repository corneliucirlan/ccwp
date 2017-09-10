<?php

    /**
     * Menus class
     *
     * @package ccwp
     */

    namespace ccwp\setup;

    class Menus
    {
        /**
         *    Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            // Register menus
            add_action('after_setup_theme', array($this, 'registerMenus'));

            // Modify navigation items' classes
            add_filter('nav_menu_css_class', array($this, 'updateNavItemClasses'), 10, 2);

            // Modify navigation items' anchor classes
            add_filter('walker_nav_menu_start_el', array($this, 'updateNavItemAnchorClasses'), 10, 4);
        }

        /**
         * Register navigation menus
         */
        public function registerMenus()
        {
            register_nav_menus(array(
                'primary' => esc_html__('Primary', 'ccwp'),
            ));
        }

        /**
         * Modify navigation items' classes
         */
        public function updateNavItemClasses($classes, $item)
        {
    		$classes[] = "nav-item";

            // Add 'active' class to current nav item
            if (in_array('current-post-ancestor', $classes) || in_array('current-page-ancestor', $classes) || in_array('current-menu-item', $classes))
                $classes[] = 'active';

    		return $classes;
        }

        /**
         * Modify navigation items' anchor classes
         */
        public function updateNavItemAnchorClasses($item_output, $item, $depth, $args)
        {
            $item_output = preg_replace('/<a /', '<a class="nav-link" ', $item_output, 1);
    		return $item_output;
        }
    }

?>
