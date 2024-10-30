<?php

/**
 * アクティベート処理
 *
 */
class LTCR_Activation
{
	/**
	 * アクティベート処理
	 */
	public function on() {
		/*
		 * 既存オプション確認
		 */
		$option = get_option( LTCR_OPTION_NAME );
		if ( ! empty( $option ) ) {
			return;
		}
		/*
		 * テキストドメイン登録
		 */
		load_plugin_textdomain( LTCR_TEXT_DOMAIN, false, 'list-calendar/languages' );
		/*
		 * カスタム投稿タイプ登録
		 */
		$this->register_post_types();
	}

	/**
	 * カスタム投稿タイプ登録
	 */
	private function register_post_types()
	{
		register_post_type(
			LTCR_POST_TYPE,
			array(
				'labels'    => array(
					'name'          => 'List Calendar',
					'singular_name' => 'List Calendar',
				),
				'rewrite'   => false,
				'query_var' => false,
				'capability_type' => array( LTCR_POST_TYPE, LTCR_POST_TYPES ),
				'map_meta_cap'    => true
			)
		);
	}
}
