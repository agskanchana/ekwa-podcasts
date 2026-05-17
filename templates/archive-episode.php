<?php
/**
 * Template: Episode Archive (Podcast Index)
 *
 * Loaded automatically by EKWA Podcasts for the episode post type archive.
 * Place archive-episode.php in your theme root to override this file.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div id="primary" class="content-area ekwa-archive-episode-wrap">
	<main id="main" class="site-main">

		<!-- Archive Header -->
		<header class="ekwa-archive-header">
			<h1 class="ekwa-archive-title">
				<?php
				$archive_title = get_queried_object();
				if ( $archive_title ) {
					echo esc_html( get_post_type_object( 'episode' )->labels->name );
				} else {
					esc_html_e( 'Podcast Episodes', 'ekwa-podcasts' );
				}
				?>
			</h1>
			<?php
			$pt_description = get_the_archive_description();
			if ( $pt_description ) :
			?>
			<div class="ekwa-archive-description">
				<?php echo wp_kses_post( $pt_description ); ?>
			</div>
			<?php endif; ?>
		</header>

		<!-- Episodes Grid -->
		<?php if ( have_posts() ) : ?>

		<div class="ekwa-archive-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				$post_id   = get_the_ID();
				$thumb_url = get_the_post_thumbnail_url( $post_id, 'medium' );
				$duration  = get_post_meta( $post_id, '_ekwa_duration', true );
				$audio_url = get_post_meta( $post_id, '_ekwa_audio_url', true );
				$excerpt   = get_the_excerpt();
				?>
				<article id="episode-<?php the_ID(); ?>" <?php post_class( 'ekwa-archive-card' ); ?>>

					<!-- Card Thumbnail -->
					<a href="<?php the_permalink(); ?>" class="ekwa-archive-card-image-link" tabindex="-1" aria-hidden="true">
						<?php if ( $thumb_url ) : ?>
						<img
							src="<?php echo esc_url( $thumb_url ); ?>"
							alt="<?php the_title_attribute(); ?>"
							class="ekwa-archive-card-thumbnail"
							loading="lazy"
						/>
						<?php else : ?>
						<div class="ekwa-archive-card-no-image"></div>
						<?php endif; ?>
					</a>

					<!-- Card Body -->
					<div class="ekwa-archive-card-body">
						<h2 class="ekwa-archive-card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<div class="ekwa-archive-card-meta">
							<span class="ekwa-archive-card-date">
								<?php echo esc_html( get_the_date( 'M j Y, g:i a' ) ); ?>
							</span>
							<?php if ( $duration ) : ?>
							<span class="ekwa-archive-card-duration">
								Duration: <?php echo esc_html( $duration ); ?>
							</span>
							<?php endif; ?>
						</div>

						<?php if ( $excerpt ) : ?>
						<p class="ekwa-archive-card-excerpt">
							<?php echo esc_html( wp_trim_words( $excerpt, 20, '...' ) ); ?>
						</p>
						<?php endif; ?>

						<a href="<?php the_permalink(); ?>" class="ekwa-archive-card-link">
							Listen &rarr;
						</a>
					</div>

				</article>
			<?php endwhile; ?>
		</div>

		<!-- Pagination -->
		<nav class="ekwa-archive-pagination" aria-label="Episodes pagination">
			<?php
			the_posts_pagination( [
				'mid_size'  => 2,
				'prev_text' => '&larr; Previous',
				'next_text' => 'Next &rarr;',
			] );
			?>
		</nav>

		<?php else : ?>

		<div class="ekwa-archive-none">
			<p><?php esc_html_e( 'No episodes have been published yet. Check back soon!', 'ekwa-podcasts' ); ?></p>
		</div>

		<?php endif; ?>

	</main>
</div>

<?php
get_footer();
