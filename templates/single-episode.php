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

	$post_id     = get_the_ID();
	$audio_url   = get_post_meta( $post_id, '_ekwa_audio_url', true );
	$duration    = get_post_meta( $post_id, '_ekwa_duration', true );
	$archive_url = apply_filters(
		'ekwa_podcasts_archive_url',
		get_post_type_archive_link( 'episode' )
	);

	$post_url    = rawurlencode( get_permalink() );
	$post_title  = rawurlencode( get_the_title() );
	$fb_url      = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
	$x_url       = 'https://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title;
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

				<!-- Audio Player (above title, matching design) -->
				<?php if ( $audio_url ) : ?>
				<div class="ekwa-audio-player">
					<audio controls>
						<source src="<?php echo esc_url( $audio_url ); ?>" />
						<?php esc_html_e( 'Your browser does not support the audio element.', 'ekwa-podcasts' ); ?>
					</audio>
				</div>
				<?php endif; ?>

				<!-- Title + Meta + Share -->
				<header class="ekwa-single-header">
					<h1 class="ekwa-single-title"><?php the_title(); ?></h1>

					<div class="ekwa-single-meta">
						<span class="ekwa-single-date">
							<?php echo esc_html( strtoupper( get_the_date( 'j M, Y' ) ) ); ?>
						</span>
						<?php if ( $duration ) : ?>
						<span class="ekwa-single-duration">
							<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php echo esc_html( $duration ); ?>
						</span>
						<?php endif; ?>
					</div>

					<!-- Share Buttons -->
					<div class="ekwa-share-buttons" role="group" aria-label="Share this episode">
						<a
							href="<?php echo esc_url( $fb_url ); ?>"
							class="ekwa-share-btn ekwa-share-facebook"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="Share on Facebook"
						>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
							Share
						</a>
						<a
							href="<?php echo esc_url( $x_url ); ?>"
							class="ekwa-share-btn ekwa-share-x"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="Share on X"
						>
							<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622 5.911-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
							Share
						</a>
					</div>
				</header>

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
