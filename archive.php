<?php

    /**
     * Template for displaying archive pages
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

		<main id="main" class="site-main" role="main">

			<?php
				if (have_posts()): ?>

    					<header>
    						<?php
    							the_archive_title('<h1 class="page-title">', '</h1>');
    							the_archive_description('<div class="archive-description">', '</div>');
    						?>
    					</header>

    					<?php
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
