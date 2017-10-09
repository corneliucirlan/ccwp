<?php

    /**
     * Settings API
     *
     * @package ccwp
     */

    namespace ccwp\api;

    class Customizer
    {
        /**
         * Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            add_action('customize_register', array($this, 'setup'));
            add_action('customize_preview_init', array($this, 'preview'));

            // Hook into wp_head to add custom CSS
            add_action('wp_head', array($this, 'header'));
        }

        /**
    	 * Add postMessage support for site title and description for the Theme Customizer.
    	 *
    	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
    	 */
    	public function setup($wp_customize)
    	{
    		$wp_customize->get_setting('blogname')->transport = 'postMessage';
    		$wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    	}

        /**
    	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
    	 */
    	public function preview()
    	{
    		wp_enqueue_script('customizer', get_template_directory_uri().'/assets/js/customizer.min.js', array('jquery', 'customize-preview'), '1.0.0', true);
    	}

        /**
         * Hook into wp_head to add custom CSS
         */
        public function header()
        {
            ?>
            <style type="text/css">
            </style>
            <?php
        }

        /**
         * Helper function to generate CSS for specific element
         */
        private function generateCSS($selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true)
        {
            $return = '';
            $mod = get_theme_mod($mod_name);
            if (!empty($mod)):
                $return = sprintf('%s { %s:%s; }', $selector, $style, $prefix.$mod.$postfix);

                if ($echo) echo $return;
            endif;

            return $return;
        }
    }

?>
