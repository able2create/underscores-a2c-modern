<?php
get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			// Uncomment if you want to enable comments on pages
			/*
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			*/

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
// get_sidebar(); // Uncomment if you want to enable sidebar
get_footer();
