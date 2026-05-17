<?php
defined( 'ABSPATH' ) || exit;

/**
 * Load plugin templates for single episodes and the episode archive,
 * allowing theme overrides: place single-episode.php or archive-episode.php
 * in your theme root to override the plugin's template.
 */
add_filter( 'template_include', 'ekwa_podcasts_template_include', 99 );
function ekwa_podcasts_template_include( $template ) {
	if ( is_singular( 'episode' ) ) {
		// Theme override wins if it exists.
		$theme_file = locate_template( [ 'single-episode.php' ] );
		if ( $theme_file ) {
			return $theme_file;
		}
		$plugin_file = EKWA_PODCASTS_PATH . 'templates/single-episode.php';
		if ( file_exists( $plugin_file ) ) {
			return $plugin_file;
		}
	}

	if ( is_post_type_archive( 'episode' ) ) {
		$theme_file = locate_template( [ 'archive-episode.php' ] );
		if ( $theme_file ) {
			return $theme_file;
		}
		$plugin_file = EKWA_PODCASTS_PATH . 'templates/archive-episode.php';
		if ( file_exists( $plugin_file ) ) {
			return $plugin_file;
		}
	}

	return $template;
}

/**
 * Append audio player above content and "Back to Podcasts" link below content
 * on single episode posts — only when the plugin template is NOT handling it
 * (i.e. a third-party theme is rendering the post without our template).
 */
add_filter( 'the_content', 'ekwa_podcasts_modify_content' );
function ekwa_podcasts_modify_content( $content ) {
	// Skip when our own template is in control.
	if ( defined( 'EKWA_PODCASTS_USING_TEMPLATE' ) ) {
		return $content;
	}

	if ( ! is_singular( 'episode' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$post_id   = get_the_ID();
	$audio_url = get_post_meta( $post_id, '_ekwa_audio_url', true );
	$duration  = get_post_meta( $post_id, '_ekwa_duration', true );

	// Audio player above content
	$audio_html = '';
	if ( $audio_url ) {
		$duration_attr = $duration ? ' data-duration="' . esc_attr( $duration ) . '"' : '';
		$audio_html    = '<div class="ekwa-audio-player">'
			. '<audio controls>'
			. '<source src="' . esc_url( $audio_url ) . '" />'
			. 'Your browser does not support the audio element.'
			. '</audio>';

		if ( $duration ) {
			$audio_html .= '<p class="ekwa-duration-label">Duration: ' . esc_html( $duration ) . '</p>';
		}

		$audio_html .= '</div>';
	}

	// Back to Podcasts link below content
	$archive_url = apply_filters(
		'ekwa_podcasts_archive_url',
		get_post_type_archive_link( 'episode' )
	);

	$back_link = '<p class="ekwa-back-to-podcasts">'
		. '<a href="' . esc_url( $archive_url ) . '">'
		. '&larr; Back to Podcasts'
		. '</a>'
		. '</p>';

	return $audio_html . $content . $back_link;
}
