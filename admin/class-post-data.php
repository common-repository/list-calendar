<?php

/**
 * 投稿データ
 *
 * 投稿(投稿タイプltcr)のラッパークラスです。
 *
 */
class LTCR_Post_Data
{
	const post_type = LTCR_POST_TYPE;
	public $is_new = false;
	public $id;
	public $title;

	/**
	 * 新規判定フラグ
	 */
	public function set_is_new( $is_new )
	{
		$this->is_new = $is_new;
	}

	/**
	 * 投稿ID設定
	 */
	public function set_id( $post_id )
	{
		$this->id = $post_id;
	}

	/**
	 * 投稿タイトル設定
	 */
	public function set_title( $title )
	{
		$this->title = $title;
	}
}
