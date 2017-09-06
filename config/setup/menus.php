<?php

    namespace ccwp\setup;

    class Menus
    {
        /*
            Contrusct class to activate actions and hooks as soon as the class is initialized
        */
        public function __construct()
        {
            add_action('after_setup_theme', array($this, 'registerMenus'));
        }

        public function registerMenus()
        {
            /*
                Register all your menus here
            */
            register_nav_menus(array(
                'primary' => esc_html__('Primary', 'ccwp'),
            ));
        }
    }

?>
