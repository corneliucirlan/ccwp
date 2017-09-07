<?php

    /**
     * Header template file
     *
     * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
     *
     * @package ccwp
     */

?>

<!DOCTYPE html>
<html <?php language_attributes() ?>>
    <head>
        <meta charset="<?php bloginfo('charset') ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

        <?php wp_head() ?>
    </head>

    <body <?php body_class() ?>>

        <!-- Header -->
        <header>
            <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
                <div class="container-fluid">

                    <!-- Navbar toggle -->
                    <button class="navbar-toggler navbar-toggler-left" type="button">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Brand -->
                    <a class="navbar-brand" href="<?php bloginfo('url') ?>"><?php function_exists('has_custom_logo') && has_custom_logo() ? the_custom_logo() : bloginfo(); ?></a>

                    <!-- Main menu -->
                    <?php
                        if (has_nav_menu('primary')):
                            $args = array(
                                'theme_location' => 'primary',
                                'menu' => 'header-menu',
                                'container' => 'ul',
                                'menu_class' => 'navbar-nav navbar-nav-left',
                                'echo' => true,
                                'fallback_cb' => 'wp_page_menu',
                                'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                'depth' => 0,
                            );
                            wp_nav_menu($args);
                        endif;
                    ?>
                </div>
            </nav>
        </header>

        <div class="container-fluid">
