<?php

    /**
     * Footer template file
     *
     * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
     *
     * @package ccwp
     */

    // Security check
    if (!defined('ABSPATH')) exit;

?>

        </div>

        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12 col-md-8">
                        <?php
                            if (has_nav_menu('primary')):
                                $args = array(
                                    'theme_location' => 'primary',
                                    'menu' => 'primary',
                                    'container' => 'ul',
                                    'menu_class' => 'footer-menu',
                                    'echo' => true,
                                    'fallback_cb' => 'wp_page_menu',
                                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'depth' => 0,
                                );
                                wp_nav_menu($args);
                            endif;
                        ?>
                    </div>

                    <div class="col-xs-12 col-md-4">Copyright &copy; <?php echo date('Y') ?> <?php bloginfo() ?>. All rights reserved.</div>
                </div>
            </div>
        </footer>
        <?php wp_footer() ?>
    </body>
</html>
