<?php

/**
 * カレンダーデータ作成
 *
 * 出力するカレンダーを作成します。
 */
class LTCR_Calendar_Data
{

	/**
	 * カレンダー出力データ取得
	 *
	 * @param $post_id 投稿IDです。
	 * @return string  カレンダー表示用マークアップです。
	 */
	public static function get( $post_id )
	{
		$year        = (int) get_post_meta( $post_id, 'year', true );
		$month       = (int) get_post_meta( $post_id, 'month', true );
		$data         = LTCR_Day::get_days( $year, $month, false );
		// 日付
		$days        = $data['days'];
		// 曜日
		$day_of_week = $data['day_of_week'];
		$total       = count( $days );
		// 選択された投稿取得
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key         = 'post-' . $i;
			$post_ids[$i] = get_post_meta( $post_id, $key, true );
		}
		$html = self::make(
			$year,
			$month,
			$day_of_week,
			$post_ids
		);
		return $html;
	}

	/**
	 * 曜日出力用マークアップ作成
	 *
	 * @param string $y year yyyy
	 * @param string $m month 1～13
	 * @param array $day_of_week 曜日(0:日〜6:土)のラベル
	 * @param array $post_ids 曜日に紐づいた投稿
	 * @return string 曜日のマークアップ
	 */
	private static function make( $y, $m, $day_of_week, $post_ids )
	{
		// $y 年 $m 月
		$t = mktime( 0, 0, 0, $m, 1, $y ); // $y年$m月1日のUNIXTIME
		$w = date( 'w', $t );              // 1日の曜日（0:日～6:土）
		$n = date( 't', $t );              // $y年$m月の日数
		if ( $m < 10 ) {
			$m = '0' . $m;
		}

		$options = (array ) json_decode( get_option( LTCR_OPTION_NAME ) );
		if (isset($options["ltcr-table-class"]) && $options["ltcr-table-class"] ){
			$table_class = ' ' . $options["ltcr-table-class"];
		} else {
			$table_class = '';
		}
		$html = '<table class="ltcr' . $table_class . '">' . PHP_EOL
			. '<caption>' . $y . ' ' . $m . '</caption>';

		for ( $i = 1 - $w; $i <= $n + 7; $i ++ ) {
			if ( ( ( $i + $w ) % 7 ) == 1 ) {
				$html .= "<tr>" . PHP_EOL;
			}
			if ( ( 0 < $i ) && ( $i <= $n ) ) {
				// 日付が有効な場合の処理
				/*
				 * 日付,曜日取得
				 */ 
				$time = mktime( 0, 0, 0, $m, $i, $y ); //$y年$m月$i日のUNIXTIME
				$day  = date( 'w', $time ); // 1日の曜日（0:日～6:土）

				$day_class='';
				if ( 0 === (int) $day ) {
					$day_class = ' class="ltcr-td-sun"';
				} else {
					if ( 6 === (int) $day ) {
						$day_class = ' class="ltcr-td-sat"';
					}
				}
				/*
				 * 日付、曜日出力
				 */
				$html .= '<tr>' . PHP_EOL
					. '<td' . $day_class . '>' . PHP_EOL
					. '<span class="ltcr-date">' . $i . '</span>' . PHP_EOL
					. '<span class="ltcr-day">' . $day_of_week[$day] . '</span>' . PHP_EOL
					. '</td>' . PHP_EOL;
				/*
				 * 投稿取得
				 */
				if ( is_numeric( $post_ids[$i] ) ) {
					$relate_post = get_post( $post_ids[$i] );
					$link   = '<a href="' . esc_url(get_permalink( $post_ids[$i] )) . '">' 
						. esc_html($relate_post->post_title) . '</a>';
				} else {
					$link = '';
				}
				/*
				 * 投稿出力
				 */
				$html .= '<td>' . PHP_EOL;
				if ( ! empty( $link ) ) {
					$html .= '<span class="ltcr-link">' . $link . '</span>';
				}
			} else { 
				// 日付が無効な場合
				continue;
			}
			if ( ( ( $i + $w ) % 7 ) == 0 ) {
				$html .= '</td></tr>' . PHP_EOL;
				if ( $i >= $n ) {
					break;
				}
			}
		}
		$html .= '</table>' . PHP_EOL;
		return $html;
	}

}
