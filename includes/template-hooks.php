<?php
defined( 'ABSPATH' ) || exit;

/**
 * Append audio player above content and "Back to Podcasts" link below content
 * on single episode posts.
 */
add_filter( 'the_content', 'ekwa_podcasts_modify_content' );
function ekwa_podcasts_modify_content( $content ) {
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
