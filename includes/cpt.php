<?php
defined( 'ABSPATH' ) || exit;

add_action( 'init', 'ekwa_register_episode_cpt' );
function ekwa_register_episode_cpt() {
	$labels = [
		'name'               => 'Podcast Episodes',
		'singular_name'      => 'Episode',
		'add_new'            => 'Add New Episode',
		'add_new_item'       => 'Add New Episode',
		'edit_item'          => 'Edit Episode',
		'new_item'           => 'New Episode',
		'view_item'          => 'View Episode',
		'search_items'       => 'Search Episodes',
		'not_found'          => 'No episodes found',
		'not_found_in_trash' => 'No episodes found in trash',
		'menu_name'          => 'Podcasts',
	];

	$args = [
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => 'podcasts',
		'rewrite'            => [
			'slug'       => 'episode',
			'with_front' => false,
		],
		'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'menu_icon'          => 'dashicons-microphone',
		'show_in_rest'       => true,
		'menu_position'      => 5,
	];

	register_post_type( 'episode', $args );
}
