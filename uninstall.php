<?php
//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

function ltcr_delete_plugin()
{
	// プラグインoption削除
	delete_option( 'ltcr');

	// 投稿削除
	$posts = get_posts(
		array(
			'numberposts' => - 1,
			'post_type'   => 'ltcr',
			'post_status' => 'any'
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

ltcr_delete_plugin();
