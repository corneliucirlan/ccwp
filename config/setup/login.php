<?php

    /**
     * Login class
     *
     * @package ccwp
     */

    namespace ccwp\setup;

    class Login
    {
        /**
         * Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            // Update logo
            add_action('login_enqueue_scripts', array($this, 'updateLogo'));

            // Update Logo URL
            add_filter('login_headerurl', array($this, 'updateLogoURL'));

            // Update Logo Title
            add_filter('login_headertitle', array($this, 'updateLogoTitle'));
        }

        /**
         * Update logo
         */
        public function updateLogo()
        {
            if (has_custom_logo()):
                $logoID = get_theme_mod('custom_logo');
                $logo = wp_get_attachment_image_src($logoID , 'full');
                ?>
                <style type="text/css">
                    #login h1 a, .login h1 a {
                        display: inline-block;
                        width: 100%;
                        margin: 0;
                        background-image: url(<?php echo $logo[0] ?>);
        				background-size: contain;
                    }
                </style>
                <?php
            endif;
        }

        /**
         * Update Logo URL
         */
        public function updateLogoURL()
        {
            return home_url();
        }

        /**
         * Update Logo Title
         */
        public function updateLogoTitle()
        {
            return get_bloginfo('name');
        }
    }

?>
