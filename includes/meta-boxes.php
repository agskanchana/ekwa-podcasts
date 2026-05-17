<?php
defined( 'ABSPATH' ) || exit;

add_action( 'add_meta_boxes', 'ekwa_podcasts_add_meta_boxes' );
function ekwa_podcasts_add_meta_boxes() {
	add_meta_box(
		'ekwa_podcast_details',
		'Podcast Details',
		'ekwa_podcast_details_callback',
		'episode',
		'normal',
		'high'
	);
}

function ekwa_podcast_details_callback( $post ) {
	wp_nonce_field( 'ekwa_podcast_details_save', 'ekwa_podcast_nonce' );
	$audio_url = get_post_meta( $post->ID, '_ekwa_audio_url', true );
	$duration  = get_post_meta( $post->ID, '_ekwa_duration', true );
	?>
	<table class="form-table" style="width:100%;">
		<tr>
			<th style="width:140px;"><label for="ekwa_audio_url">Audio File URL</label></th>
			<td>
				<div style="display:flex;gap:8px;align-items:center;">
					<input
						type="url"
						id="ekwa_audio_url"
						name="ekwa_audio_url"
						value="<?php echo esc_attr( $audio_url ); ?>"
						class="large-text"
						placeholder="https://example.com/episode.mp3"
						style="flex:1;"
					/>
					<button type="button" class="button ekwa-upload-audio-btn">Upload / Select</button>
				</div>
				<p class="description">Enter or upload an audio file (MP3, M4A, OGG, etc.)</p>
			</td>
		</tr>
		<tr>
			<th><label for="ekwa_duration">Duration</label></th>
			<td>
				<input
					type="text"
					id="ekwa_duration"
					name="ekwa_duration"
					value="<?php echo esc_attr( $duration ); ?>"
					placeholder="00:14:51"
					style="width:120px;"
				/>
				<p class="description">Format: HH:MM:SS</p>
			</td>
		</tr>
	</table>
	<script>
	(function( $ ) {
		$( '.ekwa-upload-audio-btn' ).on( 'click', function( e ) {
			e.preventDefault();
			var frame = wp.media( {
				title:   'Select Audio File',
				button:  { text: 'Use this file' },
				library: { type: 'audio' },
				multiple: false,
			} );
			frame.on( 'select', function() {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$( '#ekwa_audio_url' ).val( attachment.url );
			} );
			frame.open();
		} );
	})( jQuery );
	</script>
	<?php
}

add_action( 'save_post_episode', 'ekwa_podcast_save_meta' );
function ekwa_podcast_save_meta( $post_id ) {
	if (
		! isset( $_POST['ekwa_podcast_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['ekwa_podcast_nonce'] ) ),
			'ekwa_podcast_details_save'
		)
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['ekwa_audio_url'] ) ) {
		update_post_meta(
			$post_id,
			'_ekwa_audio_url',
			esc_url_raw( wp_unslash( $_POST['ekwa_audio_url'] ) )
		);
	}

	if ( isset( $_POST['ekwa_duration'] ) ) {
		$duration = sanitize_text_field( wp_unslash( $_POST['ekwa_duration'] ) );
		if ( preg_match( '/^\d{1,2}:\d{2}(:\d{2})?$/', $duration ) ) {
			update_post_meta( $post_id, '_ekwa_duration', $duration );
		} else {
			delete_post_meta( $post_id, '_ekwa_duration' );
		}
	}
}
