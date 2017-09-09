<?php

    /**
     * Main WordPress template file
     *
     * @link https://codex.wordpress.org/Template_Hierarchy
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
    			if (have_posts()): ?>
        				<?php if (is_home() && ! is_front_page()): ?>
        					<header>
        						<h1 class="page-title"><?php single_post_title(); ?></h1>
        					</header>
        				<?php endif;

        				// Start the Loop
        				while (have_posts()):
                            the_post();
        					get_template_part('templates/post', get_post_format());
        				endwhile;
        				the_posts_navigation();
                    else:
                        get_template_part('templates/post', 'none');
                endif;
            ?>
		</main>
	</div>

	<div class="col-xs-12 col-md-4">
		<?php get_sidebar() ?>
	</div>
</div>

<?php get_footer() ?>
