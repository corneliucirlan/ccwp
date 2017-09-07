<?php

    namespace ccwp\setup;

    class Setup
    {
        /**
         * Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            add_action('after_setup_theme', array($this, 'setup'));

            // Define content width
            add_action('after_setup_theme', array($this, 'contentWidth'), 0);
        }

        public function setup()
        {
            // Activate this if building a multilingual theme
            // load_theme_textdomain('ccwp', get_template_directory() . '/languages' );

            // Default Theme Support options better have
            add_theme_support('automatic-feed-links');
            add_theme_support('title-tag');
            add_theme_support('post-thumbnails');
            add_theme_support('html5', array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
            ));
            add_theme_support('custom-background', array(
                'default-color' => 'ffffff',
                'default-image' => '',
            ));
            add_theme_support('post-formats', array(
                'aside',
                'gallery',
                'link',
                'image',
                'quote',
                'status',
                'video',
                'audio',
                'chat',
            ));
        }

        /**
         *    Define a max content width to allow WordPress to properly resize your images
         */
        public function contentWidth()
        {
            $GLOBALS['content_width'] = apply_filters('content_width', 1440);
        }
    }

?>
