	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<?php
			printf(
				/* translators: 1: current year, 2: site name. */
				esc_html__( '&copy; %1$s %2$s', '_s' ),
				esc_html( gmdate( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
