<?php
/**
 * Template: Single Episode
 *
 * Loaded automatically by EKWA Podcasts for single 'episode' posts.
 * Place single-episode.php in your theme root to override this file.
 */

defined( 'ABSPATH' ) || exit;

// Signal to the_content filter that the plugin template handles output directly.
define( 'EKWA_PODCASTS_USING_TEMPLATE', true );

get_header();

while ( have_posts() ) :
	the_post();

	$post_id   = get_the_ID();
	$audio_url = get_post_meta( $post_id, '_ekwa_audio_url', true );
	$duration  = get_post_meta( $post_id, '_ekwa_duration', true );
	$archive_url = apply_filters(
		'ekwa_podcasts_archive_url',
		get_post_type_archive_link( 'episode' )
	);
	?>

	<div id="primary" class="content-area ekwa-single-episode-wrap">
		<main id="main" class="site-main">

			<article id="episode-<?php the_ID(); ?>" <?php post_class( 'ekwa-episode-article' ); ?>>

				<!-- Featured Image -->
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="ekwa-single-featured-image">
					<?php the_post_thumbnail( 'large', [ 'class' => 'ekwa-single-thumbnail' ] ); ?>
				</div>
				<?php endif; ?>

				<!-- Title + Meta -->
				<header class="ekwa-single-header">
					<h1 class="ekwa-single-title"><?php the_title(); ?></h1>
					<div class="ekwa-single-meta">
						<span class="ekwa-single-date">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
							<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
						</span>
						<?php if ( $duration ) : ?>
						<span class="ekwa-single-duration">
							<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php echo esc_html( $duration ); ?>
						</span>
						<?php endif; ?>
					</div>
				</header>

				<!-- Audio Player -->
				<?php if ( $audio_url ) : ?>
				<div class="ekwa-audio-player">
					<audio controls>
						<source src="<?php echo esc_url( $audio_url ); ?>" />
						<?php esc_html_e( 'Your browser does not support the audio element.', 'ekwa-podcasts' ); ?>
					</audio>
				</div>
				<?php endif; ?>

				<!-- Episode Content -->
				<div class="ekwa-single-content entry-content">
					<?php the_content(); ?>
				</div>

				<!-- Back to Podcasts -->
				<div class="ekwa-back-to-podcasts">
					<a href="<?php echo esc_url( $archive_url ); ?>">
						&larr; <?php esc_html_e( 'Back to Podcasts', 'ekwa-podcasts' ); ?>
					</a>
				</div>

			</article>

		</main>
	</div>

	<?php
endwhile;

get_footer();
