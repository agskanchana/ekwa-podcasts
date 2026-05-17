<?php
/**
 * Plugin Name: EKWA Podcasts
 * Description: Custom post type for podcast episodes with featured image, audio file, and carousel shortcode.
 * Version:     1.0.0
 * Author:      EKWA
 * License:     GPL-2.0+
 * Text Domain: ekwa-podcasts
 */

defined( 'ABSPATH' ) || exit;

require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/agskanchana/ekwa-podcasts/',
	__FILE__,
	'ekwa-podcasts'
);


define( 'EKWA_PODCASTS_VERSION', '1.0.0' );
define( 'EKWA_PODCASTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'EKWA_PODCASTS_URL', plugin_dir_url( __FILE__ ) );

require_once EKWA_PODCASTS_PATH . 'includes/cpt.php';
require_once EKWA_PODCASTS_PATH . 'includes/meta-boxes.php';
require_once EKWA_PODCASTS_PATH . 'includes/template-hooks.php';
require_once EKWA_PODCASTS_PATH . 'includes/shortcode.php';

register_activation_hook( __FILE__, 'ekwa_podcasts_activate' );
function ekwa_podcasts_activate() {
	ekwa_register_episode_cpt();
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

add_action( 'wp_enqueue_scripts', 'ekwa_podcasts_enqueue_assets' );
function ekwa_podcasts_enqueue_assets() {
	wp_enqueue_style(
		'ekwa-podcasts',
		EKWA_PODCASTS_URL . 'assets/css/ekwa-podcasts.css',
		[],
		EKWA_PODCASTS_VERSION
	);
	wp_enqueue_script(
		'ekwa-podcasts',
		EKWA_PODCASTS_URL . 'assets/js/ekwa-podcasts.js',
		[ 'jquery' ],
		EKWA_PODCASTS_VERSION,
		true
	);
	wp_localize_script( 'ekwa-podcasts', 'ekwaPodcasts', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'ekwa_podcasts_nonce' ),
	] );
}

add_action( 'admin_enqueue_scripts', 'ekwa_podcasts_admin_assets' );
function ekwa_podcasts_admin_assets( $hook ) {
	global $post;
	if ( $hook === 'post-new.php' || $hook === 'post.php' ) {
		if ( isset( $post ) && $post->post_type === 'episode' ) {
			wp_enqueue_media();
		}
	}
}
