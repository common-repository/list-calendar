<?php

/**
 * カレンダー描画
 *
 * ショートタグでカレンダーを出力します。
 */
class LTCR_Calendar_Drawing
{

	/*
	 * カレンダー描画処理
	 * 
	 */
	public function run()
	{
		add_action( 'plugins_loaded', array( &$this, 'add_shortcodes' ), 1 );
	}

	/**
	 * ショートコード実行
	 */
	function add_shortcodes()
	{
		add_shortcode( 'ltcr', array( &$this, 'draw' ) );
	}

	/**
	 * カレンダー描画
	 * 
	 * ショートコードは戻り値を描画します。
	 *
	 * [ltcr id="1234" title="example"]
	 *
	 * @param $atts
	 *   id 投稿ID , title 投稿タイトル
	 * @param null $content
	 * @param string $code ショートコード名
	 * @return string 描画データのマークアップ
	 */
	public function draw( $atts, $content = null, $code = '' )
	{
		if ( is_feed() ) {
			return '[ltcr]';
		}

		if ( 'ltcr' === $code ) {
			$atts = shortcode_atts(
				array( 'id' => 0 ),
				$atts
			);
			$id   = (int) $atts['id'];
		}

		// make calendar markup
		$unit_tag = 'ltcr-' . $id;
		$html     = '<div id="' . $unit_tag . '" class="ltcr">';
		/*
		 * カレンダーデータ取得
		 */
		$html .= LTCR_Calendar_Data::get( $id );
		$html .= '</div>';

		return $html;

	}
}
