	<footer id="colophon" class="site-footer">
		<?php if ( has_nav_menu( 'menu-2' ) ) : ?>
			<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer', '_s' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-2',
						'menu_id'        => 'footer-menu',
						'depth'          => 1,
					)
				);
				?>
			</nav><!-- .footer-navigation -->
		<?php endif; ?>
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
