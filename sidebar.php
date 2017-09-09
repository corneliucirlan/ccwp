<?php

    /**
     * The sidebar containing the main widget area
     *
     * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
     *
     * @package ccwp
     */

    // Security check
    if (!defined('ABSPATH')) exit;

?>

<?php

    // Check if sidebar is activated
    if (!is_active_sidebar('ccwp-sidebar')) return;

?>

<aside class="widget-area" role="complementary">
	<?php dynamic_sidebar('ccwp-sidebar'); ?>
</aside>
