<?php

    /**
     * Remove class
     *
     * @package ccwp
     */

    namespace ccwp\setup;

    class Remove
    {
        /**
         * Contrusct class to activate actions and hooks as soon as the class is initialized
         */
        public function __construct()
        {
            // Remove CSS/JS version
            add_filter('style_loader_src', array($this, 'removeScriptsVersion'), 10, 2);
        	add_filter('script_loader_src', array($this, 'removeScriptsVersion'), 10, 2);

            // Remove WP version
            remove_action('wp_head', 'wp_generator');

            // Remove Windows Live Writer Manifest Link
        	remove_action('wp_head', 'wlwmanifest_link');

            // Remove Weblog Client Link
        	remove_action('wp_head', 'rsd_link');

            // Remove RSS feed
        	remove_action('wp_head', 'feed_links_extra', 3);

            // Remove shortlink
        	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

            // Remove RSS Feed
            // add_action('do_feed', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_rdf', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_rss', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_rss2', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_atom', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_rss2_comments', array($this, 'removeRSSFeed'), 1);
        	// add_action('do_feed_atom_comments', array($this, 'removeRSSFeed'), 1);

            // Remove XML-RPC WordPress API
            add_filter('xmlrpc_methods', 'removeXMLRPC');
        }

        /**
         * Remove CSS/JS version
         */
        public function removeScriptsVersion($src)
        {
            if (strpos($src, '?ver='))
    	        $src = remove_query_arg('ver', $src);

    	    return $src;
        }

        /**
         * Remove RSS Feed
         */
        public function removeRSSFeed()
        {
            wp_die(__('No feed available, please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!'));
        }

        public function removeXMLRPC($methods)
        {
            unset($methods['wp.getUsersBlogs']);

    		return $methods;
        }
    }

?>
