<?php

/**
 * LTCR_Field
 *
 * カスタムフィールドを管理します。
 */
class LTCR_Field
{

	/**
	 * カステムフィールド設定
	 *
	 * 投稿へカスタムフィールドを追加します。
	 *
	 * @param LTCR_Post_Data $post_data 投稿データ
	 * @param string $html 投稿画面のマークアップ
	 */
	public static function set( $post_data, $html )
	{
		/*
		 * カスタムフィールドの値取得
		 */
		// 年, 月取得
		$year  = (int) get_post_meta( $post_data->id, 'year', true );
		$month = (int) get_post_meta( $post_data->id, 'month', true );

		// 既存値なければ本日を設定
		$today = getdate();
		$year  = ( ! empty( $year ) ) ? $year : (int) $today['year'];
		$month = ( ! empty( $month ) ) ? $month : (int) $today['mon'];

		// 曜日取得
		$days  = LTCR_Day::get_days( $year, $month, true );
		$days  = $days["days"];
		$total = count( $days );

		// 関連する投稿取得
		$related_posts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'post-' . $i;
			// if post_data->id is NULL, then $related_posts[ $i ] is false.
			$related_posts[$i] = get_post_meta( $post_data->id, $key, true );
		}

		/*
		 * カスタムフィールド値表示
		 */
		$html .= '<div id="fields">';
		// yeaer
		$html .= '<div id="fields_year_month">' . PHP_EOL
			. __( 'Year', 'ltcr' )
			. ' : <select name="year"><option value="-">--</option>';
		for ( $y = 2000; $y < 2050; $y ++ ) {
			if ( $y === $year ) {
				$html .= '<option value="' . $year . '" selected="selected">' . $year . '</option>';
			} else {
				$html .= '<option value="' . $y . '">' . $y . '</option>';
			}
		}
		$html .= '</select>&nbsp;';

		// month 
		$html .= 'Month : <select name="month"><option value="-">--</option>';
		for ( $m = 1; $m < 13; $m ++ ) {
			if ( $m === $month ) {
				$html .= '<option value="' . $month . '" selected = "selected">' . $month . '</option>';
			} else {
				$html .= '<option value="' . $m . '">' . $m . '</option>';
			}
		}
		$html .= '</select></div><!-- ltcr_fileds_yearandmonth -->' . PHP_EOL;

		$html .= '<div id="fields_date">';

		// オプション取得
		$options = (array) json_decode( get_option( LTCR_OPTION_NAME ) );
		// 設定値表示
		$tags = ( isset( $options['ltcr-tag'] ) ) ? $options['ltcr-tag'] : false;
		for ( $i = 1; $i <= $total; $i ++ ) {
			$html .= '<div class="field">' . PHP_EOL;
			// 日
			$html .= '<span class="date">' . $i . '</span>' . PHP_EOL;
			// 曜日
			$html .= '<span class="days">' . $days[$i] . '</span>' . PHP_EOL;
			// 関連投稿
			if ( ! empty( $tags ) ) {
				$tags_name = explode( ',', $tags );
				$tags_id   = array();
				foreach ( $tags_name as $key => $value ) {
					$tag_prop = get_term_by( 'name', trim( $value ), 'post_tag' );
					if ( ! empty( $tag_prop ) ) {
						$tags_id[$key] = $tag_prop->term_id;
					}
				}

				if ( ! empty( $tags_id ) ) {
					// 該当タグがある場合
					$myposts = get_posts(
						array(
							'numberposts' => 100,
							'tag__in'     => $tags_id
						)
					);
					$html .= '<div class="related-post">' . PHP_EOL;
					$html .= 'post: <select name="post-' . $i . '">' . PHP_EOL;
					$html .= '<option value="-">--</option>';
					foreach ( $myposts as $mypost ) {
						if ( $related_posts[$i] == $mypost->ID ) {
							$html .= '<option value="' . $mypost->ID . '" selected="selected">' . $mypost->post_title . '</option>';
						} else {
							$html .= '<option value="' . $mypost->ID . '">' . $mypost->post_title . '</option>';
						}
					}
					$html .= '</select>' . PHP_EOL;
					$html .= '</div><!-- related-post -->' . PHP_EOL;

				} else {
					$html .= '<br>' . __( 'Tag Not found. Please check tag on List Calendar > Settings', 'ltcr' );
				}
			} else {
				$html .= '<br>' . __( 'Please Set tag do display related post on List Calendar > Settings', 'ltcr' );
			}
			$html .= '</div><!-- field -->';
		}
		$html .= '</div><!-- fields-date -->' . PHP_EOL
			. '</div><!-- fields -->' . PHP_EOL
			. '</div><!-- poststuff -->' . PHP_EOL
			. '</form>' . PHP_EOL
			. '</div>';
		return $html;
	}


	/**
	 * 投稿更新(save)
	 *
	 * @param LTCR_Post_Data $post_data 投稿データ
	 */
	public static function update( $post_data )
	{
		$post_id = $post_data->id;
		// security check (_wpnonce:wp number used once)
		$nonce = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : null;
		if ( ! wp_verify_nonce( $nonce, 'save' ) && ! wp_verify_nonce( $nonce, 'save' . - 1 ) ) {
			return $post_id;
		}

		/*
		 * get value
		 */
		// date
		$year  = (int) $_POST['year'];
		$month = (int) $_POST['month'];
		$days  = LTCR_Day::get_days( $year, $month, true );
		$total = count( $days['days'] );
		$date  = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'date-' . $i;
			if ( isset( $_POST[$key] ) ) {
				$date[$i] = $_POST[$key];
			} else {
				$date[$i] = '';
			}
		}
		// 関連記事
		$related_posts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'post-' . $i;
			if ( isset( $_POST[$key] ) ) {
				$related_posts[$i] = $_POST[$key];
			} else {
				$related_posts[$i] = '';
			}
		}

		/*
		 * 更新
		 */
		if ( '' === $year ) {
			delete_post_meta( $post_id, 'year' );
		} else {
			update_post_meta( $post_id, 'year', $year );
		}
		if ( '' === $month ) {
			delete_post_meta( $post_id, 'month' );
		} else {
			update_post_meta( $post_id, 'month', $month );
		}
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'post-' . $i;
			if ( '' === $related_posts[$i] ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $related_posts[$i] );
			}
		}

	}
}