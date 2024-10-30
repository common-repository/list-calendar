<?php

/**
 * Post_Dataファクトリー
 *
 * LTCR_Post_Dataのファクトリーです。
 */
class LTCR_Post_Factory
{
	/**
	 * @param $post_id 投稿IDです
	 * @return LTCR_Post_Dataを返します
	 */
	public static function get_post_data( $post_id = null )
	{
		$post_data = new LTCR_Post_Data();
		$post_data->set_id( $post_id );
		$post = get_post( $post_id );
		if ( ! empty( $post ) || 'ltcr' === get_post_type( $post ) ) {
			// 既存投稿
			$post_data->set_is_new( false );
			$post_data->set_id( $post->ID );
			$post_data->set_title( $post->post_title );
		} else {
			// 新規投稿
			$post_data->set_is_new( true );
			$post_data->set_title( __( 'Untitled', 'ltcr' ) );
		}
		return $post_data;
	}
}
