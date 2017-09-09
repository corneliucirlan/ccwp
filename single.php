<?php

    /**
     * Template for displaying single posts
     *
     * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
     *
     * @package ccwp
     */

    // Security check
    if (!defined('ABSPATH')) exit;

?>

<?php get_header() ?>

<div class="row">
    <div class="col-xs-12 col-md-8">

		<main class="site-main" role="main">
            <?php
				/* Start the Loop */
				while (have_posts()):
                    the_post();
					get_template_part('templates/post', get_post_format());
					the_post_navigation();

                    // If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()):
						comments_template();
					endif;
				endwhile;
			?>
        </main>
    </div>
</div>

<?php get_footer() ?>
