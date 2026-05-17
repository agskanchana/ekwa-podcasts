<?php
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode: [ekwa_podcasts_carousel]
 *
 * Attributes:
 *   show_name        – Podcast show name
 *   show_image       – Show image URL
 *   show_description – Short description of the show
 *   posts_per_page   – Episodes loaded per AJAX page (default 9)
 *   per_slide        – Episode cards visible per carousel slide (default 3)
 */
add_shortcode( 'ekwa_podcasts_carousel', 'ekwa_podcasts_carousel_shortcode' );

function ekwa_podcasts_carousel_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'show_name'        => '',
			'show_image'       => '',
			'show_description' => '',
			'posts_per_page'   => 9,
			'per_slide'        => 3,
		],
		$atts,
		'ekwa_podcasts_carousel'
	);

	$posts_per_page = max( 1, min( 50, absint( $atts['posts_per_page'] ) ) );
	$per_slide      = max( 1, min( 6, absint( $atts['per_slide'] ) ) );

	// Count total episodes for pagination
	$count_query = new WP_Query( [
		'post_type'      => 'episode',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	] );
	$total_posts = (int) $count_query->found_posts;
	$total_pages = $total_posts > 0 ? (int) ceil( $total_posts / $posts_per_page ) : 0;

	$slides_html = ekwa_get_episode_slides( 1, $posts_per_page, $per_slide );

	ob_start();
	?>
	<div class="ekwa-podcasts-wrap"
		 data-posts-per-page="<?php echo esc_attr( $posts_per_page ); ?>"
		 data-per-slide="<?php echo esc_attr( $per_slide ); ?>">

		<?php if ( $atts['show_name'] || $atts['show_image'] ) : ?>
		<div class="ekwa-podcast-show-header">
			<?php if ( $atts['show_image'] ) : ?>
			<div class="ekwa-show-image-wrap">
				<img
					src="<?php echo esc_url( $atts['show_image'] ); ?>"
					alt="<?php echo esc_attr( $atts['show_name'] ); ?>"
					class="ekwa-show-image"
				/>
			</div>
			<?php endif; ?>
			<div class="ekwa-show-info">
				<?php if ( $atts['show_name'] ) : ?>
				<h2 class="ekwa-show-name"><?php echo esc_html( $atts['show_name'] ); ?></h2>
				<?php endif; ?>
				<p class="ekwa-show-episode-count"><?php echo esc_html( $total_posts ); ?> Episodes</p>
				<?php if ( $atts['show_description'] ) : ?>
				<p class="ekwa-show-description"><?php echo esc_html( $atts['show_description'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( $slides_html ) : ?>
		<div class="ekwa-carousel-outer">
			<button class="ekwa-carousel-arrow ekwa-arrow-prev" aria-label="Previous slide" disabled>&#8249;</button>
			<div class="ekwa-episodes-slider">
				<div class="ekwa-episodes-track">
					<?php echo $slides_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
			<button class="ekwa-carousel-arrow ekwa-arrow-next" aria-label="Next slide">&#8250;</button>
		</div>
		<?php else : ?>
		<p class="ekwa-no-episodes">No episodes found.</p>
		<?php endif; ?>

		<?php if ( $total_pages > 1 ) : ?>
		<div class="ekwa-episodes-pagination">
			<?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
			<button
				class="ekwa-page-btn<?php echo 1 === $i ? ' active' : ''; ?>"
				data-page="<?php echo esc_attr( $i ); ?>"
				aria-label="Load page <?php echo esc_attr( $i ); ?>"
			><?php echo esc_html( $i ); ?></button>
			<?php endfor; ?>
		</div>
		<?php endif; ?>

	</div>
	<?php
	return ob_get_clean();
}

/**
 * Build the episode slides HTML for a given page.
 *
 * Episodes are grouped into slides of $per_slide cards each.
 *
 * @param int $page           1-based page number.
 * @param int $posts_per_page Episodes per AJAX page.
 * @param int $per_slide      Cards per carousel slide.
 * @return string HTML string.
 */
function ekwa_get_episode_slides( $page = 1, $posts_per_page = 9, $per_slide = 3 ) {
	$query = new WP_Query( [
		'post_type'      => 'episode',
		'posts_per_page' => $posts_per_page,
		'paged'          => $page,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );

	if ( ! $query->have_posts() ) {
		return '';
	}

	$cards = [];

	while ( $query->have_posts() ) {
		$query->the_post();

		$post_id   = get_the_ID();
		$thumb_url = get_the_post_thumbnail_url( $post_id, 'medium' );
		$duration  = get_post_meta( $post_id, '_ekwa_duration', true );
		$excerpt   = get_the_excerpt();

		if ( strlen( $excerpt ) > 120 ) {
			$excerpt = substr( $excerpt, 0, strrpos( substr( $excerpt, 0, 120 ), ' ' ) ) . '...';
		}

		ob_start();
		?>
		<div class="ekwa-episode-card">
			<a href="<?php the_permalink(); ?>" class="ekwa-episode-image-link">
				<?php if ( $thumb_url ) : ?>
				<img
					src="<?php echo esc_url( $thumb_url ); ?>"
					alt="<?php the_title_attribute(); ?>"
					class="ekwa-episode-thumbnail"
					loading="lazy"
				/>
				<?php else : ?>
				<div class="ekwa-episode-no-image"></div>
				<?php endif; ?>
			</a>
			<div class="ekwa-episode-card-body">
				<h3 class="ekwa-episode-title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h3>
				<p class="ekwa-episode-date"><?php echo esc_html( get_the_date( 'M j Y, g:i a' ) ); ?></p>
				<?php if ( $excerpt ) : ?>
				<p class="ekwa-episode-excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<?php if ( $duration ) : ?>
				<p class="ekwa-episode-duration">Duration: <?php echo esc_html( $duration ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
		$cards[] = ob_get_clean();
	}
	wp_reset_postdata();

	// Group cards into slides
	$html         = '';
	$slide_groups = array_chunk( $cards, $per_slide );
	foreach ( $slide_groups as $group ) {
		$html .= '<div class="ekwa-slide">' . implode( '', $group ) . '</div>';
	}

	return $html;
}

// AJAX handlers for pagination
add_action( 'wp_ajax_ekwa_load_episodes', 'ekwa_ajax_load_episodes' );
add_action( 'wp_ajax_nopriv_ekwa_load_episodes', 'ekwa_ajax_load_episodes' );

function ekwa_ajax_load_episodes() {
	check_ajax_referer( 'ekwa_podcasts_nonce', 'nonce' );

	$page           = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 9;
	$per_slide      = isset( $_POST['per_slide'] ) ? absint( $_POST['per_slide'] ) : 3;

	$page           = max( 1, $page );
	$posts_per_page = max( 1, min( 50, $posts_per_page ) );
	$per_slide      = max( 1, min( 6, $per_slide ) );

	$html = ekwa_get_episode_slides( $page, $posts_per_page, $per_slide );

	wp_send_json_success( [ 'html' => $html ] );
}
