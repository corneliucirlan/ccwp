<?php

    /**
     * ACF Loader class
     *
     * @package ccwp
     */

    namespace ccwp\custom;

    class ACFLoader
    {
        /**
         * Construct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct($hide = false)
        {
            // Update ACF path
            add_filter('acf/settings/path', array($this, 'updatePath'));

            // Update ACF dir
            add_filter('acf/settings/dir', array($this, 'updateDir'));

            // Hide ACF from menu
            if ($hide)
                add_action('admin_menu', array($this, 'hideFromMenu'), 999);

            // Load ACF
            include_once(get_template_directory().'/config/acf/acf.php');
        }

        /**
         * Update ACF path
         */
        private function updatePath($path)
        {
            // Set path
            $path = get_stylesheet_directory() . '/config/acf/';

            // Return new path
            return $path;
        }

        /**
         * Update ACF dir
         */
        private function updateDir($dir)
        {
            // Set dir
            $dir = get_stylesheet_directory_uri() . '/config/acf/';

            // Return new dir
            return $dir;
        }

        // Hide ACF from menu
        public function hideFromMenu()
        {
            remove_menu_page('edit.php?post_type=acf');
        }
    }

?>
