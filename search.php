<?php

    /**
     * Template for displaying search result pages
     *
     * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

    					<header>
    						<h1 class="page-title"><?php printf(esc_html__('Search Results for: %s', 'ccwp'), '<span>'.get_search_query().'</span>' ); ?></h1>
    					</header>

        				<?php
        					// Start the Loop
        					while (have_posts()):
                                the_post();
        						get_template_part('templates/post', 'search');
        					endwhile;
        					the_posts_navigation();
                    else:
						get_template_part('templates/post', 'none');
				endif;
            ?>
		</main>
    </div>

	<div class="col-sm-4">
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer() ?>
