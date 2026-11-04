<?php
get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', '_s' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', '_s' ); ?></p>

					<?php
					get_search_form();
					?>

					<div class="widget widget_recent_entries">
						<h2 class="widget-title"><?php esc_html_e( 'Recent Posts', '_s' ); ?></h2>
						<ul>
							<?php
							$_s_recent_posts = get_posts(
								array(
									'numberposts'         => 5,
									'ignore_sticky_posts' => true,
								)
							);
							foreach ( $_s_recent_posts as $_s_recent_post ) :
								?>
								<li><a href="<?php echo esc_url( get_permalink( $_s_recent_post ) ); ?>"><?php echo esc_html( get_the_title( $_s_recent_post ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div><!-- .widget -->

					<div class="widget widget_categories">
						<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', '_s' ); ?></h2>
						<ul>
							<?php
							wp_list_categories(
								array(
									'orderby'    => 'count',
									'order'      => 'DESC',
									'show_count' => 1,
									'title_li'   => '',
									'number'     => 10,
								)
							);
							?>
						</ul>
					</div><!-- .widget -->

					<div class="widget widget_archive">
						<h2 class="widget-title"><?php esc_html_e( 'Archives', '_s' ); ?></h2>
						<p><?php esc_html_e( 'Try looking in the monthly archives.', '_s' ); ?></p>
						<ul>
							<?php
							wp_get_archives(
								array(
									'type'  => 'monthly',
									'limit' => 12,
								)
							);
							?>
						</ul>
					</div><!-- .widget -->

					<div class="widget widget_tag_cloud">
						<h2 class="widget-title"><?php esc_html_e( 'Tags', '_s' ); ?></h2>
						<?php wp_tag_cloud(); ?>
					</div><!-- .widget -->

			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
